<?php
session_start();
require '../../vendor/autoload.php';
require_once '../../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Inventario");

// Fuente global
$spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

// ============================
// 🟩 TÍTULO PRINCIPAL
// ============================
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A1', 'Inventario del sistema');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('000000');
$sheet->getStyle('A1')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

// ============================
// 🟦 ENCABEZADOS
// ============================
$headers = ['Cantidad', 'Descripción', 'Precio', 'Costo', 'Ubicación'];
$col = 'A';
foreach ($headers as $title) {
    $sheet->setCellValue($col . '2', $title);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

// Altura de encabezados
$sheet->getRowDimension('2')->setRowHeight(30); // 🔸 Altura de 30px

// Estilo encabezado
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'D9D9D9']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];
$sheet->getStyle('A2:E2')->applyFromArray($headerStyle);

// ============================
// 🔍 CONSULTA SQL
// ============================
$db = Database::connect();
$query = "
    SELECT 
        p.cantidad, 
        p.nombre_producto AS descripcion, 
        p.precio_unitario AS precio, 
        p.precio_costo AS costo, 
        ps.referencia AS ubicacion
    FROM productos p
    LEFT JOIN productos_con_posiciones pps ON p.producto_id = pps.producto_id
    LEFT JOIN posiciones ps ON ps.posicion_id = pps.posicion_id
    ORDER BY p.nombre_producto ASC
";
$datos = $db->query($query);

// ============================
// 🧾 RELLENAR DATOS
// ============================
$row = 3;
while ($result = $datos->fetch_object()) {
    $sheet->setCellValue("A$row", $result->cantidad);
    $sheet->setCellValue("B$row", $result->descripcion);
    $sheet->setCellValue("C$row", $result->precio);
    $sheet->setCellValue("D$row", $result->costo);
    $sheet->setCellValue("E$row", $result->ubicacion);

    // Formato numérico sin símbolos ($)
    $sheet->getStyle("C$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $sheet->getStyle("D$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

    // Bordes tipo tabla
    $sheet->getStyle("A$row:E$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $row++;
}

// ============================
// 📤 EXPORTAR
// ============================
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="inventario-sistema.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
