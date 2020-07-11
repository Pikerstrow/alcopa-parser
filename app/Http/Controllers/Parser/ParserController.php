<?php

namespace App\Http\Controllers\Parser;

use App\Auction;
use App\Http\Controllers\Controller;
use App\Parser\AuctionCarsJsonParser;
use App\Parser\AuctionsParser;
use Illuminate\Http\Request;


class ParserController extends Controller
{
    public function parse()
    {
        //TODO:: don't forget remove buffering after task finish.
        try {
            $parser = new AuctionsParser();
            $auctions_data = $parser->parse();
            $test = [];
            foreach ($auctions_data as $item) {
                $auction = Auction::updateOrCreate(
                    ['auction_id' => $item['auction_id']],
                    $item
                );
                $cars_parser = new AuctionCarsJsonParser();
                $auction_cars_data = $cars_parser->parse($auction->url);
                $cars_url = array_column($auction_cars_data['car'], 'url');
                $test[] = $cars_url;
            }
            dd($test);
        } catch (\Throwable $exception) {
            //TODO::Logging
            dd($exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine());
        }
    }
}
