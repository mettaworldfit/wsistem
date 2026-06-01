/**============================================================= 
* FUNCIONES
===============================================================*/

/**
* Inicializa la impresora térmica ESCPOS o ZPL o EPL.
* 
* Dependiendo del lenguaje de impresión, genera el código adecuado.
*
* @param {string} language - El lenguaje de impresión: 'ESCPOS', 'ZPL', 'EPL', etc.
* @returns {string} Comandos de inicialización según el lenguaje de impresión.
*/
export function initPrinter(language = 'ESCPOS') {
    if (language === 'ESCPOS') {
        // ESCPOS (típico para impresoras térmicas de recibos)
        return (
            '\x1B@' +        // ESC @  → reset
            '\x1B!\x00' +    // tamaño normal
            '\x1BE\x00' +    // negrita OFF
            '\x1Ba\x00' +    // alineación izquierda
            '\x1BM\x00'      // fuente A
        );
    }
    else if (language === 'ZPL') {
        // ZPL (Zebra Programming Language) para impresoras Zebra
        return '^XA\n^FO50,50^ADN,36,20^FDHello ZPL!^FS\n^XZ';  // ZPL básico
    }
    else if (language === 'EPL') {
        // EPL (Eltron Programming Language) para impresoras Eltron
        return 'N\nA50,50,0,4,1,1,N,"Hello EPL!"\nP1';  // EPL básico
    }
    else if (language === 'SBPL') {
        // SBPL (Sato Barcode Programming Language) para impresoras Sato
        return ''; // Aquí deberías agregar el código para SBPL, depende de tu impresora Sato
    }
    else if (language === 'FGL') {
        // FGL (FlexiGrid Language) para impresoras Datamax
        return ''; // Aquí deberías agregar el código para FGL, depende de tu impresora Datamax
    }
    else {
        console.warn("Lenguaje no soportado: " + language);
        return '';  // Si el lenguaje no es compatible
    }
}

/**
 * Crea un buffer de impresión que puede aceptar comandos para diferentes lenguajes de impresión.
 * 
 * Permite agregar comandos de impresión en cadena para ESCPOS, ZPL, EPL, SBPL, FGL.
 *
 * @param {string} language - Lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', 'SBPL', 'FGL')
 * @returns {object} Objeto con los métodos `push` y `get`
 */
export function createPrintBuffer(language = 'ESCPOS') {
    const d = [];

    return {
        /**
         * Agrega uno o más elementos al buffer dependiendo del lenguaje de impresión.
         * 
         * @param {...string} items - Comandos de impresión
         * @returns {object} El objeto del buffer (para encadenar)
         */
        push: (...items) => {
            // Si estamos usando ZPL o EPL, los comandos deben formatearse según el lenguaje.
            if (language === 'ZPL') {
                // Modificar los comandos según ZPL
                d.push(...items.map(item => `^${item}`));  // ZPL usa una sintaxis diferente
            }
            else if (language === 'EPL') {
                // Modificar los comandos según EPL
                d.push(...items.map(item => `N${item}`));  // EPL tiene una estructura diferente
            }
            else if (language === 'SBPL') {
                // Modificar los comandos según SBPL
                d.push(...items.map(item => `SBPL:${item}`)); // Ejemplo para SBPL
            }
            else if (language === 'FGL') {
                // Modificar los comandos según FGL
                d.push(...items.map(item => `FGL:${item}`));  // Ejemplo para FGL
            }
            else {
                // Si es ESCPOS, seguir con la estructura de ESCPOS
                d.push(...items);
            }
            return this;
        },

        /**
         * Obtiene el contenido del buffer como un string.
         * 
         * @returns {string} El contenido del buffer de impresión
         */
        get: () => d.join('')
    };
}

/**
 * Normaliza texto para impresoras térmicas ESCPOS.
 *
 * Convierte caracteres acentuados y especiales a ASCII básico
 * para evitar símbolos corruptos en impresoras POS que no
 * soportan UTF-8 (ej: POS80, 2connect, genéricas).
 *
 * ⚠️ Útil cuando NO se usa codepage extendido (CP437/CP850).
 *
 * @param {string} text - Texto original con acentos y caracteres especiales
 * @returns {string} Texto normalizado compatible con impresoras térmicas
 */
export function normalizeText(text) {
    return text
        .replace(/Á|À|Â|Ä/g, 'A')
        .replace(/á|à|â|ä/g, 'a')
        .replace(/É|È|Ê|Ë/g, 'E')
        .replace(/é|è|ê|ë/g, 'e')
        .replace(/Í|Ì|Î|Ï/g, 'I')
        .replace(/í|ì|î|ï/g, 'i')
        .replace(/Ó|Ò|Ô|Ö/g, 'O')
        .replace(/ó|ò|ô|ö/g, 'o')
        .replace(/Ú|Ù|Û|Ü/g, 'U')
        .replace(/ú|ù|û|ü/g, 'u')
        .replace(/Ñ/g, 'N')
        .replace(/ñ/g, 'n')
        .replace(/°/g, '')
        .replace(/´/g, '')
        .replace(/¨/g, '');
}

/**
 * Genera una línea horizontal para tickets térmicos o etiquetas.
 *
 * Dependiendo del lenguaje de impresión (ESCPOS, ZPL, EPL, SBPL, FGL),
 * se genera el comando adecuado para cada caso.
 *
 * @param {number} W - Ancho del ticket en caracteres o unidades de medida.
 * @param {string} [char='-'] - Carácter usado para dibujar la línea.
 * @param {string} language - Lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @returns {string} Línea horizontal con salto de línea
 */
export function line(W, char = '-', language = 'ESCPOS') {
    if (language === 'ESCPOS') {
        // ESCPOS usa caracteres ASCII para dibujar la línea
        return Array(W + 1).join(char) + '\n';
    }
    else if (language === 'ZPL') {
        // ZPL usa el comando ^GB para generar una línea
        return `^FO50,50^GB${W * 10},0,0^FS\n`;  // W * 10 para ajustarlo a unidades ZPL
    }
    else if (language === 'EPL') {
        // EPL usa un comando similar a ZPL para dibujar líneas
        return `N\nB100,100,0,2,2,100,50\nP1`;  // Formato básico en EPL, ajusta según tu impresora
    }
    else if (language === 'SBPL') {
        // SBPL usa un comando específico para líneas
        return `SBPL:L${W}\n`;  // Ejemplo simplificado para SBPL
    }
    else if (language === 'FGL') {
        // FGL también tiene su propia forma de dibujar líneas
        return `FGL:LINE(${W},0,0,0)\n`;  // Ejemplo simplificado para FGL
    }
    else {
        return '';  // Si no se encuentra el lenguaje, retorna vacío
    }
}

/**
 * Activa o desactiva la negrita dependiendo del lenguaje de impresión.
 * 
 * @param {boolean} enable - Si es `true`, activa negrita. Si es `false`, la desactiva.
 * @param {string} language - Lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @returns {string} Comando para activar o desactivar la negrita
 */
export function bold(enable = true, language = 'ESCPOS') {
    if (language === 'ESCPOS') {
        // ESCPOS usa el comando \x1B E para negrita
        return '\x1B' + 'E' + (enable ? '\x01' : '\x00');
    }
    else if (language === 'ZPL') {
        // ZPL no tiene un comando específico para negrita, pero se puede cambiar el tipo de fuente
        return '^FO50,50^A0N,50,50';  // Cambia a fuente más gruesa, pero no tiene negrita como tal
    }
    else if (language === 'EPL') {
        // EPL no tiene un comando específico para negrita, no aplica directamente
        return '';  // No cambia nada
    }
    else if (language === 'SBPL') {
        // SBPL generalmente no soporta negrita como ESCPOS, no hay cambio específico
        return '';  // No cambia nada
    }
    else if (language === 'FGL') {
        // FGL también depende de la impresora, generalmente no hay soporte directo para negrita
        return '';  // No cambia nada
    }
    else {
        return '';  // Si el lenguaje no es soportado, no se realiza ninguna acción
    }
}

