$(document).ready(function () {

    $("#col-dateq1, #col-dateq2,#col-year,#col-month").fadeOut(200);

    $('#action').on('change', function () {
        const selected = this.value;

        console.log(selected)

        if (selected === "productos_vendidos" || selected === "servicios_vendidos") {

            // Ocultar los campos
            $("#col-year,#col-month").fadeOut(200);
            // Mostrar los campos
            $("#col-dateq1, #col-dateq2,#col-query").fadeIn(400);
            // Requerir todos los campos necesarios
            $("#query, #dateq1, #dateq2").attr("required", true);

        } else if (selected === "serial_facturado") {

            // Ocultar los campos
            $("#col-dateq1, #col-dateq2,#col-year,#col-month").fadeOut(200);
            // Mostrar campo keyword
            $('#col-query').fadeIn(400);

            // Solo 'query' es requerido
            $("#query").attr("required", true);
            $("#dateq1, #dateq2").removeAttr("required");

        } else if (selected === "detalle_ventas_mes") {

            // Ocultar los campos
            $("#col-dateq1, #col-dateq2, #col-query").fadeOut(200);
            // Mostrar los campos 
            $('#col-month,#col-year').fadeIn(400);

            $("#year, #month").attr("required", true);
            $("#query, #dateq1, #dateq2").removeAttr("required");

        } else {

            // Otras acciones: limpiar y ocultar por defecto
            $("#col-dateq1, #col-dateq2,#col-year,#col-month").fadeOut(200);
            // Mostrar campo keyword
            $('#col-query').fadeIn(400);
            $("#query, #dateq1, #dateq2").removeAttr("required");
        }
    });


    /**
    * Reporte de ventas filtrado por fecha 
    */

    $('#date_query').change((e) => {
        e.preventDefault()

        const url = new URL(SITE_URL + 'src/excel/reporte-fecha.php');
        url.searchParams.set('date', $('#date_query').val());

        window.location.href = url.toString();
    })


    /**============================================================= 
    * FUNCIONES Y EVENTOS DEL CIERRE DE CAJA 
    ===============================================================*/

    // Obtener datos del cierre de caja al abrir
    $("#modalCashClosing").on("show.bs.modal", function () {

        sendAjaxRequest({
            url: "services/reports.php",
            data: {
                action: "obtener_datos_caja"
            },
            successCallback: (res) => {

                const data = JSON.parse(res)
                console.log(data)

                // Datos
                $('#closingId').val(data['caja']['apertura'].cierre_id);
                $('#tickets_invoices').val(data['ventas']['tickets_emitidos'].total_facturas);
                $('#tickets_payments').val(data['ventas']['tickets_emitidos'].total_pagos);
                $('#opening_date').val(data['caja']['apertura'].fecha_apertura);

                // Header
                $('#total').val(data['caja']['total_real']);

                // Resumen
                $('#initial_balance').val(data['caja']['apertura'].saldo_inicial);
                $('#input_initial_balance').val(format.format(data['caja']['apertura'].saldo_inicial));
                $('#cash_income').val(format.format(data['ventas']['pagos']['efectivo']));
                $('#card_income').val(format.format(data['ventas']['pagos']['tarjeta_total']));
                $('#transfer_income').val(format.format(data['ventas']['pagos']['transferencias']));
                $('#check_income').val(format.format(data['ventas']['pagos']['cheques']))
                $('#external_expenses').val(format.format(data['gastos']['fuera_caja']));
                $('#cash_expenses').val(format.format(data['gastos']['desde_caja']));

                // Calcular
                calculateExpectedTotal()
            },
            errorCallback: (res) => {
                console.error(res)
                notifyAlert(res, 'error')
            }, verbose: false
        });

    })


    /**
     * Calcula el total esperado al cierre de caja.
     *
     * Fórmula aplicada:
     * totalEsperado = saldoInicial + ingresosEfectivo - gastosCaja - retiros - reembolsos
     *
     * Los valores se obtienen desde inputs del DOM.
     * Si un campo está vacío o contiene un valor inválido, se toma como 0.
     *
     * @function calculateExpectedTotal
     * @returns {void}
     */
    function calculateExpectedTotal() {
        // Obtener los valores de cada campo (si está vacío se usa 0)
        const saldoInicial = parseFloat($('#initial_balance').val()) || 0;
        const ingresosEfectivo = parseFloat($('#cash_income').val().replace(/,/g, "")) || 0;
        const cash_expenses = parseFloat($('#cash_expenses').val().replace(/,/g, "")) || 0;
        const withdrawals = parseFloat($('#withdrawals').val().replace(/,/g, "")) || 0;
        const refunds = parseFloat($('#refund').val().replace(/,/g, "")) || 0;

        // Calcular el total esperado
        const totalEsperado = saldoInicial + ingresosEfectivo - cash_expenses - withdrawals - refunds;

        // Mostrar el resultado en el campo correspondiente
        $('#total_expected').val(format.format(totalEsperado));
    }

    // Calcular al abrir cierre de caja
    $('#cash_closing').on('click', () => {
        calculateExpectedTotal();
    })

    // Ejecutar el cálculo cuando se modifique cualquiera de los campos relacionados
    $('#initial_balance, #cash_income, #cash_expenses, #withdrawals, #refund').on('input', calculateExpectedTotal);


    /**
     * Calcula y actualiza la diferencia entre el total real y el total esperado
     * en el cierre de caja.
     *
     * Fórmula aplicada:
     * diferencia = totalReal - totalEsperado
     *
     * - Si la diferencia es positiva → sobrante (+)
     * - Si la diferencia es negativa → faltante (-)
     * - Si es cero → balanceado
     *
     * Además:
     * - Aplica formato monetario
     * - Muestra signo correspondiente
     * - Cambia el color del campo según el resultado
     *
     * @function updateDifference
     * @returns {void}
     */
    function updateDifference() {
        const valReal = parseFloat($('#current_total').val()) || 0;
        const valEsperado = parseFloat($('#total_expected').val().replace(/,/g, "")) || 0;

        const diferenciaValor = (valReal - valEsperado);
        const signo = diferenciaValor > 0 ? '+' : (diferenciaValor < 0 ? '-' : '');
        const diferenciaFormateada = signo + format.format(Math.abs(diferenciaValor));

        // Actualiza el campo con el valor formateado
        $('#total_difference').val(diferenciaFormateada);

        // Establece el color según el valor
        const $diffField = $('#total_difference');
        $diffField.removeClass('text-success text-danger');

        if (diferenciaValor > 0) {
            $diffField.addClass('text-success');
        } else if (diferenciaValor < 0) {
            $diffField.addClass('text-danger');
        }

        // Actualiza también el campo real con formato
        $('#real').val(format.format(valReal));
    }

    // Recalcular cuando se escriba en los campos relevantes
    $('#current_total,#initial_balance').on('input', updateDifference);
    $('#cash_expenses,#withdrawals, #refund').on('input', updateDifference);

    /**============================================================= 
    * FACTURACION, CRUD E IMPRESION DEL CIERRE DE CAJA
    ===============================================================*/

    // Abrir caja
    $('#formCashOpening').on('submit', function (e) {
        e.preventDefault()

        const btn = $('#btnOpenCash');

        // Evitar doble ejecución
        if (btn.prop('disabled')) return;

        btn.prop('disabled', true).text('Abriendo...');

        let OpeningDateRaw = $('#opening').val();
        let localDate = new Date(OpeningDateRaw);

        let formattedOpeningDate = localDate.getFullYear() + '-' +
            String(localDate.getMonth() + 1).padStart(2, '0') + '-' +
            String(localDate.getDate()).padStart(2, '0') + ' ' +
            String(localDate.getHours()).padStart(2, '0') + ':' +
            String(localDate.getMinutes()).padStart(2, '0') + ':00';

        const data = {
            action: "abrir_caja",
            initial_balance: parseFloat($('#cash_initial').val()) || 0,
            opening_date: formattedOpeningDate,
        };

        sendAjaxRequest({
            url: "services/reports.php",
            data,
            successCallback: () => {
                $('.float-right').load(window.location.href + ' .float-right > *');
                $('.pos-sidebar-header div').load(window.location.href + ' .pos-sidebar-header div > *');

                $('#modalCashOpening').modal('hide');

                notifyAlert("Datos registrados correctamente")
            },
            errorCallback: (res) => {
                btn.prop('disabled', false).text('Abrir caja');
                mysql_error(res);
            },
            verbose: false
        });
    })


    // Cierre de caja
    $('#formCashClosing').on('submit', function (e) {
        e.preventDefault()

        function formatDate(dateString) {
            let localDate = new Date(dateString);

            // Formatear a "YYYY-MM-DD HH:MM:SS"
            let formattedDate = localDate.getFullYear() + '-' +
                String(localDate.getMonth() + 1).padStart(2, '0') + '-' +
                String(localDate.getDate()).padStart(2, '0') + ' ' +
                String(localDate.getHours()).padStart(2, '0') + ':' +
                String(localDate.getMinutes()).padStart(2, '0') + ':' +
                '00'; // segundos fijos

            return formattedDate;
        }

        const data = {
            // Datos de impresion
            tickets_invoices: $('#tickets_invoices').val(),
            tickets_payments: $('#tickets_payments').val(),
            difference: $('#total_difference').val().replace(/,/g, ""),
            total_expected: $('#total_expected').val().replace(/,/g, ""),
            user_name: $('#user_name').val(),
            opening_date: formatDate($('#opening_date').val()),

            // Datos para guardar
            action: "cierre_caja",
            user_id: $('#user_id').val(),
            closing_date: formatDate($('#closing_date').val()), // closing_date
            initial_balance: parseFloat($('#initial_balance').val()) || 0,
            cash_income: parseFloat($('#cash_income').val().replace(/,/g, "")) || 0,
            card_income: parseFloat($('#card_income').val().replace(/,/g, "")) || 0,
            transfer_income: parseFloat($('#transfer_income').val().replace(/,/g, "")) || 0,
            check_income: parseFloat($('#check_income').val().replace(/,/g, "")) || 0,
            cash_expenses: parseFloat($('#cash_expenses').val().replace(/,/g, "")) || 0,
            external_expenses: parseFloat($('#external_expenses').val().replace(/,/g, "")) || 0,
            withdrawals: parseFloat($('#withdrawals').val()) || 0,
            refunds: parseFloat($('#refund').val().replace(/,/g, "")) || 0,
            total: parseFloat($('#total').val().replace(/,/g, "")) || 0,
            current_total: $('#current_total').val(),
            notes: $('#notes').val() || ""
        };

        sendAjaxRequest({
            url: "services/reports.php",
            data: data,
            successCallback: (res) => {
                $('.float-right').load(window.location.href + ' .float-right > *');
                $('.pos-sidebar-header div').load(window.location.href + ' .pos-sidebar-header div > *');

                notifyAlert("Datos registrados correctamente")

                $('#modalCashClosing').modal('hide'); // Cerrar ventana

                printerClosing(data, res)
                // sendCashClosing(res) // Enviar el cierr de caja por correo
            },
            errorCallback: (res) => mysql_error(res),
            verbose: false
        })
    })

    // Imprimir cierre
    // function printerClosing(data, cierre_id) {
    //     $.ajax({
    //         type: "POST",
    //         url: PRINTER_SERVER + "cierre_caja.php",
    //         data: JSON.stringify({
    //             data: data,
    //             id: cierre_id
    //         }),
    //         contentType: "application/json", // Enviamos JSON
    //         dataType: "json", // Esperamos JSON de respuesta
    //         success: function (res) {

    //             if (res.success) {
    //                 console.log("✅ Impresión completada:", res.message);
    //             } else {
    //                 console.warn("⚠️ Error en impresión:", res.message);
    //                 // notifyAlert(res.message || "Error al imprimir", "error");
    //             }
    //         },
    //         error: function (xhr, status, error) {
    //             console.error("❌ Error AJAX:", error);
    //             //  notifyAlert("No se pudo conectar con el servidor de impresión.", "error");
    //         }
    //     });
    // }

   
    /* ==============================
   HELPER NUMÉRICO
================================ */
function num(v) {
    return parseFloat(String(v).replace(/,/g, '')) || 0;
}

/* ==============================
   IMPRESIÓN CIERRE DE CAJA
================================ */
function printerClosing(data, cierre_id) {

    const totalExpected = num(data.total_expected);
    const difference    = num(data.difference);
    const total         = num(data.total);

    /* ===== CONEXIÓN SEGURA ===== */
    const connectQZ = qz.websocket.isActive()
        ? Promise.resolve()
        : qz.websocket.connect();

    connectQZ
        .then(() => qz.printers.find("POS-80")) // ⚠️ nombre EXACTO
        .then(printer => {

            const config = qz.configs.create(printer, {
                copies: 1,
                units: "mm",
                size: { width: 80 },     // 80mm
                margins: { top: 0, right: 0, bottom: 0, left: 0 }
            });

            const printData = [
                '\x1B\x40',        // INIT
                '\x1B\x61\x01',    // CENTRAR
                'CIERRE DE CAJA\n',
                '================================\n',

                '\x1B\x61\x00',    // IZQUIERDA
                `Caja #: ${cierre_id}\n`,
                `Usuario: ${data.user_name}\n`,
                `Apertura: ${data.opening_date}\n`,
                `Cierre: ${data.closing_date}\n`,
                '--------------------------------\n',

                `Efectivo:        RD$ ${num(data.cash_income).toFixed(2)}\n`,
                `Tarjeta:         RD$ ${num(data.card_income).toFixed(2)}\n`,
                `Transferencias:  RD$ ${num(data.transfer_income).toFixed(2)}\n`,
                `Cheques:         RD$ ${num(data.check_income).toFixed(2)}\n`,
                '--------------------------------\n',

                `Gastos caja:     RD$ ${num(data.cash_expenses).toFixed(2)}\n`,
                `Gastos externos: RD$ ${num(data.external_expenses).toFixed(2)}\n`,
                `Retiros:         RD$ ${num(data.withdrawals).toFixed(2)}\n`,
                `Reembolsos:      RD$ ${num(data.refunds).toFixed(2)}\n`,
                '--------------------------------\n',

                '\x1B\x45\x01', // NEGRITA ON
                `TOTAL ESPERADO:  RD$ ${totalExpected.toFixed(2)}\n`,
                `TOTAL CONTADO:   RD$ ${total.toFixed(2)}\n`,
                `DIFERENCIA:      RD$ ${difference.toFixed(2)}\n`,
                '\x1B\x45\x00', // NEGRITA OFF

                '================================\n',
                '\x1B\x61\x01',
                '*** FIN DEL CIERRE ***\n',

                '\n\n\n\n\n',     // 👈 EMPUJA PAPEL (CLAVE)
                '\x1D\x56\x00'    // 👈 CORTE
            ];

            return qz.print(config, printData);
        })
        .then(() => {
            console.log('✅ Cierre de caja impreso correctamente');
        })
        .catch(err => {
            console.error('❌ Error QZ Tray:', err);
        });
}



    // Enviar cierre de caja por correo
    function sendCashClosing(id) {
        var url = SITE_URL + 'src/phpmailer/cierre_caja.php?id=' + id;

        fetch(url)
            .then(response => response.text())
            .then(data => {
                console.log("Respuesta:", data);

                // Recargar la página después del envío
                if (data.includes("enviado correctamente") || data.includes("ok")) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error("Error al ejecutar cierre_caja.php:", error);
            });
    }

    // Eliminar cierre de caja
    $(document).on('click', '.erase_closing', function () {

        const cierre_id = $(this).data('id');

        alertify.confirm("Eliminar cierre", "¿Estas seguro que deseas eliminar el cierre '" + cierre_id + "'? ",
            function () {
                sendAjaxRequest({
                    url: "services/reports.php",
                    data: {
                        action: "eliminar_cierre",
                        id: cierre_id
                    },
                    successCallback: (res) => {
                        dataTablesInstances['cashClosing'].ajax.reload()
                    },
                    errorCallback: (err) => {
                        console.error(err);
                        notifyAlert("Ha ocurrido un erro inesperado", "error")
                    },
                })
            },
            function () { }
        );
    })

    // Generar cierre pdf
    $(document).on('click', '.generate_pdf', function () {
        const cierre_id = $(this).data('id');
        var width = 1000;
        var height = 800;

        // Centrar la ventana
        var x = parseInt((window.screen.width / 2) - (width / 2));
        var y = parseInt((window.screen.height / 2) - (height / 2));

        var url = SITE_URL + 'src/pdf/cierre_caja.php?id=' + cierre_id;
        window.open(url, 'ciere_caja', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')
    })


}); // Ready



/**============================================================= 
* FUNCIONES DE CONSULTAS
===============================================================*/

function Query() {
    const action = $('#action').val();

    if (action === "productos_vendidos") {
        getSoldProducts(); // Productos vendidos
    } else if (action === "piezas_vendidas") {
        getSoldPieces(); // Piezas vendidas
    } else if (action === "servicios_vendidos") {
        getSoldServices(); // Servicios vendidos
    } else if (action === "serial_facturado") {
        getInvoicedSerials(); // Seriales facturados
    } else if (action === "detalle_ventas_mes") {
        getMonthlySalesDetails()
    }
}

// Funciones de consultas

function getSoldProducts() {

    sendAjaxRequest({
        url: "services/reports.php",
        data: {
            query: $('#query').val(),
            dateq1: $('#dateq1').val(),
            dateq2: $('#dateq2').val(),
            action: $('#action').val()
        },
        successCallback: (res) => {
            var data = JSON.parse(res);

            // Vaciar campos de resultado
            document.querySelector("#queryResult").innerHTML = `
            <table class="queryTable" id="echoQuery" style="width: 100%; margin-top: 2.5em; background: #f1f1f1; border-radius: 7px;">
                <thead>
                    <tr>
                        <th style="padding: 2px 10px; font-size: 13px;">Nombre producto</td>
                        <th style="padding: 2px 10px; font-size: 13px;">Cantidad</td>
                        <th style="padding: 2px 10px; font-size: 13px;">Costo total</td>
                        <th style="padding: 2px 10px; font-size: 13px;">Total</td>
                        <th style="padding: 2px 10px; font-size: 13px;">Ganancias</td>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>`;

            // Generar filas por separado
            $(data).each(function (index, element) {
                var row = `
            <tr>
                <td style='padding: 2px 10px; font-size: 13px;'>${element.nombre_producto}</td>
                <td style='padding: 2px 10px; font-size: 13px;'>${element.cantidad}</td>
                <td style='padding: 2px 10px; font-size: 13px;'>${format.format(element.costo)}</td>
                <td style='padding: 2px 10px; font-size: 13px;'>${format.format(element.total)}</td>
                <td style='padding: 2px 10px; font-size: 13px;'>${format.format(element.ganancia)}</td>
            </tr>`;

                $('#echoQuery tbody').append(row);
            });

        }
    })
}


function getSoldPieces() {
    sendAjaxRequest({
        url: "services/reports.php",
        data: {
            query: $('#query').val(),
            dateq1: $('#dateq1').val(),
            dateq2: $('#dateq2').val(),
            action: $('#action').val()
        },
        successCallback: (res) => {

            var data = JSON.parse(res);

            document.querySelector("#queryResult").innerHTML = ""; // Vaciar campos de resultado
            document.querySelector("#queryResult").innerHTML = ` 
            <table class="queryTable" id="echoQuery" style="width: 100%; margin-top: 2.5em; 
            background: #f1f1f1; border-radius: 7px;">
                    <thead>
                        <tr>
                            <th style=" padding: 2px 10px; font-size: 13px;">Nombre pieza</th>
                            <th style=" padding: 2px 10px; font-size: 13px;">Cantidad</th>
                            <th style=" padding: 2px 10px; font-size: 13px;">Costo total</th>
                            <th style=" padding: 2px 10px; font-size: 13px;">Total</th>
                            <th style=" padding: 2px 10px; font-size: 13px;">Ganancias</th>
                        </tr>
                    </thead>
                </table>`;

            var tr = "<tr>";
            $(data).each(function (index, element) {

                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.nombre_pieza + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.cantidad + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.costo) + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.total) + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.ganancia) + "</td>";
                tr += "</tr>";
            });
            $('#echoQuery').append(tr);
        }
    })
}


