import { getData } from "../utils/data.js";
import { convertImageUrlToBase64 } from "../utils/convert.js";
import { initPrinter, getPrinterWidth } from "../core/commands.js";
import { normalizeText, formatMoney, getPrintDate, padLeft, padRight } from "../core/formatters.js";
import { createPrintBuffer } from "../core/buffer.js";
import { generateBarcodeCommand } from "../core/barcode.js";
import { cutPaper, align, bold, size, line, feed } from "../core/commands.js";

/**
 * Funcion para imprimir las facturas de venta 
 * @param {Object} dataInv
 * @param {Array<Object>} detail
 */
export async function invoice(dataInv, detail) {

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
     qz.print(config, data)
        .then(() => {
            console.log("%c[QZ]", "color:#1976d2;font-weight:bold;", "Impresión exitosa en:", printer.printer_name);
        })
        .catch(err => {
            console.error("%c[QZ]", "color:#df1212;font-weight:bold;", "Error en:", printer.printer_name, err);
        })
        .catch(console.error);

}