<?php
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/x-pem-file');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$host = $_SERVER['HTTP_HOST'] ?? '';

// Localhost
$isLocal =
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false ||
    strpos($host, '::1') !== false;

$certFile = $isLocal
    ? __DIR__ . '/local-certificate.txt'
    : __DIR__ . '/qz-certificate.txt';

if (!file_exists($certFile)) {
    http_response_code(404);
    exit;
}

readfile($certFile);
exit;