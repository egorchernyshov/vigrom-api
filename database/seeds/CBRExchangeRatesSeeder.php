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

        DB::table('exchange_rates')->insert([
            'name' => $faker->sentence,
            'num_code' => $faker->numberBetween(),
            'char_code' => $faker->word,
            'nominal' => 1,
            'value' => $faker->randomFloat(null, 1, 100),
        ]);
    }
}