/**
 * Cambia el tipo de fuente dependiendo del lenguaje de impresión.
 * 
 * A = fuente normal (fiscal)
 * B = fuente pequeña
 *
 * @param {'A'|'B'} font - El tipo de fuente a usar.
 * @param {string} language - El lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @returns {string} Comando para cambiar el tipo de fuente
 */
export function font(font = 'A', language = 'ESCPOS') {
    if (language === 'ESCPOS') {
        // ESCPOS usa el comando \x1B M para cambiar entre fuentes
        return '\x1B' + 'M' + (font === 'B' ? '\x01' : '\x00');
    }
    else if (language === 'ZPL') {
        // ZPL usa ^A para cambiar el tipo de fuente
        return `^FO50,50^A0${font === 'B' ? 'N' : 'B'},50,50`;  // A0N para fuente normal, A0B para fuente pequeña
    }
    else if (language === 'EPL') {
        // EPL usa comandos similares a ESCPOS para cambiar la fuente
        return `A50,50,0,${font === 'B' ? 4 : 3},1,1,N,"Fuente ${font}"`;  // El comando para fuente en EPL
    }
    else if (language === 'SBPL') {
        // SBPL no tiene un comando directo para fuentes, pero podría ajustarse según el modelo
        return '';  // No cambia nada en SBPL (depende de la impresora Sato)
    }
    else if (language === 'FGL') {
        // FGL generalmente no tiene un comando directo para cambiar la fuente
        return '';  // No cambia nada en FGL
    }
    else {
        return '';  // Si el lenguaje no es soportado
    }
}

/**
 * Cambia el tamaño de la fuente dependiendo del lenguaje de impresión.
 * 
 * width y height aceptan valores:
 * 1 = normal
 * 2 = doble
 *
 * @param {number} width - Ancho de la fuente (1 = normal, 2 = doble)
 * @param {number} height - Alto de la fuente (1 = normal, 2 = doble)
 * @param {string} language - Lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @returns {string} Comando para cambiar el tamaño de la fuente
 */
export function size(width = 1, height = 1, language = 'ESCPOS') {
    const w = Math.max(1, Math.min(width, 2)) - 1;
    const h = Math.max(1, Math.min(height, 2)) - 1;

    if (language === 'ESCPOS') {
        // ESCPOS usa el comando \x1D! para cambiar el tamaño de la fuente
        return '\x1D' + '!' + String.fromCharCode((w << 4) | h);
    }
    else if (language === 'ZPL') {
        // ZPL usa el comando ^A0 para cambiar el tamaño de la fuente
        return `^FO50,50^A0${w === 1 ? 'N' : 'B'},${width * 50},${height * 50}`;  // Ajustamos el tamaño en ZPL
    }
    else if (language === 'EPL') {
        // EPL usa el comando A para cambiar el tamaño de la fuente
        return `A50,50,0,${width === 1 ? 3 : 4},${height === 1 ? 1 : 2},1,N,"Tamaño ${width}x${height}"`;  // Ajuste en EPL
    }
    else if (language === 'SBPL') {
        // SBPL no tiene un comando directo para cambiar el tamaño de la fuente, pero se puede ajustar la fuente de manera indirecta
        return '';  // No cambia nada en SBPL, se tendría que verificar el modelo de impresora
    }
    else if (language === 'FGL') {
        // FGL también tiene su propio formato para cambiar el tamaño de la fuente
        return '';  // No cambia nada en FGL
    }
    else {
        return '';  // Si el lenguaje no es soportado, no se realiza ninguna acción
    }
}


/**
 * Agrega saltos de línea dependiendo del lenguaje de impresión.
 *
 * @param {number} n - Número de saltos de línea.
 * @param {string} language - Lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @returns {string} Comandos para agregar saltos de línea
 */
export function feed(n = 1, language = 'ESCPOS') {
    if (language === 'ESCPOS') {
        // ESCPOS usa \n para los saltos de línea
        return '\n'.repeat(n);
    }
    else if (language === 'ZPL') {
        // ZPL no usa \n, se usa ^FS y comandos de formato para indicar saltos de línea
        return '^FO50,100^FD' + '^FS'.repeat(n);  // ^FO coloca un campo y ^FS marca el final
    }
    else if (language === 'EPL') {
        // EPL usa \n o un comando para mover la impresora hacia abajo
        return '\n'.repeat(n);  // En EPL también usamos \n
    }
    else if (language === 'SBPL') {
        // SBPL no tiene un comando directo para saltos de línea
        return '';  // Se tendría que usar un comando específico de la impresora
    }
    else if (language === 'FGL') {
        // FGL generalmente usa comandos de posicionamiento para saltos de línea
        return '';  // FGL no tiene un comando directo para saltos de línea
    }
    else {
        return '';  // Si el lenguaje no es soportado, no realiza nada
    }
}

/**
 * Cambia la alineación del texto dependiendo del lenguaje de impresión.
 * 
 * @param {'left'|'center'|'right'} align - La alineación del texto (izquierda, centro, derecha)
 * @param {string} language - El lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @returns {string} Comando para cambiar la alineación del texto
 */
export function align(align = 'left', language = 'ESCPOS') {
    if (language === 'ESCPOS') {
        // ESCPOS usa \x1B a para cambiar la alineación
        const map = { left: '\x00', center: '\x01', right: '\x02' };
        return '\x1B' + 'a' + (map[align] || '\x00');
    }
    else if (language === 'ZPL') {
        // ZPL usa ^FO para posicionar los campos y alinearlos
        let xPosition = 50;  // Valor por defecto para posicionar horizontalmente
        if (align === 'center') {
            xPosition = 200;  // Centrado (ajustar según el tamaño de la impresora)
        } else if (align === 'right') {
            xPosition = 350;  // Alineado a la derecha
        }
        return `^FO${xPosition},50`;  // Cambia la posición horizontal según la alineación
    }
    else if (language === 'EPL') {
        // EPL usa ^FO para posicionar los campos
        let xPosition = 50;  // Valor por defecto para posicionar horizontalmente
        if (align === 'center') {
            xPosition = 200;  // Centrado (ajustar según el tamaño de la impresora)
        } else if (align === 'right') {
            xPosition = 350;  // Alineado a la derecha
        }
        return `^FO${xPosition},50`;  // Cambia la posición horizontal según la alineación
    }
    else if (language === 'SBPL') {
        // SBPL usa un sistema de posicionamiento específico
        // Aquí debes implementar el ajuste según tu impresora
        return '';  // No hay un comando directo para alineación
    }
    else if (language === 'FGL') {
        // FGL usa un sistema de posicionamiento de texto similar a SBPL
        // Se debe realizar un ajuste de la posición según el tipo de impresora
        return '';  // No hay un comando directo para alineación
    }
    else {
        return '';  // Si el lenguaje no es soportado, no realiza ninguna acción
    }
}

/**
 * Realiza el corte de papel dependiendo del lenguaje de impresión.
 * 
 * Usa corte completo (Full cut) para ESCPOS y otros lenguajes.
 *
 * @param {string} language - Lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @returns {string} Comando para cortar el papel
 */
export function cutPaper(language = 'ESCPOS') {
    if (language === 'ESCPOS') {
        // ESCPOS usa GS V 0 para corte de papel
        return '\x1D' + 'V' + '\x00';  // GS V 0 (Full cut)
    }
    else if (language === 'ZPL') {
        // ZPL usa ^MMT para indicar el modo de corte
        return '^MMT\n';  // Modo de corte (Full Cut) en ZPL
    }
    else if (language === 'EPL') {
        // EPL usa ^PQ o P1 para cortar el papel
        return 'P1';  // Comando de corte para EPL
    }
    else if (language === 'SBPL') {
        // SBPL puede tener un comando similar para cortar
        return 'SBPL:CUT\n';  // Ejemplo para corte en SBPL
    }
    else if (language === 'FGL') {
        // FGL también tiene un comando específico para cortar
        return 'FGL:CUT\n';  // Ejemplo para corte en FGL
    }
    else {
        return '';  // Si el lenguaje no es soportado, no realiza ninguna acción
    }
}

