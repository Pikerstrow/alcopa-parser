<?php


namespace App\Parser;

use App\Exceptions\AuctionsParserException;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

class AuctionCarsParser
{
    private const BASE_URL = 'https://www.alcopa-auction.fr';
    private const STREAM_CONTEXT_OPTIONS = [
        'http' => [
            'method' => "GET",
            'header' => "Accept-language: en\r\n" .
                "Cookie: foo=bar\r\n" .
                "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
        ]
    ];



    public function parse($uri)
    {
        try {
            $html = $this->getWebPage($uri);
//            $html = include('test.php');
            preg_match('#window\.Alcopa\.searchResultsJSONString =(.*?);#i', $html, $matches);
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
            $json = preg_replace("/u([0-9a-fA-F]{4})/", "&#x\\1;", $encoded_json);
            $json = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $json);
            $json = html_entity_decode($json, ENT_COMPAT, 'UTF-8');
            $json = stripcslashes($json);
            $json = trim($json, "\ \t\n\r\0\x0B'");
            $json = str_replace('\\', '', $json);
//            dd($json);
            return dd(json_decode($json, true, 512, JSON_INVALID_UTF8_SUBSTITUTE));
        } catch (\Throwable $exception) {
            throw new \Exception('Decode json with cars info operation failed. Reason: ' .$exception->getMessage());
        }
    }


    /**
     * @param $uri
     * @return bool|string
     * @throws \Exception
     */
   private function getWebPage($uri)
   {
       try {
           $ch = curl_init( static::BASE_URL . $uri );
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
           $contents = curl_exec($ch);
           curl_close($ch);
           return $contents;
       } catch (\Throwable $exception) {
           throw new \Exception('Getting auction page operation via curl failed');
       }
   }
}
