<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\Resource;

class QuoteResource extends Resource {

    public function toArray($request) {
        return [
            'id'        => $this->id,
            'site_id'   => $this->site_id,
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'email'     => $this->email,
            'mobile'    => $this->mobile,
            'amount'    => $this->loan_amount,
            'tenure'    => $this->tenure,
            'age'       => $this->age,
            'created'   => $this->created_at,
            'updated'   => $this->updated_at,
        ];
    }

    public function toCampaignMonitor($request) {
        return [
            'EmailAddress' => $this->email,
            'Name' => $this->firstname . ' ' . $this->lastname,
            'RestartSubscriptionBasedAutoresponders' => true,
            'ConsentToTrack' => 'Yes',
            'CustomFields' => [
                [
                    'Key' => 'tenure',
                    'Value' => $this->tenure
                ],
                [
                    'Key' => 'age',
                    'Value' => $this->age
                ],
                [
                    'Key' => 'loan_amount',
                    'Value' => $this->loanAmount
                ]
            ]
        ];
    }

}