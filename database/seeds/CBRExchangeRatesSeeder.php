<?php

use Faker\Factory;
use Illuminate\Database\Seeder;

class CBRExchangeRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $count = 10;
        while ($count--) {
            DB::table('exchange_rates')->insert([
                'value' => $faker->randomFloat(null, 1, 100),
            ]);
        }
    }
}