/**
 * Envía pulso eléctrico para abrir la gaveta (caja registradora).
 * 
 * Usa el pin 2 del conector RJ11 (estándar) para abrir la gaveta en ESCPOS y otros lenguajes.
 *
 * @param {string} language - Lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', etc.)
 * @returns {string} Comando para abrir la gaveta (caja registradora)
 */
export function openCashDrawer(language = 'ESCPOS') {
    if (language === 'ESCPOS') {
        // ESCPOS usa \x1B p para abrir la gaveta
        return '\x1B' + 'p' + '\x00' + '\x19' + '\xFA';  // Pulso eléctrico para abrir gaveta
    }
    else if (language === 'ZPL') {
        // ZPL no tiene un comando directo para abrir la gaveta
        // Necesitarás manejar esto por un puerto o por la interfaz del hardware
        return '';  // No tiene un comando directo en ZPL
    }
    else if (language === 'EPL') {
        // EPL tampoco tiene un comando directo para abrir la gaveta
        // Usualmente se hace a través del hardware o el puerto de control
        return '';  // No tiene un comando directo en EPL
    }
    else if (language === 'SBPL') {
        // SBPL tiene su propio comando para controlar dispositivos externos
        return 'SBPL:OPEN_CASH_DRAWER\n';  // Ejemplo simplificado para SBPL
    }
    else if (language === 'FGL') {
        // FGL también usa comandos específicos para controlar dispositivos
        return 'FGL:OPEN_CASH_DRAWER\n';  // Ejemplo simplificado para FGL
    }
    else {
        return '';  // Si el lenguaje no es soportado, no realiza ninguna acción
    }
}

/**
 * Calcula el ancho de la impresora dependiendo del tamaño del papel.
 *
 * @param {'80mm'|'58mm'} paperSize - Tamaño del papel, '80mm' o '58mm'.
 * @returns {number} El ancho de la impresora en caracteres.
 */
export function getPrinterWidth(paperSize = '80mm') {
    const sizes = {
        '80mm': 48,  // Fuente A para 80mm
        '58mm': 32   // Fuente A para 58mm
    };
    return sizes[paperSize] || 48; // Predeterminado a 80mm si no se pasa el tamaño
}

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
 * Convierte una imagen desde una URL a Base64 sin el prefijo `data:image/png;base64,`.
 * 
 * @param {string} imageUrl - La URL de la imagen a convertir.
 * @returns {Promise<string>} - Promesa que devuelve la cadena Base64 sin el prefijo.
 */
export function convertImageUrlToBase64(imageUrl) {
    return new Promise((resolve, reject) => {
        const image = new Image();

        // 🔴 IMPORTANTE para evitar canvas tainted
        image.crossOrigin = 'anonymous';

        image.onload = () => {
            try {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                canvas.width = image.width;
                canvas.height = image.height;

                ctx.drawImage(image, 0, 0);

                const base64 = canvas
                    .toDataURL('image/png')
                    .split(',')[1];

                resolve(base64);
            } catch (e) {
                reject(e);
            }
        };

        image.onerror = () => reject('No se pudo cargar la imagen');

        // 🔴 evita cache viejo
        image.src = imageUrl + '?v=' + Date.now();
    });
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

export function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result.split(',')[1]);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

/**
 * Obtiene la configuración de impresión desde el servidor.
 *
 * Realiza una petición POST a services/config.php enviando
 * la acción "configuracion_de_impresion".
 *
 * @async
 * @function getData
 * @returns {Promise<Object|null>} Retorna un objeto con la configuración
 * o null si ocurre un error.
 */
async function getData() {
    try {

        const response = await fetch(SITE_URL + "services/config.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                action: "configuracion_de_impresion"
            })
        });

        if (!response.ok) {
            throw new Error("Error en la petición");
        }

        const data = await response.json();

        return data;

    } catch (error) {
        console.log('%c[CONFIG]', 'color:#b51717;font-weight:bold;', error);
        return null;
    }
}


/**
 * Genera la fecha y hora actual formateada
 * en formato: DD-MM-YYYY hh:mm:ss AM/PM
 *
 * @async
 * @function getPrintDate
 * @returns {Promise<string>} Fecha formateada lista para impresión.
 */
async function getPrintDate() {
    const now = new Date();

    const pad = (n) => n.toString().padStart(2, '0');

    let day = pad(now.getDate());
    let month = pad(now.getMonth() + 1);
    let year = now.getFullYear();

    let hours = now.getHours();
    let minutes = pad(now.getMinutes());
    let seconds = pad(now.getSeconds());

    let ampm = hours >= 12 ? 'AM' : 'PM';
    ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12;
    hours = pad(hours);

    return `${day}-${month}-${year} ${hours}:${minutes}:${seconds} ${ampm}`;
}

/**
 * Rellena texto con espacios a la derecha
 * hasta alcanzar la longitud indicada.
 *
 * @function padRight
 * @param {string|number} text - Texto a formatear.
 * @param {number} length - Longitud final deseada.
 * @returns {string} Texto con padding a la derecha.
 */
function padRight(text, length) {
    return text.toString().padEnd(length, ' ');
}

/**
 * Rellena texto con espacios a la derecha
 * hasta alcanzar la longitud indicada.
 *
 * @function padLeft
 * @param {string|number} text - Texto a formatear.
 * @param {number} length - Longitud final deseada.
 * @returns {string} Texto con padding a la derecha.
 */
function padLeft(text, length) {
    return text.toString().padStart(length, ' ');
}


/**
 * Formatea un número como moneda con 2 decimales.
 *
 * Usa formato en-US (1,000.00).
 *
 * @function formatMoney
 * @param {number|string} number - Número a formatear.
 * @returns {string} Número formateado con 2 decimales.
 */
export function formatMoney(number) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(Number(number) || 0);
}


/**============================================================= 
* CERTIFICADOS Y SEGURIDAD
===============================================================*/

/* ===== QZ-TRAY VERBOSE MODE ===== */
const QZ_VERBOSE = false;

function qzLog(...args) {
    if (!QZ_VERBOSE) return;
    console.log('%c[QZ]', 'color:#1976d2;font-weight:bold;', ...args);
}

function qzWarn(...args) {
    if (!QZ_VERBOSE) return;
    console.warn('%c[QZ]', 'color:#f9a825;font-weight:bold;', ...args);
}

function qzError(...args) {
    if (!QZ_VERBOSE) return;
    console.error('%c[QZ]', 'color:#d32f2f;font-weight:bold;', ...args);
}


/* ===== SEGURIDAD QZ-TRAY | CERTIFICADO ===== */

qz.security.setCertificatePromise(function (resolve, reject) {

    qzLog('Solicitando certificado…');

    fetch(SITE_URL + "src/qz-tray/get-cert.php", {
        cache: 'no-store'
    })
        .then(res => {
            qzLog('HTTP status certificado:', res.status);
            if (!res.ok) throw new Error('Cert not loaded');
            return res.text();
        })
        .then(cert => {

            qzLog('Certificado recibido');
            qzLog('Longitud:', cert.length);
            qzLog('BEGIN:', cert.slice(0, 40));
            qzLog('END:', cert.slice(-40));

            // Validación dura
            if (
                !cert.includes('-----BEGIN CERTIFICATE-----') ||
                !cert.includes('-----END CERTIFICATE-----')
            ) {
                throw new Error('Contenido NO es un certificado X509');
            }

            qzLog('Certificado X509 válido ✔');
            resolve(cert);
        })
        .catch(err => {
            qzError('❌ Error certificado:', err);
            reject(err);
        });
});

/* ===== SEGURIDAD QZ-TRAY | FIRMA ===== */

