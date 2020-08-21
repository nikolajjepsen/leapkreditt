<?php
session_start();

require 'vendor/autoload.php';

$config  = new \App\Application\Config;
$db      = \App\Database\Instance::get();
$api     = new \App\Api\Api;
$quote   = new \App\Application\Quote;
$request = $api->request();

$currentQuoteId = (int)$quote->getCurrent();
$currentLoanId  = isset($_GET['loanId']) && is_numeric($_GET['loanId']) ? (int) $_GET['loanId'] : 0;

if ($currentQuoteId != 0 || $currentLoanId != 0) {
    // Missing params, maybe a static by-country loan fallback??
    $response = $request->get('/register/click/' . $currentQuoteId . '/' . $currentLoanId);
    //echo $response->getBody()->getContents();
    if ($response->getStatusCode() == 200) {
        $response = json_decode($response->getBody()->getContents());
        header("Location: " . $response->redirectLink);
        exit();
    }
}
/*
var_dump($_SESSION);
var_dump($_GET);

var_dump($currentQuoteId);
var_dump($currentLoanId);
*/

// TODO: Get basic redirect in case above failed one or more logical checks.