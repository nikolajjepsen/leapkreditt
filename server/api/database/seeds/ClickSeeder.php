<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ClickSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Click::create([
            'quote_id' => 1,
            'loan_id' => 1,
        ]);
        App\Click::create([
            'quote_id' => 1,
            'loan_id' => 2,
        ]);
        App\Click::create([
            'quote_id' => 3,
            'loan_id' => 4,
        ]);
        App\Click::create([
            'quote_id' => 3,
            'loan_id' => 3,
        ]);
    }
}