qz.security.setSignatureAlgorithm('SHA512');
qz.security.setSignaturePromise(function (toSign) {

    return function (resolve, reject) {

        qzLog('Solicitud de firma enviada');
        qzLog('Payload:', toSign);

        fetch(SITE_URL + 'src/qz-tray/sign.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request: toSign })
        })
            .then(res => {
                qzLog('HTTP status firma:', res.status);
                if (!res.ok) throw new Error('Firma no generada');
                return res.text();
            })
            .then(signature => {

                qzLog('Firma recibida');
                qzLog('Longitud firma:', signature.length);

                resolve(signature.trim());
            })
            .catch(err => {
                qzError('❌ Error firma:', err);
                reject(err);
            });
    };
});


/************************************************************
 * ESTADO GLOBAL
 ************************************************************/
let autoDetectedLanguage = null;
let manualLanguageChanged = false;

/**
 * Detecta el lenguaje de impresión basándose en el nombre
 * @param {string} printerName
 * @returns {string|null}
 */
export function detectLanguageByName(printerName) {

    const name = printerName.toUpperCase();

    if (name.includes('ZEBRA') || name.includes('ZPL')) return 'ZPL';
    if (name.includes('TSC') || name.includes('TSPL')) return 'TSPL';
    if (name.includes('ELTRON') || name.includes('EPL')) return 'EPL';

    // Impresoras POS comunes (ESCPOS)
    if (
        name.includes('EPSON') ||
        name.includes('STAR') ||
        name.includes('BIXOLON') ||
        name.includes('POS') ||
        name.includes('XP-')
    ) return 'ESCPOS';

    return null;
}

/* ===== CONEXION ===== */
qz.websocket.connect()
    .then(() => qz.printers.find())
    .then(printers => {

        const $select = $('#impresoraSelect');
        $select.empty().append('<option value=""></option>');

        printers.forEach(printer => {
            $select.append(
                $('<option>', { value: printer, text: printer })
            );
        });

        const defaultPrinter = 'POS-80';
        if (printers.includes(defaultPrinter)) {
            $select.val(defaultPrinter).trigger('change');
        }
    })
    .catch(err => {
        console.error('QZ Tray error:', err);
    });

/**
 * Intenta detectar el lenguaje enviando comandos mínimos
 * @param {string} printer
 * @returns {Promise<string|null>}
 */
export async function probePrinterLanguage(printer) {

    const tests = [
        { lang: 'ZPL', data: '^XA^HH^XZ' },
        { lang: 'TSPL', data: 'SIZE 10 mm,10 mm\r\nCLS\r\n' },
        { lang: 'ESCPOS', data: '\x1B@' }
    ];

    for (const test of tests) {
        try {
            const config = qz.configs.create(printer, { copies: 1 });
            await qz.print(config, [{ type: 'raw', data: test.data }]);
            return test.lang;
        } catch (e) {
            // Probar siguiente lenguaje
        }
    }

    return null;
}

/**
 * Detecta el lenguaje de la impresora
 * @param {string} printer
 * @returns {Promise<string>}
 */
export async function detectPrinterLanguage(printer) {

    const cacheKey = 'printer_lang_' + printer;

    // 1. Cache
    const cached = localStorage.getItem(cacheKey);
    if (cached) return cached;

    // 2. Nombre
    let lang = detectLanguageByName(printer);
    if (lang) {
        localStorage.setItem(cacheKey, lang);
        return lang;
    }

    // 3. Prueba real
    lang = await probePrinterLanguage(printer);
    if (lang) {
        localStorage.setItem(cacheKey, lang);
        return lang;
    }

    // 4. Fallback seguro
    return 'ESCPOS';
}

// Selecionar impresora
$('#impresoraSelect').select2({
    placeholder: 'Seleccione una impresora',
    width: '100%'
});

$('#impresoraSelect').on('change', async function () {

    const printer = $(this).val();
    if (!printer) return;

    manualLanguageChanged = false;

    autoDetectedLanguage = await detectPrinterLanguage(printer);

    $('[name="printer_language"]')
        .val(autoDetectedLanguage)
        .trigger('change');

});

$('[name="printer_language"]').on('change', function () {
    manualLanguageChanged = true;
    comparePrinterLanguages();
});

/**
 * Compara el lenguaje detectado automáticamente
 * contra el seleccionado manualmente
 */
export function comparePrinterLanguages() {

    if (!autoDetectedLanguage) return;

    const manualLang = $('[name="printer_language"]').val();
    if (!manualLanguageChanged) return;

    // Coinciden
    if (manualLang === autoDetectedLanguage) {
        notifyAlert(`Lenguaje confirmado: ${manualLang}`, 1000);
        return;
    }

    // No coinciden
    notifyAlert(
        `Advertencia: la impresora parece ser ${autoDetectedLanguage}, pero seleccionaste ${manualLang}`,
        'warning',
        5000
    );
}


/**============================================================= 
 * DIAGNOSTICOS Y PRUEBAS
===============================================================*/

// Selectores
export function getSelectedPrinter() {
    return $('#impresoraSelect').val();
}

export function getSelectedLanguage() {
    return $('[name="printer_language"]').val() || 'ESCPOS';
}

/* =========================
   DIAGNÓSTICO QZ
========================= */
export async function runQzDiagnostic() {

    // Datos predeterminados
    const printer_config = await getData();
    // const printers = printer_config[0]; // Impresoras
    const site = printer_config[1]; // Datos del sitio

    console.group('%c[QZ DIAGNOSTIC]', 'color:#1565c0;font-weight:bold;');

    try {

        /* 1️⃣ QZ cargado */
        if (typeof qz === 'undefined') {
            throw new Error('QZ Tray JS no está cargado');
        }
        console.log('✔ Librería QZ cargada');

        /* 2️⃣ WebSocket */
        if (!qz.websocket.isActive()) {
            console.log('Conectando a QZ Tray…');
            await qz.websocket.connect();
        }
        console.log('✔ WebSocket activo');

        /* 3️⃣ Versión */
        const version = await qz.api.getVersion();
        console.log('✔ Versión QZ:', version);

        /* 4️⃣ Impresoras */
        const printers = await qz.printers.find();
        console.log('✔ Impresoras encontradas:', printers.length);

        /* 5️⃣ Impresión real */
        const printer = getSelectedPrinter();
        if (!printer) throw new Error('No hay impresora seleccionada');

        const language = getSelectedLanguage();

        console.log('🖨️ Probando impresión real en:', printer);
        console.log('📄 Lenguaje:', language);

        // Configuracion
        const config = qz.configs.create(printer, { copies: 1 });

        const paperSize = '80mm';
        const W = getPrinterWidth(paperSize);

        const buffer = createPrintBuffer(language);
        buffer.push(initPrinter(language));
        buffer.push(align("center", language));

        let logoBase64 = null;

        try {
            let logoPath = site[7]?.config_value
                ? SITE_URL + 'public/uploads/' + site[7].config_value
                : SITE_URL + 'public/imagen/sistem/pdf.png';

            logoBase64 = await convertImageUrlToBase64(logoPath);
        } catch (e) {
            console.warn('Logo no disponible, usando texto');
        }

        // ================== LOGO O TEXTO ==================
        const data = [];

        data.push(feed(1));
        data.push(align("center"));

        // 👉 IMAGEN (RAW)
        data.push({
            type: 'raw',
            format: 'image',
            flavor: 'base64',
            data: logoBase64,
            options: { language: "ESCPOS" }
        });

        data.push(feed(1));
        data.push("QZ Tray - Diagnóstico\n");
        data.push("Certificado y firma OK\n");
        data.push(feed(4));
        data.push(cutPaper());

        qz.print(config, data).catch(console.error);


        console.log('✔ Impresión de prueba enviada correctamente');
        notifyAlert('Diagnóstico QZ completado correctamente ✔', 'success', 4000);

    } catch (err) {

        console.error('❌ Error diagnóstico:', err);
        notifyAlert('Error en diagnóstico QZ: ' + err.message, 'error', 6000);

    } finally {
        console.groupEnd();
    }
}

