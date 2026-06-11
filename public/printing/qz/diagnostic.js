import { getData } from "../utils/data.js";

// Selectores
export function getSelectedPrinter() {
    return $('#impresoraSelect').val();
}

export function getSelectedLanguage() {
    return $('[name="printer_language"]').val() || 'ESCPOS';
}

/**
 * Ejecuta un diagnóstico completo de QZ Tray verificando:
 *
 * 1. Que la librería QZ Tray esté cargada.
 * 2. Que la conexión WebSocket con QZ Tray esté activa.
 * 3. La versión instalada de QZ Tray.
 * 4. Las impresoras disponibles en el sistema.
 * 5. La impresora seleccionada por el usuario.
 * 6. El envío de una impresión de prueba con imagen y texto.
 *
 * Durante la ejecución obtiene la configuración de impresión,
 * carga el logotipo configurado en el sistema, genera una prueba
 * de impresión y la envía a la impresora seleccionada.
 *
 * Los resultados del diagnóstico son mostrados en la consola del
 * navegador y mediante notificaciones visuales al usuario.
 *
 * @async
 * @function runQzDiagnostic
 * @returns {Promise<void>} Promesa que se resuelve cuando finaliza
 * el proceso de diagnóstico.
 *
 * @throws {Error}
 * Puede generar errores cuando:
 * - QZ Tray no está cargado.
 * - No se puede establecer conexión con QZ Tray.
 * - No existe una impresora seleccionada.
 * - Ocurre un fallo al obtener la configuración.
 * - Falla el envío de la impresión de prueba.
 *
 * @example
 * await runQzDiagnostic();
 */
export async function runQzDiagnostic() {

    // Datos predeterminados
    const printer_config = await getData();
    // const printers = printer_config[0]; // Impresoras
    const site = printer_config[1]; // Datos del sitio

    console.group('%c[QZ DIAGNOSTIC]', 'color:#1565c0;font-weight:bold;');

    try {

        /* 1. QZ cargado */
        if (typeof qz === 'undefined') {
            throw new Error('QZ Tray JS no está cargado');
        }
        console.log('✔ Librería QZ cargada');

        /* 2. WebSocket */
        if (!qz.websocket.isActive()) {
            console.log('Conectando a QZ Tray…');
            await qz.websocket.connect();
        }
        console.log('✔ WebSocket activo');

        /* 3. Versión */
        const version = await qz.api.getVersion();
        console.log('✔ Versión QZ:', version);

        /* 4. Impresoras */
        const printers = await qz.printers.find();
        console.log('✔ Impresoras encontradas:', printers.length);

        /* 5. Impresión real */
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

        // IMAGEN (RAW)
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