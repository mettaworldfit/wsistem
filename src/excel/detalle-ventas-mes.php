<?php
session_start();
require '../../vendor/autoload.php';
require_once '../../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

if (!isset($_GET['month'], $_GET['year'])) {
    echo "Parámetros inválidos.";
    exit;
}

// Parámetros de mes y año
$month = $_GET['month'];   // puedes reemplazar con $_GET['mes']
$year = $_GET['year']; // puedes reemplazar con $_GET['anio']

$db = Database::connect();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

if ($month < 1 || $month > 12 || $year < 2000) {
    echo "Mes o año no válido.";
    exit;
}

$query = "SELECT 
  nombre, 
  tipo, 
  SUM(cantidad) AS cantidad, 
  SUM(costo) AS costo,
  SUM(total) AS total, 
  ROUND(SUM(ganancia), 2) AS ganancia
FROM (
    -- Productos en facturas de ventas
    SELECT 
      p.nombre_producto AS nombre,
      'Producto' AS tipo,
      SUM(d.cantidad) AS cantidad,
      SUM(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) AS costo,
      SUM(d.precio * d.cantidad - d.descuento) AS total,
      SUM(
        (f.recibido / NULLIF(ft.total_facturado, 0)) * 
        ((d.precio * d.cantidad - d.descuento) - 
        COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0))
      ) AS ganancia
    FROM detalle_facturas_ventas d
    INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
    INNER JOIN (
      SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
      FROM detalle_facturas_ventas
      WHERE MONTH(fecha) = 07 AND YEAR(fecha) = 2025
      GROUP BY factura_venta_id
    ) ft ON ft.factura_venta_id = f.factura_venta_id
    INNER JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
    INNER JOIN productos p ON p.producto_id = dp.producto_id
    WHERE MONTH(d.fecha) = 07 AND YEAR(d.fecha) = 2025
    GROUP BY p.nombre_producto

    UNION ALL

    -- Piezas en facturas de ventas
    SELECT 
      p.nombre_pieza AS nombre,
      'Pieza' AS tipo,
      SUM(d.cantidad) AS cantidad,
      SUM(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) AS costo,
      SUM(d.precio * d.cantidad - d.descuento) AS total,
      SUM(
        (f.recibido / NULLIF(ft.total_facturado, 0)) * 
        ((d.precio * d.cantidad - d.descuento) - 
        COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0))
      ) AS ganancia
    FROM detalle_facturas_ventas d
    INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
    INNER JOIN (
      SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
      FROM detalle_facturas_ventas
      WHERE MONTH(fecha) = 07 AND YEAR(fecha) = 2025
      GROUP BY factura_venta_id
    ) ft ON ft.factura_venta_id = f.factura_venta_id
    INNER JOIN detalle_ventas_con_piezas_ dp ON dp.detalle_venta_id = d.detalle_venta_id
    INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
    WHERE MONTH(d.fecha) = 07 AND YEAR(d.fecha) = 2025
    GROUP BY p.nombre_pieza

    UNION ALL

    -- Piezas en ordenes de reparacion (facturasRP)
    SELECT 
      p.nombre_pieza AS nombre,
      'Pieza' AS tipo,
      SUM(d.cantidad) AS cantidad,
      SUM(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) AS costo,
      SUM(d.precio * d.cantidad - d.descuento) AS total,
      SUM(
        (frp.recibido / NULLIF(ft.total_facturado, 0)) * 
        ((d.precio * d.cantidad - d.descuento) - 
        COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0))
      ) AS ganancia
    FROM detalle_ordenRP d
    INNER JOIN facturasRP frp ON frp.orden_rp_id = d.orden_rp_id
    INNER JOIN (
      SELECT orden_rp_id, SUM(precio * cantidad - descuento) AS total_facturado
      FROM detalle_ordenRP
      WHERE MONTH(fecha) = 07 AND YEAR(fecha) = 2025
      GROUP BY orden_rp_id
    ) ft ON ft.orden_rp_id = frp.orden_rp_id
    INNER JOIN detalle_ordenRP_con_piezas dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
    WHERE MONTH(d.fecha) = 07 AND YEAR(d.fecha) = 2025
    GROUP BY p.nombre_pieza

    UNION ALL

    -- Servicios en facturas de ventas
    SELECT 
      s.nombre_servicio AS nombre,
      'Servicio' AS tipo,
      SUM(d.cantidad) AS cantidad,
      SUM(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)) AS costo,
      SUM(d.precio * d.cantidad - d.descuento) AS total,
      SUM(
        (f.recibido / NULLIF(ft.total_facturado, 0)) * 
        ((d.precio * d.cantidad - d.descuento) - 
        COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0))
      ) AS ganancia
    FROM detalle_facturas_ventas d
    INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
    INNER JOIN (
      SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
      FROM detalle_facturas_ventas
      WHERE MONTH(fecha) = 07 AND YEAR(fecha) = 2025
      GROUP BY factura_venta_id
    ) ft ON ft.factura_venta_id = f.factura_venta_id
    INNER JOIN detalle_ventas_con_servicios ds ON ds.detalle_venta_id = d.detalle_venta_id
    INNER JOIN servicios s ON s.servicio_id = ds.servicio_id
    WHERE MONTH(d.fecha) = 07 AND YEAR(d.fecha) = 2025
    GROUP BY s.nombre_servicio

    UNION ALL

    -- Servicios en ordenes de reparacion (facturasRP)
    SELECT 
      s.nombre_servicio AS nombre,
      'Servicio' AS tipo,
      SUM(d.cantidad) AS cantidad,
      SUM(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)) AS costo,
      SUM(d.precio * d.cantidad - d.descuento) AS total,
      SUM(
        (frp.recibido / NULLIF(ft.total_facturado, 0)) * 
        ((d.precio * d.cantidad - d.descuento) - 
        COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0))
      ) AS ganancia
    FROM detalle_ordenRP d
    INNER JOIN facturasRP frp ON frp.orden_rp_id = d.orden_rp_id
    INNER JOIN (
      SELECT orden_rp_id, SUM(precio * cantidad - descuento) AS total_facturado
      FROM detalle_ordenRP
      WHERE MONTH(fecha) = 07 AND YEAR(fecha) = 2025
      GROUP BY orden_rp_id
    ) ft ON ft.orden_rp_id = frp.orden_rp_id
    INNER JOIN detalle_ordenRP_con_servicios dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    INNER JOIN servicios s ON s.servicio_id = dp.servicio_id
    WHERE MONTH(d.fecha) = 07 AND YEAR(d.fecha) = 2025
    GROUP BY s.nombre_servicio
) AS detalle_ventas_mes GROUP BY nombre, tipo
 ORDER BY tipo DESC;";

