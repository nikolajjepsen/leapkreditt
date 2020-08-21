<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Site::create([
            'name' => 'Leapkreditt',
            'email' => 'info@leapkreditt.com',
            'url' => 'https://leapkreditt.com',
            'country_id' => 3,
            'api_token' => '%r_4JEW3ouCkvn_UiKFI2wrstrhQ48"LGG51RaD-dL_bj-Ohm6O9*FL2GkpjuEq=XX%_N1WfVC6cVOfl',
        ]);
        App\Site::create([
            'name' => 'SomeLaina',
            'email' => 'https://somelaina.com',
            'url' => 'https://somelaina.com',
            'country_id' => 2,
            'api_token' => 'U2C_3UtJSi=y5f9EOmG6almVm_t_ff19A_picAsHSho%oXg-OCptEOSt6YSCBsqS%RyBbquiEJu5i8Vz',
        ]);
    }
}