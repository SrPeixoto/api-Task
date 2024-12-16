<?php
require_once 'vendor/autoload.php';

$config = include('oauth-config.php');

// Configuração do cliente Google
$client = new Google\Client();
$client->setClientId($config['client_id']);
$client->setClientSecret($config['client_secret']);
$client->setRedirectUri($config['redirect_uri']);
$client->addScope($config['scopes']);

// Inicia a sessão, se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) {
    // Usuário já está logado, redireciona para a página inicial
    header('Location: index.php');
    exit();
}

if (!isset($_GET['code'])) {
    // Verifica se o usuário já está autenticado
    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        header('Location: index.php');
        exit();
    }

    // Caso contrário, redireciona para a página de autenticação do Google
    $authUrl = $client->createAuthUrl();
    echo "Redirecionando para autenticação com o Google...";
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
} else {
    // Obtém o token de acesso
    $authCode = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
    $token = $client->fetchAccessTokenWithAuthCode($authCode);

    // Verifica se houve erro ao obter o token
    if (isset($token['error'])) {
        echo 'Erro ao autenticar: ' . htmlspecialchars($token['error_description'] ?? $token['error']);
        exit();
    }

    // Valida o token e armazena na sessão
    $client->setAccessToken($token);
    $oauth2 = new Google\Service\Oauth2($client);

    try {
        $userInfo = $oauth2->userinfo->get(); // Obtém informações do usuário
        $_SESSION['user_email'] = $userInfo->email;
        $_SESSION['user_name'] = $userInfo->name; // Armazena o nome do usuário
        $_SESSION['access_token'] = $token; // Armazenando o token completo

        // Redireciona para o index.php após login bem-sucedido
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        echo 'Erro ao validar o token: ' . $e->getMessage();
        exit();
    }
}
?>
