<?php
require_once 'vendor/autoload.php';
session_start();

if (!isset($_SESSION['access_token'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

$config = include('oauth-config.php');
$client = new Google\Client();
$client->setAccessToken($_SESSION['access_token']);
$service = new Google\Service\Tasks($client);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        $service->tasks->delete('@default', $data['id']);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
