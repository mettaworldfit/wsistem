<?php

session_start();

if (!isset($_SESSION['identity'])) {

    header('location: ../');
}

require '../../vendor/autoload.php';
require_once '../../config/parameters.php';
require_once '../../config/db.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!empty($_REQUEST['f'])) {

    $db = Database::connect();

    $invID = $_REQUEST['f'];
    $subtotal = $_REQUEST['sub'];
    $discount = $_REQUEST['dis'];
    $taxes = $_REQUEST['tax'];
    $total = $_REQUEST['total'];
    $method = $_REQUEST['method'];
    $date = $_REQUEST['date'];

    /**
     * TODO: Datos cliente
 -------------------------------------------------------------------------------------------------------------------------------*/

    date_default_timezone_set('America/New_York');

    $query = "SELECT c.cedula ,c.nombre as nombre_cliente, c.apellidos as apellidos_cliente,c.telefono1,c.telefono2,
 c.email,c.direccion,m.nombre_metodo, u.nombre as nombre_usuario, u.apellidos as apellidos_usuario, 
 f.factura_venta_id, f.fecha, f.descripcion FROM facturas_ventas f
				INNER JOIN clientes c ON c.cliente_id = f.cliente_id
				INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
				INNER JOIN usuarios u ON u.usuario_id = f.usuario_id
				WHERE f.factura_venta_id = '$invID'";

    $data = $db->query($query)->fetch_object();


    /**
     * TODO: Detalle de factura
 -------------------------------------------------------------------------------------------------------------------------------*/

    $query_detail = "SELECT p.nombre_producto, df.precio, pz.nombre_pieza, s.nombre_servicio, df.cantidad as cantidad_total, 
df.detalle_venta_id as 'id', df.descuento, df.impuesto, i.valor FROM detalle_facturas_ventas df
		 LEFT JOIN detalle_ventas_con_productos dvp ON df.detalle_venta_id = dvp.detalle_venta_id
		 LEFT JOIN productos p ON p.producto_id = dvp.producto_id
		 LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
		 LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
		 LEFT JOIN detalle_ventas_con_piezas_ dvpz ON df.detalle_venta_id = dvpz.detalle_venta_id
		 LEFT JOIN piezas pz ON pz.pieza_id = dvpz.pieza_id
		 LEFT JOIN detalle_ventas_con_servicios dvs ON df.detalle_venta_id = dvs.detalle_venta_id
		 LEFT JOIN servicios s ON s.servicio_id = dvs.servicio_id
		 WHERE df.factura_venta_id = '$invID'";

    $result_detail = $db->query($query_detail);

    // datos de las sesion del cliente
    $logoPDF = $_SESSION['infoClient']['logo'];
    $slogan = $_SESSION['infoClient']['slogan'];
    $direction = $_SESSION['infoClient']['direction'];
    $tel = $_SESSION['infoClient']['phone'];
    $policyPDF = $_SESSION['infoClient']['footer'];
    $footerPDF = $_SESSION['infoClient']['caption'];

    // ======================================
    // Obtener Email del cliente
    // ======================================

    $query2 = "SELECT c.email, c.nombre, c.apellidos FROM facturas_ventas f 
              INNER JOIN clientes c ON c.cliente_id = f.cliente_id
              WHERE factura_venta_id = '$invID'";
    $customer = $db->query($query2)->fetch_object();

    $CustMail = $customer->email;
    $CustName = $customer->nombre;
    $CustLastName = $customer->apellidos;

    // ==================================
    // Generación del PDF con dompdf
    // ==================================    

    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);

    $dompdf = new Dompdf($options); // Instanciar dompdf

    // Cargar plantilla HTML
    ob_start();
    include('../pdf/facturas/factura_venta.php');
    $html = ob_get_clean();

    $dompdf->loadHtml($html);

    // Configurar el tamaño de papel y la orientación (opcional)
    $dompdf->setPaper('letter', 'portrait');
    // Renderizar el HTML como PDF
    $dompdf->render();

    // Obtener el contenido del PDF en una variable
    $pdfOutput = $dompdf->output();


    // ===========================================
    // Envío del PDF mediante PHPMailer
    // ===========================================


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
        $mail->Subject = 'Recibo de su compra';
        $mail->Body    = 'Este correo contiene un PDF';


        // Adjuntar el PDF directamente desde la variable
        $mail->addStringAttachment($pdfOutput, 'factura.pdf');

        // Añadir imagen embebida
        // $mail->addEmbeddedImage('chino_com.png', 'cid:chino_com');

        // Lee el contenido del archivo HTML
       // $html = file_get_contents('ventas.html');
        ob_start();
        include('ventas.php');
        $html = ob_get_clean();

        // Asigna el contenido HTML al cuerpo del correo
        $mail->Body = $html;

        $mail->send(); // Enviar correo

        echo 'El correo ha sido enviado';
    } catch (Exception $e) {
        echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
    }
} else {
    echo "No es posible generar la factura.";
}
