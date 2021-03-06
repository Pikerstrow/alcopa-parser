<?php

namespace App\Console\Commands;

use App\Auction;
use App\Parser\AuctionCarParser;
use App\Parser\AuctionCarsJsonParser;
use App\Parser\AuctionsParser;
use App\ParserInfo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;



class ParseAlcopaAuctions extends Command
{
    /**
     * @var array For gathering parser results and store them in DB in case when parser fails
     */
    private $parsed_auctions = [];
    private $parsed_cars = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:parse_alcopa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses all current auctions from `https://alcopa-auction.fr/en/` and each car within auction' ;


    /**
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $parser = new AuctionsParser();
            $auctions_data = $parser->parse();

            //Auctions progress bar
            $bar = $this->output->createProgressBar(count($auctions_data));
            $bar->start();

            foreach ($auctions_data as $item) {
                $auction = Auction::updateOrCreate(
                    ['auction_id' => $item['auction_id']],
                    $item
                );

                if (!in_array($auction->auction_id, $this->parsed_auctions)) {
                    $cars_parser = new AuctionCarsJsonParser();
                    $auction_cars_data = $cars_parser->parse($auction->uri);

                    foreach ($auction_cars_data as $category => $cars){
                        $bar_cars = $this->output->createProgressBar(count($cars));
                        foreach ($cars as $info){
                            $parsed_cars_array = $this->parsed_cars[$auction->auction_id] ?? [];
                            if (!in_array($info['id'], $parsed_cars_array)) {
                                $car_parser = new AuctionCarParser($info, $auction);
                                $car = $car_parser->parse();
                                $this->parsed_cars[$auction->auction_id][] = $car->alcopa_car_id;
                                $bar_cars->advance();
                            }
                        }
                        $bar_cars->finish();
                    }
                    $this->parsed_auctions[] = $auction->auction_id;
                    $bar->advance();
                }
            }

            $bar->setMessage('Parsing finished!');
            $bar->finish();

            ParserInfo::updateOrCreate(['id' => 1], [
                'last_parsing_date' => (new Carbon())->toDateTimeString(),
                'last_parsing_result' => ParserInfo::SUCCESS
            ]);
        } catch (\Throwable $exception) {
            Log::info(['error'  => $exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine()]);
            ParserInfo::updateOrCreate(['id' => 1], [
                'last_parsing_date' => (new Carbon())->toDateTimeString(),
                'last_parsing_result' => ParserInfo::FAILED,
                'parsed_auctions' => json_encode($this->parsed_auctions),
                'parsed_cars' => json_encode($this->parsed_cars)
            ]);
        }
    }


    /**
     * @throws \Exception
     */
    private function getLastParsingInfo()
    {
        $last_parsing_info = ParserInfo::first();
        if (!empty($last_parsing_info)) {
            $now = new Carbon();
            $last_parsing_date = new Carbon($last_parsing_info->last_parsing_date);

            if ($now->diffInHours($last_parsing_date) > TIME_GAP_HOURS && !$last_parsing_info->last_parsing_result) {
                if (!empty($last_parsing_info->parsed_auctions)) {
                    $this->parsed_auctions = json_decode($last_parsing_info->parsed_auctions, true);
                }
                if (!empty($last_parsing_info->parsed_cars)) {
                    $this->parsed_cars = json_decode($last_parsing_info->parsed_cars, true);
                }
            }
        }
    }
}
