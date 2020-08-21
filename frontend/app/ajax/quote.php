<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';

$config = new \App\Application\Config;
$api = new \App\Api\leapBaseApi;
$quote = new \App\Application\Quote;
$db = \App\Database\Instance::get();
use Tracy\Debugger;

if (isset($_REQUEST['task']) && !empty($_REQUEST['task'])) {
    $task = $_REQUEST['task'];
    
    if ($task == 'submitApplication') {
        $name =       (!isset($_POST['name'])               || empty($_POST['name']))               ? '' : $_POST['name'];
        $email =      (!isset($_POST['email'])              || empty($_POST['email']))              ? '' : $_POST['email'];
        $mobile =     (!isset($_POST['mobile'])             || empty($_POST['mobile']))             ? '' : $_POST['mobile'];
        $loanAmount = (!isset($_POST['slider-amount-val'])  || empty($_POST['slider-amount-val']))  ? '' : $_POST['slider-amount-val'];

        try {
            $request = $api->request();
            $response = $request->post('/api/v1/quotes', [
                'form_params' => [
                    'fullName' => $name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'loanAmount' => $loanAmount
                ]
            ]);
            $responseContent = $response->getBody()->getContents();
            if ($response->getStatusCode() == 200) {
                // Valid request.
                $json = json_decode($responseContent);
                $_SESSION['quoteId'] = $json->quoteId;
    
                echo $responseContent;
            } elseif ($response->getStatusCode() == 400) {
                // Validation error, missing fields, headers wrong
                echo $responseContent;
            } else {
                echo $responseContent;
                echo json_encode(['status' => 'failed', 'reason' => 'general error']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'failed', 'reason' => 'connection unavailable']);
            Debugger::log('500: Connection refused Remote host unavailable. Unable to create quote.');
        }
        $response = null;
    } elseif ($task == 'sendConfirmCode' && isset($_GET['quoteId'])) {
        $quoteId = $_GET['quoteId'] ?? null;
        try {
            $request = $api->request();
            $response = $request->get('/api/v1/quotes/' . $quoteId . '/confirm');

            // Returns 404 with default json encoded error or 500 if code generation failed with json encoded content.
            if ($response->getStatusCode() == 200) {
                echo $response->getBody()->getContents();
            } else {
                echo $response->getBody()->getContents();
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'failed', 'reason' => 'connection unavailable']);
            Debugger::log('500: Connection refused Remote host unavailable. Unable to create quote.');
        }
        $request = null;
    } elseif ($task == 'validateConfirmCode' && isset($_POST['confirmCode']) && isset($_POST['quoteId'])) {
        $quoteId = $_POST['quoteId'] ?? 0; 
        $code = $_POST['confirmCode'];

        try {
            $request = $api->request();
            $response = $request->post('/api/v1/quotes/' . $quoteId . '/confirm', [
                'form_params' => [
                    'code' => $code
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                echo json_encode(['status' => 'success']);
                $_SESSION['quoteId'] = $quoteId;
            } elseif ($response->getStatusCode() == 404) {
                // Code not found
                echo json_encode(['status' => 'failed', 'reason' => 'invalid code']);
            } else {
                // Most likely expired, or 500 Internal.
                echo $response->getBody()->getContents();
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'failed', 'reason' => 'connection unavailable']);
            Debugger::log('500: Connection refused Remote host unavailable. Unable to create quote.');
        }
    } elseif ($task == 'updateQuoteInformation' && isset($_POST['quoteId'])) {
        $amount     = $_POST['slider-amount-val'] ?? '';
        $tenure     = $_POST['tenure']      ?? '';
        $mobile     = $_POST['mobile']      ?? '';
        $email      = $_POST['email']       ?? '';
        $firstname  = $_POST['firstname']   ?? '';
        $lastname   = $_POST['lastname']    ?? '';
        $age        = $_POST['age']         ?? '';
        $quoteId    = $_POST['quoteId'];

        if (!$quoteId == $_SESSION['quoteId']) {
            echo 'quoteIdInvalid';
        }
        
        try {
            $request = $api->request();
            $response = $request->patch('/api/v1/quotes/' . $quoteId, [
                'form_params' => [
                    'fullName' => $firstname . ' ' . $lastname,
                    'email' => $email,
                    'mobile' => $mobile,
                    'loanAmount' => $amount,
                    'tenure' => $tenure,
                    'age' => $age
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                // Valid request.
                unset($_SESSION['loans']);
    
                echo $response->getBody()->getContents();
            } elseif ($response->getStatusCode() == 400) {
                // Validation error, missing fields, headers wrong
                echo $response->getBody()->getContents();
            } else {
                echo ($response->getBody()->getContents());
                echo json_encode(['status' => 'failed', 'reason' => 'general error']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'failed', 'reason' => 'connection unavailable']);
            Debugger::log('500: Connection refused Remote host unavailable. Unable to create quote.');
        }
    }
}
