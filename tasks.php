<?php
require_once 'vendor/autoload.php';

session_start();

if (!isset($_SESSION['access_token'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

$config = include('oauth-config.php');
$client = new Google\Client();
$client->setClientId($config['client_id']);
$client->setClientSecret($config['client_secret']);
$client->setAccessToken($_SESSION['access_token']);

// Verifica se o token expirou e tenta renová-lo
if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $newToken = $client->fetchAccessTokenWithRefreshToken();
        $_SESSION['access_token'] = $newToken;
    } else {
        echo json_encode(['error' => 'Token expirado e sem refresh token']);
        exit();
    }
}

$service = new Google\Service\Tasks($client);

try {
    $tasksList = $service->tasks->listTasks('@default');
    echo json_encode($tasksList->getItems());
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
