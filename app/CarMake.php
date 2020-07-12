<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;



class CarMake extends Model
{
    protected $fillable = ['name'];


    /**
     * @return HasMany
     */
    public function models()
    {
        return $this->hasMany(CarModel::class);
    }
}
