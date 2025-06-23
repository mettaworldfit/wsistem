<?php
session_start();

if (!isset($_SESSION['identity'])) {

  header('location: ../');
}

require_once '../../vendor/autoload.php';
require_once '../../config/parameters.php';
require_once '../../config/db.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

// Variables de ejemplo
$nombrePuntoVenta = "POS HERNAN";
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


$html80mm = "
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <style>
  body {
    font-family: Arial, sans-serif;
    font-size: 8pt;
    color: #000;
    margin: 0;
    padding: 0;
  }

  .container {
    width: 100%;
    padding: 4pt 8pt;
  }

  .title {
    font-size: 12pt;
    font-weight: bold;
    text-align: center;
    margin-bottom: 4pt;
  }

  .sub-title {
    text-align: center;
    font-size: 8pt;
    margin-bottom: 4pt;
  }

  .section {
    margin-bottom: 5pt;
  }

  .table {
    width: 100%;
    border-collapse: collapse;
  }

  .table td,
  .table th {
    padding: 2pt;
    font-size: 8pt;
  }

  .table th {
    background-color: #f2f2f2;
    font-weight: bold;
    text-align: left;
  }

  .highlight {
    background: #f2f9ff;
  }

  .bold {
    font-weight: bold;
  }

  .center {
    text-align: center;
  }

  .right {
    text-align: right;
  }

  .box-notas {
    border-top: 0.5pt dashed #000;
    padding-top: 3pt;
    margin-top: 3pt;
    font-size: 7pt;
  }

  .logo {
    max-height: 38pt;
    display: block;
    margin: 0 auto 4pt auto;
  }

  .small {
    font-size: 7pt;
    text-align: center;
    margin-top: 4pt;
  }
</style>

</head>
<body>

<div class='container'>
  <div class='center'>
    <img src='" . $imagenBase64 . "' class='logo' alt='Logo'>
    <div style='font-size: 14px; font-weight: bold;'>$empresa</div>
  </div>

  <div class='title'>CIERRE DE CAJA</div>
  <div class='sub-title'>
    Cajero: $cajero<br>
    Fecha cierre: $fechaCierre
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
        <td>Total esperado</td>
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
      <tr><th colspan='2'>Apertura / Cierre</th></tr>
      <tr><td>Apertura</td><td class='right'>$fechaApertura</td></tr>
      <tr><td>Cierre</td><td class='right'>$fechaCierre</td></tr>
      <tr><td>Saldo inicial</td><td class='right'>\$" . number_format($montoInicial, 2) . "</td></tr>
    </table>
  </div>

  <div class='section'>
    <table class='table'>
      <tr><th colspan='2'>Ingresos</th></tr>
      <tr><td>Efectivo</td><td class='right'>\$" . number_format($efectivo, 2) . "</td></tr>
      <tr><td>Transferencias</td><td class='right'>\$" . number_format($transferencia, 2) . "</td></tr>
      <tr><td>Tarjeta</td><td class='right'>\$" . number_format($tarjeta, 2) . "</td></tr>
      <tr><td>Cheques</td><td class='right'>\$" . number_format($cheque, 2) . "</td></tr>
    </table>
  </div>

  <div class='section'>
    <table class='table'>
      <tr><th colspan='2'>Egresos</th></tr>
      <tr><td>Gastos caja</td><td class='right'>\$" . number_format($gastos_caja, 2) . "</td></tr>
      <tr><td>Gastos fuera</td><td class='right'>\$" . number_format($gastos_fuera, 2) . "</td></tr>
      <tr><td>Reembolsos</td><td class='right'>\$" . number_format($reembolsos, 2) . "</td></tr>
      <tr><td>Retiros</td><td class='right'>\$" . number_format($retiros, 2) . "</td></tr>
    </table>
  </div>

  <div class='section'>
    <strong>Notas:</strong>
    <div class='box-notas'>" . $nota . "</div>
  </div>

  <div class='small'>Generado por wsistems.com - " . $datetimeRD . "</div>
</div>

</body>
</html>";

$options = new Options();
$options->set('isRemoteEnabled', TRUE);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'portrait');

// $dompdf->loadHtml($html80mm);
// $dompdf->setPaper([0, 0, 226.77, 1000], 'portrait'); // 80mm de ancho

// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
$dompdf->stream('cotizacion.pdf', array('Attachment' => 0));
exit;

} else {
	echo "No es posible generar el cierre de caja.";
}