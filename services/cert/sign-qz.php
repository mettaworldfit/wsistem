<?php
// Cargar la clave privada
$privateKey = file_get_contents(__DIR__ . '/../cert/qz-private.key');

// Verifica si la clave se cargó correctamente
if ($privateKey === false) {
    echo "❌ Error al cargar la clave privada. Verifica la ruta y los permisos del archivo.";
    exit;
} else {
    echo "✔ Clave privada cargada correctamente.";
}

// Verificar el contenido de la clave privada (esto mostrará la clave en base64 o en formato legible, úsalo solo para depuración)
echo "<pre>";
echo base64_encode($privateKey);  // Esto te da la clave privada en formato base64
echo "</pre>";

// Asegurarse de que los datos se reciben correctamente
$toSign = file_get_contents("php://input");  // Esto recibe el cuerpo de la solicitud como texto plano

// Verifica si se recibió algo para firmar
if ($toSign) {
    // Crear la firma
    if (openssl_sign($toSign, $signature, $privateKey, OPENSSL_ALGO_SHA512)) {
        // Verificar si la firma fue generada
        echo "✔ Firma generada correctamente.";
        echo "<pre>";
        echo base64_encode($signature);  // Muestra la firma en base64
        echo "</pre>";
    } else {
        echo "❌ Error al generar la firma.";
    }
} else {
    echo "❌ No se recibió ningún dato para firmar.";
}
?>
