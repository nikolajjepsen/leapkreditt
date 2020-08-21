<?php
namespace App\Application;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../vendor/autoload.php';
use Tracy\Debugger;

/**
 * This class contains all methods regarding quotes on site.
 * 
 * @category Quotes
 * @package  Application
 * @author   Nikolaj Jepsen <nj@codefighter.dk>
 * @license  No License
 * @link     http://progressmedia.dk
 */
class Quote
{
    /**
     * Object api
     * The Backend class
     */
    private $api;

    /**
     * Object request
     * New Guzzle Client with base headers and URI.
     */
    private $request;

    /**
     * Object config
     * The Config class
     */
    private $config;

    public function __construct()
    {
        $this->config = new \App\Application\Config;
        $this->api = new \App\Api\leapBaseApi;
        $this->request = $this->api->request();
    }

    /**
     * Find the current quote if any based on cookies and sessions.
     * Sessions has priority; if none is set but a cookie found
     * a session will be set.
     * 
     * getCurrent
     *
     * @return integer The current quote
     */
    public function getCurrent()
    {
        if (isset($_SESSION['quoteId']) && is_numeric($_SESSION['quoteId'])) {
            $quoteId = $_SESSION['quoteId'];
        } elseif (!isset($_SESSION['quoteId']) && isset($_COOKIE['l_qId']) && is_numeric($_COOKIE['l_qId'])) {
            $_SESSION['quoteId'] = $_COOKIE['l_qId'];
            $quoteId = $_COOKIE['l_qId'];
        } else {
            $quoteId = 0;
        }

        return $quoteId;
    }

    /**
     * Request a $quoteId higher than 0 and checks if it's a valid quote id.
     * validateAndGetQuote
     *
     * @param  integer $quoteId
     *
     * @return mixed JSON representation of quote or false.
     */
    public function validateAndGetQuoteById($quoteId)
    {
        if (!is_numeric($quoteId) || $quoteId <= 0) {
            return false;
        }
            
        try {
            $response = $this->request->get('/api/v1/quotes/' . $quoteId);
            $content = $response->getBody()->getContents();
            $this->api->handleError($content, $response->getStatusCode());
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Debugger::log($e->getMessage());
            return false;
        } catch (\Exception $e) {
            Debugger::log($e->getMessage());
            return false;
        }

        if (isset($response) && $response->getStatusCode() == 200) {
            return $content;
        }

        return false;
    }

    /*
    // Sort by EPC - further slice.
    usort($loans, function ($a, $b) {
        return $b['epc'] <=> $a['epc'];
    });
    */

    public function getSuggestedLoansByQuoteId($quoteId, $sortKey = 'epc', $sortDirection = 'DESC', $limit = 1)
    {
        try {
            $response = $this->request->get('/api/v1/quotes/' . $quoteId . '/loans');
            $contents = $response->getBody()->getContents();
            $this->api->handleError($contents, $response->getStatusCode());
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Debugger::log($e->getMessage());
            return false;
        } catch (\Exception $e) {
            Debugger::log($e->getMessage());
            return false;
        }

        if (!$contents = json_decode($contents, true)) {
            Debugger::log('Unable to decode JSON @ getSuggestedLoansByQuoteId');
            return [];
        }
        $type = $contents['content'];
        $loans = $contents['loans'];

        if ($sortDirection == 'DESC') {
            usort($loans, function ($a, $b) use ($sortKey) {
                return $b[$sortKey] <=> $a[$sortKey];
            });
        } else {
            usort($loans, function ($a, $b) use ($sortKey) {
                return $a[$sortKey] <=> $b[$sortKey];
            });
        }


        $sliceArray = array_slice($loans, 0, $limit);

        $_SESSION['loans'] = json_encode(['content' => $type, 'loans' => $sliceArray]);
        return ['content' => $type, 'loans' => $sliceArray];
        
    }
}
