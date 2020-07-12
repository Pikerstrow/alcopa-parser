<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CarModel extends Model
{
    protected $fillable = ['name', 'car_make_id'];


    /**
     * @return BelongsTo
     */
    public function make()
    {
        return $this->belongsTo(CarMake::class);
    }
}
