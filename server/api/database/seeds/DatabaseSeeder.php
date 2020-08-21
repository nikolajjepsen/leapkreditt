<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CountrySeeder::class,
            SiteSeeder::class,
            QuoteSeeder::class,
            LoanSeeder::class,
            ClickSeeder::class,
            ConversionSeeder::class,
        ]);
    }
}
