<?php

use App\ExchangeRates;
use App\Services\CurrencyConversionService;
use Illuminate\Database\Seeder;

class AccountHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $exchangeRate = ExchangeRates::query()
            ->orderByDesc('id')
            ->first(['id', 'value'])
            ->toArray();

        $datetime = new DateTimeImmutable();

        $count = 50;
        while ($count--) {
            $value = $faker->numberBetween(1, 100);
            $currency = $faker->randomElement(['USD', 'RUB']);
            $transaction = $faker->randomElement(['debit', 'credit']);
            $valueUsdToRub = null;

            if ('USD' === $currency) {
                $valueUsdToRub = CurrencyConversionService::convertUsd($value, $exchangeRate['value']);
            }

            DB::table('account_histories')
                ->insert([
                    'account_number' => 241,
                    'created_at' => $datetime->modify("-{$count} day")->format('Y-m-d H:i:s'),
                    'currency' => $currency,
                    'transaction_type' => $transaction,
                    'change_reason' => $faker->randomElement(['stock', 'refund']),
                    'original_value' => $value,
                    'exchange_rate_id' => ('USD' === $currency) ? $exchangeRate['id'] : null,
                    'value' => $valueUsdToRub ?? $value,
                ]);
        }
    }
}