/**============================================================= 
* EJEMPLOS
===============================================================*/

// Imprimir imagen de prueba
export function printImageExample() {
    var config = qz.configs.create("POS-80");
    var buffer = createPrintBuffer();

    // Llamar a la función para convertir la imagen de una URL a Base64
    convertImageUrlToBase64(SITE_URL + 'public/imagen/sistem/pdf.png')
        .then(base64Image => {
            // Ahora que tenemos la cadena Base64 en la variable, la pasamos al objeto de datos de QZ Tray

            var data = [
                feed(1),
                align("center"),
                {
                    type: 'raw',
                    format: 'image',
                    flavor: 'base64',  // Especificamos que la imagen está en Base64
                    data: base64Image,  // Usamos la variable base64Image que contiene la cadena Base64 de la imagen
                    options: {
                        language: "ESCPOS"
                    }
                },
                "Logo impreso correctamente\n"
            ];

            // Enviar los datos al QZ Tray para impresión
            qz.print(config, data).catch(function (e) {
                console.error('Error al imprimir:', e);
            });

        })
        .catch(error => {
            console.error('Error al convertir la imagen:', error);
        });
}



/**============================================================= 
* FACTURA DE VENTA
===============================================================*/


/**
 * Funcion para imprimir las facturas de venta 
 * @param {Object} dataInv
 * @param {Array<Object>} detail
 */
export async function factura_venta(dataInv, detail) {

    // Datos predeterminados
    const printer_config = await getData();
    const printers = printer_config[0]; // Impresoras
    const site = printer_config[1]; // Datos del sitio

    const printer = printers.find(p =>
        p.printer_type === "main"
    );

    if (!printer) {
        console.error("No main printer configured");
        return;
    }

    // Configuracion de impresion

    const paperSize = parseInt(printer.paper_width) + 'mm'; // Aquí puedes cambiarlo a '58mm' si necesitas otro tamaño
    const W = getPrinterWidth(paperSize);  // Calculamos el ancho en función del tamaño del papel

    const language = printer.printer_language;

    const config = qz.configs.create(printer.printer_name, { copies: printer.copies });
    const buffer = createPrintBuffer();  // Creamos el buffer de impresión
    const data = [];

    // ================== CONFIG LOGO ==================
    let logoBase64 = null;

    try {
        logoBase64 = await convertImageUrlToBase64(SITE_URL + 'public/uploads/' + site[7].config_value);
    } catch (e) {
        console.warn('Logo no disponible, usando texto');
    }

    data.push(initPrinter(language));

    if (printer.feed_start > 0) buffer.push(feed(printer.feed_start, language));
    data.push(align('center', language));

    // 👉 LOGO FUERA DEL BUFFER
    if (logoBase64) {
        data.push({
            type: 'raw',
            format: 'image',
            flavor: 'base64',
            data: logoBase64,
            options: {
                language: "ESCPOS",
                dotDensity: printer.logo_density
            }
        });

    } else {
        data.push(bold(true, language));
        data.push(size(2, 2, language));
        data.push(normalizeText(site[0].config_value) + '\n'),
            data.push(size(1, 1, language));
        data.push(bold(false, language));
    }

    buffer.push(bold(true, language))
    buffer.push(normalizeText(site[9].config_value + "\n"))
    buffer.push(normalizeText("Tel.: " + site[13].config_value + "\n"))
    buffer.push(bold(false, language))
    buffer.push(feed(1))

    // ================== INFORMACIÓN FACTURA ================== //

    buffer.push(align("left", language))
    buffer.push(normalizeText("Factura #: FT-00" + dataInv.invoice_id + "\n"))
    buffer.push("Fecha: " + dataInv.date + "\n")
    buffer.push(normalizeText("Fecha impresión: " + await getPrintDate() + "\n"))
    buffer.push(normalizeText("Condición: " + dataInv.payment_method.toUpperCase() + "\n"))
    buffer.push(normalizeText("Cliente: " + dataInv.customer.toUpperCase() + "\n"))
    buffer.push(normalizeText("Usuario: " + dataInv.seller.toUpperCase() + "\n"))
    buffer.push(feed(1, language))


    // ================== TIPO FACTURA ================== //
    buffer.push(align('center'))
    buffer.push(bold(true))
    if (!dataInv.pending || dataInv.pending == 0) {
        buffer.push("*** FACTURA CONTADO ***\n")
    } else {
        buffer.push("*** FACTURA A CRÉDITO ***\n")
    }

    // ================== DETALLE ================== //
    buffer.push(align('left'))
    buffer.push(line(W, "-", language))
    buffer.push("DESCRIPCION                  ITBIS     VALOR\n")
    buffer.push(line(W, "-", language))
    buffer.push(bold(false))

    detail.forEach(item => {
        let cant = parseFloat(item[4]);
        let precio = parseFloat(item[1]);
        let impuesto = parseFloat(item[7] ?? 0) || 0;

        let valor = cant * precio;

        // 🔹 Formatear cantidad
        if (cant % 1 === 0) {
            cant = parseInt(cant);
        } else {
            cant = cant.toFixed(2);
        }

        // Línea principal
        let linea =
            (cant + " x " + formatMoney(precio)).padEnd(28, ' ') +
            ((formatMoney(cant * impuesto))).padStart(10, ' ') +
            (formatMoney(valor)).padStart(10, ' ');

        buffer.push(linea + "\n");

        // Descripción
        let descripcion = '';

        if (item[0]) descripcion = item[0];
        else if (item[2]) descripcion = item[2];
        else if (item[3]) descripcion = item[3];

        if (descripcion) {

            if (descripcion.length > 46) {
                descripcion = descripcion.substring(0, 43) + '...';
            }

            buffer.push(bold(true, language)); // bold ON
            const descripcionSinSaltos = descripcion.replace(/(\r\n|\n|\r)/gm, " "); // Reemplaza saltos de línea por espacio
            buffer.push(normalizeText(descripcionSinSaltos) + "\n");
            buffer.push(bold(false, language)); // bold OFF
        }
    });

    // ================== TABLA DE PRECIO ================== //

    buffer.push(bold(true, language));
    buffer.push(line(W, "-", language));
    buffer.push(bold(false, language));

    // (solo si hay pendiente)
    if (dataInv.pending && parseFloat(dataInv.pending) > 0) {
        buffer.push(align('left', language));
        buffer.push(
            padRight("Recibido", 20) +
            "$ " +
            padLeft(formatMoney(parseFloat(dataInv.received)), 10) + "\n"
        );

        buffer.push(
            padRight("Balance Pendiente", 20) +
            "$ " +
            padLeft(formatMoney(parseFloat(dataInv.pending)), 10) + "\n"
        );

        buffer.push(feed(1));
    }

    // Subtotal
    buffer.push(
        padRight("Subtotal", 20) +
        "$ " +
        padLeft(formatMoney(parseFloat(dataInv.subtotal)), 10) + "\n"
    );

    // Impuesto
    buffer.push(
        padRight("+ Impuesto", 20) +
        "$ " +
        padLeft(formatMoney(parseFloat(dataInv.taxes)), 10) + "\n"
    );

    // Descuento
    buffer.push(
        padRight("- Descuento", 20) +
        "$ " +
        padLeft(formatMoney(parseFloat(dataInv.discount)), 10) + "\n"
    );

    buffer.push(bold(true, language));
    buffer.push(line(W, "-", language));

    buffer.push(align("left", language));
    buffer.push(size(2, 2, language));
    buffer.push(
        padRight("TOTAL", 6) +
        " " +
        padLeft("$" + formatMoney(parseFloat(dataInv.total)), 8) + "\n"
    );
    buffer.push(size(1, 1, language));
    buffer.push(bold(false, language));
    buffer.push(feed(1, language));

    // ================== NOTAS ================== //
    if (dataInv.observation && dataInv.observation.trim() !== "") {
        buffer.push(bold(true, language));
        buffer.push("Nota:\n");
        buffer.push(bold(false, language));
        buffer.push(dataInv.observation + "\n");
        buffer.push(feed(1));
    }

    // ===== GARANTÍA Y DESPACHADOR ====== //

    buffer.push(bold(true, language));
    buffer.push(normalizeText(printer.policy_footer + "\n"))
    buffer.push(bold(true, language));

    if (printer.signature > 0) {
        buffer.push(feed(1, language));
        buffer.push(align("center", language));
        buffer.push(bold(true, language));
        buffer.push(line(W, "-", language))
        buffer.push("Despachado por" + "\n")
        buffer.push(bold(false, language));
    }

    buffer.push(feed(1, language));
    buffer.push(align("center", language))
    buffer.push(normalizeText(printer.ticket_footer))

    // ============= QR y BARCODE ============ //

    if (printer.use_barcode > 0) {
        buffer.push(feed(1, language));
        buffer.push(align('center', language));
        buffer.push(
            generateBarcodeCommand(
                dataInv.invoice_id,
                language,
                printer.barcode_width,
                printer.barcode_height
            )
        );
    }

    // ======== CIERRE ======= //

    if (printer.feed_end > 0) buffer.push(feed(printer.feed_end, language));
    if (printer.auto_cut > 0) buffer.push(cutPaper(language));

    data.push({
        type: 'raw',
        format: 'command',
        data: buffer.get() // Obtener el contenido del buffer
    });

    // ======= PRINT ======
    qz.print(config, data).catch(console.error);
}


