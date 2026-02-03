<?php
// ⚠️ NADA antes de <?php
// ⚠️ NADA después del echo

ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$certFile = __DIR__ . '/qz-certificate.txt';

if (!file_exists($certFile)) {
    http_response_code(404);
    exit;
}

echo trim(file_get_contents($certFile));
exit;
