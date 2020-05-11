<?php

namespace App\Providers;

use App\ExchangeRates;
use App\Services\ExchangeRatesServiceContract;
use Cache;
use Config;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cache::macro('exchangeRate', function () {
            if (null !== $data = Cache::get('exchange_rates')) {
                return $data;
            }

            // Exchange rates update from remote service
            app(ExchangeRatesServiceContract::class)->update();

            $exchangeRates = ExchangeRates::query()->orderByDesc('id')->first(['id', 'value']);
            if (null === $exchangeRates) {
                throw new RuntimeException('Exchange rate not found. Operation impossible.');
            }

            Cache::set('exchange_rates', $exchangeRates->toArray(), Config::get('exchange_rates.ttl'));

            return $exchangeRates->toArray();
        });
    }
}
