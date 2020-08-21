<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignMonitorQuoteResource extends JsonResource {

    public function toArray($request) {
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