<?php

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

        for ($i = 0; $i <= 10; $i++) {
            DB::table('account_histories')
                ->insert([
                    'account_number' => 241,
                    'currency' => $faker->randomElement(['USD', 'RUB']),
                    'transaction_type' => $transaction = $faker->randomElement(['debit', 'credit']),
                    'change_reason' => $faker->randomElement(['stock', 'refund']),
                    'value' => ('debit' === $transaction)
                        ? -$faker->numberBetween(1, 100)
                        : $faker->numberBetween(1, 100),
                ]);
        }
    }
}
