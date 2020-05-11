<?php

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
        $debit = DB::table('account_histories')
            ->where('account_number', self::ACCOUNT_NUMBER)
            ->where('transaction_type', 'debit')
            ->sum('value');

        $credit = DB::table('account_histories')
            ->where('account_number', self::ACCOUNT_NUMBER)
            ->where('transaction_type', 'credit')
            ->sum('value');

        return $credit - $debit;
    }
}
