<?php

namespace App\Http\Controllers\Parser;

use App\Auction;
use App\Http\Controllers\Controller;
use App\Parser\AuctionCarParser;
use App\Parser\AuctionCarsJsonParser;
use App\Parser\AuctionsParser;
use App\ParserInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


/**
 * Class ParserController
 * @package App\Http\Controllers\Parser
 *
 * IMPORTANT!!! Controller for developing mode only (better debugging)!
 * For development use command (php artisan parser:parse_alcopa)
 */
class ParserController extends Controller
{
    private $parsed_auctions = [];
    private $parsed_cars = [];


    public function __construct()
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


    public function parse()
    {
        try {
            $parser = new AuctionsParser();
            $auctions_data = $parser->parse();

            foreach ($auctions_data as $item) {
                $auction = Auction::updateOrCreate(
                    ['auction_id' => $item['auction_id']],
                    $item
                );
                if (!in_array($auction->auction_id, $this->parsed_auctions)) {
                    $cars_parser = new AuctionCarsJsonParser();
                    $auction_cars_data = $cars_parser->parse($auction->uri);

                    foreach ($auction_cars_data as $category => $cars){
                        foreach ($cars as $info){
                            $parsed_cars_array = $this->parsed_cars[$auction->auction_id] ?? [];
                            if (!in_array($info['id'], $parsed_cars_array)) {
                                $car_parser = new AuctionCarParser($info, $auction);
                                $car = $car_parser->parse();
                                $this->parsed_cars[$auction->auction_id][] = $car->alcopa_car_id;
                            }
                        }
                    }
                    $this->parsed_auctions[] = $auction->auction_id;
                }
            }
            ParserInfo::updateOrCreate(['id' => 1], [
                'last_parsing_date' => (new Carbon())->toDateTimeString(),
                'last_parsing_result' => ParserInfo::SUCCESS
            ]);
            echo "<h1 style='color:red;text-align:center'>FINISHED!!!!</h1>";
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
}
