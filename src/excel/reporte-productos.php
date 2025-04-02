<?php

session_start();

require '../../vendor/autoload.php';
require_once '../../config/db.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

/**
 * Stylos
 -----------------------------------------------------*/


$sheet->setTitle("Productos");

$sheet->setCellValue('A1', 'Nombre');
$sheet->setCellValue('B1', 'P/Compra');
$sheet->setCellValue('C1', 'P/Unitario');
$sheet->setCellValue('D1', 'Existencia');
$sheet->setCellValue('E1', 'Marca');
$sheet->setCellValue('F1', 'Categoría');
$sheet->setCellValue('G1', 'Ubicación');


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

$spreadsheet->getActiveSheet()->getStyle("B1:G1")->getAlignment()->applyFromArray(
            [
                'horizontal'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'textRotation' => 0,
                'wrapText'     => TRUE
            ]
);


$spreadsheet->getActiveSheet()->getStyle("B:G")->getAlignment()->applyFromArray(
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

$query = "SELECT *, p.producto_id as idproducto FROM productos p 
                INNER JOIN estados_generales e ON p.estado_id = e.estado_id
                INNER JOIN almacenes a on p.almacen_id = a.almacen_id
                LEFT JOIN productos_con_marcas pm ON p.producto_id = pm.producto_id
                LEFT JOIN marcas m ON pm.marca_id = m.marca_id 
                LEFT JOIN productos_con_categorias pc ON p.producto_id = pc.producto_id
                LEFT JOIN categorias c ON pc.categoria_id = c.categoria_id 
                LEFT JOIN productos_con_posiciones pps ON p.producto_id = pps.producto_id
                LEFT JOIN posiciones ps ON ps.posicion_id = pps.posicion_id
                ORDER BY nombre_producto ASC";

$datos = $db->query($query);


// Loop

$i = 2;

while($result = $datos->fetch_object()) {

    $sheet->setCellValue('A' .$i, $result->nombre_producto);
    $sheet->setCellValue('B' .$i, $result->precio_costo);
    $sheet->setCellValue('C' .$i, $result->precio_unitario);
    $sheet->setCellValue('D' .$i, $result->cantidad);
    $sheet->setCellValue('E' .$i, $result->nombre_marca);
    $sheet->setCellValue('F' .$i, $result->nombre_categoria);
    $sheet->setCellValue('G' .$i, $result->referencia);


    $i++;

}



/* Here there will be some code where you create $spreadsheet */
// redirect output to client browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-productos.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;

