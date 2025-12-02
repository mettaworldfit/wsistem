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
      WHERE MONTH(fecha) = '$month' AND YEAR(fecha) = '$year'
      GROUP BY factura_venta_id
    ) ft ON ft.factura_venta_id = f.factura_venta_id
    INNER JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
    INNER JOIN productos p ON p.producto_id = dp.producto_id
    WHERE MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year'
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
      WHERE MONTH(fecha) = '$month' AND YEAR(fecha) = '$year'
      GROUP BY factura_venta_id
    ) ft ON ft.factura_venta_id = f.factura_venta_id
    INNER JOIN detalle_ventas_con_piezas_ dp ON dp.detalle_venta_id = d.detalle_venta_id
    INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
    WHERE MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year'
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
      WHERE MONTH(fecha) = '$month' AND YEAR(fecha) = '$year'
      GROUP BY orden_rp_id
    ) ft ON ft.orden_rp_id = frp.orden_rp_id
    INNER JOIN detalle_ordenRP_con_piezas dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
    WHERE MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year'
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
      WHERE MONTH(fecha) = '$month' AND YEAR(fecha) = '$year'
      GROUP BY factura_venta_id
    ) ft ON ft.factura_venta_id = f.factura_venta_id
    INNER JOIN detalle_ventas_con_servicios ds ON ds.detalle_venta_id = d.detalle_venta_id
    INNER JOIN servicios s ON s.servicio_id = ds.servicio_id
    WHERE MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year'
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
      WHERE MONTH(fecha) = '$month' AND YEAR(fecha) = '$year'
      GROUP BY orden_rp_id
    ) ft ON ft.orden_rp_id = frp.orden_rp_id
    INNER JOIN detalle_ordenRP_con_servicios dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    INNER JOIN servicios s ON s.servicio_id = dp.servicio_id
    WHERE MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year'
    GROUP BY s.nombre_servicio
) AS detalle_ventas_mes GROUP BY nombre, tipo
 ORDER BY tipo DESC;";

$result = $db->query($query);


// Encabezados
$headers = ['Nombre', 'Tipo', 'Cantidad total', 'Costo total', 'Ganancias', 'Total vendido'];
$sheet->fromArray($headers, NULL, 'A1');

// Bordes exteriores gruesos en la fila de totales
$sheet->getStyle("A1:F1")
  ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A1:F1")
  ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A1:F1")
  ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A1:F1")
  ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);

// Establecer color negro para los bordes
$sheet->getStyle("A1:F1")
  ->getBorders()->getAllBorders()->getColor()->setRGB('000000');

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
  $sheet->setCellValue("E{$row}", $r['ganancia']);
  $sheet->setCellValue("F{$row}", $r['total']);

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
$sheet->setCellValue("E{$row}",$totalGanancia);
$sheet->setCellValue("F{$row}", $totalTotal);

// Bordes exteriores gruesos en la fila
$sheet->getStyle("A{$row}:F{$row}")
  ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:F{$row}")
  ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:F{$row}")
  ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:F{$row}")
  ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);

// Establecer color negro para los bordes
$sheet->getStyle("A{$row}:F{$row}")
  ->getBorders()->getAllBorders()->getColor()->setRGB('000000');

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


/**====================================================
 * GASTOS
 =====================================================*/ 


// Agregar un espacio de 4 filas
$row += 4;

// Consulta para los gastos
$queryGastos = "SELECT 
    m.descripcion, 
    d.observacion, 
    d.cantidad, 
    d.precio,
    (d.cantidad * d.precio) + d.impuestos AS total,
    d.fecha
FROM detalle_gasto d
INNER JOIN ordenes_gastos o ON o.orden_id = d.orden_id
INNER JOIN gastos g ON g.orden_id = o.orden_id
INNER JOIN motivos m ON m.motivo_id = d.motivo_id
WHERE MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year'";

$resultGastos = $db->query($queryGastos);

// Encabezado para los gastos
$sheet->setCellValue("A{$row}", "GASTOS");
$sheet->mergeCells("A{$row}:F{$row}");
$sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true);
$row++;

// Establecer encabezado para los gastos
$headersGastos = ['Descripción', 'Observación', 'Cantidad', 'Precio Unitario', 'Total gastado'];
$sheet->fromArray($headersGastos, NULL, "A{$row}");

// Aplicar negrita
$sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);

// Bordes exteriores gruesos en la fila
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);

// Establecer color negro para los bordes
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getAllBorders()->getColor()->setRGB('000000');

$row++;

// Variables para los totales
$totalPrecio = 0;
$totalTotal = 0;


while ($r = $resultGastos->fetch_assoc()) {

  $sheet->setCellValue("A{$row}", $r['descripcion']);
  $sheet->setCellValue("B{$row}", $r['observacion']);
  $sheet->setCellValue("C{$row}", $r['cantidad']);
  $sheet->setCellValue("D{$row}", $r['precio']);
  $sheet->setCellValue("E{$row}", $r['total']);

  // Acumular los totales
  $totalPrecio += $r['precio'];
  $totalTotal += $r['total'];

  $row++;
}

// Formato contable para los totales
$sheet->getStyle("D{$row}:E{$row}")
  ->getNumberFormat()
  ->setFormatCode('"$"* #,##0.00_);[Red]("$"* #,##0.00)');

// Centrar columna C ("Cantidad")
$sheet->getStyle("C2:C{$row}")
  ->getAlignment()
  ->setHorizontal(Alignment::HORIZONTAL_CENTER);


// Agregar fila de totales
$sheet->setCellValue("A{$row}", "TOTALES");
$sheet->mergeCells("A{$row}:B{$row}");
$sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true);

// Mostrar totales
$sheet->setCellValue("D{$row}", $totalPrecio);
$sheet->setCellValue("E{$row}", $totalTotal);

// Formato contable para los totales
$sheet->getStyle("D{$row}:E{$row}")
  ->getNumberFormat()
  ->setFormatCode('"$"* #,##0.00_);[Red]("$"* #,##0.00)');

// Bordes exteriores gruesos en la fila de totales
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);

// Establecer color negro para los bordes
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getAllBorders()->getColor()->setRGB('000000');

// Establecer color negro para los bordes exteriores de la fila de totales
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getTop()->getColor()->setRGB('000000');
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getBottom()->getColor()->setRGB('000000');
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getLeft()->getColor()->setRGB('000000');
$sheet->getStyle("A{$row}:E{$row}")
  ->getBorders()->getRight()->getColor()->setRGB('000000');

// Ajustar el ancho de las columnas para los gastos
foreach (range('A', 'E') as $col) {
  $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Descargar archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="detalle-ventas-y-gastos ' . $month . '-' . $year . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
