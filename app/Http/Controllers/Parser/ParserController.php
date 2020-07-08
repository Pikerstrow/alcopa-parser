<?php

namespace App\Http\Controllers\Parser;

use App\Auction;
use App\Http\Controllers\Controller;
use App\Parser\AuctionsParser;
use Illuminate\Http\Request;


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
            echo "Data parsed! Storing auctions in DB </br>" . PHP_EOL;
            ob_flush();
            flush();
            foreach ($auctions_data as $item) {
                $auction = Auction::updateOrCreate(
                    ['auction_id' => $item['auction_id']],
                    $item
                );
            }
            echo "Done!!!";
            ob_end_flush();
        } catch (\Throwable $exception) {
            //TODO::Logging
            dd($exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine());
        }
    }
}
