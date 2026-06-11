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
