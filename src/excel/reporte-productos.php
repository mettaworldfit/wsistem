<?php
session_start();
require '../../vendor/autoload.php';
require_once '../../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Productos");

// Estilos globales
$defaultFont = $spreadsheet->getDefaultStyle()->getFont();
$defaultFont->setName('Arial')->setSize(10);

// Estilo encabezado
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 11
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F81BD']
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        'wrapText'   => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];

// Estilo filas alternas (zebra)
$rowAltStyle = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'F2F2F2']
    ]
];

// Encabezados
$headers = ['Nombre', 'P/Compra', 'P/Unitario', 'Existencia', 'Marca', 'Categoría', 'Ubicación'];
$col = 'A';
foreach ($headers as $title) {
    $sheet->setCellValue($col . '1', $title);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

// Aplicar estilo al encabezado
$sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Consulta SQL
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

$row = 2;
while ($result = $datos->fetch_object()) {
    $sheet->setCellValue("A$row", $result->nombre_producto);
    $sheet->setCellValue("B$row", $result->precio_costo);
    $sheet->setCellValue("C$row", $result->precio_unitario);
    $sheet->setCellValue("D$row", $result->cantidad);
    $sheet->setCellValue("E$row", $result->nombre_marca);
    $sheet->setCellValue("F$row", $result->nombre_categoria);
    $sheet->setCellValue("G$row", $result->referencia);

    // Formato contable para precios
    $sheet->getStyle("B$row")->getNumberFormat()->setFormatCode('"$"#,##0.00;[Red]-"$"#,##0.00');
    $sheet->getStyle("C$row")->getNumberFormat()->setFormatCode('"$"#,##0.00;[Red]-"$"#,##0.00');

    // Cantidad como entero
    $sheet->getStyle("D$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

    // Zebra striping
    if ($row % 2 == 0) {
        $sheet->getStyle("A$row:G$row")->applyFromArray($rowAltStyle);
    }

    // Bordes
    $sheet->getStyle("A$row:G$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    $row++;
}

// Exportar Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte-productos.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
