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
