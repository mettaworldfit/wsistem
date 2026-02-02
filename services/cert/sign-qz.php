<?php

// Sample key.  Replace with one used for CSR generation
// $KEY = 'C:/wamp64/www/proyecto/services/cert/private-key.pem';


// Ruta relativa
$KEY = '/var/www/wsistem/services/cert/private-key.pem';

// Verifica si el archivo existe
if (file_exists($KEY)) {
    echo "La ruta existe: " . $KEY;
} else {
    echo "La ruta no existe.";
}

$req = $_GET['request'];
$privateKey = openssl_get_privatekey(file_get_contents($KEY) /*, $PASS */);

$signature = null;
openssl_sign($req, $signature, $privateKey, "sha512"); // Use "sha1" for QZ Tray 2.0 and older

if ($signature) {
	header("Content-type: text/plain");
	echo base64_encode($signature);
	exit(0);
}

echo '<h1>Error signing message</h1>';
http_response_code(500);
exit(1);

?>
