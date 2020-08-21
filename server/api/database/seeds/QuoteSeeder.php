<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Quote::create([
            'site_id' => 1,
            'firstname' => 'Nikolaj',
            'lastname' => 'Jepsen',
            'email' => 'nj@codefighter.dk',
            'mobile' => '27299072',
            'loan_amount' => 15000
        ]);
        App\Quote::create([
            'site_id' => 1,
            'firstname' => 'Andreas',
            'lastname' => 'Jepsen',
            'email' => 'aj@codefighter.dk',
            'mobile' => '25242624',
            'loan_amount' => 200000
        ]);
        App\Quote::create([
            'site_id' => 2,
            'firstname' => 'Nikolaj',
            'lastname' => 'Finmand',
            'email' => 'nj@codefaina.fi',
            'mobile' => '00000000',
            'loan_amount' => 5000
        ]);
        App\Quote::create([
            'site_id' => 2,
            'firstname' => 'Andreas',
            'lastname' => 'Finmand',
            'email' => 'aj@codefaina.fi',
            'mobile' => '11111111',
            'loan_amount' => 7500
        ]);
    }
}