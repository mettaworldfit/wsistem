// Agregar orden de venta

function registerSalesOrder() {
    const data = {
        action: "registrar_orden",
        customer_id: $('#ov_customer_id').val(),
        name: $('#fullname').val(),
        tel: $('#tel').val(),
        direction: $('#direction').val(),
        observation: $('#observation').val(),
        delivery: $('#delivery').val()
    }

    sendAjaxRequest({
        url: "services/invoices.php",
        data: data,
        successCallback: (res) => {
            $('input[type="text"]').val('');
            dataTablesInstances['orders'].ajax.reload(null, false);

            window.location.href = SITE_URL + 'invoices/add_order&id=' + res
        },
        errorCallback: (res) => mysql_error(res)
    })
}


// Editar orden de venta
function editSalesOrder(orderId) {

    const data = {
        action: "editar_orden",
        order_id: orderId,
        customer_id: $('#edit_customer_id').val(),
        name: $('#edit_fullname').val(),
        tel: $('#edit_tel').val(),
        direction: $('#edit_direction').val(),
        observation: $('#edit_observation').val(),
        delivery: $('#edit_delivery').val()
    }

    sendAjaxRequest({
        url: "services/invoices.php",
        data: data,
        successCallback: (res) => {
            mysql_row_update(res)
        },
        errorCallback: (res) => mysql_error(res),
    })
}


// Actualizar estado de la orden

function updateOrderStatus(selectElement) { // recibimos por parametro el elemento select

    // obtenemos la opción seleccionada .
    var order_id = $('option:selected', selectElement).attr('order_id');
    var status_id = $('option:selected', selectElement).attr('value');

    sendAjaxRequest({
        url: "services/invoices.php",
        data: {
            status: status_id,
            order_id: order_id,
            action: 'actualizar_estado_orden'
        },
        successCallback: () => dataTablesInstances['orders'].ajax.reload(null, false),
        errorCallback: (res) => mysql_error(res)
    })
}

// Eliminar orden

function deleteOrder(id) {

    alertify.confirm("Eliminar orden", "¿Estas seguro que deseas eliminar esta orden? ",
        function () {

            sendAjaxRequest({
                url: "services/invoices.php",
                data: {
                    id: id,
                    action: 'eliminar_orden'
                },
                successCallback: () => dataTablesInstances['orders'].ajax.reload(null, false),
                errorCallback: (res) => mysql_error("Ha ocurrido un error inesperado")

            });
        },
        function () {

        });
}

/**
 * Muestra una alerta con el monto de devolución en estilo limpio y con animación de latido solo en el valor.
 * Usa Alertify.js.
 * 
 * @param {number|string} data - Monto a devolver.
 */
function cashback(data) {
    alertify.alert(
        `<div class="cashback-modal">
            <div class="cashback-header">
                <i class="fa fa-dollar-sign icon"></i>
                <h2 class="title">Cambio a devolver</h2>
            </div>
            <div class="cashback-body">
                <p class="amount heartbeat">$${data}</p>
            </div>
        </div>`
    ).set({
        basic: true,
        movable: false,
        closable: true,
        transition: 'fade'
    });
}

// Total de la factura

function calculateTotalInvoice(bonus = 0) {
    // Determinar acción según la URL
    let action, invoice_id, order_id;

    if (pageURL.includes("invoices/addpurchase")) {
        action = 'precios_detalle_temp';
    } else if (pageURL.includes("invoices/edit_quote")) {
        action = 'total_cotizacion';
        invoice_id = $("#quote_id").val();
    } else if (pageURL.includes("invoices/edit")) {
        action = 'precios_detalle_venta';
        invoice_id = $("#invoice_id").val();
    } else if (pageURL.includes("invoices/add_order")) {
        action = 'precios_ordenes_ventas';
        order_id = $("#order_id").val();
    }

    // Cargar totales según acción
    loadInvoiceTotals(action, invoice_id, order_id);

    // Función para cargar totales de la factura
    function loadInvoiceTotals(action, invoice_id, order_id) {
        if (!action) return; // si no hay action, salir

        sendAjaxRequest({
            url: "services/invoices.php",
            data: { action, invoice_id, order_id },
            successCallback: (res) => {
                const data = JSON.parse(res)[0];

                const discount = format.format(data.descuentos);
                const taxes = format.format(data.taxes);
                const subtotal = format.format(data.precios);

                const totalValue = parseFloat(data.precios) + parseFloat(data.taxes) - parseFloat(data.descuentos);
                const total = isNaN(totalValue) ? '0.00' : format.format(totalValue);
                const totalRaw = total.replace(/,/g, "");

                // Asignar valores al formulario principal
                $('#total_price').val(totalRaw);
                $('#in-subtotal').val(subtotal);
                $('#in-taxes').val(taxes);
                $('#in-discount').val(discount);
                $('#in-total').val(total);

                // Inicializar valores comunes
                $('#cash-received').val('0.00');
                $('#credit-received').val('0.00');

                // Modal Factura Editar
                if (pageURL.includes("invoices/edit")) {
                    setCashModal(data.total, data.pendiente, data.recibido);
                } else {
                    setCashModalWithBonus(totalRaw);
                }

                // Modal Factura a crédito
                setCreditModal(totalRaw);

                // Botones y validaciones
                toggleElementsByTotal(totalValue);
            }
        })


    }

    // Establecer valores en el modal de factura al contado (edit)
    function setCashModal(total, pending, received) {
        $('#cash-topay, #cash-topay2').val(total);
        $('#cash-pending, #cash-pending2').val(pending);
        $('#cash-received, #cash-received2').val(received);
    }

    // Establecer valores en el modal de factura al contado con bono
    function setCashModalWithBonus(total) {
        const bonusValue = parseFloat(bonus) || 0;
        const totalAfterBonus = (parseFloat(total) - bonusValue).toFixed(2);

        $('#cash-bonus').val(format.format(bonusValue));
        $('#cash-topay, #cash-pending').val(format.format(totalAfterBonus));
    }

    // Establecer valores en el modal de factura a crédito
    function setCreditModal(total) {
        $('#credit-topay, #credit-pending').val(format.format(total));
    }

    // Mostrar/ocultar elementos según el total
    function toggleElementsByTotal(total) {
        const isValid = !isNaN(total) && total > 0;
        $('#cash-in-finish, #cash-in-finish-receipt').toggle(isValid);
        $('.pay').toggle(isValid);
    }
}

