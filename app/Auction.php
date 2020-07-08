<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = ['name', 'url', 'start_date', 'end_date', 'city', 'lots_number', 'auction_id'];
}
