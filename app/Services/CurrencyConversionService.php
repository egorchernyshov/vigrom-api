<?php

namespace App\Services;

class CurrencyConversionService
{
    private $usdToRubRate;

    public function __construct($ratios)
    {
        $this->usdToRubRate = (float) $ratios['usd_to_rub'];
    }

    public function convertUsdToRub($value): int
    {
        return round($value) * round($this->usdToRubRate);
    }
}