function reloadInvoiceDetail() {
    // Actualizar detalle según la página
    const tableKey = pageURL.includes('invoices/addpurchase') ? 'detailTemp'
        : pageURL.includes('invoices/edit') ? 'editInvoice'
            : pageURL.includes('invoices/add_order') ? 'addorder'
                : null;

    if (tableKey) {
        dataTablesInstances[tableKey].ajax.reload();
    }


    // Ocultar elementos relacionados con pagos
    $('#cash-in-finish, #cash-in-finish-receipt, #credit-in-finish, #credit-in-finish-receipt').hide();
}

function resetModal() {
    // Ocultar botones de agregar item
    $("#add_item_free, #add_item").hide();

    // Limpiar campos de entrada
    $("#code, #piece_code, #stock, #quantity, #price_out").val('');

    // Limpiar contenedores select2
    $("#select2-variant_id-container, #select2-product-container, #select2-piece-container").empty();

    // Deshabilitar selector de variantes
    $("#variant_id").prop("disabled", true);
}

$(document).ready(function () {

    // Detectar el cambio en los inputs con la clase .input-quantity
    $(document).on('change', '.input-quantity', function () {
        var newQuantity = $(this).val();
        var detail_id = $(this).data('id');  // Obtener el detalle_id del atributo data-id
        var itemId = $(this).data('item-id');
        var type = $(this).data('item-type');


        // Determinar acción según la URL
        let action;

        if (pageURL.includes("invoices/addpurchase")) {
            action = 'actualizar_cantidad_detalle_temporal';
        } else if (pageURL.includes("invoices/add_order")) {
            action = 'actualizar_cantidad_orden_venta';
        }

        if (newQuantity && !isNaN(newQuantity)) {

            sendAjaxRequest({
                url: "services/invoices.php",
                data: {
                    id: detail_id,
                    quantity: newQuantity,
                    item_id: itemId,
                    item_type: type,
                    action: action
                },
                successCallback: (res) => {

                    try {

                        var result = JSON.parse(res);

                        if (result.error) {
                            mdtoast(result.message, {
                                duration: 5000,
                                position: "bottom right",
                                type: 'error',
                            });
                        } else {
                            mdtoast("Cantidad actualizada correctamente", {
                                interactionTimeout: 1500,
                                type: 'success',
                                position: "bottom right",
                            });
                        }

                        calculateTotalInvoice();
                        reloadInvoiceDetail();

                    } catch (e) {
                        // Si ocurre un error al parsear el JSON
                        mdtoast("Error al procesar la respuesta del servidor.", {
                            duration: 5000,
                            position: "bottom right",
                            type: 'error',
                        });
                        console.error("Error al parsear JSON: ", e);
                    }

                }, verbose: false
            })


        } else {
            alert('Por favor, ingrese una cantidad válida');
        }
    });


    // Ocultar botones por defecto (cotización, editar última factura, tipos de facturación)
    $('#SaveQuote, #last_invoice_edit, #credit-in-finish, #credit-in-finish-receipt, #cash-in-finish-receipt, #cash-in-finish').hide();

    if (
        pageURL.includes("invoices/addpurchase") ||
        pageURL.includes("invoices/edit") ||
        pageURL.includes("invoices/edit_quote") ||
        pageURL.includes("invoices/quote") ||
        pageURL.includes("invoices/add_order")
    ) {
        // Calcular total actual de la factura
        calculateTotalInvoice();

        // Ocultar todos los tipos inicialmente
        $('.piece, .service, #piece_code, #add_item_free').hide();
        $('#piece, #service').attr('required', false);

        // Manejar el cambio de tipo de ítem (pieza, producto o servicio)
        $('input:radio[name=tipo]').change(function () {
            const tipo = $(this).val();

            // Limpiar campos comunes
            $('#code, #piece_code, #stock, #discount, #quantity, #service_quantity, #price_out').val('');

            switch (tipo) {
                case "pieza":
                    // Mostrar campos relacionados con piezas
                    $('.piece').show();
                    $('.product, .service',).hide();
                    $('#piece_code').show();
                    $('.product-piece, .discount').show();
                    $('#code').hide();

                    // Modal total
                    $("#totalPricePiece").show();
                    $("#totalPricePiece").val("0.00");
                    $("#totalPriceProduct").hide();
                    $("#totalPriceService").hide();

                    // Requerimientos
                    $('#service, #product').attr('required', false);
                    $('#piece').attr('required', true);

                    // Placeholder de Select2
                    $('#select2-piece-container').html("Buscar piezas");
                    break;

                case "producto":
                    // Mostrar campos relacionados con productos
                    $('.product').show();
                    $('.piece, .service').hide();
                    $('#piece_code').hide();
                    $('.product-piece, .discount').show();
                    $('#code').show();

                    // Modal total
                    $("#totalPriceProduct").show();
                    $("#totalPriceProduct").val("0.00");
                    $("#totalPricePiece").hide();
                    $("#totalPriceService").hide();

                    // Requerimientos
                    $('#service').attr('required', false);
                    $('#product').attr('required', true);
                    $('#piece').attr('required', false);

                    // Placeholder de Select2
                    $('#select2-product-container').html("Buscar productos");
                    break;

                case "servicio":
                    // Mostrar campos relacionados con servicios
                    $('.service').show();
                    $('.product, .piece, .product-piece').hide();
                    $('.discount').hide();
                    // $('.discount, #cost-field').hide();
                    $('#discount_service').show();
                    $('#add_item_free').hide();

                    // Modal total
                    $("#totalPriceService").show();
                    $("#totalPriceService").val("0.00");
                    $("#totalPricePiece").hide();
                    $("#totalPriceProduct").hide();

                    // Requerimientos
                    $('#service').attr('required', true);
                    $('#product, #piece').attr('required', false);
                    $('#quantity').attr('required', false);
                    $('#discount, #price_out').attr('disabled', false);

                    // Mostrar botón para agregar servicio
                    $('#add_item').show();

                    // Placeholder de Select2
                    $('#select2-service-container').html("Buscar servicios");
                    break;
            }
        });
    }

    // Botón: Crear factura al contado sin ticket
    $('#cash-in-finish').on('click', function (e) {
        e.preventDefault();
        createCashInvoice(false);
    });

    // Botón: Crear factura al contado con ticket
    $('#cash-in-finish-receipt').on('click', function (e) {
        e.preventDefault();
        createCashInvoice(true);
    });

    function createCashInvoice(receipt = false) {

        const data = {
            // Datos del ticket
            customer: $('#select2-cash-in-customer-container').attr('title'),
            seller: $('#cash-in-seller').val(),
            payment_method: $('#select2-cash-in-method-container').attr('title'),
            subtotal: parseFloat($('#in-subtotal').val().replace(/,/g, "")) || 0,
            discount: parseFloat($('#in-discount').val().replace(/,/g, "")) || 0,
            taxes: parseFloat($('#in-taxes').val().replace(/,/g, "")) || 0,
            total: parseFloat($('#in-total').val().replace(/,/g, "")) || 0,
            bonus: parseFloat($('#cash-bonus').val().replace(/,/g, "")) || 0,
            date: $('#cash-in-date').val(),
            observation: $('#observation').val(),

            // Datos para la factura
            action: "factura_contado",
            customer_id: $('#cash-in-customer').val(),
            method_id: $('#cash-in-method').val(),
            total_invoice: parseFloat($('#cash-topay').val().replace(/,/g, "")),
        };

        // Validación rápida
        if (!data.customer_id || !data.method_id || !data.seller) {
            alert("Completa todos los datos obligatorios.");
            return;
        }

        let isProcessing = false;  // Establecer el flag antes de la solicitud

        if (!isProcessing) {
            isProcessing = true;  // Establecer el flag para indicar que ya se está procesando

            sendAjaxRequest({
                url: "services/invoices.php",
                data: data,
                successCallback: (res) => {
                    if (res > 0) {
                        registerInvoiceDetails(res, data, receipt);
                        $('#buttons').hide(); // Ocultar botones luego de facturar la orden
                        $('#cash-received').val($('#cash-topay').val());
                        $('#cash-pending').val('0.00');

                        $('#last_invoice_edit').show();
                        $('#last_invoice_edit').attr('href', SITE_URL + 'invoices/edit&id=' + res); // botón para editar la última factura agregada
                    }
                    // Resetear el flag después de la respuesta de la solicitud
                    isProcessing = false;
                },
                errorCallback: (res) => {
                    // Manejar el error (puedes mostrar un mensaje al usuario o algo similar)
                    console.error('Error al crear la factura:', res);
                    // Resetear el flag en caso de error
                    isProcessing = false;
                }
            });
        }

        // Función separada para registrar detalles con el ID de la factura y manejar impresión

        function registerInvoiceDetails(invoice_id, data, receipt) {

            const action = pageURL.includes('invoices/add_order') ? 'registrar_detalle_orden_venta'
                : 'registrar_detalle_de_venta';

            sendAjaxRequest({
                url: "services/invoices.php",
                data: {
                    action: action,
                    invoice_id: invoice_id,
                    order_id: $('#order_id').val(),
                    date: data.date
                },
                successCallback: (res) => {
                    try {
                        var result = JSON.parse(res);
                        console.log(result);

                        if (result.message === 'success') {
                            // Imprimir ticket
                            if (receipt === true) {
                                // Imprime en impresora
                                printer(invoice_id, res.data, data, "cash");

                                // Enviar email (si está marcado)
                                if ($("#sendMail").is(':checked')) {
                                    SendmailCashft(invoice_id);
                                }

                            } else {
                                // Generar PDF solo si #sendPDF está marcado
                                if ($("#sendPDF").is(':checked')) {
                                    GeneratePDF(invoice_id);  // Imprimir/generar PDF
                                }

                                // Enviar email si #sendMail está marcado
                                if ($("#sendMail").is(':checked')) {
                                    SendmailCashft(invoice_id);  // Enviar correo
                                }
                            }

                            // Calcular devolución
                            var topay = $('#cash-topay').val().replace(/,/g, "");
                            var received = $('#calc_return').val();
                            let calc_return;

                            if (received != '') {
                                calc_return = received - topay;
                                cashback(format.format(calc_return));
                            } else {
                                mysql_row_affected();
                            }

                            reloadInvoiceDetail(); // Actualizar datos
                        } else if (result.message === "error") {
                            alertify.error('Ha ocurrido un error. Intenta nuevamente.');

                        }
                    } catch (e) {
                        console.error("Error al analizar JSON: ", e);
                    }

                },
                errorCallback: (res) => mysql_error(res)
            })
        }
    }

    // Generar factura pdf

    function GeneratePDF(invoice) {

        data = {
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            taxes: $('#in-taxes').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
        }

        var width = 1000;
        var height = 800;

        // Centrar la ventana
        var x = parseInt((window.screen.width / 2) - (width / 2));
        var y = parseInt((window.screen.height / 2) - (height / 2));

        var url = SITE_URL + 'src/pdf/generar_factura_venta.php?f=' + invoice + '&sub=' + data.subtotal + '&dis=' + data.discount + '&tax=' + data.taxes + '&total=' + data.total;
        window.open(url, 'Factura', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

    }

    // Generar factura PDF al dar click

    $('#generatePDF').on('click', (e) => {
        e.preventDefault()

        var id = $('#invoice_id').val()
        GeneratePDF(id)
    })


    // Generar Email de factura al contado

    function SendmailCashft(invoice) {

        data = {
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            taxes: $('#in-taxes').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
            method: $('#select2-cash-in-method-container').attr('title') != null ? $('#select2-cash-in-method-container').attr('title') : $('#select2-method-container').attr('title'),
            date: $('#cash-in-date').val() != null ? $('#cash-in-date').val() : $('#date').val()
        }

        const url = SITE_URL + 'src/phpmailer/ventas.php' +
            '?f=' + invoice +                 // Número o ID de la factura
            '&sub=' + data.subtotal +         // Subtotal de la venta
            '&dis=' + data.discount +         // Descuento aplicado
            '&tax=' + data.taxes +            // Impuestos
            '&total=' + data.total +          // Total final
            '&method=' + data.method +        // Método de pago (efectivo, tarjeta, etc.)
            '&date=' + data.date;             // Fecha de la venta

        fetch(url)
            .then(response => response.text())
            .then(result => {
                // Mostramos la respuesta en la consola del navegador
                console.log("Respuesta:", result);


                if (result.includes("enviado correctamente") || result.includes("ok")) {
                    console.log("Correo ha sido enviado correctamente.");

                    // Mostrar notificación de éxito en azul
                    mdtoast("correo enviado correctamente", {
                        interactionTimeout: 1500,
                        type: 'success',
                        position: "bottom right",
                    });
                } else {
                    console.warn("Ocurrió un problema al enviar el correo:", result);

                    mdtoast("Ocurrió un problema al enviar el correo", {
                        interactionTimeout: 1500,
                        type: 'error',
                        position: "bottom right",
                    });
                }
            })
            .catch(error => {
                // Si ocurre un error de conexión o ejecución, lo mostramos en consola
                console.error("Error al enviar la factura:", error);
            });

    }


    // Generar Email al dar click

    $('#SendmailCashft').on('click', (e) => {
        e.preventDefault()

        var id = $('#invoice_id').val()
        SendmailCashft(id)
    })

    // Consultar si el cliente a crédito tiene un bono

    $("#include_bond").click(function () {
        if ($("#include_bond").is(':checked')) {

            // Aplicar bono
            var customer_id = $('#cash-in-customer').val()

            if (customer_id > 1) { // Diferente al consumidor final

                sendAjaxRequest({
                    url: "services/invoices.php",
                    data: {
                        action: "consultar_bono",
                        customer_id: customer_id
                    },
                    successCallback: (res) => {
                        var data = JSON.parse(res);

                        if (data.valor > 0) {
                            calculateTotalInvoice(data.valor) // aplicar bono a el total de la factura
                        }
                    }
                })

            } else {
                calculateTotalInvoice()
            }
        } else {
            calculateTotalInvoice()
        }
    });


    // Evento: validar y mostrar boton de facturar a credito

    $('#credit-pay').on('keyup', (e) => {
        e.preventDefault();

        var pay = $('#credit-pay').val();
        $('#credit-received').val(format.format(pay))

        // Mostrar botón de facturar

        var pay = parseFloat($('#credit-pay').val());
        var pending = parseFloat($('#credit-pending').val().replace(/,/g, ""));

        if (pay <= pending) {
            $('#credit-in-finish').show()
            $('#credit-in-finish-receipt').show()
        } else {
            $('#credit-in-finish').hide()
            $('#credit-in-finish-receipt').hide()
        }


    })

    // Crear factura a crédito

    $('#credit-in-finish').on('click', (e) => {

        CREDIT_INV_FINISH()

    });

    $('#credit-in-finish-receipt').on('click', (e) => {

        data = {
            customer: $('#select2-credit-in-customer-container').attr('title'),
            seller: $('#credit-in-seller').val(),
            payment_method: $('#select2-credit-in-method-container').attr('title'),
            description: $('#observation').val(),
            total_invoice: $('#credit-topay').val().replace(/,/g, ""),
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            taxes: $('#in-taxes').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
            pay: $('#credit-pay').val(),
            date: $('#credit-in-date').val()
        }

        CREDIT_INV_FINISH(true, data)

    });


    function CREDIT_INV_FINISH(receipt = false, data = {}) {

        // Ocultar los botones de facturar en ambos modal para evitar insertar datos vacios

        $('#credit-in-finish').hide()
        $('#credit-in-finish-receipt').hide()
        $('#cash-in-finish').hide()
        $('#cash-in-finish-receipt').hide()

        var pending = $('#credit-pending').val().replace(/,/g, "");

        $.ajax({
            type: "post",
            url: SITE_URL + "services/invoices.php",
            data: {
                action: "factura_credito",
                customer_id: $('#credit-in-customer').val(),
                payment_method: $('#credit-in-method').val(),
                description: $('#observation').val(),
                total_invoice: $('#credit-topay').val().replace(/,/g, ""),
                pay: $('#credit-pay').val(),
                date: $('#credit-in-date').val()
            },
            success: function (res) {
                if (res > 0) {


                    REGISTER_DETAIL_ON_CREDIT(res, data, receipt); // Cargar de nuevo el detalle
                    $('#buttons').hide() // Ocultar botones luego de facturar la orden

                    // Vaciar campos
                    $('#in-subtotal').val('0')
                    $('#in-taxes').val('0')
                    $('#in-discount').val('0')
                    $('#in-total').val('0')
                    $('#credit-pending').val(format.format(pending)) // Imprimir valor pendiente en el modal

                    $('#last_invoice_edit').show()
                    $('#last_invoice_edit').attr('href', SITE_URL + '/invoices/edit&id=' + res) // botón para editar la  última factura agregada


                } else {
                    mysql_error(res)
                    // Ocultar los botones de facturar en ambos modal para evitar insertar datos vacios
                    $('#credit-in-finish').hide()
                    $('#credit-in-finish-receipt').hide()
                    $('#cash-in-finish').hide()
                    $('#cash-in-finish-receipt').hide()
                }
            }
        });


        function REGISTER_DETAIL_ON_CREDIT(invoice_id, data, receipt) {

            const action = pageURL.includes('invoices/add_order') ? 'registrar_detalle_orden_venta'
                : 'registrar_detalle_de_venta';

            $.ajax({
                type: "post",
                url: SITE_URL + "services/invoices.php",
                data: {
                    action: action,
                    invoice_id: invoice_id,
                    order_id: $('#order_id').val(),
                    date: $('#credit-in-date').val()
                },
                success: function (res) {

                    if (res != "") {

                        mysql_row_affected()
                        reloadInvoiceDetail()

                        // Imprimir ticket 
                        if (receipt == true) {
                            printer(invoice_id, res, data, "credit");
                        }

                    } else {
                        mysql_error(res)
                    }
                }
            });

        }


    }


    /**
     * Imprime una factura según el tipo especificado.
     *
     * @param {number|string} invoice_id - El ID de la factura. Puede ser un número o una cadena.
     * @param {object} detail - Un objeto que contiene información detallada sobre los artículos de la factura. La estructura exacta depende de la aplicación.
     * @param {object} data - Un objeto que contiene datos generales de la factura (por ejemplo, información del cliente, fechas). La estructura exacta depende de la aplicación.
     * @param {string} type - El tipo de factura, ya sea "cash" (contado) o "credit" (crédito). Esto determina qué archivo PHP se utiliza.
     */
    function printer(invoice_id, detail, data, type) {
        console.log('imprimiendo.....')

        let file;
        if (type == "cash") {
            file = "factura_al_contado.php"
        } else if (type == "credit") {
            file = "factura_credito.php"
        }

        $.ajax({
            type: "post",
            url: PRINTER_SERVER + file,
            data: {
                detail: detail,
                data: data,
                id: invoice_id
            },
            success: function (res) {
                console.log(res)
            }
        });

    }


    /**
     * Evento para imprimir la orden de venta.
     * Escucha el click en el botón con id "printOrder", obtiene los datos de la orden
     * y envía la información al servidor de impresión.
     */
    $('#printOrder').on('click', (e) => {
        e.preventDefault();

        /**
         * Objeto con los totales de la orden.
         * Los valores numéricos se obtienen desde el DOM y se les eliminan las comas.
         * @type {{subtotal: string, discount: string, taxes: string, total: string, orderId: string}}
         */
        const totals = {
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            taxes: $('#in-taxes').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
            orderId: $('#order_id').val()
        };

        console.log('Obteniendo datos .....');

        // Solicita los detalles de la orden de venta al servidor
        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "obtener_detalle_orden",
                orderId: totals.orderId
            },
            successCallback: (res) => {
                const detail = JSON.parse(res)[0]; // Detalle de los productos/piezas/servicios
                const data = JSON.parse(res)[1];   // Información general de la orden

                console.log('Datos del detalle', detail);
                console.log('Datos de la orden', data);
                console.log(totals);

                // Envía los datos a la impresora
                printOrder(detail, data, totals);
            }
        });

        /**
         * Envía la orden de venta al servidor de impresión.
         * @param {Object} detail - Lista de ítems de la orden.
         * @param {Object} orderData - Información general de la orden.
         * @param {Object} orderTotal - Totales de la orden (subtotal, descuento, impuestos, total).
         */
        function printOrder(detail, orderData, orderTotal) {

            $.ajax({
                type: "POST",
                url: PRINTER_SERVER + "orden_venta.php",
                data: JSON.stringify({
                    detail: detail,
                    data: orderData,
                    totals: orderTotal
                }),
                contentType: "application/json", // Enviamos JSON
                dataType: "json", // Esperamos JSON de respuesta
                success: function (res) {
                    if (res.success) {
                        console.log("✅ Impresión completada:", res.message);
                        alertify.success(res.message || "Ticket impreso correctamente.");
                    } else {
                        console.warn("⚠️ Error en impresión:", res.message);
                        alertify.error(res.message || "Error al imprimir el ticket.");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("❌ Error AJAX:", error);
                    alertify.error("No se pudo conectar con el servidor de impresión.");
                }
            });

        }
    });


    // Evento para agregar ítem manual sin precio
    $("#add_item_free").on("click", () => {
        const tipo = $('input[name=tipo]:checked').val();
        const price = clean($('#price_out').val());
        const tax = (price * clean($('#taxes').val())) / 100;

        // Determinar acción según la URL
        const action = pageURL.includes("addpurchase") ? "agregar_detalle_temporal" :
            pageURL.includes("edit") ? "agregar_detalle_venta" : null;
        if (!action) return;

        // Variables comunes
        let product_id = 0, piece_id = 0, service_id = 0, cost = 0, quantity = 1, discount = 0;
        let description = "", variant_id = [], total_variant = 0;

        // Configuración según tipo
        if (tipo === 'servicio') {
            service_id = $('#service').val();
            discount = clean($('#discount_service').val());
            description = $('#select2-service-container').attr('title');
        } else if (tipo === 'pieza') {
            piece_id = $('#piece').val();
            cost = $('#piece_cost').val();
            quantity = $('#quantity').val();
            discount = clean($('#discount').val());
            description = $('#select2-piece-container').attr('title');
        } else if (tipo === 'producto') {
            product_id = $('#product').val();
            cost = $('#product_cost').val();
            quantity = $('#quantity').val();
            discount = clean($('#discount').val());
            description = $('#select2-product-container').attr('title');
            variant_id = $('#variant_id').val();
            total_variant = parseInt($('#total_variant').val()) || 0;

            // Validar si hay variantes obligatorias
            $('.empty-variant').css("border", "#ced4da 1px solid");
            if (total_variant > 0 && variant_id.length != quantity) {
                $('.empty-variant').css("border", "1px solid red");
                return;
            }
        }

        // Enviar detalle al servidor
        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action,
                invoice: $('#invoice_id').val(),
                product_id, piece_id, service_id, description, quantity, cost,
                price, taxes: tax,
                discount: price + tax
            },
            successCallback: (res) => {
                if (res > 0) {
                    calculateTotalInvoice();
                    reloadInvoiceDetail();
                    if (total_variant > 0) assignVariants(res, variant_id);
                } else {
                    mysql_error(res === "duplicate" ? "Este ítem ya ha sido agregado al detalle" : res);
                }
            }
        });

        function assignVariants(detail_id, array) {
            const action2 = pageURL.includes("addpurchase") ? "asignar_variantes_temporales" :
                pageURL.includes("edit") ? "asignar_variantes" : null;

            if (!Array.isArray(array)) return;
            array.forEach(id => {
                sendAjaxRequest({
                    url: "services/invoices.php",
                    data: {
                        action: action2,
                        variant_id: id,
                        detail_id: detail_id,

                    },
                    successCallback: (res) => {

                    }
                })
            });
        }

        function clean(val) {
            return parseFloat((val || "").toString().replace(/,/g, "")) || 0;
        }
    });



    /**
     * ! Imprimir factura de venta
     *  Imprimir la factura luego de ser facturada en la sección editar factura
     */


    $('#printer_inv').on('click', (e) => {
        e.preventDefault;

        const data = {
            customer: $('#select2-customer-container').attr('title')?.trim(),
            seller: $('#cash-in-seller').val(),
            payment_method: $('#select2-method-container').attr('title'),
            invoice_id: $("#invoice_id").val(),
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            taxes: $('#in-taxes').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
            received: $('#cash-received').val(),
            pending: $('#cash-pending').val(),
            date: $('#date').val(),
            observation: $('#observation').val()
        };

        $.ajax({
            type: "POST",
            url: PRINTER_SERVER + "factura_venta.php",
            data: {
                detail: $('#detail_inv').val(),
                data: data
            },
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    alertify.success(res.message);
                    console.log(res.data)
                } else {
                    alertify.error(res.message);
                }
                console.log("Respuesta del servidor:", res);
            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", error);
                alertify.error("No se pudo conectar con la impresora.");
            }
        });


    })


    // Auto cargar detalle cotizacion desde LocalStorage

    if (pageURL.includes("invoices/quote")) {

        $(function () {

            // Verificar
            if (localStorage.getItem("detalle_cotizacion")) {
                QuoteLocalStorage = JSON.parse(localStorage.getItem("detalle_cotizacion"));

                // Loop del detalle en localStorage 
                QuoteLocalStorage.forEach((element, index) => {

                    let data = {
                        description: element.description,
                        quantity: element.quantity,
                        price: element.price,
                        tax_value: element.tax_value,
                        discount: element.discount
                    }

                    ArrayItem.push(data); // Guardar de localStorage a ArrayItem

                    CalcQuote(ArrayItem);

                    var total = (element.quantity * element.price) - element.discount;
                    var price = format.format(element.price);

                    document.querySelector('#rows').innerHTML += `
                 <tr>
                     <td>${element.description}</td>
                     <td>${element.quantity}</td>
                     <td>${price}</td>
                     <td class="hide-cell">${0} - ${0}%</td>
                     <td>${element.discount}</td>
                     <td>${format.format(total)}</td>
                     <td>
                       <span class="action-delete" onClick="DeleteItemQ(${index});"><i class="fas fa-times"></i></span>
                     </td>
                 </tr>
                 `;


                });
            }
        })
    }

    // Generar cotizacion PDF al dar click

    $('#QuotePDF').on('click', (e) => {
        e.preventDefault()

        var id = $('#quote_id').val()
        GenerateQuotePDF(id)
    })


    // Enviar cotizacion por Email 

    $('#sendMailQuote').on('click', (e) => {
        e.preventDefault()

        var id = $('#quote_id').val()
        sendMailQuote(id)
    })


    /**
     * Evento que se ejecuta cuando se abre el modal de edición de orden
     */
    $("#modalEditComanda").on("show.bs.modal", function () {

        // Función para obtener parámetro de la URL
        function getParam(name) {
            const url = window.location.href;
            const regex = new RegExp("[?&]" + name + "=([^&#]*)", "i");
            const match = url.match(regex);
            return match ? decodeURIComponent(match[1]) : null;
        }

        const orderId = getParam("id");

        if (!orderId) {
            console.warn("No se encontró ID de la orden en la URL");
            return; // salir si no hay ID
        }

        // Función para llenar el formulario de edición
        function fillEditOrderForm(data) {

            $("#edit_customer_id").val(data.cliente_id || "").trigger("change");
            $("#edit_direction").val(data.direccion_entrega || "");
            $("#edit_fullname").val(data.nombre_receptor || "");
            $("#edit_tel").val(data.telefono_receptor || "");
            $("#edit_delivery").val(data.tipo_entrega || "-").trigger("change");
            $("#edit_observation").val(data.observacion || "");
        }

        // Función para cargar los datos vía AJAX
        function loadOrderData(orderId) {
            sendAjaxRequest({
                url: "services/invoices.php",
                data: {
                    orden_id: orderId,
                    action: "obtener_datos_orden"
                },
                successCallback: (res) => {
                    let data = JSON.parse(res);

                    if (Array.isArray(data) && data.length > 0) {
                        fillEditOrderForm(data[0]);
                    } else {
                        console.warn("No se encontraron datos de la orden");
                    }
                },
                errorCallback: (res) => mysql_error(res)
            });
        }

        // Cargar datos de la orden
        loadOrderData(orderId);
    });



}); // Ready


