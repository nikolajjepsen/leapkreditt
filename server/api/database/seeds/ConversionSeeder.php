<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ConversionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Conversion::create([
            'click_id' => 2,
            'payout' => 30,
            'currency' => 'EUR'
        ]);
        App\Conversion::create([
            'click_id' => 3,
            'payout' => 30,
            'currency' => 'EUR'
        ]);
    }
}