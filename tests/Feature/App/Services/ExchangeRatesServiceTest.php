<?php

namespace Tests\Feature\App\Services;

use App\ExchangeRates;
use App\Services\CurrencyConversionService;
use App\Services\ExchangeRatesServiceContract;
use Cache;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use RuntimeException;
use Tests\TestCase;

class ExchangeRatesServiceTest extends TestCase
{
    use DatabaseMigrations;

    public function test_should_be_convert_usd_to_ruble_and_return_value_in_cents(): void
    {
        $this->app
            ->get('cache')
            ->set(CurrencyConversionService::class, ['usd_to_rub' => 3]);

        $service = $this->app->get(CurrencyConversionService::class);

        self::assertEquals(60, $service->convertUsdToRub(20));
    }

    public function test_should_be_passed_exception_exchange_rate_not_found(): void
    {
        Cache::clear();
        $this->mockExchangeRatesService();
        ExchangeRates::truncate();

        $this->expectException(RuntimeException::class);
        $this->app->get(CurrencyConversionService::class);
    }

    public function test_should_be_exchange_rate_update_success(): void
    {
        Cache::clear();
        $service = $this->app->get(CurrencyConversionService::class);

        self::assertIsInt($service->convertUsdToRub(20));
    }

    private function mockExchangeRatesService(): void
    {
        $this->app->singleton(ExchangeRatesServiceContract::class, static function () {
            return new class implements ExchangeRatesServiceContract {
                public function update(): bool
                {
                    return true;
                }
            };
        });
    }
}
