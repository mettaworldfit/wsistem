$(document).ready(function () {

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
    function initPrinter(language = 'ESCPOS') {
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
    function createPrintBuffer(language = 'ESCPOS') {
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
    function normalizeText(text) {
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
    function line(W, char = '-', language = 'ESCPOS') {
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
    function bold(enable = true, language = 'ESCPOS') {
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
    function font(font = 'A', language = 'ESCPOS') {
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
    function size(width = 1, height = 1, language = 'ESCPOS') {
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
    function feed(n = 1, language = 'ESCPOS') {
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
    function align(align = 'left', language = 'ESCPOS') {
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
    function cutPaper(language = 'ESCPOS') {
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
    function openCashDrawer(language = 'ESCPOS') {
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
    function getPrinterWidth(paperSize = '80mm') {
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
    function generateBarcodeCommand(code, language = 'ESCPOS', width = 1, height = 80) {
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
    /**
    * Convierte una imagen desde una URL a Base64 sin el prefijo data:image.
    * @param {string} imageUrl
    * @returns {Promise<string>}
    */
    function convertImageUrlToBase64(imageUrl) {
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
    function generateQRCommand(data, size = 6) {
        const chr = n => String.fromCharCode(n);  // Función para convertir números en caracteres
        const len = data.length + 3;  // Calculamos la longitud del dato

        // Comando para generar el código QR en ESCPOS con el tamaño proporcionado.
        return (
            '\x1D(k' + chr(4) + chr(0) + '1A' + chr(size) + '\x00' +  // Configuración de tamaño
            '\x1D(k' + chr(len) + chr(0) + '1P0' + data +  // Codificación del dato
            '\x1D(k' + chr(3) + chr(0) + '1Q0'  // Fin del comando de QR
        );
    }


    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result.split(',')[1]);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
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
    function detectLanguageByName(printerName) {

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

    /**
     * Intenta detectar el lenguaje enviando comandos mínimos
     * @param {string} printer
     * @returns {Promise<string|null>}
     */
    async function probePrinterLanguage(printer) {

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
    async function detectPrinterLanguage(printer) {

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
            notifyAlert(
                'No se pudo conectar con QZ Tray',
                'error',
                5000
            );
        });

    $('#impresoraSelect').on('change', async function () {

        const printer = $(this).val();
        if (!printer) return;

        manualLanguageChanged = false;

        autoDetectedLanguage = await detectPrinterLanguage(printer);

        $('[name="printer_language"]')
            .val(autoDetectedLanguage)
            .trigger('change');

        // notifyAlert(
        //     `Lenguaje detectado automáticamente: ${autoDetectedLanguage}`,
        //     'info',
        //     3000
        // );
    });

    $('[name="printer_language"]').on('change', function () {
        manualLanguageChanged = true;
        comparePrinterLanguages();
    });

    /**
     * Compara el lenguaje detectado automáticamente
     * contra el seleccionado manualmente
     */
    function comparePrinterLanguages() {

        if (!autoDetectedLanguage) return;

        const manualLang = $('[name="printer_language"]').val();
        if (!manualLanguageChanged) return;

        // Coinciden
        if (manualLang === autoDetectedLanguage) {
            notifyAlert(
                `Lenguaje confirmado: ${manualLang}`,
                'success',
                2000
            );
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
    function getSelectedPrinter() {
        return $('#impresoraSelect').val();
    }

    function getSelectedLanguage() {
        return $('[name="printer_language"]').val() || 'ESCPOS';
    }

    // Boton
    $('#btnQzDiagnostico').on('click', function () {
        runQzDiagnostic();
    });


    /* =========================
       DIAGNÓSTICO QZ
    ========================= */
    async function runQzDiagnostic() {

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
                logoBase64 = await convertImageUrlToBase64(
                    SITE_URL + 'public/imagen/sistem/pdf.png'
                );
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

    $("#printTest").on('click', function () {
        printExample();
    });

    // Imprimir imagen de prueba
    function printImageExample() {
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

    // Prueba de ticket
    async function printExample() {

        // ================== DATOS MOCK ==================
        const negocio = {
            nombre: "SUPERMERCADO EL AHORRO",
            direccion1: "Av. Duarte #123",
            direccion2: "Santo Domingo, R.D.",
            telefono: "809-555-8899",
            firma: true
        };

        const dataOrder = {
            orderId: "1234",
            fecha: "06/02/2026 09:30 AM",
            cajero: "María López",
            nombre: "José Pérez",
            telefono1: "809-222-3344",
            nombre_receptor: "Ana Rodríguez",
            telefono_receptor: "809-333-4455",
            tipo_entrega: "DELIVERY",
            direccion_entrega: "Calle 10, Los Prados",
            observacion: "Entregar antes de las 12:00 PM"
        };

        const arr = [
            [150.00, 0, "Café expreso grande", 0, 1],
            [120.00, 0, "Sandwich mixto especial", 0, 2],
            [90.00, 0, "Jugo natural de naranja", 0, 1],
            [50.00, 0, "Empanada de pollo", 0, 3]
        ];

        const info = {
            subtotal: 610.00,
            discount: 50.00,
            total: 560.00
        };

        // ================== FORM CONFIGURACION ==================

        const form = document.getElementById('formPrinter');
        const f = new FormData(form);

        const printer = f.get('impresora');
        const language = f.get('printer_language') || 'ESCPOS';
        const paperWidth = parseInt(f.get('paper_width')) || 80;
        const copies = parseInt(f.get('copies')) || 1;
        const autoCut = f.get('auto_cut') === '1';

        const feedStart = parseInt(f.get('feed_start')) || 0;
        const feedEnd = parseInt(f.get('feed_end')) || 0;

        const useBarcode = f.get('use_barcode') === '1';
        const barcodeHeight = parseInt(f.get('barcode_height')) || 80;
        const barcodeWidth = parseInt(f.get('barcode_width')) || 2;

        const useQr = f.get('use_qr') === '1';
        const qrSize = parseInt(f.get('qr_size')) || 6;

        const logoDensity = f.get('logo_density') || 'single';
        const companyName = f.get('company_name');


        // const language = '';  // Detecta el lenguaje automáticamente

        const paperSize = '80mm'; // Aquí puedes cambiarlo a '58mm' si necesitas otro tamaño
        const W = getPrinterWidth(paperSize);  // Calculamos el ancho en función del tamaño del papel

        const config = qz.configs.create(printer, { copies: copies });
        const buffer = createPrintBuffer();  // Creamos el buffer de impresión

        // ================== CONFIG LOGO ==================
        let logoBase64 = null;

        try {
            logoBase64 = await convertImageUrlToBase64(SITE_URL + 'public/imagen/sistem/chino_com.png');
        } catch (e) {
            console.warn('Logo no disponible, usando texto');
        }

        // let logoBase64 = null;
        // const logoFile = f.get('logo');

        // if (logoFile && logoFile.size > 0) {
        //     logoBase64 = await fileToBase64(logoFile);
        // }

        // ================== INIT ==================
        const data = [];
        data.push(initPrinter(language));

        // if (feedStart > 0) buffer.push(feed(feedStart, language));
        // data.push(align('center', language));

        // // 👉 LOGO FUERA DEL BUFFER
        // if (logoBase64) {
        //     data.push({
        //         type: 'raw',
        //         format: 'image',
        //         flavor: 'base64',
        //         data: logoBase64,
        //         options: {
        //             language: 'ESCPOS',
        //             dotDensity: logoDensity
        //         }
        //     });

        // } else {
        //     data.push(bold(true, language));
        //     data.push(size(2, 2, language));
        //     data.push(normalizeText(companyName) + '\n'),
        //         data.push(size(1, 1, language));
        //     data.push(bold(false, language));
        //     data.push(feed(1, language));
        // }

        // // ================== ENCABEZADO ==================
        // buffer.push(
        //     align('center'),
        //     normalizeText(negocio.direccion1) + '\n',
        //     normalizeText(negocio.direccion2) + '\n',
        //     `Tel.: ${negocio.telefono}\n`,
        //     line(W)
        // );

        // // ================== DATOS ==================
        // buffer.push(
        //     align('left'),
        //     `FACTURA: FT-00${dataOrder.orderId}\n`,
        //     `Fecha: ${dataOrder.fecha}\n`,
        //     normalizeText('Cajero: ' + dataOrder.cajero + '\n'),
        //     normalizeText('Cliente: ' + dataOrder.nombre + '\n'),
        //     normalizeText('Teléfono: ' + dataOrder.telefono1 + '\n'),
        // );

        // // ================== TITULO ==================
        // buffer.push(
        //     feed(),
        //     align('center'),
        //     bold(true),
        //     normalizeText('*** FACTURA DE VENTA ***\n'),
        //     bold(false)
        // );

        // // ================== DETALLE ==================
        // buffer.push(
        //     align('left'),
        //     line(W),
        //     normalizeText('CANT  DESCRIPCIÓN').padEnd(34) + 'VALOR'.padStart(14) + '\n',
        //     line(W)
        // );

        // arr.forEach(row => {
        //     let precio = row[0];
        //     let desc = row[2];
        //     let cant = row[4];
        //     let total = precio * cant;

        //     buffer.push(
        //         `${cant}`.padEnd(6) +
        //         normalizeText(desc).substring(0, 28).padEnd(28) +
        //         total.toFixed(2).padStart(14) + '\n'
        //     );
        // });

        // // ================== TOTALES ==================
        // buffer.push(
        //     line(W),
        //     normalizeText('Subtotal').padEnd(34) + info.subtotal.toFixed(2).padStart(14) + '\n',
        //     normalizeText('Descuento').padEnd(34) + info.discount.toFixed(2).padStart(14) + '\n',
        //     bold(true),
        //     normalizeText('TOTAL').padEnd(34) + info.total.toFixed(2).padStart(14) + '\n',
        //     bold(false),
        //     feed(2)
        // );

        // ================== CÓDIGO DE BARRAS ==================

        if (useBarcode) {
            buffer.push(feed(1, language));
            buffer.push(align('center', language));
            buffer.push(
                generateBarcodeCommand(
                    '88556321',
                    language,
                    barcodeWidth,
                    barcodeHeight
                )
            );
        }

        // ================= QR =================
        if (useQr) {
            buffer.push(feed(1, language));
            buffer.push(align('center', language));
            buffer.push(generateQRCommand('https://codevrd.com', qrSize));
        }


        // ================== PIE ==================
        // buffer.push(
        //     feed(2, language),
        //     normalizeText(f.get('ticket_footer') || '') + '\n'
        // );
        // ================== FINAL ==================

        if (feedEnd > 0) buffer.push(feed(feedEnd, language));
        if (autoCut > 0) buffer.push(cutPaper(language));

        // 👉 TEXTO DEL BUFFER AL ARRAY
        data.push({
            type: 'raw',
            format: 'command',
            data: buffer.get() // Obtener el contenido del buffer
        });

        // ================== PRINT ==================
        qz.print(config, data).catch(console.error);
    }




    //   $('#launch').on('click', function () {
    //     /* ===== CONEXIÓN SEGURA ===== */
    //     const connectQZ = qz.websocket.isActive()
    //         ? Promise.resolve()
    //         : qz.websocket.connect();

    //     connectQZ.then(() => {
    //         console.log("Conexión establecida con QZ Tray.");
    //         return qz.printers.find("POS-80");
    //     })
    //         .then(printer => {
    //             console.log("Impresora encontrada:", printer);
    //             const config = qz.configs.create(printer, {
    //                 copies: 1,
    //                 units: "mm",
    //                 size: { width: 80 },
    //                 margins: { top: 0, right: 0, bottom: 0, left: 0 }
    //             });

    //             const printData = [
    //                 '\x1B\x40',       // INIT (inicia la impresora)
    //                 '\x1B\x61\x01',   // CENTRAR
    //                 'Prueba de Impresión\n',  // Texto de prueba
    //                 '\x1B\x61\x00',   // IZQUIERDA
    //                 'Texto de prueba\n',  // Más texto
    //                 '\x1D\x56\x00'    // CORTE (corta el papel)
    //             ];

    //             return qz.print(config, printData);
    //         })
    //         .then(() => {
    //             console.log('✅ Impresion de prueba realizada correctamente');
    //         })
    //         .catch(err => {
    //             console.error('❌ Error QZ Tray:', err);
    //         });
    // })

}); // Ready