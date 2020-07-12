<?php


namespace App\Parser\Traits;


use Carbon\Carbon;



trait SharedMethods
{
    /**
     * @param string $date
     * @return string
     * @throws \Exception
     */
    private static function transformDate (string $date)
    {
        return (new Carbon(strtotime($date)))->toDateTimeString();
    }



    /**
     * @param $uri
     * @return bool|string
     * @throws \Exception
     */
    private function getWebPage($url)
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $contents = curl_exec($ch);
            curl_close($ch);
            return $contents;
        } catch (\Throwable $exception) {
            throw new \Exception('Getting web page operation via curl failed');
        }
    }
}
