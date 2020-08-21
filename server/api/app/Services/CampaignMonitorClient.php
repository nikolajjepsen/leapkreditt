<?php
namespace App\Services;
use GuzzleHttp\Client;

class CampaignMonitorClient {
    protected $client;
    protected $resource;

    public function __construct() {
        $this->client = new Client([
            'base_uri' => 'https://api.createsend.com/api/v3.2/',
            'auth' => [config('services.campaignmonitor.key'), '']
        ]);

    }

    public function getClient() {
        return $this->client;
    }

    public function subscriber($listId, $action, $details, $query = '') {
        $response = $this->client->request('POST', 'subscribers/' . $listId . '.json' . $query, [
            'json' => $details
        ]);
        //var_dump($response->getBody()->getContents());
    }
}