<?php
/**
 * PREVIEW DE ETIQUETA TCPDF
 * Configuración 100% desde GET
 */

session_start();

if (!isset($_SESSION['identity'])) {
    http_response_code(403);
    exit;
}

require_once '../../vendor/tecnickcom/tcpdf/tcpdf.php';

// =========================
// DATOS DEL PRODUCTO
// =========================
$codigo      = $_GET['codigo'] ?? '';
$descripcion = $_GET['descripcion_producto'] ?? '';
$precio      = $_GET['precio'] ?? '';

// =========================
// CONFIG ETIQUETA
// =========================
$ancho = (float)($_GET['ancho_mm'] ?? 35);
$alto  = (float)($_GET['alto_mm'] ?? 25);

$pdf = new TCPDF(
    $_GET['orientacion'] ?? 'L',
    'mm',
    [$ancho, $alto],
    true,
    'UTF-8',
    false
);

$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// =========================
// CÓDIGO DE BARRAS
// =========================
$barcodeStyle = [
    'position' => 'C',
    'align'    => 'C',
    'stretch'  => false,
    'fitwidth' => true,
    'border'   => false,
    'text'     => (bool)($_GET['mostrar_texto_barcode'] ?? 1),
    'font'     => 'helvetica',
    'fontsize' => (int)($_GET['barcode_font_size'] ?? 5)
];

$pdf->write1DBarcode(
    $codigo,
    $_GET['tipo_barcode'] ?? 'C128',
    (float)$_GET['barcode_x'],
    (float)$_GET['barcode_y'],
    (float)$_GET['barcode_width'],
    (float)$_GET['barcode_height'],
    0.4,
    $barcodeStyle,
    'N'
);

// =========================
// DESCRIPCIÓN
// =========================
if ((int)($_GET['mostrar_descripcion'] ?? 1) === 1) {

    $pdf->SetFont('helvetica', '', (int)$_GET['descripcion_font_size']);
    $pdf->SetXY(
        (float)$_GET['descripcion_x'],
        (float)$_GET['descripcion_y']
    );

    $pdf->MultiCell(
        (float)$_GET['descripcion_width'],
        (float)$_GET['descripcion_height'],
        $descripcion,
        0,
        'C'
    );
}

// =========================
// PRECIO
// =========================
if ((int)($_GET['mostrar_precio'] ?? 1) === 1) {

    $pdf->SetFont('helvetica', 'B', (int)$_GET['precio_font_size']);
    $pdf->SetXY(
        (float)$_GET['precio_x'],
        (float)$_GET['precio_y']
    );

    $pdf->Cell(
        (float)$_GET['precio_width'],
        (float)$_GET['precio_height'],
        $precio,
        0,
        1,
        'C'
    );
}

// =========================
// OUTPUT
// =========================
$pdf->Output('preview_etiqueta.pdf', 'I');