// Agregar producto al detalle temporal / detalle de venta

function addDetailItem() {
    // Verificar a cual detalle insertar el producto

    let action = pageURL.includes("invoices/addpurchase") ? "agregar_detalle_temporal"
        : pageURL.includes("invoices/edit") ? "agregar_detalle_venta"
            : pageURL.includes("invoices/add_order") ? "agregar_detalle_venta"
                : null;
    if (!action) return;

    const tipo = $('input:radio[name=tipo]:checked').val();

    let description, cost, quantity, discount, piece_id = 0, product_id = 0, service_id = 0, variant_id, total_variant = 0;

    if (tipo === 'servicio') {
        cost = $('#service_cost').val().replace(/,/g, "");
        quantity = $('#service_quantity').val();
        discount = $('#discount_service').val().replace(/,/g, "") || 0;
        service_id = $('#service').val();
        description = $('#select2-service-container').attr('title');
        addItem();

    } else if (tipo === 'pieza') {
        piece_id = $('#piece').val();
        discount = $('#discount').val().replace(/,/g, "") || 0;
        quantity = $('#quantity').val();
        description = $('#select2-piece-container').attr('title');
        cost = $('#piece_cost').val();
        addItem();
        resetModal();

    } else if (tipo === 'producto') {
        discount = $('#discount').val().replace(/,/g, "") || 0;
        product_id = $('#product').val() || $('#product_id').val();
        quantity = $('#quantity').val();
        cost = $('#product_cost').val();
        description = $('#select2-product-container').attr('title');
        variant_id = $('#variant_id').val();
        total_variant = parseInt($('#total_variant').val()) || 0;

        $('.empty-variant').css("border", "1px solid #ced4da");

        if (total_variant > 0) {
            if (variant_id.length == quantity) {
                addItem();
                resetModal();
            } else {
                $('.empty-variant, .verify-quantity').css("border", "1px solid red");
            }
        } else {
            addItem();
            resetModal();
        }
    }

    // Asignar variantes al detalle de factura
    function assignVariants(detail_id, variants) {
        const action2 = pageURL.includes("invoices/addpurchase") ? "asignar_variantes_temporales"
            : pageURL.includes("invoices/edit") ? "asignar_variantes"
                : pageURL.includes("invoices/add_order") ? "asignar_variantes"
                    : null;

        if (!action2) return;

        variants.forEach(variant_id => {
            sendAjaxRequest({
                url: "services/invoices.php",
                data: { action: action2, variant_id, detail_id },
                errorCallback: res => mysql_error(res)
            });
        });
    }

    function addItem() {
        console.log("action:", action)
        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: action,
                order_id: $('#order_id').val(),
                invoice: $('#invoice_id').val(),
                product_id: product_id,
                piece_id: piece_id,
                service_id: service_id,
                description: description,
                quantity: quantity,
                discount: discount,
                taxes: ($('#price_out').val().replace(/,/g, "") * $('#taxes').val()) / 100, // Calcular impuestos
                price: $('#price_out').val().replace(/,/g, ""),
                cost: cost
            },
            successCallback: (res) => {
                calculateTotalInvoice()
                reloadInvoiceDetail()
                if (total_variant > 0) {
                    assignVariants(res, variant_id); // Asignar variantes al detalle temporal
                }
            },
            errorCallback: (res) => mysql_error(res),
            verbose: true
        });
    }
}

