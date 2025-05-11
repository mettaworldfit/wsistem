// Total de la factura

function invoice_total_rp() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/repair.php",
        data: {
            action: 'precio_detalle',
            orden_id: $('#orden_id').val()
        },
        success: function(res) {

            var data = JSON.parse(res);

            var discount = format.format(data[0].descuentos);
            var subtotal = format.format(data[0].precios);
            var total = format.format(parseFloat(data[0].precios) - parseFloat(data[0].descuentos));

            var total_price = total.replace(/,/g, "")

            $('#total_price').val(total_price)
            $('#in-subtotal').val(subtotal)
            $('#in-discount').val(discount)

            if (total != 'NaN') {
                $('#in-total').val(total)
            } else {
                $('#in-total').val('0')
            }

            // Modal Factura al contado y actualizar datos de factura
            $('#cash-received').val('0.00')
            if (pageURL.includes("invoices/repair_edit")) {


                if (total != 'NaN') {

                    $('#cash-topay').val(format.format(data[1].total))
                    $('#cash-pending').val(format.format(data[1].pendiente))
                    $('#cash-received').val(format.format(data[1].recibido))

                    $('#cash-topay2').val(format.format(data[1].total))
                    $('#cash-pending2').val(format.format(data[1].pendiente))
                    $('#cash-received2').val(format.format(data[1].recibido))

                } else {
                    $('#cash-topay').val('0.00')
                    $('#cash-pending').val('0.00')
                }

            } else {

                if (total != 'NaN') {

                    $('#cash-topay').val(total)
                    $('#cash-pending').val(total)

                } else {
                    $('#cash-topay').val('0.00')
                    $('#cash-pending').val('0.00')
                }
            }

            // Modal Factura a crédito

            $('#credit-received').val('0.00')
            if (total != 'NaN') {

                $('#credit-topay').val(total)
                $('#credit-pending').val(total)

            } else {
                $('#credit-topay').val('0.00')
                $('#credit-pending').val('0.00')
            }

            // Validar botón de procesar venta al contado

            if (total != 'NaN') {
                $('#cash-in-finish_rp').show();
                $('#cash-in-finish-rp-receipt').show();
                $('#credit-in-finish-rp-receipt').show();
                $('#credit-in-finish_rp').show();
            } else {
                $('#cash-in-finish_rp').hide();
                $('#cash-in-finish-rp-receipt').hide();
                $('#credit-in-finish-rp-receipt').hide();
                $('#credit-in-finish_rp').hide();
            }

            // Validar campo ingresar monto factura a crédito

            if (total != 'NaN') {
                $('.pay').show();
            } else {
                $('.pay').hide();
            }

        }
    });
}

function reload_rp() {
    // Actualizar detalle
    $('#Detalle').load(location.href + " #Detalle");

    setTimeout('document.location.reload()', 2000);


}


