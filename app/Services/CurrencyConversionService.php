<?php

namespace App\Services;

class CurrencyConversionService
{
    public static function convertUsd($value, float $rate): int
    {
        return round($value) * round($rate);
    }
}
