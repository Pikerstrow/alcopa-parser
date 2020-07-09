<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuctionCarImage extends Model
{
    protected $fillable = ['auction_car_id', 'uri', 'path'];


}
