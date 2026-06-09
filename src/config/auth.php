<?php

session_start();

if (!isset($_SESSION['identity'])) {

    http_response_code(401);

    echo json_encode([
        'success' => false
    ]);

    exit;
}

echo json_encode([
    'success' => true,
    'usuario_id' => $_SESSION['identity']->usuario_id,
    'usuario' => $_SESSION['identity']->username,
    'password' => $_SESSION['identity']->password,
    'database' => $_SESSION['infoClient']['dbname']
]);