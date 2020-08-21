<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Twilio\Rest\Client;


class ValidatePhone implements Rule
{
    protected $client;

    public function __construct() {
        $this->client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
    }
    public function passes($attribute, $value) {
        try {
            $number = $this->client->lookups->v1->phoneNumbers($value)->fetch();
        } catch (\Exception $e) {
            //var_dump($e->getMessage());
            return false;
        }
        return true;
    }

    public function message() {
        return __('validation.quote.mobile.invalid');
    }
}