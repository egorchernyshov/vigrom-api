<?php

namespace App\Providers;

use App\Services\CBRExchangeRatesFetchingService;
use App\Services\ExchangeRatesServiceContract;
use Illuminate\Support\ServiceProvider;

class ExchangeRatesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ExchangeRatesServiceContract::class, static function () {
            return new CBRExchangeRatesFetchingService();
        });
    }
}