// Eliminar item del detalle temporar / detalle de venta

function deleteInvoiceDetail(id) {

    function getDeleteAction(url) {
        if (url.includes("invoices/addpurchase")) return "eliminar_detalle_temporal";
        if (url.includes("invoices/edit")) return "eliminar_detalle_venta";
        if (url.includes("invoices/add_order")) return "eliminar_detalle_venta";
        return null;
    }

    const action = getDeleteAction(pageURL);

    sendAjaxRequest({
        url: "services/invoices.php",
        data: {
            action: action,
            id: id
        },
        successCallback: () => {

            if (pageURL.includes('invoices/addpurchase')) {
                dataTablesInstances['detailTemp'].ajax.reload();
            } else if (pageURL.includes('invoices/edit')) {
                dataTablesInstances['editInvoice'].ajax.reload();
            } else if (pageURL.includes('invoices/add_order')) {
                dataTablesInstances['addorder'].ajax.reload();
            }


            calculateTotalInvoice()
        },
        errorCallback: (res) => mysql_error(res)
    });
}

// Eliminar factura

function deleteInvoice(id) {

    alertify.confirm("Eliminar factura", "¿Estas seguro que deseas eliminar esta factura? ",
        function () {

            $.ajax({
                url: SITE_URL + "services/invoices.php",
                method: "post",
                data: {
                    action: "eliminar_factura",
                    id: id
                },
                success: function (res) {

                    if (res == "ready") {

                        (pageURL.includes("invoices/index")) ?
                            dataTablesInstances['invoices'].ajax.reload(null, false) : dataTablesInstances['today'].ajax.reload(null, false);

                    } else {
                        mysql_error(res)
                    }
                }
            });
        },
        function () {

        });
}

