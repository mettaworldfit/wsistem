import { getData } from "../utils/data.js";
import { convertImageUrlToBase64 } from "../utils/convert.js";
import { initPrinter, getPrinterWidth } from "../core/commands.js";
import { normalizeText, formatMoney, getPrintDate, padLeft, padRight } from "../core/formatters.js";
import { createPrintBuffer } from "../core/buffer.js";
import { cutPaper, align, bold, size, line, feed } from "../core/commands.js";

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
export async function cash_closing(info) {

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