/**
 * Genera e imprime una orden de venta en las impresoras configuradas
 * usando QZ Tray.
 *
 * @param {Array<Array<any>>} detail 
 * Array con el detalle de productos/servicios.
 *
 * @param {Object} info 
 *
 * @returns {Promise<void>} Promesa que se resuelve cuando termina la impresión.
 */
export async function orden_venta(detail, info) {

    async function buildTicket(printer, site, detail, info) {

        const language = printer.language;
        const paperSize = parseInt(printer.paper_width) + 'mm';
        const W = getPrinterWidth(paperSize);

        const buffer = createPrintBuffer();  // Creamos el buffer de impresión
        const data = []

        // ================== CONFIG LOGO ==================
        let logoBase64 = null;

        try {
            logoBase64 = await convertImageUrlToBase64(SITE_URL + 'public/uploads/' + site[7].config_value);
        } catch (e) {
            console.warn('Logo no disponible, usando texto');
        }

        data.push(initPrinter(language));

        if (printer.feed_start > 0) buffer.push(feed(printer.feed_start, language));
        data.push(align('center', language));

        // 👉 LOGO FUERA DEL BUFFER
        if (logoBase64) {
            data.push({
                type: 'raw',
                format: 'image',
                flavor: 'base64',
                data: logoBase64,
                options: {
                    language: "ESCPOS",
                    dotDensity: printer.logo_density
                }
            });

        } else {
            data.push(bold(true, language));
            data.push(size(2, 2, language));
            data.push(normalizeText(site[0].config_value) + '\n'),
                data.push(size(1, 1, language));
            data.push(bold(false, language));
        }

        data.push(initPrinter(language))

        buffer.push(align('center', language));
        buffer.push(bold(true, language))
        buffer.push(normalizeText(site[9].config_value + "\n"))
        buffer.push(normalizeText("Tel.: " + site[13].config_value + "\n"))
        buffer.push(bold(false, language))
        buffer.push(feed(1))

        // ================== INFORMACIÓN FACTURA ================== //

        buffer.push(align("left", language))
        buffer.push(normalizeText("Orden: OV-00" + info.order_id + "\n"))
        buffer.push("Fecha: " + info.fecha + "\n")
        buffer.push(normalizeText("Fecha impresión: " + await getPrintDate() + "\n"))
        buffer.push(normalizeText("Cliente: " + info.nombre.toUpperCase() + "\n"))
        buffer.push(normalizeText("Cajero(a): " + info.cajero.toUpperCase() + "\n"))
        buffer.push(
            normalizeText(
                "Teléfono: " + ((info.telefono1 ?? "").toString().toUpperCase()) + "\n"
            )
        );
        buffer.push(feed(1, language))

        buffer.push(line(W, "-", language));

        if (info.nombre_receptor && info.nombre_receptor.trim() !== "") {
            buffer.push(normalizeText("Recibe.: " + info.nombre_receptor.toUpperCase() + "\n"))
        }
        if (info.telefono_receptor && info.telefono_receptor !== "") {
            buffer.push("Tel.: " + info.telefono_receptor + "\n")
        }
        if (info.tipo_entrega && info.tipo_entrega !== "") {
            buffer.push("Entrega.: " + info.tipo_entrega.toUpperCase() + "\n")
        }
        if (info.direccion_entrega && info.direccion_entrega !== "") {
            buffer.push(normalizeText("Dirección.: " + info.direccion_entrega.toUpperCase() + "\n"))
        }
        if (info.observacion && info.observacion !== "") {
            buffer.push(normalizeText("Observación.: " + info.observacion + "\n"))
        }

        buffer.push(feed(1, language))
        buffer.push(align("center", language))
        buffer.push(bold(true, language))
        buffer.push("*** ORDEN DE VENTA ***" + "\n")
        buffer.push(bold(false, language))

        // ================== DETALLE ================== //
        buffer.push(align("left", language))
        buffer.push(bold(true, language))
        buffer.push(line(W, "-", language))
        buffer.push("DESCRIPCION                  ITBIS     VALOR" + "\n")
        buffer.push(line(W, "-", language))
        buffer.push(bold(false, language))

        detail.forEach(item => {

            let cant = parseFloat(item[4]);               // cantidad
            let precio = parseFloat(item[0]);               // precio
            let desc = item[2] || '';                     // descripcion
            let impuesto = parseFloat(item[5]);               // impuesto

            // 🔹 Validar impuesto (si viene null, undefined o NaN)
            if (!impuesto || isNaN(impuesto)) {
                impuesto = 0;
            }

            let valor = cant * precio;
            let totalImpuesto = cant * impuesto;

            // 🔹 Formatear cantidad
            if (cant % 1 === 0) {
                cant = parseInt(cant);
            } else {
                cant = cant.toFixed(2);
            }

            // 🔹 Línea principal
            let linea =
                (cant + " x " + formatMoney(precio)).padEnd(28, ' ') +
                (formatMoney(totalImpuesto)).padStart(10, '  ') +
                (formatMoney(valor)).padStart(10, ' ');

            buffer.push(linea + "\n");

            // 🔹 Descripción
            if (desc) {

                if (desc.length > 46) {
                    desc = desc.substring(0, 43) + '...';
                }

                buffer.push(bold(true, language));  // ON
                const descripcionSinSaltos = desc.replace(/(\r\n|\n|\r)/gm, " "); // Reemplaza saltos de línea por espacio
                buffer.push(normalizeText(descripcionSinSaltos) + "\n");
                buffer.push(bold(false, language)); // OFF
            }

        });

        // ================== TABLA DE PRECIO ================== //
        buffer.push(bold(true, language));  // ON
        buffer.push(line(W, "-", language) + "\n");
        buffer.push(bold(false, language)); // OFF

        buffer.push(padRight("Subtotal", 20) + "$ " +
            padLeft(formatMoney(parseFloat(info.subtotal)), 10) + "\n"
        );

        buffer.push(padRight("+ Impuesto", 20) + "$ " +
            padLeft(formatMoney(parseFloat(info.taxes)), 10) + "\n"
        );

        buffer.push(padRight("- Descuento", 20) + "$ " +
            padLeft(formatMoney(parseFloat(info.discount)), 10) + "\n"
        );

        buffer.push(bold(true, language));
        buffer.push(line(W, "-", language));

        buffer.push(align("left", language));
        buffer.push(size(2, 2, language));
        buffer.push(
            padRight("TOTAL", 6) +
            " " +
            padLeft("$" + formatMoney(parseFloat(info.total)), 8) + "\n"
        );
        buffer.push(size(1, 1, language));
        buffer.push(bold(false, language));
        buffer.push(feed(1, language));

        // ================== PIE DE PÁGINA ================== //

        buffer.push(align("center", language));
        buffer.push(bold(true, language));
        buffer.push("ESTADO DE FACTURA: PENDIENTE\n")
        buffer.push(bold(false, language));
        buffer.push("Este documento es solo una orden\n")

        if (printer.signature > 0) {
            buffer.push(feed(1, language));
            buffer.push(align("center", language));
            buffer.push(bold(true, language));
            buffer.push(line(W, "-", language))
            buffer.push("Firma de conformidad" + "\n")
            buffer.push(bold(false, language));
        }

        // ============= QR y BARCODE ============ //

        if (printer.use_barcode > 0) {
            buffer.push(feed(1, language));
            buffer.push(align('center', language));
            buffer.push(
                generateBarcodeCommand(
                    info.order_id,
                    language,
                    printer.barcode_width,
                    printer.barcode_height
                )
            );
        }

        // ======== CIERRE ======= //

        if (printer.feed_end > 0) buffer.push(feed(printer.feed_end, language));
        if (printer.auto_cut > 0) buffer.push(cutPaper(language));

        data.push({
            type: 'raw',
            format: 'command',
            data: buffer.get() // Obtener el contenido del buffer
        })

        return data;
    }


    // Datos predeterminados
    const printer_config = await getData();
    const printers = printer_config[0];
    const site = printer_config[1]; // Datos del sitio

    const printJobs = printers.map(async (printer) => {

        const config = qz.configs.create(
            printer.printer_name,
            { copies: printer.copies }
        );

        const buffer = await buildTicket(printer, site, detail, info); // Constructor

        return qz.print(config, buffer)
            .then(() => {
                console.log("%c[QZ]", "color:#1976d2;font-weight:bold;", "Impresión exitosa en:", printer.printer_name);
            })
            .catch(err => {
                console.error("%c[QZ]", "color:#df1212;font-weight:bold;", "Error en:", printer.printer_name, err);
            });
    });

    await Promise.all(printJobs).catch(console.error);
}


