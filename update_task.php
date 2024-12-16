<?php
require_once 'vendor/autoload.php';

session_start();

// Verifica autenticação
if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
    exit();
}

// Configuração do cliente Google API
$client = new Google\Client();
$client->setAccessToken($_SESSION['access_token']);
$service = new Google\Service\Tasks($client);

// Recebendo dados do frontend
$Edit_taskTitle = $_POST['Edit_title'] ?? null;
$Edit_taskNotes = $_POST['Edit_notes'] ?? null;
$Edit_taskDueDate = $_POST['Edit_due_date'] ?? null;
$Edit_taskDueTime = $_POST['Edit_due_time'] ?? null;

// Recebe e decodifica os dados JSON enviados
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['taskId'], $data['completed']) && is_bool($data['completed'])) {
    $taskId = $data['taskId'];
    $completed = $data['completed'];

    // Validação do ID da tarefa
    if (empty($taskId)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'O ID da tarefa é obrigatório.']);
        exit();
    }

    try {
        // Busca a tarefa existente
        $task = $service->tasks->get('@default', $taskId);

        $task->setTitle($Edit_taskTitle);

        if (!empty($Edit_taskNotes)) {
            $task->setNotes($Edit_taskNotes);
        }

        // Combinação de data e hora
        if (!empty($Edit_taskDueDate) && !empty($Edit_taskDueTime)) {
            $EditdueDateTime = $Edit_taskDueDate . 'T' . $Edit_taskDueTime . ':00.000Z';
            $task->setDue($EditdueDateTime);
        } elseif (!empty($Edit_taskDueDate)) {
            $task->setDue($Edit_taskDueDate . 'T00:00:00.000Z');
        }

        // Atualiza o status da tarefa
        $task->setStatus($completed ? 'completed' : 'needsAction');
        $updatedTask = $service->tasks->update('@default', $taskId, $task);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Tarefa atualizada com sucesso!',
            'task' => $updatedTask
        ]);
    } catch (Exception $e) {
        error_log($e->getMessage()); // Log para depuração
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar a tarefa: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Dados inválidos ou formato incorreto.']);
}
?>
