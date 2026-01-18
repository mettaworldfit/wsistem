<?php

session_start();
require '../../vendor/autoload.php';
require_once '../../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$fecha = $_GET['date'];
$sheet->setTitle("Ingresos y Gastos");

$db = Database::connect();
$config = Database::getConfig();
$user_id = $_SESSION['identity']->usuario_id;
$row = 1;

// ===== Título Ingresos =====
$sheet->setCellValue("A$row", "INGRESOS DEL DÍA");
$sheet->mergeCells("A$row:I$row");
$sheet->getStyle("A$row")->getFont()->setBold(true)->setSize(13);
$sheet->getStyle("A$row")->getAlignment()->setHorizontal('center');
$sheet->getStyle("A$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
$row++;

// ===== Encabezados Ingresos =====
$headers = ['Descripción', 'Cantidad', 'Precio Unidad', 'Descuento', 'Recibido', 'Factura','Método','Estado', 'Total Final'];
$colIndex = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($colIndex . $row, $header);
    $sheet->getStyle($colIndex . $row)->getFont()->setBold(true);
    $sheet->getStyle($colIndex . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D0E0E3');
    $colIndex++;
}
$row++;

// ===== Datos Ingresos =====
$queryIngresos = "SELECT * FROM (

       SELECT 
        '1' AS cantidad,
        concat('Abono recibido de',' | ',c.nombre, ' ', IFNULL(c.apellidos, '')) AS descripcion,
        '-' AS precio,
        (f.recibido - IFNULL(SUM(p.recibido), 0)) AS recibido,
        CONCAT('FT-00', f.factura_venta_id) AS factura,
	    concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
        e.nombre_estado AS estado,
        '-' AS descuento,
        f.fecha, mp.nombre_metodo as tipo_pago
    FROM facturas_ventas f 
    INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
    INNER JOIN estados_generales e ON e.estado_id = f.estado_id
    INNER JOIN clientes c ON c.cliente_id = f.cliente_id
    LEFT JOIN pagos_a_facturas_ventas pf ON pf.factura_venta_id = f.factura_venta_id
    LEFT JOIN pagos p ON pf.pago_id = p.pago_id
    WHERE f.fecha = '$fecha' AND e.nombre_estado = 'por cobrar'
    GROUP BY f.factura_venta_id, f.recibido, e.nombre_estado, f.fecha

    UNION ALL

    SELECT 
        '1' AS cantidad,
        concat('Abono recibido de',' | ',c.nombre, ' ', IFNULL(c.apellidos, '')) AS descripcion,
        '-' AS precio,
        (f.recibido - IFNULL(SUM(p.recibido), 0)) AS recibido,
        CONCAT('RP-00', f.facturaRP_id) AS factura,
        concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
        e.nombre_estado AS estado,
        '-' AS descuento,
        f.fecha, mp.nombre_metodo as tipo_pago
    FROM facturasRP f 
	INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
    INNER JOIN estados_generales e ON e.estado_id = f.estado_id
    INNER JOIN clientes c ON c.cliente_id = f.cliente_id
    LEFT JOIN pagos_a_facturasRP pf ON pf.facturaRP_id = f.facturaRP_id
    LEFT JOIN pagos p ON pf.pago_id = p.pago_id
    WHERE f.fecha = '$fecha' AND e.nombre_estado = 'por cobrar'
    GROUP BY f.facturaRP_id, f.recibido, e.nombre_estado, f.fecha

    UNION ALL

    SELECT d.cantidad, p.nombre_producto AS descripcion, d.precio AS precio,
           '-' AS recibido,
           CONCAT('FT-00', f.factura_venta_id), concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
           e.nombre_estado, d.descuento, f.fecha, mp.nombre_metodo as tipo_pago
    FROM detalle_facturas_ventas d
    INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
     INNER JOIN clientes c ON c.cliente_id = f.cliente_id
	INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
    INNER JOIN estados_generales e ON e.estado_id = f.estado_id
    INNER JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
    INNER JOIN productos p ON p.producto_id = dp.producto_id
    WHERE f.fecha = '$fecha' AND e.nombre_estado <> 'por cobrar'

    UNION ALL

    SELECT d.cantidad, s.nombre_servicio AS descripcion, d.precio AS precio,
           '-' AS recibido,
           CONCAT('FT-00', f.factura_venta_id), concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
           e.nombre_estado, d.descuento, f.fecha, mp.nombre_metodo as tipo_pago
    FROM detalle_facturas_ventas d
    INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
     INNER JOIN clientes c ON c.cliente_id = f.cliente_id
	INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
    INNER JOIN estados_generales e ON e.estado_id = f.estado_id
    INNER JOIN detalle_ventas_con_servicios ds ON ds.detalle_venta_id = d.detalle_venta_id
    INNER JOIN servicios s ON s.servicio_id = ds.servicio_id
    WHERE f.fecha = '$fecha' AND e.nombre_estado <> 'por cobrar'

    UNION ALL

    SELECT d.cantidad, pz.nombre_pieza AS descripcion, d.precio AS precio,
           '-' AS recibido,
           CONCAT('FT-00', f.factura_venta_id), concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
           e.nombre_estado, d.descuento, f.fecha, mp.nombre_metodo as tipo_pago
    FROM detalle_facturas_ventas d
    INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
     INNER JOIN clientes c ON c.cliente_id = f.cliente_id
	INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
    INNER JOIN estados_generales e ON e.estado_id = f.estado_id
    INNER JOIN detalle_ventas_con_piezas_ dp ON dp.detalle_venta_id = d.detalle_venta_id
    INNER JOIN piezas pz ON pz.pieza_id = dp.pieza_id
    WHERE f.fecha = '$fecha' AND e.nombre_estado <> 'por cobrar'

    UNION ALL

    SELECT d.cantidad, s.nombre_servicio AS descripcion, d.precio AS precio,
           '-' AS recibido,
           CONCAT('RP-00', f.facturaRP_id), concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
           e.nombre_estado, d.descuento, f.fecha, mp.nombre_metodo as tipo_pago
    FROM detalle_ordenRP d
    INNER JOIN facturasRP f ON f.orden_rp_id = d.orden_rp_id
     INNER JOIN clientes c ON c.cliente_id = f.cliente_id
	INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
    INNER JOIN estados_generales e ON e.estado_id = f.estado_id
    INNER JOIN detalle_ordenRP_con_servicios dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    INNER JOIN servicios s ON s.servicio_id = dp.servicio_id
    WHERE f.fecha = '$fecha' AND e.nombre_estado <> 'por cobrar'

    UNION ALL

    SELECT d.cantidad, d.descripcion, d.precio AS precio,
           '-' AS recibido,
           CONCAT('RP-00', f.facturaRP_id), concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
           e.nombre_estado, d.descuento, f.fecha, mp.nombre_metodo as tipo_pago
    FROM detalle_ordenRP d
    INNER JOIN facturasRP f ON f.orden_rp_id = d.orden_rp_id
	INNER JOIN clientes c ON c.cliente_id = f.cliente_id
	INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
    INNER JOIN estados_generales e ON e.estado_id = f.estado_id
    INNER JOIN detalle_ordenRP_con_piezas dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    INNER JOIN piezas pz ON pz.pieza_id = dp.pieza_id
    WHERE f.fecha = '$fecha' AND e.nombre_estado <> 'por cobrar'

    UNION ALL

    SELECT 1 AS cantidad, CONCAT('Pago de factura ','FT-00',f.factura_venta_id) AS descripcion,
       '-' AS precio,
       p.recibido AS recibido,
       CONCAT('P-00', p.pago_id) AS factura, concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
       CASE 
           WHEN e.nombre_estado = 'por cobrar' THEN e.nombre_estado
           ELSE 'pagada'
       END AS estado,
       0 AS descuento, p.fecha, mp.nombre_metodo as tipo_pago
FROM pagos p
INNER JOIN clientes c ON c.cliente_id = p.cliente_id
INNER JOIN pagos_a_facturas_ventas pf ON pf.pago_id = p.pago_id
INNER JOIN facturas_ventas f ON f.factura_venta_id = pf.factura_venta_id
INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
INNER JOIN estados_generales e ON e.estado_id = f.estado_id
WHERE p.fecha = '$fecha'
  AND (
      e.nombre_estado = 'por cobrar'
      OR (e.nombre_estado <> 'por cobrar' AND f.fecha <> p.fecha)
  )

UNION ALL

SELECT 1 AS cantidad, CONCAT('Pago de factura ','RP-00',f.facturaRP_id) AS descripcion,
       '-' AS precio,
       p.recibido AS recibido,
       CONCAT('P-00', p.pago_id) AS factura, concat(c.nombre,' ', IFNULL(c.apellidos, '')) AS cliente_proveedor,
       CASE 
           WHEN e.nombre_estado = 'por cobrar' THEN e.nombre_estado
           ELSE 'pagada'
       END AS estado,
       0 AS descuento, p.fecha, mp.nombre_metodo as tipo_pago
FROM pagos p
INNER JOIN clientes c ON c.cliente_id = p.cliente_id
INNER JOIN pagos_a_facturasRP pf ON pf.pago_id = p.pago_id
INNER JOIN facturasRP f ON f.facturaRP_id = pf.facturaRP_id
INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = f.metodo_pago_id
INNER JOIN estados_generales e ON e.estado_id = f.estado_id
WHERE p.fecha = '$fecha'
  AND (
      e.nombre_estado = 'por cobrar'
      OR (e.nombre_estado <> 'por cobrar' AND f.fecha <> p.fecha)
  )
) ingresos ORDER BY estado ASC;";

