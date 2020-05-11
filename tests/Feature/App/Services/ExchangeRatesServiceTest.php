<?php

namespace Tests\Feature\App\Services;

use App\ExchangeRates;
use App\Services\CurrencyConversionService;
use App\Services\ExchangeRatesServiceContract;
use Cache;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use RuntimeException;
use Tests\TestCase;

class ExchangeRatesServiceTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function test_should_be_get_last_exchange_rates_data_from_cache(): void
    {
        $this->app
            ->get('cache')
            ->set('exchange_rates', [
                'id' => $id = $this->faker->numberBetween(),
                'value' => $value = $this->faker->numberBetween(),
            ]);

        $exchangeRates = Cache::exchangeRate();

        self::assertEquals($id, $exchangeRates['id']);
        self::assertEquals($value, $exchangeRates['value']);
    }

    public function test_should_be_convert_usd_to_ruble_and_return_value_in_cents(): void
    {
        self::assertEquals(60, CurrencyConversionService::convertUsd(20, 3));
    }

    public function test_should_be_passed_exception_exchange_rate_not_found(): void
    {
        Cache::clear();
        $this->mockExchangeRatesService();
        ExchangeRates::truncate();

        $this->expectException(RuntimeException::class);
        Cache::exchangeRate();
    }

    public function test_should_be_exchange_rate_update_success(): void
    {
        Cache::clear();
        $dataFromService = Cache::exchangeRate();
        $dataFromDB = ExchangeRates::query()
            ->orderByDesc('id')
            ->first(['id', 'value'])
            ->toArray();

        self::assertEquals($dataFromDB['id'], $dataFromService['id']);
        self::assertEquals($dataFromDB['value'], $dataFromService['value']);
    }

    private function mockExchangeRatesService(): void
    {
        $this->app->singleton(ExchangeRatesServiceContract::class, static function () {
            return new class() implements ExchangeRatesServiceContract {
                public function update(): bool
                {
                    return true;
                }
            };
        });
    }
}
