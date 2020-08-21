<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Loan::create([
            'name' => 'Affiliate 1 Loan',
            'url' => 'https://somelong.url/?poaram=9193njodhsfioqwef',
            'active' => 1,
            'min_amount' => 500,
            'max_amount' => 500000,
            'yearly_cost_percent' => 15.5,
            'min_tenure' => 1,
            'max_tenure' => 150,
            'min_age' => 21,
            'country_id' => 3
        ]);
        App\Loan::create([
            'name' => 'Affiliate 2 Loan',
            'url' => 'https://somelong2.url/?poaram=9193njodhsfioqwef',
            'active' => 1,
            'min_amount' => 500,
            'max_amount' => 500000,
            'yearly_cost_percent' => 15.5,
            'min_tenure' => 1,
            'max_tenure' => 150,
            'min_age' => 21,
            'country_id' => 3
        ]);
        App\Loan::create([
            'name' => 'Affiliate 3 Loan',
            'url' => 'https://somelong3.url/?poaram=9193njodhsfioqwef',
            'active' => 1,
            'min_amount' => 500,
            'max_amount' => 500000,
            'yearly_cost_percent' => 15.5,
            'min_tenure' => 1,
            'max_tenure' => 150,
            'min_age' => 21,
            'country_id' => 2
        ]);
        App\Loan::create([
            'name' => 'Affiliate 4 Loan',
            'url' => 'https://somelong4.url/?poaram=9193njodhsfioqwef',
            'active' => 1,
            'min_amount' => 500,
            'max_amount' => 500000,
            'yearly_cost_percent' => 15.5,
            'min_tenure' => 1,
            'max_tenure' => 150,
            'min_age' => 21,
            'country_id' => 1
        ]);
    }
}