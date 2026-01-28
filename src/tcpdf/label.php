<?php
/**
 * IMPRESIÓN DINÁMICA DE ETIQUETAS (CON GAP)
 * Usa configuración desde la tabla `etiquetas`
 * TCPDF standalone
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
$codigo      = $_GET['code'] ?? '123456789012';
$precio      = isset($_GET['price']) && is_numeric($_GET['price']) ? '$' . $_GET['price'] : '$0';
$descripcion = $_GET['name'] ?? 'PRODUCTO DE PRUEBA';

// =========================
// CONFIG DE ETIQUETA
// =========================
$etiqueta_id = $config['etiqueta_id'];

$stmt = $db->prepare("
    SELECT *
    FROM etiquetas
    WHERE etiqueta_id = ?
      AND activo = 1
    LIMIT 1
");
$stmt->bind_param("i", $etiqueta_id);
$stmt->execute();

$element = $stmt->get_result()->fetch_assoc();

if (!$element) {
    die('Configuración de etiqueta no encontrada');
}

// =========================
// MEDIDAS (ETIQUETA + GAP)
// =========================
$anchoEtiqueta = (float)$element['ancho_mm'];
$altoEtiqueta  = (float)$element['alto_mm'];

// GAP desde BD (recomendado)
// Si no tienes la columna aún, usa: $gap = 3;
$gap = isset($element['gap_mm']) ? (float)$element['gap_mm'] : 3;

// 👉 ALTURA TOTAL DE LA PÁGINA
$altoPagina = $altoEtiqueta + $gap;

// =========================
// CREAR PDF
// =========================
$pdf = new TCPDF(
    $element['orientacion'],   // P | L
    'mm',
    [$anchoEtiqueta, $altoPagina],
    true,
    'UTF-8',
    false
);

// =========================
// CONFIG BASE
// =========================
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// ⚠️ IMPORTANTE:
// TODO se dibuja SOLO dentro del alto real de la etiqueta
// (0 → $altoEtiqueta)

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
    (float)$element['barcode_x'],
    (float)$element['barcode_y'],
    (float)$element['barcode_width'],
    (float)$element['barcode_height'],
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
        (float)$element['descripcion_x'],
        (float)$element['descripcion_y']
    );

    $pdf->MultiCell(
        (float)$element['descripcion_width'],
        (float)$element['descripcion_height'],
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
        (float)$element['precio_x'],
        (float)$element['precio_y']
    );

    $pdf->Cell(
        (float)$element['precio_width'],
        (float)$element['precio_height'],
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
    "etiqueta_{$anchoEtiqueta}x{$altoEtiqueta}mm.pdf",
    'I'
);
