<?php
require_once 'vendor/autoload.php';

session_start();

// Verifica autenticação
if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado. Faça login novamente.']);
    exit();
}

// Configuração do cliente Google API
$client = new Google\Client();
$client->setAccessToken($_SESSION['access_token']);
$service = new Google\Service\Tasks($client);

// Apenas aceita requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
    exit();
}

// Recebendo dados do frontend
$taskTitle = $_POST['title'] ?? null;
$taskNotes = $_POST['notes'] ?? null;
$taskDueDate = $_POST['due_date'] ?? null;
$taskDueTime = $_POST['due_time'] ?? null;

// Validação de dados
if (empty($taskTitle)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'O título é obrigatório.']);
    exit();
}

try {
    $task = new Google\Service\Tasks\Task();
    $task->setTitle($taskTitle);

    if (!empty($taskNotes)) {
        $task->setNotes($taskNotes);
    }

    // Combinação de data e hora
    if (!empty($taskDueDate) && !empty($taskDueTime)) {
        $dueDateTime = $taskDueDate . 'T' . $taskDueTime . ':00.000Z';
        $task->setDue($dueDateTime);
    } elseif (!empty($taskDueDate)) {
        $task->setDue($taskDueDate . 'T00:00:00.000Z');
    }

    // Inserção da tarefa
    // $createdTask = $service->tasks->insert('primary', $task);
    $createdTask = $service->tasks->insert('@default', $task);

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Tarefa criada com sucesso!',
        'task' => $createdTask
    ]);

    // echo json_encode(['task' => $createdTask]);
} catch (Exception $e) {
    error_log($e->getMessage()); // Log para depuração
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Erro ao criar a tarefa: ' . $e->getMessage()]);
}
?>