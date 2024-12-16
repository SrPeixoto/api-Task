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

    if (empty($data['id']) || empty($data['title'])) {
        echo json_encode(['error' => 'ID da tarefa e título são obrigatórios']);
        exit();
    }
    
    try {
        $task = $service->tasks->get('@default', $data['id']);
        $task->setTitle($data['title']);
        $task->setNotes($data['notes']);

        $updatedTask = $service->tasks->update('@default', $task->getId(), $task);
        echo json_encode(['success' => true, 'task' => $updatedTask]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Erro ao atualizar a tarefa: ' . $e->getMessage()]);
    }
}

?>
