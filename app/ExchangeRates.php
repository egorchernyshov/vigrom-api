<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeRates extends Model
{
    protected $fillable = [
        'name',
        'num_code',
        'char_code',
        'value',
        'nominal',
    ];
}
