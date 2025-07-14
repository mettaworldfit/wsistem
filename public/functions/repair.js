// Total de la factura

function calculateInvoiceTotalRp() {

    sendAjaxRequest({
        url: "services/repair.php",
        data: {
            action: 'precio_detalle',
            orden_id: $('#orden_id').val()
        },
        successCallback: (res) => {
            const data = JSON.parse(res);
            const formatValue = (val) => format.format(parseFloat(val || 0));
            const safeNumber = (val) => parseFloat(val || 0);

            // Valores base
            const subtotal = safeNumber(data[0].precios);
            const discount = safeNumber(data[0].descuentos);
            const total = subtotal - discount;

            // Mostrar valores en inputs
            $('#in-subtotal').val(formatValue(subtotal));
            $('#in-discount').val(formatValue(discount));
            $('#in-total').val(isNaN(total) ? '0' : formatValue(total));
            $('#total_price').val(isNaN(total) ? '0' : total.toFixed(2).replace(/,/g, ''));

            // Limpiar recibido
            $('#cash-received, #credit-received').val('0.00');

            // Factura al contado
            if (pageURL.includes("invoices/repair_edit") && !isNaN(total)) {
                const detalle = data[1];
                const totalFmt = formatValue(detalle.total);
                const pendienteFmt = formatValue(detalle.pendiente);
                const recibidoFmt = formatValue(detalle.recibido);

                $('#cash-topay, #cash-topay2').val(totalFmt);
                $('#cash-pending, #cash-pending2').val(pendienteFmt);
                $('#cash-received, #cash-received2').val(recibidoFmt);
            } else {
                const totalFmt = isNaN(total) ? '0.00' : formatValue(total);
                $('#cash-topay, #cash-pending').val(totalFmt);
            }

            // Factura a crédito
            const creditFmt = isNaN(total) ? '0.00' : formatValue(total);
            $('#credit-topay, #credit-pending').val(creditFmt);

            // Mostrar/Ocultar botones
            const showButtons = !isNaN(total);
            $('#cash-in-finish_rp, #cash-in-finish-rp-receipt, #credit-in-finish-rp-receipt, #credit-in-finish_rp').toggle(showButtons);
            $('.pay').toggle(showButtons);
        }
    })
}

function reloadOrdenRepair() {
    // Actualizar detalle
    (pageURL.includes("invoices/addrepair"))
        ? dataTablesInstances['addrepair'].ajax.reload()
        : dataTablesInstances['editrepair'].ajax.reload();
}


