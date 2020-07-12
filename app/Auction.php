<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Auction extends Model
{
    protected $fillable = ['name', 'uri', 'start_date', 'end_date', 'city', 'lots_number', 'auction_id'];


    /**
     * @return HasMany
     */
    public function cars(): HasMany
    {
        return $this->hasMany(AuctionCar::class);
    }
}
