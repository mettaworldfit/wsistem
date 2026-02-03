<?php

//$KEY = 'C:/wamp64/www/proyecto/services/cert/private-key.pem';
// $KEY = '/var/www/wsistem/services/cert/private-key.pem';



error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: text/plain");

$keyPath = 'C:/wamp64/www/proyecto/services/cert/private-key.pem';

if (!file_exists($keyPath)) {
    http_response_code(500);
    echo "KEY_NOT_FOUND";
    exit;
}

$keyData = file_get_contents($keyPath);
if ($keyData === false) {
    http_response_code(500);
    echo "KEY_NOT_READABLE";
    exit;
}

$privateKey = openssl_pkey_get_private($keyData);
if ($privateKey === false) {
    http_response_code(500);
    echo "INVALID_PRIVATE_KEY";
    exit;
}

$data = file_get_contents("php://input");
if (!$data) {
    http_response_code(500);
    echo "EMPTY_INPUT";
    exit;
}

$signature = null;
if (!openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
    http_response_code(500);
    echo "SIGN_FAILED";
    exit;
}

echo base64_encode($signature);