// actualizar datos de factura

function Update_info_purchase() {

    $.ajax({
        url: SITE_URL + "services/invoices.php",
        method: "post",
        data: {
            action: "actualizar_factura",
            customer_id: $('#customer').val(),
            observation: $('#observation').val(),
            method: $('#method').val(),
            id: $('#invoice_id').val()
        },
        success: function (res) {
            if (res == "ready") {

                mysql_row_update()

            } else {
                mysql_error(res)
            }
        }
    });

}

// Agregar detalle de cotizacion

let ArrayItem = [];

function AddDQuote(onDb = false) {

    if ($('input:radio[name=tipo]:checked').val() == 'servicio') {

        var data = {
            quantity: $('#service_quantity').val(),
            discount: $('#discount_service').val().replace(/,/g, ""),
            service_id: $('#service').val(),
            description: $('#select2-service-container').attr('title').trim(),
            price: $('#price_out').val().replace(/,/g, ""),
            tax_value: 0
        }

        if (onDb == true) {
            addItemDB($("#quote_id").val(), data) // Agregar a Base de datos
        } else {

            ArrayItem.push(data); // Insertar datos al arreglo
            CreateStorage(ArrayItem); // crear el localstorage del detalle
        }

    } else if ($('input:radio[name=tipo]:checked').val() == 'pieza') {

        var data = {
            piece_id: $('#piece').val(),
            discount: $('#discount').val().replace(/,/g, ""),
            quantity: $('#quantity').val(),
            description: $('#select2-piece-container').attr('title').trim(),
            price: $('#price_out').val().replace(/,/g, ""),
            tax_value: 0
        }


        if (onDb == true) {
            addItemDB($("#quote_id").val(), data) // Agregar a Base de datos
        } else {
            ArrayItem.push(data); // Insertar datos al arreglo
            CreateStorage(ArrayItem); // crear el localstorage del detalle
        }
        // resetModal()

    } else if ($('input:radio[name=tipo]:checked').val() == 'producto') {


        var data = {
            discount: $('#discount').val().replace(/,/g, ""),
            product_id: $('#product').val(),
            quantity: $('#quantity').val(),
            price: $('#price_out').val().replace(/,/g, ""),
            description: $('#select2-product-container').attr('title').trim(),
            tax_value: 0

        }

        $('.empty-variant').css("border", "1px solid #ced4da");

        if (onDb == true) {
            addItemDB($("#quote_id").val(), data) // Agregar a Base de datos
        } else {
            ArrayItem.push(data); // Insertar datos al arreglo
            CreateStorage(ArrayItem); // crear el localstorage del detalle
        }

    }

    // Agregar detalle a la base de datos
    function addItemDB(id, data) {
        RegisterDetail(id, true, data)
    }

} // function