/**
 * Imprime el cierre de caja con la información proporcionada.
 * 
 * Esta función toma un objeto `info` que contiene los detalles del cierre de caja,
 * como el total de ingresos, egresos, diferencias, etc. Luego, genera e imprime el
 * reporte de cierre de caja de forma asincrónica.
 * 
 * @async
 * @function
 * @param {Object} info - Información del cierre de caja.
 */
export async function cierre_caja(info) {

    // Datos predeterminados
    const printer_config = await getData();
    const printers = printer_config[0]; // Impresoras
    const site = printer_config[1]; // Datos del sitio

    const printer = printers.find(p =>
        p.printer_type === "main"
    );

    if (!printer) {
        console.error("No main printer configured");
        return;
    }

    // Configuracion de impresion

    const paperSize = parseInt(printer.paper_width) + 'mm'; // Aquí puedes cambiarlo a '58mm' si necesitas otro tamaño
    const W = getPrinterWidth(paperSize);  // Calculamos el ancho en función del tamaño del papel

    const language = printer.printer_language;

    const config = qz.configs.create(printer.printer_name, { copies: printer.copies });
    const buffer = createPrintBuffer();  // Creamos el buffer de impresión
    const data = [];

    // ================== CONFIG LOGO ==================
    let logoBase64 = null;

    try {
        logoBase64 = await convertImageUrlToBase64(SITE_URL + 'public/uploads/' + site[7].config_value);
    } catch (e) {
        console.warn('Logo no disponible, usando texto');
    }

    data.push(initPrinter(language));

    if (printer.feed_start > 0) buffer.push(feed(printer.feed_start, language));
    data.push(align('center', language));

    // 👉 LOGO FUERA DEL BUFFER
    if (logoBase64) {
        data.push({
            type: 'raw',
            format: 'image',
            flavor: 'base64',
            data: logoBase64,
            options: {
                language: "ESCPOS",
                dotDensity: printer.logo_density
            }
        });

    } else {
        data.push(bold(true, language));
        data.push(size(2, 2, language));
        data.push(normalizeText(site[0].config_value) + '\n'),
            data.push(size(1, 1, language));
        data.push(bold(false, language));
    }

    buffer.push(align("center", language))
    buffer.push(bold(true, language))
    buffer.push("CIERRE DE LA CAJA", "\n")
    buffer.push(bold(false, language))

    buffer.push("Fecha: " + info.opening_date + "\n")
    buffer.push("Cajero: " + info.user_name + "\n")
    buffer.push(feed(1, language))


    // ================== DETALLES DE CIERRE ================== //

    buffer.push(align("left", language))
    buffer.push(line(W, "-", language))
    buffer.push("Cierre N°: " + info.cierre_id + "\n")
    buffer.push("Fecha de Apertura: " + info.opening_date + "\n")
    buffer.push("Fecha de Cierre: " + info.closing_date + "\n")
    buffer.push("Monto Inicial: \$" + info.initial_balance + "\n")
    buffer.push(feed(1, language))

    // ================== RESUMEN DE INGRESOS ================== //

    buffer.push(line(W, "-", language))
    buffer.push(bold(true, language))
    buffer.push("RESUMEN DE INGRESOS", "\n")
    buffer.push(bold(false, language))
    buffer.push(feed(1, language))

    buffer.push(padRight("Efectivo:", 25) + padLeft(formatMoney(info.cash_income), 15) + "\n");
    buffer.push(padRight("Transferencias:", 25) + padLeft(formatMoney(info.transfer_income), 15) + "\n");
    buffer.push(padRight("Tarjeta:", 25) + padLeft(formatMoney(info.card_income), 15) + "\n");
    buffer.push(padRight("Cheques:", 25) + padLeft(formatMoney(info.check_income), 15) + "\n");
    buffer.push(feed(1, language))

    // ================== RESUMEN DE GASTOS ================== //

    buffer.push(line(W, "-", language))
    buffer.push(bold(true, language))
    buffer.push("RESUMEN DE GASTOS", "\n")
    buffer.push(bold(false, language))
    buffer.push(feed(1, language))

    // Alineación de los datos a la derecha con formato de tabla
    buffer.push(padRight("Gastos de caja:", 25) + padLeft(formatMoney(info.cash_expenses), 15) + "\n");
    buffer.push(padRight("Gastos fuera de caja:", 25) + padLeft(formatMoney(info.external_expenses), 15) + "\n");
    buffer.push(padRight("Reembolsos:", 25) + padLeft(formatMoney(info.refunds), 15) + "\n");
    buffer.push(padRight("Retiros:", 25) + padLeft(formatMoney(info.withdrawals), 15) + "\n");
    buffer.push(feed(1, language))

    // ================== RESUMEN DE VENTAS ================== //

    buffer.push(line(W, "-", language))
    buffer.push(bold(true, language))
    buffer.push("ESTADO DE CAJA", "\n")
    buffer.push(bold(false, language))
    buffer.push(feed(1, language))

    // Alineación de los datos a la derecha con formato de tabla
    buffer.push(padRight("Total Real Vendido:", 25) + padLeft(formatMoney(info.total), 15) + "\n");
    buffer.push(padRight("Total Esperado:", 25) + padLeft(formatMoney(info.total_expected), 15) + "\n");
    buffer.push(padRight("Total Efectivo en Caja:", 25) + padLeft(formatMoney(info.current_total), 15) + "\n");
    buffer.push(padRight("Diferencia:", 25) + padLeft(formatMoney(info.difference), 15) + "\n");
    buffer.push(feed(1, language))

    // ================== TOTAL DE TICKETS EMITIDOS ================== //

    buffer.push(padRight("N° Tickets:", 25) + padLeft(formatMoney(info.tickets_invoices), 15) + "\n");
    buffer.push(feed(1, language))

    // ================== NOTAS ================== //

    buffer.push(line(W, "-", language))
    buffer.push(bold(true, language))
    buffer.push("Notas:", "\n")
    buffer.push(bold(false, language))
    buffer.push(info.notes, "\n")
    buffer.push(feed(1, language))

    // ================== MENSAJE FINAL ================== //
    buffer.push(align("center", language))
    buffer.push("Generado por wsistems.com" + "\n")

    // ======== CIERRE ======= //
    if (printer.feed_end > 0) buffer.push(feed(printer.feed_end, language));
    if (printer.auto_cut > 0) buffer.push(cutPaper(language));

    data.push({
        type: 'raw',
        format: 'command',
        data: buffer.get() // Obtener el contenido del buffer
    });

    // ======= PRINT ======
    qz.print(config, data)
        .then(() => {
            console.log("%c[QZ]", "color:#1976d2;font-weight:bold;", "Impresión exitosa en:", printer.printer_name);
        })
        .catch(err => {
            console.error("%c[QZ]", "color:#df1212;font-weight:bold;", "Error en:", printer.printer_name, err);
        })
        .catch(console.error);

}


