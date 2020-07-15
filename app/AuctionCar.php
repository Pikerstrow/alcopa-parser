<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuctionCar extends Model
{
    protected $fillable = [
        'car_make_id',
        'car_model_id',
        'auction_id',
        'alcopa_car_id',
        'category',
        'lot_number',
        'lot_price',
        'lot_price_currency',
        'finish',
        'registration_number',
        'fuel_type',
        'first_produced',
        'mileage',
        'vin',
        'storage_location',
        'gearbox',
        'description',
        'inspection_report_url'
    ];



    /**
     * @return BelongsTo
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }


    /**
     * @return BelongsTo
     */
    public function carMake(): BelongsTo
    {
        return $this->belongsTo(CarMake::class);
    }


    /**
     * @return BelongsTo
     */
    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }



    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(AuctionCarImage::class);
    }
}
