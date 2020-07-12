<?php


namespace App\Parser;

use App\Exceptions\AuctionsParserException;
use App\Parser\Traits\SharedMethods;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

class AuctionCarsJsonParser
{
    use SharedMethods;


    private const BASE_URL = 'https://www.alcopa-auction.fr';


    /**
     * @param $uri
     * @return mixed
     * @throws AuctionsParserException
     */
    public function parse($uri)
    {
        try {
            $url = static::BASE_URL . $uri;
            $html = $this->getWebPage($url);
            $html = preg_replace('/\s+/', ' ', $html);
            preg_match('#searchResultsJSONString\s?=\s?\'(.*)\';#i', $html, $matches);
            if(!empty($matches) && isset($matches[1])){
                $encoded_json = $matches[1];
                return $this->decodeJson($encoded_json);
            }
            throw new \Exception('Cutting encoded json from web page filed.');
        } catch (\Throwable $exception) {
            $message = 'Auction cars parsing operation failed. Reason: ' . $exception->getMessage();
            $message .= '. File: ' . $exception->getFile() . '. Line: ' . $exception->getLine();
            throw new AuctionsParserException($message);
        }
    }


    /**
     * @param string $encoded_json
     * @return mixed
     * @throws \Exception
     */
    private function decodeJson(string $encoded_json)
    {
        try {
            $json = preg_replace("/\\\\u([0-9a-fA-F]{4})/", "&#x\\1;", $encoded_json);
            $json = iconv("UTF-8", "ISO-8859-1//IGNORE", $json);
            $json = html_entity_decode($json, ENT_COMPAT, 'UTF-8');
            $json = stripcslashes($json);
            return json_decode($json, true, 512, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Throwable $exception) {
            throw new \Exception('Decode json with cars info operation failed. Reason: ' .$exception->getMessage());
        }
    }
}
