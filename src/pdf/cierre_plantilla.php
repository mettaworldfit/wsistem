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

// Variables de ejemplo
$nombrePuntoVenta = "POS HERNAN";
$fechaCierre = "17/06/2025 19:32:00";
$cajero = "Wilmin Jose";
$cierreNumero = 54;
$totalCierre = 8500.00;
$cierreAnterior = 8100.00;
$fechaApertura = "17/06/2025 16:19:00";
$fechaCierre = "17/06/2025 19:32:00";
$montoInicial = 1500.00;

$efectivo = 7000.00;
$tarjeta = 4600.00;
$cheque = 5000.00;

$subtotal = 12000.00;
$iva = 2160.00;
$servicio = 0.00;
$descuento = 500.00;
$total = 13460.00;
$propina = 300.00;

$datafast = 1200.00;
$medianet = 2200.00;
$dataexpress = 1200.00;
$totalRedes = $datafast + $medianet + $dataexpress;

$gastos = 10000.00;
$retiros = 0.00;

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
  <div class='title'>Cierre de Caja</div>
  <div class='sub-title'>
    <div><strong>$nombrePuntoVenta</strong></div>
    <div>Fecha: $fechaCierre</div>
    <div>Cajero: $cajero</div>
  </div>

  <div class='section'>
    Estimado(a), se detalla a continuación el cierre de caja n° $cierreNumero del punto de venta de $nombrePuntoVenta.
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
      <td>Diferencia</td>
      <td class='right'>\$" . number_format($totalCierre - $cierreAnterior, 2) . "</td>
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
      <tr><td>Transferencias</td><td class='right'>\$" . '$0.00' . "</td></tr>
      <tr><td>Tarjeta</td><td class='right'>\$" . number_format($tarjeta, 2) . "</td></tr>
      <tr><td>Cheques</td><td class='right'>\$" . number_format($cheque, 2) . "</td></tr>
    </table>
  </div>


   <div class='section'>
    <table class='table'>
      <tr><th colspan='2'>Resumen de Gastos</th></tr>
      <tr><td>Gastos de caja</td><td class='right'>\$" . number_format($gastos, 2) . "</td></tr>
      <tr><td>Gastos fuera de caja</td><td class='right'>\$" . '$0.00' . "</td></tr>
      <tr><td>Retiros</td><td class='right'>\$" . number_format($retiros, 2) . "</td></tr>
    </table>
  </div>

  <div class='section'>
    <strong>Notas:</strong>
    <div class='box-notas'></div>
  </div>

  <div class='center small'>Generado por wsistems.com - " . date('d/m/Y H:i') . "</div>
</div>

</body>
</html>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("cierre_caja.pdf", ["Attachment" => false]);
