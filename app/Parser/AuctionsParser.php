<?php


namespace App\Parser;

use App\Exceptions\AuctionsParserException;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

class AuctionsParser
{
    private const BASE_URL = 'https://www.alcopa-auction.fr/en/';
    private const NEEDLE_H4 = 'Room sale';
//    private const AUCTION_DATA_TAG = 'div.p-2';
    private const STREAM_CONTEXT_OPTIONS = [
        'http' => [
            'method' => "GET",
            'header' => "Accept-language: en\r\n" .
                "Cookie: foo=bar\r\n" .
                "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
        ]
    ];
    private $tags;


    /**
     * AuctionsParser constructor.
     */
    public function __construct()
    {
        $this->tags = config('parser.auctions');
    }


    public function parse()
    {
        $auctions_data = [];
        $tags          = $this->tags;

        try {
            $context = stream_context_create(static::STREAM_CONTEXT_OPTIONS);
            $html    = file_get_contents(static::BASE_URL, false, $context);
            $crawler = new Crawler(null, static::BASE_URL);
            $crawler->addHtmlContent($html, 'UTF-8');

            //Find all auctions blocks (Room sales, Web sale, etc)
            $nodes = $crawler->filter('div.col-md-6 h4.h5' );
            //Find needle (Room sale)
            $auctions_nodes = null;
            if (!empty($nodes) && count($nodes) > 0) {
                $nodes->each(static function (Crawler $node) use(&$auctions_nodes){
                   if ($node->text() === static::NEEDLE_H4) {
                       $auctions_nodes = $node->siblings();
                   };
                });
            }

            if (!empty($auctions_nodes)) {
                $auctions_nodes->each(static function (Crawler $node) use ($tags, &$auctions_data) {
                    $auction = [];
                    foreach ($tags as $property => $tag) {
                        if (!empty($tag)) {
                            //Sometimes we can get an empty node (filter method). In such case library throw an exception.
                            //So for avoid script crushing we need to wrap next block of code into 'try catch'.
                            //We don't need to handle exception as this is normal situation... all we need to do is just continue our iteration
                            //and gather necessary information.
                            try {
                                $property === 'url'
                                    ? $auction[$property] = $node->filter($tag)->first()->attr('href')
                                    : $auction[$property] = $node->filter($tag)->first()->text();
                            } catch (\InvalidArgumentException $exception) {
                                continue;
                            }
                        };
                    }
                    $auction['auction_id']  = static::getAuctionId($auction['url']);
                    $auction['lots_number'] = static::clearLotsNumber($auction['lots_number']);
                    $auction['start_date']  = static::transformDate($auction['start_date']);
                    $auctions_data[]        = $auction;
                });
            }
            return $auctions_data;
        } catch (\Throwable $exception) {
            $message = 'Auctions parsing operation failed. Reason: ' . $exception->getMessage();
            $message .= '. File: ' . $exception->getFile() . '. Line: ' . $exception->getLine();
            throw new AuctionsParserException($message);
        }
    }


    /**
     * @param string $url
     * @return false|string
     */
    private static function getAuctionId (string $url)
    {
        $ques_mark_pos = strpos($url, '?');
        $slash_pos     = strrpos($url, '/');

        return $ques_mark_pos
            ? substr($url,  ($slash_pos + 1), ($ques_mark_pos - $slash_pos))
            : substr($url,  ($slash_pos + 1));
    }


    /**
     * @param string $lots_number
     * @return string|string[]|null
     */
    private static function clearLotsNumber (string $lots_number)
    {
        return preg_replace('#\D#', '', $lots_number);
    }


    /**
     * @param string $date
     * @return string
     * @throws \Exception
     */
    private static function transformDate (string $date)
    {
        return (new Carbon(strtotime($date)))->toDateTimeString();
    }
}
