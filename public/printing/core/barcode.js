/**
 * Genera un código de barras y lo agrega al flujo de impresión de la factura.
 * 
 * @param {string} code - El código que se va a convertir en un código de barras.
 * @param {string} language - Lenguaje de la impresora (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @param {number} width - Ancho de la fuente para el código de barras (1 = normal, 2 = doble).
 * @param {number} height - Altura del código de barras.
 * @returns {string} El comando para generar el código de barras.
 */
export function generateBarcodeCommand(code, language = 'ESCPOS', width = 1, height = 80) {
    // Método de conveniencia para convertir números a caracteres
    var chr = function (n) { return String.fromCharCode(n); };

    if (language === 'ESCPOS') {
        // Generar el comando ESCPOS para generar el código de barras (Code39)
        return '\x1D' + 'h' + chr(height) +   // Establece la altura del código de barras
            '\x1D' + 'f' + chr(0) +      // Establece la fuente para los números impresos
            '\x1D' + 'k' + chr(69) +     // El tipo de código de barras (Code39)
            chr(code.length) + code + chr(0); // El código de barras en sí
    }
    else if (language === 'ZPL') {
        // Generar el comando ZPL para el código de barras (Code39)
        return `^FO100,100^B3N,${height},${width},Y,N^FD${code}^FS`;  // ^B3 para Code39
    }
    else if (language === 'EPL') {
        // Generar el comando EPL para el código de barras (Code39)
        return `B100,100,0,3,3,100,50^FD${code}^FS`;  // El tamaño del código de barras se ajusta aquí
    }
    else if (language === 'SBPL') {
        // Generar el comando SBPL para el código de barras (Code39)
        return `SBPL:BARCODE CODE39,${height},${width},${code}\n`;
    }
    else if (language === 'FGL') {
        // Generar el comando FGL para el código de barras (Code39)
        return `FGL:BARCODE,${code},TYPE=CODE39,SIZE=${height},WIDTH=${width}\n`;
    }
    else {
        return '';  // Si el lenguaje no es soportado, no realiza ninguna acción
    }
}

/**
 * Genera un comando de impresión para un código QR en lenguaje ESCPOS.
 * 
 * Este comando es compatible con impresoras que soportan el lenguaje de comandos ESCPOS, 
 * y se utiliza para imprimir códigos QR a partir de un string de datos. El tamaño del 
 * código QR puede ser ajustado a través del parámetro `size`.
 *
 * El tamaño del código QR es determinado por el valor de `size`. Sin embargo, algunos 
 * modelos de impresoras pueden tener un rango limitado de tamaños soportados, lo que 
 * puede afectar su tamaño real. Si no ves un cambio en el tamaño, es posible que la 
 * impresora tenga un tamaño máximo configurado.
 * 
 * @param {string} data - El dato que se codificará en el código QR (por ejemplo, una URL o texto).
 * @param {number} [size=6] - El tamaño del código QR. El tamaño predeterminado es 6.
 * @returns {string} El comando ESCPOS para imprimir un código QR con el dato especificado y el tamaño deseado.
 */
export function generateQRCommand(data, size = 6) {
    const chr = n => String.fromCharCode(n);  // Función para convertir números en caracteres
    const len = data.length + 3;  // Calculamos la longitud del dato

    // Comando para generar el código QR en ESCPOS con el tamaño proporcionado.
    return (
        '\x1D(k' + chr(4) + chr(0) + '1A' + chr(size) + '\x00' +  // Configuración de tamaño
        '\x1D(k' + chr(len) + chr(0) + '1P0' + data +  // Codificación del dato
        '\x1D(k' + chr(3) + chr(0) + '1Q0'  // Fin del comando de QR
    );
}