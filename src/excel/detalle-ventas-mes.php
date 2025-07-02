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

$query = "SELECT nombre, tipo ,sum(cantidad) as cantidad, sum(costo) as costo,
sum(total) as total, sum(ganancia) as ganancia FROM (

    SELECT p.nombre_producto as nombre,'Producto' as tipo ,sum(d.cantidad) as cantidad,
    sum(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) as costo, 
    sum(d.precio * d.cantidad - d.descuento) as total,
    sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia  
    from detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_productos dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join productos p on p.producto_id = dp.producto_id
    where MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year' AND f.estado_id != 4 GROUP BY p.nombre_producto
    
    UNION ALL
    
    SELECT p.nombre_pieza as nombre,'Pieza' as tipo,sum(d.cantidad) as cantidad,
    sum(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) as costo, 
    sum(d.precio * d.cantidad - d.descuento) as total,
    sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia 
    from detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_piezas_ dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year' AND f.estado_id != 4 GROUP BY p.nombre_pieza

    UNION ALL

    SELECT p.nombre_pieza as nombre,'Pieza' as tipo,sum(d.cantidad) as cantidad,
    sum(d.precio * d.cantidad - d.descuento) as total,
	sum(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) as costo, 
    sum((d.precio * d.cantidad - d.descuento)-(p.precio_costo * d.cantidad)) as ganancia  
    from detalle_ordenRP d 
    inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
    inner join detalle_ordenRP_con_piezas dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year' AND frp.estado_id != 4 GROUP BY p.nombre_pieza

    UNION ALL

    SELECT s.nombre_servicio as nombre, 'Servicio' as tipo ,sum(d.cantidad) as cantidad, 
    sum(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad,0)) as costo,
    -- Total facturado (precio - descuento)
    sum(d.precio * d.cantidad - d.descuento) as total,
    -- Ganancia = total - costo
	sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
    from detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_servicios ds on ds.detalle_venta_id = d.detalle_venta_id
    inner join servicios s on s.servicio_id = ds.servicio_id 
    where MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year' AND f.estado_id != 4 GROUP BY s.nombre_servicio

    UNION ALL
    
    SELECT s.nombre_servicio as nombre,'Servicio' as tipo, sum(d.cantidad) as cantidad, 
    sum(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad,0)) as costo,
    -- Total facturado (precio - descuento)
    sum(d.precio * d.cantidad - d.descuento) as total,
    -- Ganancia = total - costo
	sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
    from detalle_ordenRP d 
    inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
    inner join detalle_ordenRP_con_servicios dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join servicios s on s.servicio_id = dp.servicio_id
    where MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year' AND frp.estado_id != 4 GROUP BY s.nombre_servicio
    
    ) detalle_ventas_mes 
    GROUP BY nombre, tipo ORDER BY tipo DESC";

$result = $db->query($query);

// Encabezados
$headers = ['Nombre', 'Tipo', 'Cantidad', 'Costo', 'Total', 'Ganancia'];
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