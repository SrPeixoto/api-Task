<?php
session_start();
require_once 'vendor/autoload.php';

if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] == '') {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

$client = new Google\Client();
$client->setAccessToken($_SESSION['access_token']);
$service = new Google\Service\Tasks($client);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $taskId = $_GET['id'];
    try {
        // Verificar se o ID é válido antes de tentar acessar a tarefa
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $taskId)) {
            throw new Exception('ID da tarefa inválido');
        }
        
        $task = $service->tasks->get('@default', $taskId);
        echo json_encode(['success' => true, 'task' => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'notes' => $task->getNotes() ?? '', 
        ]]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao recuperar tarefa: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID da tarefa não informado']);
}

?>
