<?php

namespace App\Http\Controllers\Parser;

use App\Auction;
use App\Http\Controllers\Controller;
use App\Parser\AuctionCarsJsonParser;
use App\Parser\AuctionsParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ParserController extends Controller
{
    public function parse()
    {
        //TODO:: don't forget remove buffering after task finish.
        try {
            if (ob_get_level() == 0) ob_start();

            echo "Parser started </br>" . PHP_EOL;
            ob_flush();
            flush();


            $parser = new AuctionsParser();
            $auctions_data = $parser->parse();


            $test = [];

            echo "Auctions JSON cars parser start! </br>" . PHP_EOL;
            ob_flush();
            flush();

            foreach ($auctions_data as $item) {
                $auction = Auction::updateOrCreate(
                    ['auction_id' => $item['auction_id']],
                    $item
                );
                $cars_parser = new AuctionCarsJsonParser();
                $auction_cars_data = $cars_parser->parse($auction->url);
                $test[] = $auction_cars_data;

                echo "Auction {$auction->city} cars parsed! </br>" . PHP_EOL;
                ob_flush();
                flush();
            }

            echo "Done!!!";
            ob_end_flush();

            
            dd($test);
        } catch (\Throwable $exception) {
            Log::useDailyFiles(storage_path().'/logs/parser.log');
            Log::info(['error' => $exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine()]);
//            dd($exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine());
        }
    }
}
