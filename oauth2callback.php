<?php
require_once 'vendor/autoload.php';
session_start();

$config = include('oauth-config.php');
$client = new Google\Client();
$client->setClientId($config['client_id']);
$client->setClientSecret($config['client_secret']);
$client->setRedirectUri($config['redirect_uri']);
$client->addScope($config['scopes']);

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $token = $client->getAccessToken();

    if ($token) {
        $_SESSION['access_token'] = $token;

        // Buscar informações do usuário
        $oauth = new Google\Service\Oauth2($client);
        $userinfo = $oauth->userinfo->get();

        // Armazene os dados na sessão
        $_SESSION['email'] = $userinfo->email ?? 'Email não encontrado';
        $_SESSION['name'] = $userinfo->name ?? 'Usuário';

        header('Location: index.php');
        exit();
    } else {
        echo 'Erro ao autenticar usuário.';
        exit();
    }
} else {
    echo 'Código de autenticação não encontrado.';
    exit();
}
