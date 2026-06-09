import { getData } from "../utils/data.js";
import { convertImageUrlToBase64 } from "../utils/convert.js";
import { initPrinter, getPrinterWidth } from "../core/commands.js";
import { normalizeText, formatMoney, getPrintDate, padLeft, padRight } from "../core/formatters.js";
import { createPrintBuffer } from "../core/buffer.js";
import { generateBarcodeCommand } from "../core/barcode.js";
import { cutPaper, align, bold, size, line, feed } from "../core/commands.js";


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
export async function order_invoice(detail, info) {

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