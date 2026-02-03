<?php
header("Content-Type: text/plain");

$data = json_decode(file_get_contents("php://input"), true);
$req = $data['request'] ?? null;

$KEY = 'C:/wamp64/www/proyecto/services/cert/private-key.pem';

$privateKey = openssl_pkey_get_private(file_get_contents($KEY));
openssl_sign($req, $signature, $privateKey, OPENSSL_ALGO_SHA512);
openssl_free_key($privateKey);

echo base64_encode($signature);
