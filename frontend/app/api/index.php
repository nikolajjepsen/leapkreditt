<?php

require_once __DIR__ . '/../../vendor/autoload.php';
$dbh = \App\Database\Instance::get();
$config = new \App\Application\Config;

$headers = apache_request_headers();
if (
    !isset($headers['pm_user']) || 
    !isset($headers['pm_pass']) ||
    $headers['pm_user'] != 'nj@codefighter.dk' || 
    $headers['pm_pass'] != 'b_jF5TPgQgUZ7Ff2BJS98&SLscHWEEg'
) {
    http_response_code(401);
    echo json_encode(['http_message' => 'Unauthorized', 'http_errorcode' => '401']);
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // List config
    if (isset($_GET['id'])) {
        http_response_code(200);
        if ($setting = $config->getRowById($_GET['id'])) {
            echo json_encode($setting);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'failed', 'reason' => 'Model not found']);
        }
    } else {
        http_response_code(200);
        echo json_encode($config->list());
    }
} else {
    echo $_SERVER['REQUEST_METHOD'];
}
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    parse_str(file_get_contents('php://input'), $_PUT);
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['status' => 'failed', 'reason' => 'missing inputs']);
        die;
    }
    if (!isset($_PUT['name']) || !isset($_PUT['value'])) {
        http_response_code(400);
        echo json_encode(['status' => 'failed', 'reason' => 'missing inputs']);
        die;
    }
    if ($config->update($_GET['id'], $_PUT['name'], $_PUT['value'])) {
        http_response_code(204);
        echo json_encode(['status' => 'success']);
        die;
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'failed', 'reason' => 'Model not found']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_PUT['name']) || !isset($_PUT['value'])) {
        http_response_code(400);
        echo json_encode(['status' => 'failed', 'reason' => 'missing inputs']);
    }

    if ($config->create($_POST['name'], $_POST['value'])) {
        http_response_code(201);
    } else {
        http_response_code(404);
    }
}

?>