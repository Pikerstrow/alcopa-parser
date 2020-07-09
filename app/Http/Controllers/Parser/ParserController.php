<?php

namespace App\Http\Controllers\Parser;

use App\Auction;
use App\Http\Controllers\Controller;
use App\Parser\AuctionCarsParser;
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
            foreach ($auctions_data as $item) {
                $auction = Auction::updateOrCreate(
                    ['auction_id' => $item['auction_id']],
                    $item
                );
                $cars_parser = new AuctionCarsParser();
//                $auction_cars_data = $cars_parser->parse('tete');
                $auction_cars_data = $cars_parser->parse($auction->url);
                $cars_url = array_column($auction_cars_data['car'], 'url');
                dd($cars_url);
            }
        } catch (\Throwable $exception) {
            //TODO::Logging
            dd($exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine());
        }
    }
}
