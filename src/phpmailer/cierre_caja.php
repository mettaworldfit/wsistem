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

if (!empty($_REQUEST['id'])) {

    $db = Database::connect();

    $id = $_REQUEST['id'];

    $query = "SELECT c.cierre_id,concat(u.nombre,' ',IFNULL(u.apellidos,'')) as cajero, c.total_esperado,
c.total_real,c.diferencia,c.saldo_inicial,c.ingresos_efectivo,c.ingresos_tarjeta,
c.ingresos_transferencia,c.egresos_caja,c.egresos_fuera,c.retiros,c.reembolsos,c.ingresos_cheque,
c.fecha_apertura,c.fecha_cierre,c.observaciones,c.estado FROM cierres_caja c
INNER JOIN usuarios u ON u.usuario_id = c.usuario_id WHERE c.cierre_id = '$id'";

    $result = $db->query($query);
    $data = $result->fetch_object();

    // Variables
    $fechaCierre = $data->fecha_cierre;
    $fechaApertura = $data->fecha_apertura;
    $cajero = $data->cajero;
    $cierreNumero = $data->cierre_id;

    $montoInicial = $data->saldo_inicial;
    $total_esperado = $data->total_esperado;
    $totalCierre = $data->total_real;

    $efectivo = $data->ingresos_efectivo;
    $transferencia = $data->ingresos_transferencia;
    $tarjeta = $data->ingresos_tarjeta;
    $cheque = $data->ingresos_cheque;

    $diferencia = $data->diferencia;

    $gastos_caja = $data->egresos_caja;
    $gastos_fuera = $data->egresos_fuera;
    $reembolsos = $data->reembolsos;
    $retiros = $data->retiros;
    $nota = $data->observaciones;

    date_default_timezone_set('America/Santo_Domingo');
    $datetimeRD = date('Y-m-d\TH:i');

    $query_conf = "SELECT logo_pdf,tel,direccion,empresa,condiciones,titulo 
	           FROM configuraciones WHERE config_id = 1";

    $conf = $db->query($query_conf)->fetch_object();

    $Logo_pdf = $conf->logo_pdf;
    $empresa = $conf->empresa;

    $nombreImagen = base_url . $Logo_pdf;
    $imagenBase64 = "data:image/png;base64," . base64_encode(file_get_contents($nombreImagen));

    $html = "
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
    .container { width: 100%; max-width: 700px; margin: auto; border: 1px solid #ccc; padding: 20px; }
    .title { font-size: 22px; text-align: center; margin-bottom: 5px; font-weight: bold; }
    .sub-title { text-align: center; font-size: 14px; margin-bottom: 15px; }
    .section { margin-bottom: 20px; }
    .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .box { border: 1px solid #ddd; padding: 8px; margin-top: 5px; }
    .table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    .table td, .table th { border: 1px solid #ddd; padding: 6px; font-size: 12px; }
    .table th { background: #f2f2f2; text-align: left; }
    .highlight { background: #f2f9ff; }
    .bold { font-weight: bold; }
    .center { text-align: center; }
    .right { text-align: right; }
    .small { font-size: 11px; }
  </style>
</head>
<body>

<div class='container'>
<div class='center' style='margin-bottom: 10px;'>
  <img src='" . $imagenBase64 . "' style='display:block; margin:auto; max-height:80px;' alt='Logo'>
  <div style='font-size: 18px; font-weight: bold; margin-top: 5px;'>$empresa</div>
</div>
  <div class='title'>Cierre de Caja</div>
  <div class='sub-title'>
    <div>Fecha: $fechaCierre</div>
    <div>Cajero: $cajero</div>
  </div>

  <div class='section'>
    Estimado(a), se detalla a continuación el cierre de caja n° $cierreNumero del punto de venta de $empresa.
  </div>

<div class='section'>
  <table class='table'>
    <tr class='highlight'>
      <th>Descripción</th>
      <th class='right'>Monto</th>
    </tr>
    <tr>
      <td><strong>Total del Cierre</strong></td>
      <td class='right bold'>\$" . number_format($totalCierre, 2) . "</td>
    </tr>
     <tr>
      <td>Total efectivo en caja</td>
      <td class='right'>\$" . number_format($total_esperado, 2) . "</td>
    </tr>
    <tr>
      <td>Diferencia</td>
      <td class='right'>\$" . number_format($diferencia, 2) . "</td>
    </tr>
  </table>
</div>


  <div class='section'>
    <table class='table'>
      <tr><th colspan='2'>Datos Apertura/Cierre</th></tr>
      <tr><td>Apertura</td><td class='right'>$fechaApertura</td></tr>
      <tr><td>Cierre</td><td class='right'>$fechaCierre</td></tr>
      <tr><td>Saldo Inicial</td><td class='right'>\$" . number_format($montoInicial, 2) . "</td></tr>
    </table>
  </div>

  <div class='section'>
    <table class='table'>
      <tr><th colspan='2'>Resumen de Ingresos</th></tr>
      <tr><td>Efectivo</td><td class='right'>\$" . number_format($efectivo, 2) . "</td></tr>
      <tr><td>Transferencias</td><td class='right'>\$" . number_format($transferencia, 2) . "</td></tr>
      <tr><td>Tarjeta</td><td class='right'>\$" . number_format($tarjeta, 2) . "</td></tr>
      <tr><td>Cheques</td><td class='right'>\$" . number_format($cheque, 2) . "</td></tr>
    </table>
  </div>


   <div class='section'>
    <table class='table'>
      <tr><th colspan='2'>Resumen de Gastos</th></tr>
      <tr><td>Gastos de caja</td><td class='right'>\$" . number_format($gastos_caja, 2) . "</td></tr>
      <tr><td>Gastos fuera de caja</td><td class='right'>\$" . number_format($gastos_fuera, 2) . "</td></tr>
      <tr><td>Reembolsos</td><td class='right'>\$" . number_format($reembolsos, 2) . "</td></tr>
      <tr><td>Retiros</td><td class='right'>\$" . number_format($retiros, 2) . "</td></tr>
    </table>
  </div>

  <div class='section'>
    <strong>Notas:</strong>
    <div class='box-notas'>" . $nota . "</div>
  </div>

  <div class='center small'>Generado por wsistems.com - " . $datetimeRD . "</div>
</div>

</body>
</html>
";


    // ==================================
    // Generación del PDF con dompdf
    // ==================================    

    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);

    $dompdf = new Dompdf($options); // Instanciar dompdf

    // Cargar plantilla HTML
    $dompdf->loadHtml($html);

    // Configurar el tamaño de papel y la orientación (opcional)
    $dompdf->setPaper('letter', 'portrait');
    // Renderizar el HTML como PDF
    $dompdf->render();

    // Obtener el contenido del PDF en una variable
    $pdfOutput = $dompdf->output();


      // ==============================================================
    // Obtener configuracion del servidor SMTP desde la base de datos
    // ==============================================================

    $query3 = "SELECT empresa,email,password,host,puerto,smtps,logo_url,logo_pdf,slogan,tel,direccion,
    link_fb,link_ws,link_ig,condiciones,titulo FROM configuraciones WHERE config_id = 1";

    $conf = $db->query($query3)->fetch_object();

    $Host = $conf->host;
    $Pass = $conf->password;
    $Email = $conf->email;
    $Company = $conf->empresa;
    $Port = $conf->puerto;
    $SMTPS = $conf->smtps;
    $Logo_url = $conf->logo_url;
    $Logo_pdf = $conf->logo_pdf;
    $Slogan = $conf->slogan;
    $Tel = $conf->tel;
    $Dir = $conf->direccion;
    $Policy = $conf->condiciones;
	$Title = $conf->titulo;
    // Redes sociales
    $Link_ws = $conf->link_ws;
    $Link_fb = $conf->link_fb;
    $Link_ig = $conf->link_ig;


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

        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';

        // CAMBIA ESTA VARIABLE SEGÚN TU CONFIGURACIÓN

        $smtpHost = $Host; // o 'smtp.gmail.com', etc.
        $mail->Host = $smtpHost;

        // Puerto recomendado para localhost (sin TLS)
        $mail->Port = ($smtpHost === 'localhost') ? 25 : $Port;

        // Si no es localhost, activamos TLS y autenticación
        if ($smtpHost !== 'localhost') {
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = $SMTPS;
            $mail->Username = $Email;
            $mail->Password = $Pass;
        } else {
            $mail->SMTPAuth = false;
        }

        // Remitente
        $mail->setFrom($Email, $Company);

        // Destinatario principal 
        $mail->addAddress('mettaworldfit@gmail.com', $Company);

        // Segundo destinatario oculto (BCC)
        $mail->addBCC('contacto@wsistems.com', 'Administrador del Sistema');

        // Contenido del correo
        $mail->isHTML(true); // Establecer el formato de correo 
        $mail->Subject = 'Reporte cierre de caja - '. $Company;
        $mail->Body    = 'Este correo contiene un PDF';

        // Adjuntar el PDF directamente desde la variable
        $mail->addStringAttachment($pdfOutput, 'cierre.pdf');

        ob_start();
        include('facturas/cierre_caja.php');
        $html = ob_get_clean();

        // Asigna el contenido HTML al cuerpo del correo
        $mail->Body = $html;

        $mail->send(); // Enviar correo

        echo 'El correo ha sido enviado correctamente';
    } catch (Exception $e) {
        echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
    }
} else {
    echo "No es posible generar la factura.";
}
