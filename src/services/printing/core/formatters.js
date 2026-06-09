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

/**
 * Genera la fecha y hora actual formateada
 * en formato: DD-MM-YYYY hh:mm:ss AM/PM
 *
 * @async
 * @function getPrintDate
 * @returns {Promise<string>} Fecha formateada lista para impresión.
 */
export async function getPrintDate() {
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
 * Normaliza texto para impresoras térmicas ESCPOS.
 *
 * Convierte caracteres acentuados y especiales a ASCII básico
 * para evitar símbolos corruptos en impresoras POS que no
 * soportan UTF-8 (ej: POS80, 2connect, genéricas).
 *
 * Útil cuando NO se usa codepage extendido (CP437/CP850).
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
 * Rellena texto con espacios a la derecha
 * hasta alcanzar la longitud indicada.
 *
 * @function padRight
 * @param {string|number} text - Texto a formatear.
 * @param {number} length - Longitud final deseada.
 * @returns {string} Texto con padding a la derecha.
 */
export function padRight(text, length) {
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
export function padLeft(text, length) {
    return text.toString().padStart(length, ' ');
}