<?php

session_start();

require '../../vendor/autoload.php';
require_once '../../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$fecha = $_GET['date'];

/**
 * Stylos
 -----------------------------------------------------*/


$sheet->setTitle("Productos");

$sheet->setCellValue('A1', 'Cantidad');
$sheet->setCellValue('B1', 'Descripción');
$sheet->setCellValue('C1', 'Total');
$sheet->setCellValue('D1', 'N° Factura');
$sheet->setCellValue('E1', 'Estado');
$sheet->setCellValue('F1', 'Inventario');
$sheet->setCellValue('G1', 'Fecha');


// Dimensiones

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);


// Encabezadosde las columnas

$sheet->getStyle('A1:G1')->getFont()->applyFromArray(
    [
        'name' => 'Arial',
        'bold' => TRUE,
        'italic' => FALSE,
        'strikethrough' => FALSE,
        'color' => [
            'rgb' => '000000'
        ]
    ]
);

// Fuente por defecto

$spreadsheet->getDefaultStyle()
    ->getFont()
    ->setName('Arial')
    ->setSize(10);

// Alineacion 

$spreadsheet->getActiveSheet()->getStyle("C1:G1")->getAlignment()->applyFromArray(
    [
        'horizontal'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        'textRotation' => 0,
        'wrapText'     => TRUE
    ]
);


$spreadsheet->getActiveSheet()->getStyle("C:G")->getAlignment()->applyFromArray(
    [
        'horizontal'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        'textRotation' => 0,
        'wrapText'     => TRUE
    ]
);

$spreadsheet->getActiveSheet()->getStyle("A")->getAlignment()->applyFromArray(
    [
        'horizontal'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        'textRotation' => 0,
        'wrapText'     => TRUE
    ]
);

/**
 * Datos a mostrar
 ----------------------------------------------------------------------------------------------*/



// Consulta sql
$db = Database::connect();

$query = "SELECT * FROM (

    SELECT d.cantidad ,p.nombre_producto as descripcion, d.precio as total,concat('FT-',f.factura_venta_id) as factura ,e.nombre_estado as estado,d.descuento , p.cantidad as stock, f.fecha FROM detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join estados_generales e on e.estado_id = f.estado_id
    inner join detalle_ventas_con_productos dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join productos p on p.producto_id = dp.producto_id where f.fecha = '$fecha'
     
     UNION ALL
          
    SELECT d.cantidad ,s.nombre_servicio as descripcion, d.precio as total,concat('FT-',f.factura_venta_id) as factura, e.nombre_estado as estado, d.descuento, '' as stock, f.fecha FROM detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join estados_generales e on e.estado_id = f.estado_id
    inner join detalle_ventas_con_servicios ds on ds.detalle_venta_id = d.detalle_venta_id
    inner join servicios s on s.servicio_id = ds.servicio_id where f.fecha = '$fecha'
    
    UNION ALL
    
    SELECT d.cantidad,pz.nombre_pieza as descripcion, d.precio as total, concat('FT-',f.factura_venta_id) as factura, e.nombre_estado as estado, d.descuento, pz.cantidad as stock, f.fecha FROM detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join estados_generales e on e.estado_id = f.estado_id
    inner join detalle_ventas_con_piezas_ dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join piezas pz on pz.pieza_id = dp.pieza_id where f.fecha = '$fecha'
    
    UNION ALL
    
    SELECT d.cantidad ,d.descripcion as descripcion, d.precio as total, concat('RP-',f.facturaRP_id) as factura, e.nombre_estado as estado, d.descuento, pz.cantidad as stock, f.fecha FROM detalle_ordenRP d 
    inner join facturasRP f on f.orden_rp_id = d.orden_rp_id
    inner join estados_generales e on e.estado_id = f.estado_id
    inner join detalle_ordenRP_con_piezas dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join piezas pz on pz.pieza_id = dp.pieza_id where f.fecha = '$fecha'
    
    
    UNION ALL
    
    SELECT d.cantidad ,s.nombre_servicio as descripcion, d.precio as total, concat('RP-',f.facturaRP_id) as factura, e.nombre_estado as estado, d.descuento, '' as stock, f.fecha FROM detalle_ordenRP d 
    inner join facturasRP f on f.orden_rp_id = d.orden_rp_id
    inner join estados_generales e on e.estado_id = f.estado_id
    inner join detalle_ordenRP_con_servicios dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join servicios s on s.servicio_id = dp.servicio_id where f.fecha = '$fecha'
    
    UNION ALL
    
    SELECT '1' as cantidad ,'Pago de factura' as descripcion, p.recibido as total, concat('P-',p.pago_id) as factura, '-' as estado, '0' as discuento, '' as stock, p.fecha FROM pagos p
    where p.fecha = '$fecha'
    
    
    ) detalle_ventas_de_hoy order by total desc;

    ";

$datos = $db->query($query);

// Loop

$i = 2;

while ($result = $datos->fetch_object()) {

    $total = ($result->cantidad * $result->total) - $result->descuento;
    if ($total > 0) {

        $sheet->setCellValue('A' . $i, $result->cantidad);
        $sheet->setCellValue('B' . $i, $result->descripcion);
        $sheet->setCellValue('C' . $i,$total);
        $sheet->setCellValue('D' . $i,$result->factura);
        $sheet->setCellValue('E' . $i,$result->estado);
        $sheet->setCellValue('F' . $i, $result->stock);
        $sheet->setCellValue('G' . $i, $result->fecha);


        $i++;
    }
}

// Después del último dato, agregamos una fila con la suma total
$sheet->setCellValue('B' . $i, 'Total general:');
$sheet->setCellValue('C' . $i, '=SUM(C2:C' . ($i - 1) . ')');

// Opcional: Formatear la celda del total en negrita
$sheet->getStyle('B' . $i . ':C' . $i)->getFont()->setBold(true);

// Aplicar formato $0,000 a la columna C
$sheet->getStyle('C2:C' . $i)->getNumberFormat()->setFormatCode('"$"#,##0');

/* Here there will be some code where you create $spreadsheet */
// redirect output to client browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Venta-'.$fecha.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
