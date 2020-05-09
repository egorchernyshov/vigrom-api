<?php

use App\Services\CurrencyConversionService;
use Illuminate\Database\Seeder;

class AccountBalanceUpdateSeeder extends Seeder
{
    private const ACCOUNT_NUMBER = 241;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accounts')
            ->where('number', self::ACCOUNT_NUMBER)
            ->update([
                'balance' => $this->getBalance(),
            ]);
    }

    public function getBalance(): int
    {
        Cache::clear();
        $service = app(CurrencyConversionService::class);

        $valueInRubles = DB::table('account_histories')
            ->where('account_number', self::ACCOUNT_NUMBER)
            ->where('currency', 'RUB')
            ->sum('value');

        $valueInUsd = DB::table('account_histories')
            ->where('account_number', self::ACCOUNT_NUMBER)
            ->where('currency', 'USD')
            ->sum('value');

        return $valueInRubles + $service->convertUsdToRub($valueInUsd);
    }
}