// Crear la base de datos en el localstorage

function CreateStorage(Arr) {

    localStorage.setItem('detalle_cotizacion', JSON.stringify(Arr));
    ShowDB(); // Mostrar DB

}

let QuoteLocalStorage;

function ShowDB() {

    document.querySelector('#rows').innerHTML = ""; // Vaciar detalle

    if (localStorage.getItem("detalle_cotizacion")) {
        QuoteLocalStorage = JSON.parse(localStorage.getItem("detalle_cotizacion"));
    }

    // Loop del detalle en localStorage 
    QuoteLocalStorage.forEach((element, index) => {

        var total = (element.quantity * element.price) - element.discount;
        var price = format.format(element.price);

        document.querySelector('#rows').innerHTML += `
         <tr>
             <td>${element.description}</td>
             <td>${element.quantity}</td>
             <td>${price}</td>
             <td class="hide-cell">${0} - ${0}%</td>
             <td>${element.discount}</td>
             <td>${format.format(total)}</td>
             <td>
               <span class="action-delete" onClick="DeleteItemQ(${index});"><i class="fas fa-times"></i></span>
             </td>
         </tr>
         `;

    });

    CalcQuote(QuoteLocalStorage);
}

// Calcular precio total

function CalcQuote(arr) {

    let subtotal = 0;
    let taxes = 0;
    let total = 0;
    let discount = 0;

    arr.forEach((element, index) => {

        subtotal = subtotal + (parseFloat(element.quantity) * element.price.replace(/,/g, ""));
        total = total + (subtotal + taxes - element.discount);
        discount = parseInt(discount) + element.discount;

        if (element.tax_value > 0) return taxes = parseInt(element.tax_value);
    });

    var sub = format.format(subtotal.toFixed(2));
    final = format.format(subtotal + taxes - discount);

    document.querySelector('#price').innerHTML = ""; // Vaciar precios de la factura

    document.querySelector('#price').innerHTML += `
         <span><input type="text" class="invisible-input" value="${sub}" id="in-subtotal" disabled></span>
         <span><input type="text" class="invisible-input" value="${discount}" id="in-discount" disabled></span>
         <span><input type="text" class="invisible-input" value="${taxes}" id="in-taxes" disabled></span>
         <span><input type="text" class="invisible-input" value="${final}" id="in-total" disabled></span>
         <input type="hidden" name="" value="${final}" id="total_invoice">
     `;

    $("#SaveQuote").show() // Botón registrar cotización

} // function CalcQuote()


