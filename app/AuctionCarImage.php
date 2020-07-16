<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuctionCarImage extends Model
{
    protected $fillable = ['auction_car_id', 'uri', 'path'];


    /**
     * @param $path
     * @return bool
     * Description: Some images parsed from Alcope has '.jpeg' extension but isn't image.
     * So we need to be able detect such images
     */
    public static function isXMLTypeOfFile($path)
    {
        return mime_content_type(storage_path('app' . DS . $path)) === 'text/xml';
    }
}