$res = $db->query($queryIngresos);
$startIngresosRow = $row;

while ($r = $res->fetch_object()) {
    $cantidad = is_numeric($r->cantidad) ? $r->cantidad : 0;
    $precio = is_numeric($r->precio) ? $r->precio : 0;
    $descuento = is_numeric($r->descuento) ? $r->descuento : 0;
    $recibido = is_numeric($r->recibido) ? $r->recibido : 0;
    $total = ($cantidad * $precio - $descuento) + $recibido;;

    $sheet->fromArray([
        $r->descripcion,
        $cantidad,
        $precio,
        $descuento,
        $recibido,
        $r->factura,
        $r->tipo_pago,
        $r->estado,
        $total
    ], null, "A$row");
    $row++;
}
$endIngresosRow = $row - 1;

// ===== Total Ingresos =====
$sheet->setCellValue("H$row", "Total ingresos:");
$sheet->setCellValue("I$row", "=SUM(I$startIngresosRow:I$endIngresosRow)");
$sheet->getStyle("H$row:I$row")->getFont()->setBold(true);
$sheet->getStyle("I$row")->getNumberFormat()->setFormatCode('"$"* #,##0.00_);[Red]("$"* #,##0.00)');
$row++;

// ===== Título Gastos =====
$sheet->setCellValue("A$row", "GASTOS DEL DÍA");
$sheet->mergeCells("A$row:I$row");
$sheet->getStyle("A$row")->getFont()->setBold(true)->setSize(13);
$sheet->getStyle("A$row")->getAlignment()->setHorizontal('center');
$sheet->getStyle("A$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F4CCCC');
$row++;

// ===== Encabezados Gastos ===== 
$gastoHeaders = ['Descripción', 'Cantidad', 'Precio Unidad', 'Descuento','Proveedor','Factura','Método', 'Estado', 'Total Final'];
$colIndex = 'A';
foreach ($gastoHeaders as $header) {
    $sheet->setCellValue($colIndex . $row, $header);
    $sheet->getStyle($colIndex . $row)->getFont()->setBold(true);
    $sheet->getStyle($colIndex . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FCE5CD');
    $colIndex++;
}
$row++;

// ===== Datos Gastos =====
$queryGastos = "SELECT factura,descripcion,proveedor,cantidad,precio,descuento,fecha,tipo_pago FROM (
    SELECT 
        CONCAT('G-00', g.orden_id) AS factura,
        m.descripcion AS descripcion,
        concat(p.nombre_proveedor,' ', IFNULL(p.apellidos, '')) as proveedor,
        dg.cantidad,
        dg.precio,
        '0' AS descuento,
        g.fecha, '-' as tipo_pago
    FROM gastos g
    INNER JOIN ordenes_gastos og ON g.orden_id = og.orden_id
    INNER JOIN detalle_gasto dg ON og.orden_id = dg.orden_id
    INNER JOIN motivos m ON dg.motivo_id = m.motivo_id
    INNER JOIN proveedores p ON p.proveedor_id = g.proveedor_id
    WHERE g.fecha = '$fecha'

    UNION ALL

    SELECT 
        CONCAT('OC-00', oc.orden_id),
        COALESCE(prod.nombre_producto, piez.nombre_pieza),
       concat(prov.nombre_proveedor,' ', IFNULL(prov.apellidos, '')),
        dc.cantidad,
        dc.precio,
        dc.descuentos,
        oc.fecha, mp.nombre_metodo as tipo_pago
    FROM detalle_compra dc
    INNER JOIN facturas_proveedores fp ON fp.orden_id = dc.orden_id
    INNER JOIN metodos_de_pagos mp ON mp.metodo_pago_id = fp.metodo_pago_id
    INNER JOIN ordenes_compras oc ON dc.orden_id = oc.orden_id
    INNER JOIN proveedores prov ON oc.proveedor_id = prov.proveedor_id
    LEFT JOIN detalle_compra_con_productos dcp ON dc.detalle_compra_id = dcp.detalle_compra_id
    LEFT JOIN productos prod ON dcp.producto_id = prod.producto_id
    LEFT JOIN detalle_compra_con_piezas dcz ON dc.detalle_compra_id = dcz.detalle_compra_id
    LEFT JOIN piezas piez ON dcz.pieza_id = piez.pieza_id
    WHERE oc.fecha = '$fecha' AND oc.estado_id = '12'
) AS gastos;";

$res = $db->query($queryGastos);
$startGastosRow = $row;

while ($r = $res->fetch_row()) {
    list($factura, $desc, $prov, $cant, $precio, $descuento, $fecha_gasto,$tipo_pago) = $r;
    $total = ($cant * $precio) - $descuento;
    $sheet->fromArray([$desc, $cant, $precio, $descuento,$prov,$factura,$tipo_pago, '-', $total], null, "A$row");
    $row++;
}
$endGastosRow = $row - 1;

// ===== Total Gastos =====
$sheet->setCellValue("H$row", "Total gastos:");
$sheet->setCellValue("I$row", "=SUM(I$startGastosRow:I$endGastosRow)");
$sheet->getStyle("H$row:I$row")->getFont()->setBold(true);
$sheet->getStyle("I$row")->getNumberFormat()->setFormatCode('"$"* #,##0.00_);[Red]("$"* #,##0.00)');

// ===== Ajuste Columnas y Formatos =====
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Alineación centrada para ambas secciones
$sheet->getStyle("A$startIngresosRow:I$endIngresosRow")->getAlignment()->setHorizontal('center');
$sheet->getStyle("A$startIngresosRow:I$endIngresosRow")->getAlignment()->setVertical('center');
$sheet->getStyle("A$startGastosRow:I$endGastosRow")->getAlignment()->setHorizontal('center');
$sheet->getStyle("A$startGastosRow:I$endGastosRow")->getAlignment()->setVertical('center');

// Formato moneda
$currencyFormat = '"$"* #,##0.00_);[Red]("$"* #,##0.00)';
$sheet->getStyle("C$startIngresosRow:C$endIngresosRow")->getNumberFormat()->setFormatCode($currencyFormat); // Precio
$sheet->getStyle("D$startIngresosRow:D$endIngresosRow")->getNumberFormat()->setFormatCode($currencyFormat); // Descuento
$sheet->getStyle("E$startIngresosRow:E$endIngresosRow")->getNumberFormat()->setFormatCode($currencyFormat); // ITBIS
$sheet->getStyle("I$startIngresosRow:I$endIngresosRow")->getNumberFormat()->setFormatCode($currencyFormat); // Total

$sheet->getStyle("C$startGastosRow:C$endGastosRow")->getNumberFormat()->setFormatCode($currencyFormat); // Precio
$sheet->getStyle("D$startGastosRow:D$endGastosRow")->getNumberFormat()->setFormatCode($currencyFormat); // Descuento
$sheet->getStyle("I$startGastosRow:I$endGastosRow")->getNumberFormat()->setFormatCode($currencyFormat); // Total

// ===== Salida Excel =====
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte-' . $fecha . '.xlsx"');
header('Cache-Control: max-age=0');
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