// Borrar todo del localstorage

function CancelQuote() {
    localStorage.removeItem('detalle_cotizacion'); // Vaciar localstorage
    ArrayItem = []; // Vaciar Arreglo
    CalcQuote(ArrayItem); // Calcular precios
    $('#rows').load(location.href + " #rows"); // actualizar detalle
    $("#SaveQuote").css("display", "none"); // Botón registrar cotización
}

// Eliminar item del detalle y localstorage

function DeleteItemQ(index, onDb = false) {

    if (onDb != true) {

        ArrayItem.splice(index, 1);
        CreateStorage(ArrayItem)

    } else {

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "eliminar_detalle_cotizacion",
                id: index
            },
            successCallback: (res) => {

                $('#Detalle').load(location.href + " #Detalle");
                calculateTotalInvoice() // Cargar total de la cotizacion

            },
            errorCallback: (res) => mysql_error(res)
        })
    }
}


// Crear cotizacion

function saveQuote() {

    sendAjaxRequest({
        url: "services/invoices.php",
        data: {
            action: "registrar_cotizaciones",
            customer_id: $("#customer").val(),
            total: $("#total_invoice").val().replace(/,/g, ""),
            date: $("#date").val(),
            observation: $("#observation").val()
        },
        successCallback: (res) => {
            if (res > 0) {

                RegisterDetail(res)

            } else {
                mysql_error(res)
            }
        },
        verbose: true
    });

} // function


