<?php


/**
 * IMPRESIÓN DINÁMICA DE ETIQUETAS
 * Usa configuración desde la tabla `etiquetas`
 * TCPDF standalone (sin Composer)
 */

session_start();

if (!isset($_SESSION['identity'])) {
    header('Location: ../');
    exit;
}

// =========================
// INCLUDES
// =========================
require_once '../../config/parameters.php';
require_once '../../config/db.php';
require_once '../../vendor/tecnickcom/tcpdf/tcpdf.php';

// =========================
// DATABASE Y CONFIG
// =========================
$db = Database::connect();
$config = Database::getConfig();

// =========================
// DATOS
// =========================

$codigo      = $_GET['code'];
$precio = isset($_GET['price']) && is_numeric($_GET['price']) ? '$' . strval($_GET['price']) : '$0';
$descripcion = $_GET['name'];

// =========================
// CONFIG DE ETIQUETA
// =========================

$etiqueta_id = $config['etiqueta_id'];

$stmt = $db->prepare("
    SELECT * FROM etiquetas
    WHERE activo = ?
    LIMIT 1
");
$stmt->bind_param("i", $etiqueta_id);
$stmt->execute();

$element = $stmt->get_result()->fetch_assoc();

if (!$element) {
   echo die('Configuración de etiqueta no encontrada'); 
}

// =========================
// CREAR PDF
// =========================
$ancho = (float)$element['ancho_mm'];
$alto  = (float)$element['alto_mm'];

$pdf = new TCPDF(
    $element['orientacion'],   // P | L
    'mm',
    [$ancho, $alto],
    true,
    'UTF-8',
    false
);

// Configuración base
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// =========================
// CÓDIGO DE BARRAS
// =========================
$barcodeStyle = [
    'position'  => 'C',
    'align'     => 'C',
    'stretch'   => false,
    'fitwidth'  => true,
    'border'    => false,
    'hpadding'  => 0,
    'vpadding'  => 0,
    'fgcolor'   => [0, 0, 0],
    'bgcolor'   => false,
    'text'      => (bool)$element['mostrar_texto_barcode'],
    'font'      => 'helvetica',
    'fontsize'  => (int)$element['barcode_font_size']
];

$pdf->write1DBarcode(
    $codigo,
    $element['tipo_barcode'],
    $element['barcode_x'],
    $element['barcode_y'],
    $element['barcode_width'],
    $element['barcode_height'],
    0.4,
    $barcodeStyle,
    'N'
);

// =========================
// DESCRIPCIÓN
// =========================
if ($element['mostrar_descripcion']) {

    $pdf->SetFont(
        'helvetica',
        '',
        (int)$element['descripcion_font_size']
    );

    $pdf->SetXY(
        $element['descripcion_x'],
        $element['descripcion_y']
    );

    $pdf->MultiCell(
        $element['descripcion_width'],
        $element['descripcion_height'],
        $descripcion,
        0,
        'C'
    );
}

// =========================
// PRECIO
// =========================
if ($element['mostrar_precio']) {

    $pdf->SetFont(
        'helvetica',
        'B',
        (int)$element['precio_font_size']
    );

    $pdf->SetXY(
        $element['precio_x'],
        $element['precio_y']
    );

    $pdf->Cell(
        $element['precio_width'],
        $element['precio_height'],
        $precio,
        0,
        1,
        'C'
    );
}

// =========================
// SALIDA
// =========================


$pdf->Output(
    "etiqueta_{$ancho}x{$alto}mm.pdf",
    'I'
);