$(document).ready(function () {

    // Llamar funcion calculateInvoiceTotalRp()
    if (pageURL.includes("invoices/addrepair") || pageURL.includes("invoices/repair_edit")) {
        calculateInvoiceTotalRp() // Cargar total de la factura

        // Cambiar tipo de item a agregar

        $('.service').hide()
        $('#stock').attr('disabled', true)
        // $('#quantity').attr('disabled', true)
        $('#quantity').val('')
        $('#discount').attr('disabled', true)
        $('#price_out').attr('disabled', true)
        $('#rp_service').attr('required', false)
        $('#piece').attr('required', true)

        $('input:radio[name=tipo]').change(function () {
            if ($(this).val() == "pieza") {

                $('.piece').show()
                $('.service').hide()

                // Default

                $('#stock').attr('disabled', true)
                $('#quantity').val('')
                $('#price_out').attr('disabled', true)
                $('#rp_service').attr('required', false)
                $('#piece').attr('required', true)
            // $('#quantity').attr('required', true)

            } else if ($(this).val() == "servicio") {

                $('.service').show()
                $('.piece, #cost-field').hide()

                // Default
                $('#quantity').val('')
                $('#discount').attr('disabled', false)
                $('#price_out').attr('disabled', false)
                $('#rp_service').attr('required', true)
                $('#piece').attr('required', false)
                // $('#quantity').attr('required', false)
                $('#price_out').val('')
                $('#rp_add_item').show();

            }
        });
    }

    // Crear factura al contado

    $('#cash-in-finish_rp').on('click', (e) => {
        e.preventDefault();

        CASH_INV_FINISH();

    })

    $('#cash-in-finish-rp-receipt').on('click', (e) => {
        e.preventDefault();

        CASH_INV_FINISH(true);

    })

    function CASH_INV_FINISH(receipt = false) {

        var total = $('#cash-topay').val().replace(/,/g, "");

        sendAjaxRequest({
            url: "services/repair.php",
            data: {
                action: "factura_contado",
                orden_id: $('#orden_id').val(),
                customer_id: $('#cash-in-customer').val(),
                payment_method: $('#cash-in-method').val(),
                description: $('#observation').val(),
                total_invoice: total,
                date: $('#cash-in-date').val()
            },
            successCallback: (res) => {
                if (res > 0) {

                    // Imprimir ticket 
                    if (receipt == true) {
                        printer_inv(res, 'factura_rp_cash.php')
                    }

                    mysql_row_affected()
                    reloadOrdenRepair()
                    $('#buttons').hide()
                    $('#cash-received').val(format.format(total))
                    $('#cash-pending').val('0.00')

                } else {
                    mysql_error(res)
                }
            }
        })


    }


    // Introducir monto

    $('#credit-pay_rp').on('keyup', (e) => {
        e.preventDefault();

        var pay = $('#credit-pay_rp').val();
        $('#credit-received').val(format.format(pay))

        // Mostrar botón de facturar

        var pay = parseInt($('#credit-pay_rp').val());
        var pending = parseInt($('#credit-pending').val().replace(/,/g, ""));

        if (pay < pending) {
            $('#credit-in-finish_rp').show()
            $('#credit-in-finish-rp-receipt').show()
        } else {
            $('#credit-in-finish_rp').hide()
            $('#credit-in-finish-rp-receipt').hide()
        }


    })

    // Crear factura a crédito

    $('#credit-in-finish_rp').on('click', (e) => {
        e.preventDefault();
        CREDIT_INV_FINISH();
    });

    $('#credit-in-finish-rp-receipt').on('click', (e) => {
        e.preventDefault();
        CREDIT_INV_FINISH(true);
    });


    function CREDIT_INV_FINISH(receipt = false) {

        // Ocultar los botones de facturar en ambos modal para evitar insertar datos vacios

        $('#credit-in-finish_rp').hide()
        $('#cash-in-finish_rp').hide()
        $('#credit-in-finish-rp-receipt').hide()

        $.ajax({
            type: "post",
            url: SITE_URL + "services/repair.php",
            data: {
                action: "factura_credito",
                customer_id: $('#credit-in-customer').val(),
                orden_id: $('#orden_id').val(),
                payment_method: $('#cash-in-method').val(),
                description: $('#observation').val(),
                total_invoice: $('#credit-topay').val().replace(/,/g, ""),
                pay: $('#credit-pay_rp').val(),
                pending: $('#credit-pending').val().replace(/,/g, ""),
                date: $('#cash-in-date').val()
            },
            success: function (res) {
                if (res > 0) {

                    // Imprimir ticket 
                    if (receipt == true) {
                        printer_inv(res, 'factura_rp_credit.php')
                    }

                    mysql_row_affected()
                    reloadOrdenRepair()

                    $('#buttons').hide()
                    // Vaciar campos
                    $('#in-subtotal').val('0')
                    $('#in-discount').val('0')
                    $('#in-total').val('0')
                    $('#credit-pending').val(format.format(pending)) // Imprimir valor pendiente en el modal


                } else {
                    mysql_error(res)
                    // Ocultar los botones de facturar en ambos modal para evitar insertar datos vacios
                    $('#credit-in-finish_rp').show()
                    $('#cash-in-finish_rp').show()
                    $('#credit-in-finish-rp-receipt').show()
                }
            }
        });

    }


    // Generar orden pdf

    function exportOrderToPDF(order) {

        var width = 1000;
        var height = 800;

        // Centar la ventana
        var x = parseInt((window.screen.width / 2) - (width / 2));
        var y = parseInt((window.screen.height / 2) - (height / 2));

        var url = SITE_URL + 'src/pdf/generar_orden_rp.php?o=' + order;
        window.open(url, 'Factura', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

    }

    // Exportar factura PDF al dar click

    $('#exportOrderToPDF').on('click', (e) => {
        e.preventDefault()

        var id = $('#orden_id').val()
        exportOrderToPDF(id)
    })

    // Función para abrir la orden en PDF en una ventana nueva
    function exportOrderToPDF(orderId) {

        console.log('holaa')
        var width = 1000;
        var height = 800;

        // Centrar la ventana
        var x = parseInt((window.screen.width / 2) - (width / 2));
        var y = parseInt((window.screen.height / 2) - (height / 2));

        var url = SITE_URL + 'src/pdf/generar_order_rp.php?o=' + orderId;
        window.open(url, 'Factura', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

    }


    // Generar factura pdf

    function GenerateInvPDF(invoice, order) {

        data = {
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
        }

        var width = 1000;
        var height = 800;

        // Centar la ventana
        var x = parseInt((window.screen.width / 2) - (width / 2));
        var y = parseInt((window.screen.height / 2) - (height / 2));

        var url = SITE_URL + 'src/pdf/generar_factura_rp.php?f=' + invoice + '&o=' + order + '&sub=' + data.subtotal + '&dis=' + data.discount + '&total=' + data.total;
        window.open(url, 'Factura', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

    }

    // Generar factura PDF al dar click

    $('#generateInvPDF').on('click', (e) => {
        e.preventDefault()

        var Invid = $('#invoice_id').val()
        var Orid = $('#orden_id').val()
        GenerateInvPDF(Invid, Orid)
    })


    /**
     * TODO Imprimir facturas
     */

    function printer_inv(invoice, type) {

        data = {
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
            order_id: $('#orden_id').val(),
            pay: $('#credit-pay_rp').val(),
            received: $('#cash-received').val().replace(/,/g, ""),
            observation: $('#observation').val(),
            inv_id: invoice
        }
        console.log(data)
        $.ajax({
            type: "post",
            url: PRINTER_SERVER + type,
            data: {
                detail: $('#detail_order').val(),
                device: $('#device_info').val(),
                info: data
            },
            success: function (res) {
                console.log(res)

            }
        });

    }

    /**
     * ! Botón de imprimir factura
     */

    $('#printer_inv_rp').on('click', (e) => {
        e.preventDefault();

        var invId = $('#invoice_id').val()

        printer_inv(invId, 'factura_reparacion.php');


    })













}) // Ready


// Agregar detalle

function addDetailOrdenRepair() {

    let piece_id = 0;
    let service = 0;
    let description = '';
    let quantity = 1;
    let cost = 0;

    const tipo = $('input:radio[name=tipo]:checked').val();

    if (tipo === 'servicio') {
        cost = $('#service_cost').val().replace(/,/g, "")
        service = $('#rp_service').val();
        description = $('#select2-rp_service-container').attr('title');
    } else if (tipo === 'pieza') {
        cost = $('#piece_cost').val()
        piece_id = $('#piece').val();
        quantity = $('#quantity').val();
        description = $('#select2-piece-container').attr('title');
    }

    sendAjaxRequest({
        url: "services/repair.php",
        data: {
            action: "agregar_detalle_a_orden",
            service_id: service,
            description: description,
            orden_id: $('#orden_id').val(),
            piece_id: piece_id,
            quantity: quantity,
            discount: $('#discount').val() || 0,
            price: $('#price_out').val().replace(/,/g, ""),
            cost: cost
        },
        successCallback: () => {
            calculateInvoiceTotalRp()
            reloadOrdenRepair()
            setTimeout(() => location.reload(), 1000);
        },
        errorCallback: (res) => mysql_error(res)
    })
}

// Eliminar detalle de la orden de reparación

function deleteDetail(id) {
    sendAjaxRequest({
        url: "services/repair.php",
        data: {
            action: "eliminar_detalle",
            id: id
        },
        successCallback: () => {
            (pageURL.includes("invoices/addrepair"))
                ? dataTablesInstances['addrepair'].ajax.reload()
                : dataTablesInstances['editrepair'].ajax.reload();

            calculateInvoiceTotalRp()
        },
        errorCallback: (res) => mysql_error(res)
    });
}


// Eliminar factura reparación

function deleteInvoiceRP(id) {

    alertify.confirm("Eliminar factura", "¿Estas seguro que deseas eliminar esta factura? ",
        function () {

            sendAjaxRequest({
                url: "services/repair.php",
                data: {
                    action: "eliminar_factura",
                    id: id
                },
                successCallback: () => {
                    // Actualizar datatable
                    (pageURL.includes("invoices/index_repair")) ?
                        dataTablesInstances['invoicesrp'].ajax.reload() : dataTablesInstances['today'].ajax.reload()

                },
                errorCallback: (res) => mysql_error(res)
            })

        },
        function () {

        });
}

// actualizar datos de factura

function updateInvoiceInfo() {
    sendAjaxRequest({
        url: "services/repair.php",
        data: {
            action: "actualizar_factura",
            customer_id: $('#customer').val(),
            method: $('#method').val(),
            id: $('#orden_id').val()
        },
        successCallback: () => mysql_row_update(),
        errorCallback: (res) => mysql_error(res)
    });
}

/**
 * ! Actualizar dinero recibido 
 */

function UPDATE_CASH_RECEIVED(id) {

    alertify.confirm("Actualizar factura", "¿Estas seguro que deseas actualizar el monto recibido de esta factura? ",
        function () {


            $.ajax({
                url: SITE_URL + "services/repair.php",
                method: "post",
                data: {
                    action: "actualizar_dinero_recibido",
                    id: id,
                    received: $('#update-cash-received').val(),
                    topay: $('#cash-topay2').val().replace(/,/g, "")
                },
                success: function (res) {

                    if (res == "ready") {

                        mysql_row_update()

                    } else {
                        mysql_error(res)
                    }
                }
            });
        },
        function () {

        });
}