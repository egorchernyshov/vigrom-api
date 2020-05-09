<?php

namespace App\Providers;

use App\ExchangeRates;
use App\Services\CBRExchangeRatesFetchingService;
use App\Services\CurrencyConversionService;
use App\Services\ExchangeRatesServiceContract;
use Cache;
use Config;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class ExchangeRatesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ExchangeRatesServiceContract::class, static function () {
            return new CBRExchangeRatesFetchingService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(CurrencyConversionService::class, static function (Application $application) {
            if (null !== $data = Cache::get(CurrencyConversionService::class)) {
                return new CurrencyConversionService($data);
            }

            $application->get(ExchangeRatesServiceContract::class)->update();

            $volute = ExchangeRates::query()->orderByDesc('id')->first();
            if (null === $volute) {
                throw new RuntimeException('Exchange rate not found. Operation impossible.');
            }

            $data = [
                'usd_to_rub' => $volute->toArray()['value'],
            ];

            Cache::set(
                CurrencyConversionService::class,
                $data,
                Config::get('exchange_rates.ttl')
            );

            return new CurrencyConversionService($data);
        });
    }
}
