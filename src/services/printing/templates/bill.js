import { getData } from "../utils/data.js";
import { convertImageUrlToBase64 } from "../utils/convert.js";
import { initPrinter, getPrinterWidth } from "../core/commands.js";
import { normalizeText, formatMoney, getPrintDate, padLeft, padRight } from "../core/formatters.js";
import { createPrintBuffer } from "../core/buffer.js";
import { generateBarcodeCommand } from "../core/barcode.js";
import { cutPaper, align, bold, size, line, feed } from "../core/commands.js";

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
export async function bill(info, detail) {

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