// Agregar detalle de cotizacion
function RegisterDetail(id, onDb = false, data) {

    if (onDb != true) {
        arrayL = JSON.parse(localStorage.getItem("detalle_cotizacion"));
        arrayL.forEach((element, index) => {

            // Registrar detalle antes de crear la factura
            register(id, element.description, element.quantity, element.price, element.tax_value, element.discount)

        }); // Loop
    } else {
        // Registrar detalle luego de crear la factura
        register(id, data.description, data.quantity, data.price, data.tax_value, data.discount)
    }


    function register(id, description, quantity, price, tax_value, discount) {

        const action = pageURL.includes('invoices/quote')
            ? "crear_detalle_cotizacion"
            : "agregar_detalle_cotizacion";

        $.ajax({
            type: "post",
            url: SITE_URL + "services/invoices.php",
            data: {
                action: action,
                id: id,
                description: description,
                quantity: quantity,
                price: price,
                taxes: tax_value,
                discount: discount,
            },
            success: function (res) {

                if (res == "ready") {

                    if (onDb == true) {
                        calculateTotalInvoice() // Cargar total de la cotizacion
                        $('#Detalle').load(location.href + " #Detalle");

                    } else if (onDb != true) {
                        GenerateQuotePDF(id) // Generar PDF
                        if ($("#sendMail").is(':checked')) {
                            sendMailQuote(id); // Enviar mail
                        }

                        CancelQuote() // Borrar todo del localstorage
                    }

                } else {
                    mysql_error(res)
                }

            }
        });
    }
}

// Eliminar cotizacion

function deleteQuote(id) {
    alertify.confirm("Eliminar cotización", "¿Estas seguro que deseas eliminar esta cotización? ",
        function () {
            sendAjaxRequest({
                url: "services/invoices.php",
                data: {
                    action: "eliminar_cotizacion",
                    id: id
                },
                successCallback: () => dataTablesInstances['quotes'].ajax.reload(null, false),
                errorCallback: (res) => mysql_error(error)
            });
        },
        function () {

        });
}

// Cotizacion PDF

function GenerateQuotePDF(quote_id) {

    data = {
        subtotal: $('#in-subtotal').val().replace(/,/g, ""),
        discount: $('#in-discount').val().replace(/,/g, ""),
        taxes: $('#in-taxes').val().replace(/,/g, ""),
        total: $('#in-total').val().replace(/,/g, ""),
    }

    var width = 1000;
    var height = 800;

    // Centrar la ventana
    var x = parseInt((window.screen.width / 2) - (width / 2));
    var y = parseInt((window.screen.height / 2) - (height / 2));

    var url = SITE_URL + 'src/pdf/generar_cotizacion.php?f=' + quote_id + '&sub=' + data.subtotal + '&dis=' + data.discount + '&tax=' + data.taxes + '&total=' + data.total;
    window.open(url, 'Factura', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

}

// Actualizar cotizacion

function updateQuote(id) {
    sendAjaxRequest({
        url: "services/invoices.php",
        data: {
            action: "actualizar_cotizaciones",
            quote_id: id,
            customer_id: $("#customer").val(),
            date: $("#date").val(),
            observation: $("#observation").val()
        },
        successCallback: () => mysql_row_affected(),
        errorCallback: (res) => mysql_error(res)
    });
}


// Generar Email de la cotizacion

function sendMailQuote(invoice) {

    data = {
        subtotal: $('#in-subtotal').val().replace(/,/g, ""),
        discount: $('#in-discount').val().replace(/,/g, ""),
        taxes: $('#in-taxes').val().replace(/,/g, ""),
        total: $('#in-total').val().replace(/,/g, ""),
        date: $('#cash-in-date').val() != null ? $('#cash-in-date').val() : $('#date').val()
    }

    var width = 500;
    var height = 500;

    // Centrar la ventana
    var x = parseInt((window.screen.width / 2) - (width / 2));
    var y = parseInt((window.screen.height / 2) - (height / 2));

    var url = SITE_URL + 'src/phpmailer/cotizaciones.php?f=' + invoice + '&sub=' + data.subtotal + '&dis=' + data.discount + '&tax=' + data.taxes + '&total=' + data.total + '&date=' + data.date;
    window.open(url, 'Cotizacion', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

}