$(document).ready(function() {

        // Llamar funcion invoice_total_rp()
        if (pageURL.includes("invoices/addrepair") || pageURL.includes("invoices/repair_edit")) {
            invoice_total_rp() // Cargar total de la factura

            // Cambiar tipo de item a agregar

            $('.service').hide()
            $('#stock').attr('disabled', true)
            $('#quantity').attr('disabled', true)
            $('#quantity').val('0')
            $('#discount').attr('disabled', true)
            $('#price_out').attr('disabled', true)
            $('#rp_service').attr('required', false)
            $('#piece').attr('required', true)

            $('input:radio[name=tipo]').change(function() {
                if ($(this).val() == "pieza") {

                    $('.piece').show()
                    $('.service').hide()

                    // Default

                    $('#stock').attr('disabled', true)
                    $('#quantity').val('0')
                    $('#price_out').attr('disabled', true)
                    $('#rp_service').attr('required', false)
                    $('#piece').attr('required', true)
                    $('#quantity').attr('required', true)

                } else if ($(this).val() == "servicio") {

                    $('.service').show()
                    $('.piece').hide()

                    // Default

                    $('#discount').attr('disabled', false)
                    $('#price_out').attr('disabled', false)
                    $('#rp_service').attr('required', true)
                    $('#piece').attr('required', false)
                    $('#quantity').attr('required', false)
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

            $.ajax({
                type: "post",
                url: SITE_URL + "services/repair.php",
                data: {
                    action: "factura_contado",
                    orden_id: $('#orden_id').val(),
                    customer_id: $('#cash-in-customer').val(),
                    payment_method: $('#cash-in-method').val(),
                    description: $('#observation').val(),
                    total_invoice: total,
                    date: $('#cash-in-date').val()
                },
                success: function(res) {
                    if (res > 0) {

                        // Imprimir ticket 
                        if (receipt == true) {
                            printer_inv(res, 'factura_rp_cash.php')
                        }

                        mysql_row_affected()
                        reload_rp()
                        $('#buttons').hide()
                        $('#cash-received').val(format.format(total))
                        $('#cash-pending').val('0.00')

                    } else {
                        mysql_error(res)
                    }
                }
            });
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
                success: function(res) {
                    if (res > 0) {

                        // Imprimir ticket 
                        if (receipt == true) {
                            printer_inv(res, 'factura_rp_credit.php')
                        }

                        mysql_row_affected()
                        reload_rp()

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

        function GenerateOrderPDF(order) {

            var width = 1000;
            var height = 800;

            // Centar la ventana
            var x = parseInt((window.screen.width / 2) - (width / 2));
            var y = parseInt((window.screen.height / 2) - (height / 2));

            var url = SITE_URL + 'src/pdf/generar_orden_rp.php?o=' + order;
            window.open(url, 'Factura', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

        }

        // Generar factura PDF al dar click

        $('#generateOrderPDF').on('click', (e) => {
            e.preventDefault()

            var id = $('#orden_id').val()
            GenerateOrderPDF(id)
        })


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
                success: function(res) {
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

function add_detail_rp() {

    let piece_id;
    let service;
    let description;
    let quantity;

    // Validación de los datos

    if ($('input:radio[name=tipo]:checked').val() == 'servicio') {

        piece_id = 0;
        quantity = 1;
        service = $('#rp_service').val();
        description = $('#select2-rp_service-container').attr('title')

    } else if ($('input:radio[name=tipo]:checked').val() == 'pieza') {

        piece_id = $('#piece').val();
        service = 0;
        quantity = $('#quantity').val();
        description = $('#select2-piece-container').attr('title')
    }

    $.ajax({
        url: SITE_URL + "services/repair.php",
        method: "post",
        data: {
            action: "agregar_detalle_a_orden",
            service_id: service,
            description: description,
            orden_id: $('#orden_id').val(),
            piece_id: piece_id,
            quantity: quantity,
            discount: $('#discount').val(),
            price: $('#price_out').val().replace(/,/g, ""),
        },
        success: function(res) {

            if (res > 0) {

                invoice_total_rp()
                reload_rp()

            } else if (res == "duplicate") {
                mysql_error('Esta pieza ya ha sido agregada al detalle');
            } else {
                mysql_error(res)
            }
        }
    });

}

// Eliminar detalle de la orden de reparación

function deleteDetail(id) {

    $.ajax({
        url: SITE_URL + "services/repair.php",
        method: "post",
        data: {
            action: "eliminar_detalle",
            id: id
        },
        success: function(res) {
            if (res == "ready") {

                invoice_total_rp()
                $('#Detalle').load(location.href + " #Detalle");

            } else {
                mysql_error(res)
            }
        }
    });
}


// Eliminar factura reparación

function deleteInvoiceRP(id) {

    alertify.confirm("Eliminar factura", "¿Estas seguro que deseas eliminar esta factura? ",
        function() {

            $.ajax({
                url: SITE_URL + "services/repair.php",
                method: "post",
                data: {
                    action: "eliminar_factura",
                    id: id
                },
                success: function(res) {
                    if (res == "ready") {

                        // Actualizar datatable
                        (pageURL.includes("invoices/index_repair")) ?
                        dataTablesInstances['invoicesrp'].ajax.reload(): dataTablesInstances['sales'].ajax.reload()


                    } else {
                        mysql_error(res)
                    }
                }
            });

        },
        function() {

        });
}

// actualizar datos de factura

function Update_info() {

    $.ajax({
        url: SITE_URL + "services/repair.php",
        method: "post",
        data: {
            action: "actualizar_factura",
            customer_id: $('#customer').val(),
            method: $('#method').val(),
            id: $('#orden_id').val()
        },
        success: function(res) {
            if (res == "ready") {

                mysql_row_update()

            } else {
                mysql_error(res)
            }
        }
    });

}

/**
 * ! Actualizar dinero recibido 
 */

function UPDATE_CASH_RECEIVED(id) {

    alertify.confirm("Actualizar factura", "¿Estas seguro que deseas actualizar el monto recibido de esta factura? ",
        function() {


            $.ajax({
                url: SITE_URL + "services/repair.php",
                method: "post",
                data: {
                    action: "actualizar_dinero_recibido",
                    id: id,
                    received: $('#update-cash-received').val(),
                    topay: $('#cash-topay2').val().replace(/,/g, "")
                },
                success: function(res) {

                    if (res == "ready") {

                        mysql_row_update()

                    } else {
                        mysql_error(res)
                    }
                }
            });
        },
        function() {

        });
}