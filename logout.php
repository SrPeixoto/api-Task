<?php
session_start();

// Remover token da sessão
unset($_SESSION['access_token']);
unset($_SESSION['user_email']);
unset($_SESSION['name']);

// Destruir sessão PHP
session_unset();
session_destroy();

// Redefinir o cookie da sessão
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Evitar que a página seja armazenada em cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Redirecionar para a página de login
header('Location: login.php');
exit();
