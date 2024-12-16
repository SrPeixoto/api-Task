<?php
require_once 'vendor/autoload.php';
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] == '') {
    // Caso o usuário não esteja autenticado, redireciona para a página de login do Google
    $config = include('oauth-config.php');
    $client = new Google\Client();
    $client->setClientId($config['client_id']);
    $client->setClientSecret($config['client_secret']);
    $client->setRedirectUri($config['redirect_uri']);
    $client->addScope($config['scopes']);

    // Se o código de autenticação estiver presente na URL, tentamos obter o token
    if (isset($_GET['code'])) {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        if (!isset($token['error'])) {
            $_SESSION['access_token'] = $token;  // Armazena o token na sessão
            header('Location: index.php');  // Redireciona de volta para a página inicial
            exit();
        } else {
            echo 'Erro ao processar o token: ' . $token['error'];
        }
    } else {
        // Caso o código de autenticação não esteja presente, redireciona para o login
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit();
    }
} else {
    // Caso o usuário já tenha feito login, configura o cliente do Google
    $client = new Google\Client();
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google\Service\Tasks($client);

    // Recupera as tarefas
    try {
        $tasksList = $service->tasks->listTasks('@default');
        $tasks = $tasksList->getItems();
    } catch (Exception $e) {
        $tasks = [];
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Tasks Manager</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
</head>
<body>
    <!-- Cabeçalho -->
    <header class="header">
        <h1>Gerencie suas tarefas facilmente</h1>
        <div id="login-section">
            <button id="google-login-btn" class="google-login-btn">
                <?php
                // Pegando as iniciais do email
                $google_account_info = $client->getAccessToken();
                if (isset($google_account_info['email'])) {
                    $name_initials = strtoupper(substr($google_account_info['email'], 0, 2)); // Pegando as iniciais
                } else {
                    $name_initials = 'Entrar com o Google'; // Defina um valor padrão, como 'NA' se o e-mail não estiver disponível
                }
                echo $name_initials;
                ?>
            </button>
        </div>
    </header>

    <!-- Corpo principal -->
    <main class="main-container">
        <div class="info-section">
            <h2>Sobre o Sistema</h2>
            <p>Este é um sistema simples e moderno para gerenciar suas tarefas do Google Tasks. Crie, edite, exclua e organize suas tarefas com facilidade.</p>
        </div>
        
        <div class="tasks-section">
            <div class="tasks-container">

                <h2>Suas Tarefas</h2>

                <div class="sort-options">
                    <label for="sort-tasks">Ordenar por:</label>
                    <select id="sort-tasks">
                        <option value="title">Título</option>
                        <option value="due-date">Data de Prazo</option>
                        <option value="created-at">Data de Criação</option>
                    </select>
                </div>

                <ul id="tasks-list">
                    <?php
                    if ($tasks) {
                        foreach ($tasks as $task) {
                            $isCompleted = $task->getStatus() == 'completed' ? 'completed' : '';
                            echo "<li data-id='" . $task->getId() . "' class='task $isCompleted'>
                                    <!-- Bolinha de status -->
                                    <span class='status-circle $isCompleted' data-id='" . $task->getId() . "'></span>
                                    
                                    <!-- Título da tarefa -->
                                    <span class='task-title $isCompleted'>" . $task->getTitle() . "</span>
                                    
                                    <!-- Ícones de editar e excluir -->
                                    <span class='task-actions'>
                                        <i class='fas fa-edit edit-task-icon' data-id='" . $task->getId() . "'></i>
                                        <i class='fas fa-trash delete-task-icon' data-id='" . $task->getId() . "'></i>
                                    </span>
                                </li>";
                        }
                    } else {
                        echo "<li>Você não tem tarefas no momento.</li>";
                    }
                    ?>
                </ul>




                <button id="create-task-btn">Criar Tarefa</button>
            </div>

            <!-- Seção para tarefas concluídas -->
            <div id="completed-tasks-container">
                <h3 id="toggle-completed-tasks">Tarefas Concluídas</h3>
                <ul id="completed-tasks-list" class="hidden">
                    <!-- Tarefas concluídas serão adicionadas aqui -->
                </ul>
            </div>
        </div>
    </main>

    <!-- Modal de criação de tarefa -->
    <div id="create-task-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="close-create-task-modal">&times;</span>
            <h2>Criar Tarefa</h2>
            <form id="create-task-form">
                <label for="task-title">Título:</label>
                <input type="text" id="task-title" name="title" required>

                <label for="task-notes">Notas:</label>
                <textarea id="task-notes" name="notes"></textarea>

                <button type="submit">Criar Tarefa</button>
            </form>
        </div>
    </div>
    <div id="modal-overlay"></div>

    <!-- Modal de Edição de Tarefa -->
    <div id="edit-task-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="close-edit-task-modal">&times;</span>
            <h2>Editar Tarefa</h2>
            <form id="edit-task-form">
                <input type="hidden" id="edit-task-id" name="id">
                
                <label for="edit-task-title">Título:</label>
                <input type="text" id="edit-task-title" name="title" required>
                
                <label for="edit-task-notes">Notas:</label>
                <textarea id="edit-task-notes" name="notes"></textarea>
                
                <button type="submit">Salvar Alterações</button>
            </form>
        </div>
    </div>
    <div id="modal-overlay"></div>

    <script src="https://apis.google.com/js/api.js"></script>
    <script src="app.js"></script>
</body>
</html>
