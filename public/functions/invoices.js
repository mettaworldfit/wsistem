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
function cashBack(data, timeout = 10000) {
    const alert = alertify.alert(
        `
    <div class="cashback-modal">
      <div class="cashback-header">
        <i class="fa fa-hand-holding-usd cashback-icon"></i>
        <span class="cashback-title">Cambio a devolver</span>
      </div>
      <div class="cashback-body">
        <span class="cashback-currency">$</span>
        <span class="cashback-amount">${parseFloat(data).toFixed(2)}</span>
      </div>
    </div>
    `
    ).set({
        basic: true,
        movable: false,
        closable: true,
        transition: 'fade'
    });

    // ⏱ Cerrar automáticamente
    setTimeout(() => { alert.close(); }, timeout);
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
    } else if (pageURL.includes("invoices/add_order") || pageURL.includes("invoices/pos")) {
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

                // 1. Obtener los valores crudos de precios, impuestos y descuentos
                const rawPrice = parseFloat(data.precios) || 0;
                const rawTaxes = parseFloat(data.taxes || 0);
                const rawDiscount = parseFloat(data.descuentos || 0);

                // 2. Cálculo del valor total
                const totalValue = rawPrice + rawTaxes - rawDiscount;

                // 3. Formateo de los valores de descuento, impuestos y subtotal
                const subtotal = format.format(data.precios);
                const discount = format.format(data.descuentos || 0);
                const taxes = format.format(data.taxes || 0);
                const total = isNaN(totalValue) ? '0.00' : format.format(totalValue);

                // 4. Eliminar las comas del total para usarlo en cálculos o almacenamiento
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

                // Insertar en el POS (Punto de venta)
                if (discount.replace(/,/g, "") > 0) {
                    $('#pos-discount').css('display', 'flex')
                    $('#pos-subtotal').css('display', 'flex')
                }

                if (taxes.replace(/,/g, "") > 0) {
                    $('#pos-taxes').css('display', 'flex')
                    $('#pos-subtotal').css('display', 'flex')
                }

                $('.pos-subtotal').text('$' + subtotal);
                $('.pos-discount').text('$' + discount);
                $('.pos-taxes').text('$' + taxes);
                $('.pos-total').text('$' + total);

                // Total del pos
                $('#total_pos').val(totalRaw);

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
            },
            verbose: false
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
    const tableKey = pageURL.includes('invoices/addpurchase') ? 'detailTemp' :
        pageURL.includes('invoices/edit') ? 'editInvoice' :
            pageURL.includes('invoices/add_order') ? 'addorder' :
                null;

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

    // Ocultar botones por defecto (cotización, editar última factura, tipos de facturación)
    $('#SaveQuote, #last_invoice_edit, #credit-in-finish, #credit-in-finish-receipt, #cash-in-finish-receipt, #cash-in-finish').hide();

    // Detectar el cambio en los inputs con la clase .input-quantity
    $(document).on('change', '.input-quantity', function () {
        var debounceTimer;
        var $input = $(this);  // Guardar la referencia al input actual

        clearTimeout(debounceTimer);  // Limpiar el temporizador anterior

        debounceTimer = setTimeout(function () {
            var newQuantity = parseFloat($input.val());  // Convertir el valor a número de punto flotante
            var detail_id = $input.data('id');  // Obtener el detalle_id del atributo data-id
            var itemId = $input.data('item-id');
            var type = $input.data('item-type');

            // Obtener la URL actual de la página (si aún no se ha definido)
            var pageURL = window.location.href;

            // Determinar acción según la URL
            let action;

            if (pageURL.includes("invoices/addpurchase")) {
                action = 'actualizar_cantidad_detalle_temporal';
            } else if (pageURL.includes("invoices/add_order") || pageURL.includes("invoices/pos")) {
                action = 'actualizar_cantidad_orden_venta';
            }

            // Validar si la cantidad es un número válido y mayor a 0
            if (!isNaN(newQuantity) && newQuantity > 0) {

                // Enviar la solicitud AJAX
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
                            var result = JSON.parse(res);  // Parsear la respuesta del servidor

                            // Verificar si hay algún error en la respuesta
                            if (result.error) {
                                notifyAlert(result.message, 'error');
                            } else {
                                notifyAlert("Cantidad actualizada correctamente", 'success', 1500);
                            }

                            // Actualizar la información de la factura
                            calculateTotalInvoice();
                            reloadInvoiceDetail();

                        } catch (e) {
                            console.error("Error al parsear JSON: ", e);
                            notifyAlert("Hubo un error al procesar la respuesta. Intente de nuevo.", 'error');
                        }

                    },
                    errorCallback: (res) => {
                        console.error(res);
                        notifyAlert(res, 'error');
                    },
                    verbose: false
                });

            } else {
                alert('Por favor, ingrese una cantidad válida mayor que cero');
            }
        }, 300);  // 300 ms de espera entre cambios rápidos
    });



    const validPages = [
        "invoices/addpurchase",
        "invoices/edit",
        "invoices/edit_quote",
        "invoices/quote",
        "invoices/add_order",
        "invoices/pos"
    ];

    // Verificar si la URL actual está en el array
    if (validPages.some(page => pageURL.includes(page))) {
        // Calcular total actual de la factura
        calculateTotalInvoice();
    }


    /**============================================================= 
    * FACTURACION E IMPRESION
    ===============================================================*/

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

            },
            errorCallback: (res) => mysql_error(res)
        });


        // Función separada para registrar detalles con el ID de la factura y manejar impresión

        function registerInvoiceDetails(invoice_id, data, receipt) {

            const action = pageURL.includes('invoices/add_order') ? 'registrar_detalle_orden_venta' :
                'registrar_detalle_de_venta';

            sendAjaxRequest({
                url: "services/invoices.php",
                data: {
                    action: action,
                    invoice_id: invoice_id,
                    order_id: $('#order_id').val(),
                    date: data.date
                },
                successCallback: (res) => {

                    // Imprimir ticket
                    if (receipt === true) {
                        // Imprime en impresora
                        printer(invoice_id, res, data, "cash");

                        // Enviar email (si está marcado)
                        if ($("#sendMail").is(':checked')) {
                            SendmailCashft(invoice_id);
                        }

                    } else {
                        // Generar PDF solo si #sendPDF está marcado
                        if ($("#sendPDF").is(':checked')) {
                            GeneratePDF(invoice_id); // Imprimir/generar PDF
                        }

                        // Enviar email si #sendMail está marcado
                        if ($("#sendMail").is(':checked')) {
                            SendmailCashft(invoice_id); // Enviar correo
                        }
                    }

                    // Calcular devolución
                    var topay = $('#cash-topay').val().replace(/,/g, ""); // Eliminar comas en caso de que haya
                    var received = $('#calc_return').val();
                    let calc_return;

                    // Verificar si la cantidad recibida no está vacía y es un número válido
                    if (received !== '' && !isNaN(received) && !isNaN(topay)) {
                        calc_return = parseFloat(received) - parseFloat(topay); // Realizamos el cálculo de la devolución

                        // Verificar si el valor calculado de devolución es positivo
                        if (calc_return >= 0) {
                            cashBack(calc_return); // Llamamos a la función para la devolución
                        } 
                    } else {
                        // Si no se recibe un valor válido, llamamos a la función de efecto de fila en la base de datos
                        mysql_row_affected();
                    }

                    reloadInvoiceDetail(); // Actualizar datos
                },
                errorCallback: (err) => {
                    console.error(err)
                }
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
            '?f=' + invoice + // Número o ID de la factura
            '&sub=' + data.subtotal + // Subtotal de la venta
            '&dis=' + data.discount + // Descuento aplicado
            '&tax=' + data.taxes + // Impuestos
            '&total=' + data.total + // Total final
            '&method=' + data.method + // Método de pago (efectivo, tarjeta, etc.)
            '&date=' + data.date; // Fecha de la venta

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

            const action = pageURL.includes('invoices/add_order') ? 'registrar_detalle_orden_venta' :
                'registrar_detalle_de_venta';

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
     * @param {number|string} invoice_id
     * @param {object} detail
     * @param {object} data
     * @param {string} type - "cash" | "credit"
     */
    function printer(invoice_id, detail, data, type) {

        let file = '';

        if (type === 'cash') {
            file = 'factura_al_contado.php';
        } else if (type === 'credit') {
            file = 'factura_credito.php';
        } else {
            console.error('❌ Tipo de factura inválido');
            return;
        }

        $.ajax({
            type: 'POST',
            url: PRINTER_SERVER + file,
            data: {
                id: invoice_id,
                detail: detail,
                data: data
            },
            dataType: 'json',
            success: function (res) {

                if (res.status === "success") {
                    console.log('✅ Respuesta del servidor:', res);
                } else {
                    console.error('❌ Error del servidor:', res.error || 'Error desconocido');
                }
            },
            error: function (xhr, status, error) {

                console.error('🚨 Error AJAX');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Respuesta:', xhr.responseText);
            }
        });
    }


    // Evento que imprime la factura 
    $('#printInv').on('click', function () {
        const invId = $(this).data('id');
        printerInvoice(invId);
    })

    /**
   * Envía una factura de venta al servidor de impresión
   * Obtiene los datos de la factura vía AJAX y los envía al servicio de impresión
   *
   * @param {number} invoice_id - ID de la factura de venta
   * @returns {void}
   */
    function printerInvoice(invoice_id) {

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                id: invoice_id,
                action: "devolver_datos_impresion"
            },
            successCallback: (response) => {

                var data = typeof response === 'string' ? JSON.parse(response) : response;

                const dataInv = {
                    customer: data.datos.nombre,
                    seller: data.datos.usuario,
                    payment_method: data.datos.nombre_metodo,
                    invoice_id: data.datos.factura_venta_id,
                    subtotal: data.datos.subtotal || 0,
                    discount: data.datos.total_descuento || 0,
                    taxes: data.datos.total_impuesto || 0,
                    total: data.datos.total || 0,
                    received: data.datos.recibido,
                    pending: data.datos.pendiente,
                    date: data.datos.fecha,
                    observation: data.datos.descripcion
                };

                printer(dataInv, JSON.stringify(data.detalle));
            },
            verbose: false
        });

        // Funcion de imprimir
        function printer(dataInv, detail) {
            $.ajax({
                type: "POST",
                url: PRINTER_SERVER + "factura_venta.php",
                data: {
                    detail: detail,
                    data: dataInv
                },
                dataType: "json",
                success: function (res) {
                    if (res.status === "success") {
                        console.log('✅ Impresión completada con exito: ', res.data);
                        notifyAlert("Imprimiendo factura...")
                    } else {
                        console.error('❌ Error:', res.error || 'Desconocido');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('🚨 Error AJAX:', status, error);
                    console.error(xhr.responseText);
                }
            });
        }
    }


    /**
     * Evento para imprimir la orden de venta.
     * Escucha el click en el botón con id "printOrder", obtiene los datos de la orden
     * y envía la información al servidor de impresión.
     */
    $('#printOrder').on('click', (e) => {
        e.preventDefault();

        // Solicita los detalles de la orden de venta al servidor
        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "obtener_detalle_orden",
                orderId: $('#order_id').val()
            },
            successCallback: (res) => {
                const detail = JSON.parse(res)[0]; // Detalle de los productos/piezas/servicios
                const data = JSON.parse(res)[1]; // Información general de la orden
                const total = JSON.parse(res)[2]; // totales

                const totals = {
                    subtotal: total.subtotal,
                    discount: total.total_descuento,
                    taxes: total.total_impuesto,
                    total: total.total,
                    orderId: $('#order_id').val()
                };

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
                    if (res.status === "success") {
                        console.log("✅ Impresión completada:", res.message);
                        notifyAlert(res.message || "Ticket impreso correctamente.")
                    } else {
                        console.warn("⚠️ Error en impresión:", res.message);
                        notifyAlert(res.message || "Error al imprimir el ticket.", "error");
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
        let product_id = 0,
            piece_id = 0,
            service_id = 0,
            cost = 0,
            quantity = 1,
            discount = 0;
        let description = "",
            variant_id = [],
            total_variant = 0;

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
                product_id,
                piece_id,
                service_id,
                description,
                quantity,
                cost,
                price,
                taxes: tax,
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

                    // Calcula el total, el descuento y el impuesto por unidad, solo si los valores son mayores a cero
                    var totalPrice = element.quantity * element.price;
                    var totalDiscount = (element.discount > 0) ? (element.quantity * element.discount) : 0; // Si el descuento es mayor que 0, lo calculamos
                    var totalTax = (element.tax > 0) ? (element.quantity * element.tax) : 0; // Si el impuesto es mayor que 0, lo calculamos

                    // El total final es el precio total más el impuesto menos el descuento
                    var total = totalPrice + totalTax - totalDiscount;

                    // Formateo de precio y total
                    var price = format.format(element.price); // Precio por unidad
                    var formattedTotalDiscount = format.format(totalDiscount); // Descuento total
                    var formattedTotalTax = format.format(totalTax); // Impuesto total

                    // Añade una nueva fila a la tabla
                    document.querySelector('#rows').innerHTML += `
                    <tr>
                        <td>${element.description}</td>
                        <td>${element.quantity}</td>
                        <td>${price}</td>
                        <td class="hide-cell">${formattedTotalTax} - ${element.tax > 0 ? element.tax : 0}%</td> <!-- Impuesto por unidad, solo si es mayor que 0 -->
                        <td>${formattedTotalDiscount}</td> <!-- Descuento total, solo si es mayor que 0 -->
                        <td>${format.format(total)}</td> <!-- Total con impuesto y descuento -->
                        <td>
                            <span class="action-delete" onClick="DeleteItemQ(${index});"><i class="fas fa-backspace"></i></span>
                        </td>
                    </tr>`;
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

    let action = null;

    if (pageURL.includes("invoices/addpurchase")) {
        action = "agregar_detalle_temporal";
    } else if (pageURL.includes("invoices/edit") || pageURL.includes("invoices/add_order")) {
        action = "agregar_detalle_venta";
    }

    if (!action) return;

    const tipo = $('input:radio[name=tipo]:checked').val();

    let description, cost, quantity, discount, piece_id = 0,
        product_id = 0,
        service_id = 0,
        variant_id, total_variant = 0;

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
        const action2 = pageURL.includes("invoices/addpurchase") ? "asignar_variantes_temporales" :
            pageURL.includes("invoices/edit") ? "asignar_variantes" :
                pageURL.includes("invoices/add_order") ? "asignar_variantes" :
                    null;

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
            errorCallback: (err) => {
                console.error(err);
                notifyAlert("Ha ocurrido un error inesperado", "error")
            }
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
            tax_value: $('#taxes').val() || 0
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
            tax_value: $('#taxes').val() || 0

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

        // Calcula el total, el descuento y el impuesto por unidad, solo si los valores son mayores a cero
        var totalPrice = element.quantity * element.price;
        var totalDiscount = (element.discount > 0) ? (element.quantity * element.discount) : 0; // Si el descuento es mayor que 0, lo calculamos
        var totalTax = (element.tax > 0) ? (element.quantity * element.tax) : 0; // Si el impuesto es mayor que 0, lo calculamos

        // El total final es el precio total más el impuesto menos el descuento
        var total = totalPrice + totalTax - totalDiscount;

        // Formateo de precio y total
        var price = format.format(element.price); // Precio por unidad
        var formattedTotalDiscount = format.format(totalDiscount); // Descuento total
        var formattedTotalTax = format.format(totalTax); // Impuesto total

        // Añade una nueva fila a la tabla
        document.querySelector('#rows').innerHTML += `
    <tr>
        <td>${element.description}</td>
        <td>${element.quantity}</td>
        <td>${price}</td>
        <td class="hide-cell">${formattedTotalTax} - ${element.tax > 0 ? element.tax : 0}%</td> <!-- Impuesto por unidad, solo si es mayor que 0 -->
        <td>${formattedTotalDiscount}</td> <!-- Descuento total, solo si es mayor que 0 -->
        <td>${format.format(total)}</td> <!-- Total con impuesto y descuento -->
        <td>
            <span class="action-delete" onClick="DeleteItemQ(${index});"><i class="fas fa-backspace"></i></span>
        </td>
    </tr>`;


    });

    CalcQuote(QuoteLocalStorage);
}

// Calcular precio total

function CalcQuote(arr) {

    let subtotal = 0;
    let taxes = 0;
    let total = 0;
    let discount = 0;

    // Recorre cada elemento para calcular los valores
    arr.forEach((element) => {

        // Calcula el subtotal sumando la cantidad * precio
        subtotal += parseFloat(element.quantity) * parseFloat(element.price.replace(/,/g, ""));

        // Suma el descuento total, considerando el descuento por unidad multiplicado por la cantidad
        discount += parseFloat(element.discount) * parseFloat(element.quantity);

        // Calcula el impuesto total, considerando el impuesto por unidad multiplicado por la cantidad
        if (parseFloat(element.tax_value) > 0) {
            taxes += (parseFloat(element.tax_value) / 100) * parseFloat(element.quantity) * parseFloat(element.price.replace(/,/g, ""));
        }
    });

    // Formateo de los valores calculados
    var sub = format.format(subtotal.toFixed(2)); // Subtotal formateado
    var totalDiscount = format.format(discount.toFixed(2)); // Descuento total formateado
    var totalTaxes = format.format(taxes.toFixed(2)); // Impuesto total formateado

    // Calcula el total final con impuestos y descuento
    var final = format.format((subtotal + taxes - discount).toFixed(2));

    // Vaciamos los valores actuales y los mostramos actualizados
    document.querySelector('#price').innerHTML = ""; // Vaciar precios de la factura

    document.querySelector('#price').innerHTML += `
    <span><input type="text" class="invisible-input" value="${sub}" id="in-subtotal" disabled></span>
    <span><input type="text" class="invisible-input" value="${totalDiscount}" id="in-discount" disabled></span>
    <span><input type="text" class="invisible-input" value="${totalTaxes}" id="in-taxes" disabled></span>
    <span><input type="text" class="invisible-input" value="${final}" id="in-total" disabled></span>
    <input type="hidden" name="" value="${final}" id="total_invoice">
`;

    $("#SaveQuote").show(); // Muestra el botón para registrar la cotización

}


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
        verbose: false
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

        const action = pageURL.includes('invoices/quote') ?
            "crear_detalle_cotizacion" :
            "agregar_detalle_cotizacion";

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