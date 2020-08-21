<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Psr\Http\Message\ServerRequestInterface;

$router->get('/', function () use ($router) {
    return phpinfo();
});

// Registration of clicks and conversions
$router->get('/register/click/{quoteId}/{loanId}', 'Tracking\ClickController@registerClick');
$router->get('/register/conversion/{clickId}', 'Tracking\ConversionController@registerConversion');


$router->group(['prefix' => '/api/v1', 'middleware' => 'auth:api'], function () use ($router) {
    $router->get('/startup', function() {
        return 'valid';
    });
    $router->group(['prefix' => '/quotes'], function () use ($router) {
        $router->post('/', 'Quote\QuoteController@createQuote');
        $router->get('/{quoteId}', 'Quote\QuoteController@getQuoteById');
        $router->patch('/{quoteId}', 'Quote\QuoteController@updateQuoteById');
        $router->get('/{quoteId}/loans', 'Quote\QuoteController@getQuoteLoanSuggestionsById');
        $router->get('/{quoteId}/confirm', 'Quote\ConfirmController@sendConfirmMail');
        $router->post('/{quoteId}/confirm', 'Quote\ConfirmController@validateConfirmCode');
    });
});

$router->group(['prefix' => '/backend/api/v1', 'middleware' => 'auth:backend'], function () use ($router) {
    $router->get('/startup', function() {
        return 'valid';
    });
    $router->get('/test', 'Quote\QuoteController@test');
    
    $router->group(['prefix' => '/quotes'], function () use ($router) {
        $router->get('/', 'Quote\QuoteController@getQuotes');
        $router->get('/{quoteId}', 'Quote\QuoteController@getQuoteById');
        $router->patch('/{quoteId}', 'Quote\QuoteController@updateQuoteById');
        $router->delete('/{quoteId}', 'Quote\QuoteController@deleteQuoteById');
        $router->get('/{quoteId}/loans', 'Quote\QuoteController@getQuoteLoanSuggestionsById');
        $router->get('/{quoteId}/clicks', 'Quote\QuoteController@getQuoteClicksById');
        $router->get('/{quoteId}/conversions', 'Quote\QuoteController@getQuoteConversionsById');
    });
    $router->group(['prefix' => '/sites'], function () use ($router) {
        $router->post('/', 'Site\SiteController@createSite');
        $router->get('/', 'Site\SiteController@getSites');
        $router->get('/{siteId}', 'Site\SiteController@getSiteById');
        $router->patch('/{siteId}', 'Site\SiteController@updateSiteById');
        $router->delete('/{siteId}', 'Site\SiteController@deleteSiteById');
        $router->get('/{siteId}/summary', 'Site\SiteController@displaySummaryById');
        $router->get('/{siteId}/quotes', 'Site\SiteController@displayQuotesById');
        $router->get('/{siteId}/clicks', 'Site\SiteController@displayClicksById');
        $router->get('/{siteId}/conversions', 'Site\SiteController@displayConversionsById');
    });
});