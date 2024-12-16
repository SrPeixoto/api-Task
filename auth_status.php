<?php
session_start();

$response = [
    'authenticated' => isset($_SESSION['access_token']) && !empty($_SESSION['access_token']),
    'user' => [
        'email' => $_SESSION['email'] ?? null,
        'name' => $_SESSION['name'] ?? null,
    ]
];

if ($response['authenticated']) {
    $response['user'] = [
        'email' => $_SESSION['email'] ?? 'Email não encontrado',
        'name' => $_SESSION['name'] ?? 'Usuário',
    ];
} else {
    $response['user'] = [
        'email' => null,
        'name' => null,
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