function getSoldServices() {
    sendAjaxRequest({
        url: "services/reports.php",
        data: {
            query: $('#query').val(),
            dateq1: $('#dateq1').val(),
            dateq2: $('#dateq2').val(),
            action: $('#action').val()
        },
        successCallback: (res) => {
            var data = JSON.parse(res);

            // Vaciar contenido anterior
            const queryContainer = document.querySelector("#queryResult");
            queryContainer.innerHTML = `
            <table class="queryTable" id="echoQuery" style="width: 100%; margin-top: 2.5em; background: #f1f1f1; border-radius: 7px;">
                <thead>
                    <tr>
                        <th style="padding: 2px 10pt; font-size: 10pt;">Nombre servicio</th>
                        <th style="padding: 2px 10pt; font-size: 10pt;">Cantidad</th>
                        <th style="padding: 2px 10pt; font-size: 10pt;">Costo</th>
                        <th style="padding: 2px 10pt; font-size: 10pt;">Total</th>
                        <th style="padding: 2px 10pt; font-size: 10pt;">Ganancias</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.map(el => `
                        <tr>
                            <td style="padding: 2px 10pt; font-size: 10pt;">${el.nombre}</td>
                            <td style="padding: 2px 10pt; font-size: 10pt;">${el.cantidad}</td>
                            <td style="padding: 2px 10pt; font-size: 10pt;">${format.format(el.costo || 0)}</td>
                            <td style="padding: 2px 10pt; font-size: 10pt;">${format.format(el.total || 0)}</td>
                            <td style="padding: 2px 10pt; font-size: 10pt;">${format.format(el.ganancia || 0)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>`;
        },
        verbose: true
    })
}

function getInvoicedSerials() {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/reports.php",
        data: {
            query: $('#query').val(),
            action: $('#action').val()
        },
        beforeSend: function () {

        },
        success: function (res) {

            var data = JSON.parse(res);

            document.querySelector("#queryResult").innerHTML = ""; // Vaciar campos de resultado
            document.querySelector("#queryResult").innerHTML = ` 
      <table class="queryTable" id="echoQuery" style="width: 100%; margin-top: 2.5em; 
      background: #f1f1f1; border-radius: 7px;">
            <thead>
                <tr>
                    <td style=" padding: 2px 10px; font-size: 13px;">Factura_id</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Cliente</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Serial</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Costo</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Entrada</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Facturado_el</td>
                </tr>
            </thead>
        </table>`;

            var tr = "<tr>";
            $(data).each(function (index, element) {

                tr += "<td style='padding: 2px 10px; font-size: 13px;'> FT-00" + element.factura_venta_id + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.nombre + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.serial + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.costo_unitario + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.fecha_entrada + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.fecha + "</td>";
                tr += "</tr>";
            });
            $('#echoQuery').append(tr);

        },
    });
}

function getMonthlySalesDetails() {

    const data = {
        month: $('#month').val(),
        year: $('#year').val(),
    }

    const url = new URL(SITE_URL + 'src/excel/detalle-ventas-mes.php');
    url.searchParams.set('year', data.year);
    url.searchParams.set('month', data.month);

    window.location.href = url.toString();

}