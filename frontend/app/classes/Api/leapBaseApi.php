<?php
namespace App\Api;

require_once __DIR__ . '/../../../vendor/autoload.php';

use GuzzleHttp\Client;

/**
 * This class maintains communication with the remote API.
 * It sets the default headers for auth and identification.
 * Further it sets a base URI for all requests through the class.
 *
 * @category API
 * @package  Application
 * @author   Nikolaj Jepsen <nj@codefighter.dk>
 * @license  No License
 * @link     http://progressmedia.dk
 */
class leapBaseApi
{
    protected $api;
    protected $config;

    public function __construct()
    {
        $this->config = new \App\Application\Config;
        $this->api = new Client(
            [
                'base_uri' => 'https://loan.progressmedia.dev/',
                'headers' => [
                    'Authorization' => '%r_4JEW3ouCkvn_UiKFI2wrstrhQ48"LGG51RaD-dL_bj-Ohm6O9*FL2GkpjuEq=XX%_N1WfVC6cVOfl',
                    'Site-Id' => 1,
                ],
                'http_errors' => false
            ]
        );
    }
    
    public function request()
    {
        return $this->api;
    }

    public function handleError($response, $statusCode)
    {
        if (!$response = json_decode($response, true)) {
            $response = ['error' => ''];
        }

        switch ($statusCode) {
            case 404:
                throw new \Exception('404: Ressource not found ' . $response['error']);
                break;
            case 401:
                throw new \Exception('401: Site Unauthorized with Token: ' . $this->config->getenv('API_TOKEN') . ' for Site: ' . $this->config->getenv('APP_ID'));
                break;
            case 400:
                throw new \Exception('400: Invalid arguments ' . $response['error']);
                break;
        }
    }
}
