<?php

// Sample key.  Replace with one used for CSR generation
// $KEY = 'C:/wamp64/www/proyecto/services/cert/private-key.pem';
$KEY =  $_SERVER['DOCUMENT_ROOT'] . "/services/cert/private-key.pem";

//$PASS = 'S3cur3P@ssw0rd';

$req = $_GET['request'];
$privateKey = openssl_get_privatekey(file_get_contents($KEY) /*, $PASS */);

$signature = null;
openssl_sign($req, $signature, $privateKey, "sha512"); // Use "sha1" for QZ Tray 2.0 and older

/*
// Or alternately, via phpseclib
include('Crypt/RSA.php');
$rsa = new Crypt_RSA();
$rsa.setHash('sha512'); // Use 'sha1' for QZ Tray 2.0 and older
$rsa->loadKey(file_get_contents($KEY));
$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
$signature = $rsa->sign($req);
*/

if ($signature) {
	header("Content-type: text/plain");
	echo base64_encode($signature);
	exit(0);
}

echo '<h1>Error signing message</h1>';
http_response_code(500);
exit(1);

?>
