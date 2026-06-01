<?php
/**
 * PREVIEW DE ETIQUETA TCPDF
 * Configuración 100% desde GET (CON GAP + OFFSET)
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
$gap           = (float)($_GET['gap_mm'] ?? 3); // GAP real impresora

// 👉 ALTURA TOTAL
$altoPagina = $altoEtiqueta + $gap;

// Offset lateral físico de impresora
$offsetX = 2; // mm

$pdf = new TCPDF(
    $_GET['orientacion'] ?? 'L',
    'mm',
    [$anchoEtiqueta, $altoPagina],
    true,
    'UTF-8',
    false
);

// =========================
// AJUSTES BASE (FORZADO)
// =========================
$pdf->SetMargins(0, 0, 0);
$pdf->SetCellPadding(0);
$pdf->setImageScale(1);
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
    'hpadding' => 0,
    'vpadding' => 0,
    'text'     => (bool)($_GET['mostrar_texto_barcode'] ?? 1),
    'font'     => 'helvetica',
    'fontsize' => (int)($_GET['barcode_font_size'] ?? 6)
];

$barcodeX      = (float)($_GET['barcode_x'] ?? 2);
$barcodeY      = (float)($_GET['barcode_y'] ?? 2);
$barcodeWidth  = (float)($_GET['barcode_width'] ?? 31);
$barcodeHeight = (float)($_GET['barcode_height'] ?? 9);

$pdf->write1DBarcode(
    $codigo,
    $_GET['tipo_barcode'] ?? 'C128',
    $offsetX + $barcodeX,
    $barcodeY,
    $barcodeWidth - ($offsetX * 2),
    $barcodeHeight,
    0.4,
    $barcodeStyle,
    'N'
);

// =========================
// DESCRIPCIÓN
// =========================
if ((int)($_GET['mostrar_descripcion'] ?? 1) === 1) {

    $pdf->SetFont('helvetica', '', (int)($_GET['descripcion_font_size'] ?? 6));

    $descX      = (float)($_GET['descripcion_x'] ?? 1);
    $descY      = (float)($_GET['descripcion_y'] ?? 15);
    $descWidth  = (float)($_GET['descripcion_width'] ?? 33);
    $descHeight = (float)($_GET['descripcion_height'] ?? 5);

    $pdf->SetXY(
        $offsetX + $descX,
        $descY
    );

    $pdf->MultiCell(
        $descWidth - ($offsetX * 2),
        $descHeight,
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

    $priceX      = (float)($_GET['precio_x'] ?? 1);
    $priceY      = (float)($_GET['precio_y'] ?? 21);
    $priceWidth  = (float)($_GET['precio_width'] ?? 33);
    $priceHeight = (float)($_GET['precio_height'] ?? 4);

    $pdf->SetXY(
        $offsetX + $priceX,
        $priceY
    );

    $pdf->Cell(
        $priceWidth - ($offsetX * 2),
        $priceHeight,
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
