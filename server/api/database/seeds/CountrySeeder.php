<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Country::create([
            'country_code' => '47',
            'pretty_name' => 'Norway',
            'short' => '+47',
        ]);
        App\Country::create([
            'country_code' => '358',
            'pretty_name' => 'Finland',
            'short' => 'FI',
        ]);
        App\Country::create([
            'country_code' => '45',
            'pretty_name' => 'Denmark',
            'short' => 'DK',
        ]);
        App\Country::create([
            'country_code' => '46',
            'pretty_name' => 'Sweden',
            'short' => 'SE',
        ]);
    }
}