/**
 * Genera e imprime el comprobante de gastos.
 *
 * Esta función construye el contenido a imprimir incluyendo:
 * - Información general del gasto (proveedor, vendedor, totales, etc.)
 * - Detalle de los conceptos (motivos, cantidades, precios e impuestos)
 * - Logo de la empresa (si está disponible)
 *
 * @async
 * @function gastos
 * @param {Object|Object[]} info - Información principal de la orden de gasto.
 * @param {Array<Object>} detail - Lista de detalles del gasto.
 *
 * @returns {Promise<void>} No retorna valor. Ejecuta el proceso de impresión.
 */
export async function gastos(info, detail) {

    // Datos predeterminados
    const printer_config = await getData();
    const printers = printer_config[0]; // Impresoras
    const site = printer_config[1]; // Datos del sitio

    const printer = printers.find(p =>
        p.printer_type === "main"
    );

    if (!printer) {
        console.error("No main printer configured");
        return;
    }

    // Configuracion de impresion

    const paperSize = parseInt(printer.paper_width) + 'mm'; // Aquí puedes cambiarlo a '58mm' si necesitas otro tamaño
    const W = getPrinterWidth(paperSize);  // Calculamos el ancho en función del tamaño del papel

    const language = printer.printer_language;

    const config = qz.configs.create(printer.printer_name, { copies: printer.copies });
    const buffer = createPrintBuffer();  // Creamos el buffer de impresión
    const data = [];

    // ================== CONFIG LOGO ==================
    let logoBase64 = null;

    try {
        logoBase64 = await convertImageUrlToBase64(SITE_URL + 'public/uploads/' + site[7].config_value);
    } catch (e) {
        console.warn('Logo no disponible, usando texto');
    }

    data.push(initPrinter(language));

    if (printer.feed_start > 0) buffer.push(feed(printer.feed_start, language));
    data.push(align('center', language));

    // 👉 LOGO FUERA DEL BUFFER
    if (logoBase64) {
        data.push({
            type: 'raw',
            format: 'image',
            flavor: 'base64',
            data: logoBase64,
            options: {
                language: "ESCPOS",
                dotDensity: printer.logo_density
            }
        });

    } else {
        data.push(bold(true, language));
        data.push(size(2, 2, language));
        data.push(normalizeText(site[0].config_value) + '\n'),
            data.push(size(1, 1, language));
        data.push(bold(false, language));
    }

    buffer.push(bold(true, language))
    buffer.push(normalizeText(site[9].config_value + "\n"))
    buffer.push(normalizeText("Tel.: " + site[13].config_value + "\n"))
    buffer.push(bold(false, language))
    buffer.push(feed(1))

    // ================== INFORMACIÓN FACTURA ================== //

    buffer.push(align("left", language))
    buffer.push(normalizeText("No.: G-00" + info.gasto_id + "\n"))
    buffer.push("Fecha: " + info.fecha + "\n")
    buffer.push(normalizeText("Proveedor: " + info.proveedor  + "\n"));
    buffer.push(feed(1, language))

    buffer.push(feed(1, language))
    buffer.push(align("center", language))
    buffer.push(bold(true, language))
    buffer.push("*** GASTO REGISTRADO ***" + "\n")
    buffer.push(bold(false, language))

    // ================== DETALLE ================== //
    buffer.push(align("left", language))
    buffer.push(bold(true, language))
    buffer.push(line(W, "-", language))
    buffer.push("DESCRIPCION                  ITBIS     VALOR" + "\n")
    buffer.push(line(W, "-", language))
    buffer.push(bold(false, language))

    let subtotal = 0;
    let impuestos = 0;
    let total = 0;

    detail.forEach(item => {

        const cant = parseFloat(item.cantidad) || 0;
        const precio = parseFloat(item.precio) || 0;
        const impuesto = parseFloat(item.impuestos) || 0;
        let desc = item.descripcion || '';

        const valor = cant * precio;
        const totalImpuesto = cant * impuesto;

        // 🔹 acumular totales
        subtotal += valor;
        impuestos += totalImpuesto;

        // 🔹 Formato cantidad
        const cantFormat = Number.isInteger(cant) ? cant : cant.toFixed(2);

        // 🔹 Línea principal
        const linea =
            `${cantFormat} x ${formatMoney(precio)}`.padEnd(28, ' ') +
            `${formatMoney(totalImpuesto)}`.padStart(10, ' ') +
            `${formatMoney(valor)}`.padStart(10, ' ');

        buffer.push(linea + "\n");

        // 🔹 Descripción
        if (desc) {

            desc = desc.replace(/(\r\n|\n|\r)/gm, " ");

            if (desc.length > 46) {
                desc = desc.substring(0, 43) + "...";
            }

            buffer.push(bold(true, language));
            buffer.push(normalizeText(desc) + "\n");
            buffer.push(bold(false, language));
        }

    });

    // 🔹 calcular total
    total = subtotal + impuestos;

    // ================== TABLA DE PRECIO ================== //
    buffer.push(bold(true, language));
    buffer.push(line(W, "-", language) + "\n");
    buffer.push(bold(false, language));

    buffer.push(
        padRight("Subtotal", 20) + "$ " +
        padLeft(formatMoney(subtotal), 10) + "\n"
    );

    buffer.push(
        padRight("+ Impuesto", 20) + "$ " +
        padLeft(formatMoney(impuestos), 10) + "\n"
    );

    buffer.push(bold(true, language));
    buffer.push(line(W, "-", language));

    buffer.push(align("left", language));
    buffer.push(size(2, 2, language));

    buffer.push(
        padRight("TOTAL", 6) +
        " " +
        padLeft("$" + formatMoney(total), 8) + "\n"
    );

    buffer.push(size(1, 1, language));
    buffer.push(bold(false, language));
    buffer.push(feed(1, language));

    buffer.push("Generado por: " + (info.vendedor || "Sistema") + "\n");
    buffer.push(normalizeText("Fecha impresión: " + await getPrintDate() + "\n"))
    buffer.push(feed(1, language));

    buffer.push(align("center", language));
    buffer.push(bold(true, language));
    buffer.push("---- FIN DEL TICKET ----\n");
    buffer.push(bold(false, language));

    // ======== CIERRE ======= //
    if (printer.feed_end > 0) buffer.push(feed(printer.feed_end, language));
    if (printer.auto_cut > 0) buffer.push(cutPaper(language));

    data.push({
        type: 'raw',
        format: 'command',
        data: buffer.get() // Obtener el contenido del buffer
    });

    // ======= PRINT ======
    qz.print(config, data)
        .then(() => {
            console.log("%c[QZ]", "color:#1976d2;font-weight:bold;", "Impresión exitosa en:", printer.printer_name);
        })
        .catch(err => {
            console.error("%c[QZ]", "color:#df1212;font-weight:bold;", "Error en:", printer.printer_name, err);
        })
        .catch(console.error);

}

