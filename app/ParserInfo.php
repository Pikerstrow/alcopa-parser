<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParserInfo extends Model
{
    public const SUCCESS = 1;
    public const FAILED = 0;

    protected $table = 'parser_info';

    protected $fillable = [
        'last_parsing_date',
        'last_parsing_result',
        'parsed_auctions',
        'parsed_cars'
    ];

    protected $casts = [
        'last_parsing_result' => 'boolean'
    ];
}
