<?php
// NADA antes, NADA después

ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Leer JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['request'])) {
    http_response_code(400);
    exit;
}

$data = $input['request'];

// RUTA ABSOLUTA a la clave (mejor fuera del webroot)
$KEY = '/var/www/qz/private-key.pem';

if (!file_exists($KEY)) {
    http_response_code(500);
    exit;
}

$privateKey = openssl_pkey_get_private(file_get_contents($KEY));

if (!$privateKey) {
    http_response_code(500);
    exit;
}

$signature = '';
$ok = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA512);

if (!$ok) {
    http_response_code(500);
    exit;
}

echo base64_encode($signature);
exit;

