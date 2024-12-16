<?php
return [
    'client_id' => '251059465789-286marl3v2aduqh8ikeu97hshm5uv1qe.apps.googleusercontent.com', // Substitua pelo seu client_id
    'client_secret' => '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', // Substitua pelo seu client_secret
    'redirect_uri' => 'http://localhost/apitask/oauth2callback.php', // Substitua com o seu URI de redirecionamento
    'scopes' => [
        'https://www.googleapis.com/auth/tasks',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/tasks.readonly'
    ]
];
