$(document).ready(function () {

    // Valores por defecto
    $('#received, #topay, #pending').val('0.00');
    $('#add_payment, #add_payment_print, .pay, .repair').hide();

    // Cambiar visibilidad según tipo de factura
    $('input:radio[name=tipo]').change(function () {
        const tipo = $(this).val();
        $('.sale').toggle(tipo === 'venta');
        $('.repair').toggle(tipo === 'reparacion');
    });


    // Cargar datos de la factura

    const invoiceProcedures = {
        invoiceFP: 'consultar_factura_proveedor',
        invoice: 'consultar_factura_venta',
        invoiceRP: 'consultar_factura_reparacion'
    };

    Object.entries(invoiceProcedures).forEach(([inputId, procedure]) => {
        $(`#${inputId}`).change(function () {
            const invoice_id = $(this).val();
            loadInvoice(invoice_id, procedure);
        });
    });

    function loadInvoice(invoice_id, action) {

        sendAjaxRequest({
            url: "services/payments.php",
            data: {
                action: action,
                invoice_id: invoice_id
            },
            successCallback: (res) => {
                const payment = JSON.parse(res)[0];
                const apellidos = payment.apellidos || "";

                $('.pay').show();
                $('#topay').val(format.format(payment.total));

                if (pageURL.includes("payments/add")) {
                    $('#received').val(format.format(payment.recibido));
                    $('#pending').val(format.format(payment.pendiente));
                    $('#customer').val(payment.nombre);
                    $('#customer_id').val(payment.cliente_id);
                } else if (pageURL.includes("bills/add_payment")) {
                    $('#received').val(format.format(payment.pagado));
                    $('#pending').val(format.format(payment.por_pagar));
                    $('#provider').val(`${payment.nombre_proveedor} ${apellidos}`);
                    $('#provider_id').val(payment.proveedor_id);
                }

            },
        })
    }


    // Introducir monto

    $('#pay').on('keyup', (e) => {
        e.preventDefault();

        const pay = parseFloat($('#pay').val());
        const pending = parseFloat($('#pending').val().replace(/,/g, ""));

        const show = pay <= pending;
        $('#add_payment, #add_payment_print').toggle(show);
    })



    /**
     * TODO: Agregar pago
     */

    if (pageURL.includes("payments/add")) {

        $('#add_payment').on('click', (e) => {
            e.preventDefault();

            add_payment();
        })

        $('#add_payment_print').on('click', (e) => {
            e.preventDefault();

            data = {
                topay: $("#topay").val().replace(/,/g, ""),
                pending: $("#pending").val().replace(/,/g, ""),
                pay: $("#pay").val(),
                method: $('#select2-method-container').attr('title'),
                customer: $('#customer').val(),
                date: $('#date').val(),
                seller: $('#seller').val(),
                observation: $('#observation').val()
            }

            add_payment(true, data);
        })


        function add_payment(receipt = false, data = {}) {

            let invoice_id;
            let invoiceRP_id;

            if ($('input:radio[name=tipo]:checked').val() == 'reparacion') {
                invoiceRP_id = $('#invoiceRP').val();
                invoice_id = 0;
            } else if ($('input:radio[name=tipo]:checked').val() == 'venta') {
                invoice_id = $('#invoice').val();
                invoiceRP_id = 0;
            }

            sendAjaxRequest({
                url: "services/payments.php",
                data: {
                    action: 'agregar_pago',
                    invoice_id: invoice_id,
                    invoiceRP_id: invoiceRP_id,
                    customer_id: $('#customer_id').val(),
                    comment: $('#observation').val(),
                    method: $('#method').val(),
                    received: $('#pay').val(),
                    date: $('#date').val()
                },
                successCallback: (res) => {

                    if (receipt) {
                        printer(invoice_id, invoiceRP_id, res, data);
                    }

                    const facturaId = invoiceRP_id > 0 ? invoiceRP_id : invoice_id;
                    const procedimiento = invoiceRP_id > 0 ? 'consultar_factura_reparacion' : 'consultar_factura_venta';

                    if (facturaId > 0) {
                        loadInvoice(facturaId, procedimiento);
                    }

                    mysql_row_affected();
                }
            });
        }

        // función de imprimir
        function printer(invoice_id, invoiceRP_id, num_receipt, data) {

            $.ajax({
                type: "POST",
                url: PRINTER_SERVER + "recibo.php",
                data: {
                    invoice_id: invoice_id,
                    invoiceRP_id: invoiceRP_id,
                    data: data,
                    num_receipt: num_receipt
                },
                success: function (res) {
                  
                    if (res.status === 'success') {
                        console.log("✅ Respuesta:", res.message);
                        alertify.success(res.message || "Recibo impreso correctamente.");
                    } else {
                        console.warn("⚠️ Error:", res.message);
                        alertify.error(res.message || "Error al imprimir el recibo.");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("❌ Error AJAX:", error);
                    alertify.error("No se pudo conectar con el servidor de impresión.");
                }
            });


        }

    }


    /**
     * TODO: Pagar factura a un proveedor
     */

    if (pageURL.includes("bills/add_payment")) {
        $('#add_payment').on('click', (e) => {
            e.preventDefault();

            var invoiceFP = $('#invoiceFP').val();
            $.ajax({
                type: "post",
                url: SITE_URL + "services/payments.php",
                data: {
                    action: 'agregar_pago_proveedor',
                    invoice_id: invoiceFP,
                    provider_id: $('#provider_id').val(),
                    comment: $('#observation').val(),
                    method: $('#method').val(),
                    received: $('#pay').val()
                },
                success: function (res) {
                    if (res == "ready") {

                        loadInvoice(invoiceFP, 'consultar_factura_proveedor')

                        mysql_row_affected()

                    } else {
                        mysql_error(res)
                    }

                }
            });

        })
    }



}) // Ready


// Eliminar pago

function deletePayment(id, factura_id, facturaRP_id) {
    alertify.confirm("Eliminar pago", "¿Estas seguro que deseas eliminar este pago? ",
        function () {

            $.ajax({
                url: SITE_URL + "services/payments.php",
                method: "post",
                data: {
                    action: "eliminar_pago",
                    invoice_id: factura_id,
                    invoiceRP_id: facturaRP_id,
                    id: id
                },
                success: function (res) {

                    if (res == "ready") {

                        // Actualizar datatable
                        (pageURL.includes("payments/index")) ?
                            dataTablesInstances['payments'].ajax.reload() : dataTablesInstances['today'].ajax.reload()


                    } else {
                        mysql_error(res)
                    }
                }
            });
        },
        function () {

        });
}


// Eliminar pago a factura de proveedores

function deletePaymentProvider(id) {
    alertify.confirm("Eliminar pago", "¿Estas seguro que deseas eliminar este pago? ",
        function () {

            $.ajax({
                url: SITE_URL + "services/payments.php",
                method: "post",
                data: {
                    action: "eliminar_pago_factura_proveedor",
                    id: id
                },
                success: function (res) {

                    if (res == "ready") {

                        // Recarga datatable
                        (pageURL.includes("bills/payments")) ?
                            dataTablesInstances['payments_providers'].ajax.reload() : dataTablesInstances['today'].ajax.reload();


                    } else {
                        mysql_error(res)
                    }
                }
            });
        },
        function () {

        });
}