$result = $db->query($query);

// Encabezados
$headers = ['Nombre', 'Tipo', 'Cantidad total', 'Costo total', 'Precio Total', 'Ganancia recibida'];
$sheet->fromArray($headers, NULL, 'A1');

// Estilo encabezado
$sheet->getStyle('A1:F1')->getFont()->setBold(true);
$sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Variables de totales
$totalCosto = $totalTotal = $totalGanancia = 0;
$row = 2;

while ($r = $result->fetch_assoc()) {
    $sheet->setCellValue("A{$row}", $r['nombre']);
    $sheet->setCellValue("B{$row}", $r['tipo']);
    $sheet->setCellValue("C{$row}", $r['cantidad']);
    $sheet->setCellValue("D{$row}", $r['costo']);
    $sheet->setCellValue("E{$row}", $r['total']);
    $sheet->setCellValue("F{$row}", $r['ganancia']);

    $totalCosto += $r['costo'];
    $totalTotal += $r['total'];
    $totalGanancia += $r['ganancia'];

    $row++;
}

// Fila de totales
$sheet->setCellValue("A{$row}", 'TOTALES');
$sheet->mergeCells("A{$row}:C{$row}");
$sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true);
$sheet->setCellValue("D{$row}", $totalCosto);
$sheet->setCellValue("E{$row}", $totalTotal);
$sheet->setCellValue("F{$row}", $totalGanancia);

// Formato contable
foreach (['D', 'E', 'F'] as $col) {
    $sheet->getStyle("{$col}2:{$col}{$row}")
          ->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00_);[Red]("$"* #,##0.00)');
}

// Centrar columna C ("Cantidad")
$sheet->getStyle("C2:C{$row}")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);


// Ajustar ancho automáticamente
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Descargar archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="detalle-ventas '.$month.'-'.$year.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;