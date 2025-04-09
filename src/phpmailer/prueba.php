<?php

require '../../vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true); // Instancia de PHPMailer
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

try {

    // Configuración del servidor
    $mail->SMTPDebug = 2;
    $mail->isSMTP(); // Usar SMTP
    $mail->Host = 'localhost'; // Especificar el servidor SMTP
    $mail->SMTPAuth = false; // Habilitar autenticación SMTP
    $mail->SMTPSecure = ''; // Habilitar encriptación TLS
    $mail->CharSet = 'UTF-8';
    $mail->Port = 587; // Puerto TCP para TLS

    // Destinatarios
    $mail->setFrom('wjose260@gmail.com', 'Wilmin Jose Sanchez');
    $mail->addAddress('mettaworldfit@gmail.com', 'Receptor'); // Agregar un destinatario

    // Contenido del correo
    $mail->isHTML(true); // Establecer el formato de correo 
    $mail->Subject = 'Prueba desde servidor ubuntu';
    $mail->Body    = 'Este correo contiene un PDF generado dinámicamente con dompdf y enviado mediante PHPMailer.';

    $mail->send(); // Enviar correo

    echo 'El correo ha sido enviado';
} catch (Exception $e) {
    echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
}
