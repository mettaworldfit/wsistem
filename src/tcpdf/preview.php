<?php
/**
 * PREVIEW DE ETIQUETA TCPDF
 * Configuración 100% desde GET (CON GAP)
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
$codigo      = $_GET['codigo'] ?? '123456789012';
$descripcion = $_GET['descripcion_producto'] ?? 'PRODUCTO DE PRUEBA';
$precio      = $_GET['precio'] ?? '$150.00';

// =========================
// CONFIG ETIQUETA
// =========================
$anchoEtiqueta = (float)($_GET['ancho_mm'] ?? 35);
$altoEtiqueta  = (float)($_GET['alto_mm'] ?? 25);
$gap           = (float)($_GET['gap_mm'] ?? 3); // 👈 GAP REAL DE LA IMPRESORA

// 👉 ALTURA TOTAL = ETIQUETA + GAP
$altoPagina = $altoEtiqueta + $gap;

$pdf = new TCPDF(
    $_GET['orientacion'] ?? 'L',
    'mm',
    [$anchoEtiqueta, $altoPagina],
    true,
    'UTF-8',
    false
);

// =========================
// AJUSTES BASE
// =========================
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
    'fontsize' => (int)($_GET['barcode_font_size'] ?? 6)
];

$pdf->write1DBarcode(
    $codigo,
    $_GET['tipo_barcode'] ?? 'C128',
    (float)($_GET['barcode_x'] ?? 2),
    (float)($_GET['barcode_y'] ?? 2),
    (float)($_GET['barcode_width'] ?? 31),
    (float)($_GET['barcode_height'] ?? 9),
    0.4,
    $barcodeStyle,
    'N'
);

// =========================
// DESCRIPCIÓN
// =========================
if ((int)($_GET['mostrar_descripcion'] ?? 1) === 1) {

    $pdf->SetFont('helvetica', '', (int)($_GET['descripcion_font_size'] ?? 6));
    $pdf->SetXY(
        (float)($_GET['descripcion_x'] ?? 1),
        (float)($_GET['descripcion_y'] ?? 15)
    );

    $pdf->MultiCell(
        (float)($_GET['descripcion_width'] ?? 33),
        (float)($_GET['descripcion_height'] ?? 5),
        $descripcion,
        0,
        'C'
    );
}

// =========================
// PRECIO
// =========================
if ((int)($_GET['mostrar_precio'] ?? 1) === 1) {

    $pdf->SetFont('helvetica', 'B', (int)($_GET['precio_font_size'] ?? 7));
    $pdf->SetXY(
        (float)($_GET['precio_x'] ?? 1),
        (float)($_GET['precio_y'] ?? 21)
    );

    $pdf->Cell(
        (float)($_GET['precio_width'] ?? 33),
        (float)($_GET['precio_height'] ?? 4),
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
