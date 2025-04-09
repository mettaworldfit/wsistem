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
    $mail->Host = 'smtp.gmail.com'; // Especificar el servidor SMTP
    $mail->SMTPAuth = true; // Habilitar autenticación SMTP
    $mail->Username = 'wjose260@gmail.com'; // Tu correo
    $mail->Password = 'wlgh cdau vgqo beeg'; // Tu contraseña
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Habilitar encriptación TLS
    $mail->CharSet = 'UTF-8';
    $mail->Port = 587; // Puerto TCP para TLS

    // Destinatarios
    $mail->setFrom('wjose260@gmail.com', 'Wilmin Jose Sanchez');
    $mail->addAddress('mettaworldfit@gmail.com', 'Receptor'); // Agregar un destinatario

    // Contenido del correo
    $mail->isHTML(true); // Establecer el formato de correo 
    $mail->Subject = 'Asunto del correo';
    $mail->Body    = 'Este correo contiene un PDF generado dinámicamente con dompdf y enviado mediante PHPMailer.';

    // $mail->Body = 'Hola, <br/>Esta es una prueba desde <b>Gmail</b>.';

    // Adjuntar un archivo
    //  $mail->addAttachment('/ruta/al/archivo.pdf'); // Ruta al archivo que deseas adjuntar

     // Añadir imagen embebida
    // $mail->addEmbeddedImage('chino_com.png', 'cid:chino_com');

    // Lee el contenido del archivo HTML
    // $html = file_get_contents('ejemplo1.html');

    // Asigna el contenido HTML al cuerpo del correo
    // $mail->Body = $html;

    $mail->send(); // Enviar correo

    echo 'El correo ha sido enviado';
} catch (Exception $e) {
    echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
}
