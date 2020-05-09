<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(CBRExchangeRatesSeeder::class);
        $this->call(AccountHistorySeeder::class);
        $this->call(AccountBalanceUpdateSeeder::class);
    }
}
