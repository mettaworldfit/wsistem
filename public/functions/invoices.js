// import { factura_venta, orden_venta } from "/public/test.js?v=1.0.1";
// import { calculateTotalInvoice, cashBack } from "/public/functions.js?v=1.0.1";

import { factura_venta, orden_venta } from "../test.js";
import { calculateTotalInvoice, cashBack } from "../functions.js";

$(document).ready(function () {

    // Ocultar botones por defecto (cotización, editar última factura, tipos de facturación)
    $('#SaveQuote, #last_invoice_edit, #credit-in-finish, #credit-in-finish-receipt, #cash-in-finish-receipt, #cash-in-finish').hide();

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

    /**
     * Actualiza el detalle de la factura según la página actual y oculta los elementos relacionados con pagos.
     * 
     * Esta función determina qué tabla se debe actualizar en función de la URL de la página y luego recarga los datos
     * de la tabla correspondiente usando el método `ajax.reload()` de DataTables. Además, oculta los elementos de pago
     * relacionados con el final de la transacción.
     */
    function reload() {
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
            } else if (pageURL.includes("invoices/add_order")) {
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
                            reload();

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

    // Quitar formato numérico (comas)
    const unformat = val => val?.replace(/,/g, '') || 0;

    // Ocultar botones de facturación
    const hideFinishButtons = () => {
        $('#credit-in-finish, #credit-in-finish-receipt, #cash-in-finish, #cash-in-finish-receipt').hide();
    };

    // Mostrar botones de facturación
    const showFinishButtons = () => {
        $('#credit-in-finish, #credit-in-finish-receipt, #cash-in-finish, #cash-in-finish-receipt').show();
    };

    // Obtener datos para imprimir recibo
    const getReceiptData = () => ({
        customer: $('#select2-credit-in-customer-container').attr('title'),
        seller: $('#credit-in-seller').val(),
        payment_method: $('#select2-credit-in-method-container').attr('title'),
        description: $('#observation').val(),
        total_invoice: unformat($('#credit-topay').val()),
        subtotal: unformat($('#in-subtotal').val()),
        discount: unformat($('#in-discount').val()),
        taxes: unformat($('#in-taxes').val()),
        total: unformat($('#in-total').val()),
        pay: $('#credit-pay').val(),
        date: $('#credit-in-date').val()
    });

    // Wrapper AJAX → Promise
    function ajaxPromise(options) {
        return new Promise((resolve, reject) => {
            $.ajax({
                ...options,
                success: resolve,
                error: reject
            });
        });
    }

    $('#credit-in-finish').on('click', () => {
        CREDIT_INV_FINISH();
    });

    $('#credit-in-finish-receipt').on('click', () => {
        CREDIT_INV_FINISH(true, getReceiptData());
    });

    // FACTURA A CREDITO    
    async function CREDIT_INV_FINISH(receipt = false, receiptData = {}) {

        try {
            hideFinishButtons();

            const pending = unformat($('#credit-pending').val());


            //   1️⃣ CREAR FACTURA A CRÉDITO
            const invoiceId = await ajaxPromise({
                type: "POST",
                url: SITE_URL + "services/invoices.php",
                data: {
                    action: "factura_credito",
                    customer_id: $('#credit-in-customer').val(),
                    payment_method: $('#credit-in-method').val(),
                    description: $('#observation').val(),
                    total_invoice: unformat($('#credit-topay').val()),
                    pay: $('#credit-pay').val(),
                    date: $('#credit-in-date').val()
                }
            });

            if (!invoiceId || invoiceId <= 0) {
                throw invoiceId;
            }


            // 2️⃣ REGISTRAR DETALLE
            const action = pageURL.includes('invoices/add_order')
                ? 'registrar_detalle_orden_venta'
                : 'registrar_detalle_de_venta';

            const detailRes = await ajaxPromise({
                type: "POST",
                url: SITE_URL + "services/invoices.php",
                data: {
                    action,
                    invoice_id: invoiceId,
                    order_id: $('#order_id').val(),
                    date: $('#credit-in-date').val()
                }
            });

            if (!detailRes) {
                throw detailRes;
            }


            // 3️⃣ UI + IMPRESIÓN
            mysql_row_affected();
            reload();
            resetCreditFields(pending, invoiceId);

            if (receipt === true) {
                // printer(invoiceId, detailRes, receiptData, "credit");
                printerInvoice(invoiceId)
            }

        } catch (err) {
            console.error(err);
            mysql_error(err || 'Error inesperado');
            showFinishButtons();
        }
    }


    // LIMPIEZA DE UI
    function resetCreditFields(pending, invoiceId) {

        $('#in-subtotal').val('0');
        $('#in-taxes').val('0');
        $('#in-discount').val('0');
        $('#in-total').val('0');

        $('#credit-pending').val(format.format(pending));

        $('#buttons').hide();

        $('#last_invoice_edit')
            .show()
            .attr('href', SITE_URL + '/invoices/edit&id=' + invoiceId);
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
                        printerInvoice(invoice_id)

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

                    reload(); // Actualizar datos
                },
                errorCallback: (err) => {
                    console.error(err)
                }
            })
        }
    }

    // Generar factura pdf
    function GeneratePDF(invoice) {

        const data = {
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
    // $('#credit-in-finish').on('click', (e) => {

    //     CREDIT_INV_FINISH()

    // });

    // $('#credit-in-finish-receipt').on('click', (e) => {

    //     data = {
    //         customer: $('#select2-credit-in-customer-container').attr('title'),
    //         seller: $('#credit-in-seller').val(),
    //         payment_method: $('#select2-credit-in-method-container').attr('title'),
    //         description: $('#observation').val(),
    //         total_invoice: $('#credit-topay').val().replace(/,/g, ""),
    //         subtotal: $('#in-subtotal').val().replace(/,/g, ""),
    //         discount: $('#in-discount').val().replace(/,/g, ""),
    //         taxes: $('#in-taxes').val().replace(/,/g, ""),
    //         total: $('#in-total').val().replace(/,/g, ""),
    //         pay: $('#credit-pay').val(),
    //         date: $('#credit-in-date').val()
    //     }

    //     CREDIT_INV_FINISH(true, data)

    // });


    // function CREDIT_INV_FINISH(receipt = false, data = {}) {

    //     // Ocultar los botones de facturar en ambos modal para evitar insertar datos vacios

    //     $('#credit-in-finish').hide()
    //     $('#credit-in-finish-receipt').hide()
    //     $('#cash-in-finish').hide()
    //     $('#cash-in-finish-receipt').hide()
    //     var pending = $('#credit-pending').val().replace(/,/g, "");

    //     $.ajax({
    //         type: "post",
    //         url: SITE_URL + "services/invoices.php",
    //         data: {
    //             action: "factura_credito",
    //             customer_id: $('#credit-in-customer').val(),
    //             payment_method: $('#credit-in-method').val(),
    //             description: $('#observation').val(),
    //             total_invoice: $('#credit-topay').val().replace(/,/g, ""),
    //             pay: $('#credit-pay').val(),
    //             date: $('#credit-in-date').val()
    //         },
    //         success: function (res) {
    //             if (res > 0) {

    //                 REGISTER_DETAIL_ON_CREDIT(res, data, receipt); // Cargar de nuevo el detalle
    //                 $('#buttons').hide() // Ocultar botones luego de facturar la orden

    //                 // Vaciar campos
    //                 $('#in-subtotal').val('0')
    //                 $('#in-taxes').val('0')
    //                 $('#in-discount').val('0')
    //                 $('#in-total').val('0')
    //                 $('#credit-pending').val(format.format(pending)) // Imprimir valor pendiente en el modal

    //                 $('#last_invoice_edit').show()
    //                 $('#last_invoice_edit').attr('href', SITE_URL + '/invoices/edit&id=' + res) // botón para editar la  última factura agregada


    //             } else {
    //                 mysql_error(res)
    //                 // Ocultar los botones de facturar en ambos modal para evitar insertar datos vacios
    //                 $('#credit-in-finish').hide()
    //                 $('#credit-in-finish-receipt').hide()
    //                 $('#cash-in-finish').hide()
    //                 $('#cash-in-finish-receipt').hide()
    //             }
    //         }
    //     });

    //     function REGISTER_DETAIL_ON_CREDIT(invoice_id, data, receipt) {

    //         const action = pageURL.includes('invoices/add_order') ? 'registrar_detalle_orden_venta' :
    //             'registrar_detalle_de_venta';

    //         $.ajax({
    //             type: "post",
    //             url: SITE_URL + "services/invoices.php",
    //             data: {
    //                 action: action,
    //                 invoice_id: invoice_id,
    //                 order_id: $('#order_id').val(),
    //                 date: $('#credit-in-date').val()
    //             },
    //             success: function (res) {

    //                 if (res != "") {

    //                     mysql_row_affected()
    //                     reload()

    //                     // Imprimir ticket 
    //                     if (receipt == true) {
    //                         printer(invoice_id, res, data, "credit");
    //                     }

    //                 } else {
    //                     mysql_error(res)
    //                 }
    //             }
    //         });

    //     }
    // }


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

                factura_venta(dataInv, data.detalle) // Imprimir             
            },
            verbose: false
        });
    }




    /**============================================================= 
    * C.R.U.D DETALLE / FACTURAS
    ===============================================================*/

    // Agregar producto al detalle temporal / detalle de venta
    $('#addDetailItem').on('submit', function (event) {
        event.preventDefault();  // Prevenir el envío del formulario

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
                    reload()
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
                    reload();
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


    // Eliminar item del detalle temporar / detalle de venta
    $(document).on('click', '.erase-item', function () {

        const id = $(this).data('id');

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

                calculateTotalInvoice() // Cargar totales
            },
            errorCallback: (err) => {
                console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        });
    })

    // Eliminar factura de venta
    $(document).on('click', '.erase_invoice', function () {
        const id = $(this).data('id');

        alertify.confirm("Eliminar factura", "¿Estas seguro que deseas eliminar esta factura? ",
            function () {

                sendAjaxRequest({
                    url: "services/invoices.php",
                    data: {
                        action: "eliminar_factura",
                        id: id
                    },
                    successCallback: (res) => {
                        if (pageURL.includes("invoices/index")) {
                            dataTablesInstances['invoices'].ajax.reload(null, false)
                        } else if (pageURL.includes("reports/sales")) {
                            dataTablesInstances['report_venta'].ajax.reload(null, false);
                        } else {
                            dataTablesInstances['today'].ajax.reload(null, false);
                        }
                    },
                    errorCallback: (err) => {
                        console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                        notifyAlert(err, "error", 3000)
                    }
                })
            }, function () {

            });
    })

    // Actualizar datos de la factura
    $('#updateInvoice').on('submit', function (e) {
        e.preventDefault()

        let formData = new FormData(this);

        formData.append('action', 'actualizar_factura');
        formData.append('observation', $('#observation').val());
        formData.append('id', $('#invoice_id').val());

        sendAjaxRequest({
            url: "services/invoices.php",
            data: formData,
            successCallback: (res) => {
                notifyAlert("Datos actualizados correctamente", "success", 2000)
            },
            errorCallback: (err) => {
                console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        })
    })

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

                Object.assign(data, totals);
                orden_venta(detail, data)  // Envía los datos a la impresora
            }
        });
    });

    /**============================================================= 
    * ORDENES / CRUD / IMPRESION
    ===============================================================*/

    // Evento que se ejecuta cuando se abre el modal de edición de orden
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


    // Agregar orden de venta
    $('#formOrderSales').on('submit', function (e) {
        e.preventDefault()

        let formData = new FormData(this)
        formData.append("action", "registrar_orden")

        sendAjaxRequest({
            url: "services/invoices.php",
            data: formData,
            successCallback: (res) => {
                $('input[type="text"]').val('');
                dataTablesInstances['orders'].ajax.reload(null, false);
                window.location.href = SITE_URL + 'invoices/add_order&id=' + res
            },
            errorCallback: (err) => {
                console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        })
    })


    // Editar orden de venta
    $('#editOrderSales').on('submit', function (e) {
        e.preventDefault()

        let formData = new FormData(this)
        formData.append("action", "editar_orden")

        sendAjaxRequest({
            url: "services/invoices.php",
            data: formData,
            successCallback: (res) => {
                notifyAlert("Orden actualizada", "success", 3000)
            },
            errorCallback: (err) => {
                console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        })
    })


    // Actualizar estado de la orden
    $('table').on('change', '#status_order', function () {
        var selectedValue = $(this).val(); // Obtener el valor seleccionado
        var orderId = $(this).find('option:selected').attr('order_id'); // Obtener order_id

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                status: selectedValue,
                order_id: orderId,
                action: 'actualizar_estado_orden'
            },
            successCallback: () => dataTablesInstances['orders'].ajax.reload(null, false),
            errorCallback: (err) => {
                console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        })
    });


    // Eliminar orden
    $(document).on('click', '.erase_order', function () {

        const id = $(this).data('id')

        alertify.confirm("Eliminar orden", "¿Estas seguro que deseas eliminar esta orden? ",
            function () {
                sendAjaxRequest({
                    url: "services/invoices.php",
                    data: {
                        id: id,
                        action: 'eliminar_orden'
                    },
                    successCallback: () => dataTablesInstances['orders'].ajax.reload(null, false),
                    errorCallback: (err) => {
                        console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                        notifyAlert(err, "error", 3000)
                    }
                });
            },
            function () {

            });
    })

    /**============================================================= 
   * COTIZACIONES / CRUD / IMPRESION
   ===============================================================*/

    let ArrayItem = [];
    let QuoteLocalStorage;

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
                    calculateStorage(ArrayItem);

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
                            <span class="action-delete erase_item" data-id="${index}"><i class="fas fa-backspace"></i></span>
                        </td>
                    </tr>`;
                });
            }
        })
    }

    // Maneja el envío del formulario para agregar un detalle a la cotización.
    $("#addQuoteDetail").on('submit', function (e) {
        e.preventDefault();

        // Determina el tipo seleccionado
        const tipo = $('input:radio[name=tipo]:checked').val();

        // Crea los datos correspondientes según el tipo seleccionado
        let data = createData(tipo);

        // Si estamos editando la cotización, se agrega a la base de datos
        if (pageURL.includes('invoices/edit_quote')) {
            addItemDB($("#quote_id").val(), data);
        } else {
            // Si no estamos editando, se guarda en localStorage
            ArrayItem.push(data);
            createStorage(ArrayItem); // Guardar el detalle en localStorage
        }
    });

    /**
     * Crea el objeto de datos según el tipo seleccionado.
     * @param {string} tipo - Tipo de ítem (servicio, pieza, producto).
     * @returns {Object} - Objeto con los datos para el detalle.
     */
    function createData(tipo) {
        let data = {};

        switch (tipo) {
            case 'servicio':
                data = {
                    quantity: $('#service_quantity').val(),
                    discount: $('#discount_service').val().replace(/,/g, ""),
                    service_id: $('#service').val(),
                    description: $('#select2-service-container').attr('title').trim(),
                    price: $('#price_out').val().replace(/,/g, ""),
                    tax_value: 0
                };
                break;
            case 'pieza':
                data = {
                    piece_id: $('#piece').val(),
                    discount: $('#discount').val().replace(/,/g, ""),
                    quantity: $('#quantity').val(),
                    description: $('#select2-piece-container').attr('title').trim(),
                    price: $('#price_out').val().replace(/,/g, ""),
                    tax_value: $('#taxes').val() || 0
                };
                break;
            case 'producto':
                data = {
                    discount: $('#discount').val().replace(/,/g, ""),
                    product_id: $('#product').val(),
                    quantity: $('#quantity').val(),
                    price: $('#price_out').val().replace(/,/g, ""),
                    description: $('#select2-product-container').attr('title').trim(),
                    tax_value: $('#taxes').val() || 0
                };
                $('.empty-variant').css("border", "1px solid #ced4da"); // Validación de variante
                break;
            default:
                console.error('Tipo no válido');
                return null;
        }

        return data;
    }

    /**
     * Agrega el detalle a la base de datos.
     * @param {string} id - ID de la cotización.
     * @param {Object} data - Datos del detalle de la cotización.
     */
    function addItemDB(id, data) {
        registerDetail(id, data); // Llama a la función para registrar en la base de datos
    }

    // Crear el Array en el localStorage
    function createStorage(Arr) {
        localStorage.setItem('detalle_cotizacion', JSON.stringify(Arr));
        displayStorage(); // Mostrar localStorage

    }

    function displayStorage() {

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
                    <span class="action-delete erase_item" data-id="${index}"><i class="fas fa-backspace"></i></span>
                </td>
            </tr>`;
        });

        calculateStorage(QuoteLocalStorage);
    }

    // Calcular todo el total
    function calculateStorage(arr) {

        let subtotal = 0;
        let taxes = 0;
        let total = 0;
        let discount = 0;

        // Recorre cada elemento para calcular los valores
        arr.forEach((element) => {

            // Calcula el subtotal sumando la cantidad * precio
            subtotal += (parseFloat(element.quantity) || 0) * (parseFloat(element.price.replace(/,/g, "")) || 0);

            // Suma el descuento total, considerando el descuento por unidad multiplicado por la cantidad
            discount += (parseFloat(element.discount) || 0) * (parseFloat(element.quantity) || 0);

            // Calcula el impuesto total, considerando el impuesto por unidad multiplicado por la cantidad
            if (parseFloat(element.tax_value) > 0) {
                taxes += ((parseFloat(element.tax_value) / 100) || 0) * (parseFloat(element.quantity) || 0) * (parseFloat(element.price.replace(/,/g, "")) || 0);
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

    // Crear cotizacion
    $('#formQuote').on('submit', async function (e) {
        e.preventDefault();

        // Recoger datos del formulario
        const quoteData = {
            action: "registrar_cotizaciones",
            customer_id: $("#customer").val(),
            total: $("#total_invoice").val().replace(/,/g, ""),
            date: $("#date").val(),
            observation: $("#observation").val()
        };

        try {
            // Enviar solicitud AJAX
            const res = await sendQuoteRequest(quoteData);

            // Registrar los detalles de la cotización si la respuesta es exitosa
            const success = await registerDetail(res);

            // Si todo sale bien
            if (success) {
                generateQuotePdf(res); // Generar PDF
                if ($("#sendMail").is(':checked')) return sendMailQuote(res); // Enviar mail
                eraseAllStorage(); // Borrar todo del localStorage
            }


        } catch (err) {
            console.error('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err);
            notifyAlert(err, "error", 3000);
        }

        function sendQuoteRequest(data) {
            return new Promise((resolve, reject) => {
                sendAjaxRequest({
                    url: "services/invoices.php",
                    data: data,
                    successCallback: (res) => resolve(res),
                    errorCallback: (err) => reject(err)
                });
            });
        }
    });


    // Agregar detalle de cotizacion
    async function registerDetail(id, detail = "") {

        async function register(id, detail) {

            const action = pageURL.includes('invoices/quote') ?
                "crear_detalle_cotizacion" :
                "agregar_detalle_cotizacion";

            sendAjaxRequest({
                url: "services/invoices.php",
                data: {
                    action: action,
                    id: id,
                    description: detail.description,
                    quantity: detail.quantity,
                    price: detail.price,
                    taxes: detail.tax_value,
                    discount: detail.discount,
                },
                successCallback: (res) => {
                    if (pageURL.includes("invoices/edit_quote")) {
                        calculateTotalInvoice(); // Cargar total de la cotización
                        $('#Detalle').load(location.href + " #Detalle");
                    }
                },
                errorCallback: (err) => {
                    console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                    notifyAlert(err, "error", 3000)
                }
            })
        }

        try {
            if (!pageURL.includes("invoices/edit_quote")) {
                let arrayL = JSON.parse(localStorage.getItem("detalle_cotizacion"));
                for (const element of arrayL) {
                    await register(id, element); // Esperar el registro de cada detalle
                }
            } else {
                await register(id, detail); // Registrar un solo detalle
            }

            return true;

        } catch (error) {
            console.error('Error en registerDetail:', error);
            return false;
        }
    }

    // Borrar todo del localstorage
    $('#eraseQuote').on('click', function () {
        eraseAllStorage()
    })

    function eraseAllStorage() {

        localStorage.removeItem('detalle_cotizacion'); // Vaciar localstorage
        ArrayItem = []; // Vaciar Arreglo

        calculateStorage(ArrayItem); // Calcular precios
        $('#rows').load(location.href + " #rows"); // actualizar detalle
        $("#SaveQuote").css("display", "none"); // Botón registrar cotización
    }

    // Eliminar item del detalle y localstorage
    $(document).on('click', '.erase_item', function () {
        const id = $(this).data("id");
        eraseItemStorage(id)

    })

    function eraseItemStorage(index) {

        if (!pageURL.includes('invoices/edit_quote')) {

            ArrayItem.splice(index, 1);
            createStorage(ArrayItem)

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
                errorCallback: (err) => {
                    console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                    notifyAlert(err, "error", 3000)
                }
            })
        }
    }

    // Actualizar cotizacion
    $('#editQuote').on('submit', function (e) {
        e.preventDefault()

        let formData = new FormData(this)
        formData.append("action", "actualizar_cotizaciones")
        formData.append("observation", $("#observation").val())

        sendAjaxRequest({
            url: "services/invoices.php",
            data: formData,
            successCallback: (res) => {
                notifyAlert("Datos actualizados correctamente", "success", 2000)
            },
            errorCallback: (err) => {
                console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        });
    })

    // Eliminar cotizacion
    $(document).on('click', '.erase_quote', function () {

        const id = $(this).data('id')
        alertify.confirm("Eliminar cotización", "¿Estas seguro que deseas eliminar esta cotización? ",
            function () {
                sendAjaxRequest({
                    url: "services/invoices.php",
                    data: {
                        action: "eliminar_cotizacion",
                        id: id
                    },
                    successCallback: () => dataTablesInstances['quotes'].ajax.reload(null, false),
                    errorCallback: (err) => {
                        console.log('%c[INVOICES]', 'color:#b51717;font-weight:bold;', err)
                        notifyAlert(err, "error", 3000)
                    }
                });
            },
            function () {

            });
    })

    // Generar cotizacion PDF al dar click
    $('#QuotePDF').on('click', (e) => {
        e.preventDefault()

        var id = $('#quote_id').val()
        generateQuotePdf(id)
    })


    // Enviar cotizacion por Email 
    $('#sendMailQuote').on('click', (e) => {
        e.preventDefault()

        var id = $('#quote_id').val()
        sendMailQuote(id)
    })

    // Generar archivo PDF 
    function generateQuotePdf(quote_id) {

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

    // Enviar la cotizacion por Mail
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





}); // Ready