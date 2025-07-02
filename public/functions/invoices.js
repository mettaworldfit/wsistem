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
    let action, id;
 
    if (pageURL.includes("invoices/addpurchase")) {
        action = 'precios_detalle_temp';
    } else if (pageURL.includes("invoices/edit_quote")) {
        action = 'total_cotizacion';
        id = $("#quote_id").val();
    } else if (pageURL.includes("invoices/edit")) {
        action = 'precios_detalle_venta';
        id = $("#invoice_id").val();
    }

    // Cargar totales según acción
    loadInvoiceTotals(action, id);

    // Función para cargar totales de la factura
    function loadInvoiceTotals(action, id) {

        sendAjaxRequest({
            url: "services/invoices.php",
            data: { action, id },
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
    const tableKey = pageURL.includes('invoices/addpurchase') ? 'detailTemp' : 'editInvoice';
    dataTablesInstances[tableKey].ajax.reload();

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

    if (
        pageURL.includes("invoices/addpurchase") ||
        pageURL.includes("invoices/edit") ||
        pageURL.includes("invoices/edit_quote") ||
        pageURL.includes("invoices/quote")
    ) {
        // Calcular total actual de la factura
        calculateTotalInvoice();

        // Ocultar todos los tipos inicialmente
        $('.piece, .service, #piece_code, #add_item_free').hide();
        $('#piece, #rp_service').attr('required', false);

        // Manejar el cambio de tipo de ítem (pieza, producto o servicio)
        $('input:radio[name=tipo]').change(function () {
            const tipo = $(this).val();

            // Limpiar campos comunes
            $('#code, #piece_code, #stock, #discount, #quantity, #price_out').val('');

            switch (tipo) {
                case "pieza":
                    // Mostrar campos relacionados con piezas
                    $('.piece').show();
                    $('.product, .service').hide();
                    $('#piece_code').show();
                    $('.product-piece, .discount').show();
                    $('#code').hide();

                    // Requerimientos
                    $('#rp_service, #product').attr('required', false);
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

                    // Requerimientos
                    $('#rp_service').attr('required', false);
                    $('#product').attr('required', true);
                    $('#piece').attr('required', false);

                    // Placeholder de Select2
                    $('#select2-product-container').html("Buscar productos");
                    break;

                case "servicio":
                    // Mostrar campos relacionados con servicios
                    $('.service').show();
                    $('.product, .piece, .product-piece').hide();
                    $('.discount, #cost-field').hide();
                    $('#discount_service').show();
                    $('#add_item_free').hide();

                    // Requerimientos
                    $('#rp_service').attr('required', true);
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
            total_invoice: parseFloat($('#cash-topay').val().replace(/,/g, "")) || 0,
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
                    $('#cash-received').val($('#cash-topay').val())
                    $('#cash-pending').val('0.00')

                    $('#last_invoice_edit').show()
                    $('#last_invoice_edit').attr('href', SITE_URL + 'invoices/edit&id=' + res) // botón para editar la  última factura agregada

                }
            },
            errorCallback: (res) => mysql_error(res)
        })

        // Función separada para registrar detalles y manejar impresión

        function registerInvoiceDetails(invoice_id, data, receipt) {

            sendAjaxRequest({
                url: "services/invoices.php",
                data: {
                    action: 'registrar_detalle_de_venta',
                    invoice_id: invoice_id,
                    date: data.date
                },
                successCallback: (res) => {
                    // Calcular devolucion

                    var topay = $('#cash-topay').val().replace(/,/g, "");
                    var received = $('#calc_return').val();
                    let calc_return;

                    if (received != '') {

                        calc_return = received - topay;
                        cashback(format.format(calc_return));

                    } else {
                        mysql_row_affected()
                    }

                    reloadInvoiceDetail() // Actualizar datos

                    // Imprimir ticket 
                    if (receipt == true) {
                        printer(invoice_id, res, data, "cash");

                        // Enviar email
                        if ($("#sendMail").is(':checked')) return SendmailCashft(invoice_id)

                    } else {

                        GeneratePDF(invoice_id) // Imprimir PDF
                        // Enviar email
                        if ($("#sendMail").is(':checked')) return SendmailCashft(invoice_id)

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

        var width = 500;
        var height = 500;

        // Centrar la ventana
        var x = parseInt((window.screen.width / 2) - (width / 2));
        var y = parseInt((window.screen.height / 2) - (height / 2));

        var url = SITE_URL + 'src/phpmailer/ventas.php?f=' + invoice + '&sub=' + data.subtotal + '&dis=' + data.discount + '&tax=' + data.taxes + '&total=' + data.total + '&method=' + data.method + '&date=' + data.date;
        window.open(url, 'Factura', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

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

        var pay = parseInt($('#credit-pay').val());
        var pending = parseInt($('#credit-pending').val().replace(/,/g, ""));

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
            customer: $('#select2-cash-in-customer-container').attr('title'),
            seller: $('#cash-in-seller').val(),
            payment_method: $('#select2-cash-in-method-container').attr('title'),
            description: $('#observation').val(),
            total_invoice: $('#cash-topay').val().replace(/,/g, ""),
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            taxes: $('#in-taxes').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
            pay: $('#credit-pay').val(),
            bonus: $('#cash-bonus').val().replace(/,/g, ""),
            date: $('#cash-in-date').val()
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
                pending: pending,
                date: $('#credit-in-date').val()
            },
            success: function (res) {
                if (res > 0) {

                    REGISTER_DETAIL_ON_CREDIT(res, data, receipt); // Cargar de nuevo el detalle

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
            $.ajax({
                type: "post",
                url: SITE_URL + "services/invoices.php",
                data: {
                    action: 'registrar_detalle_de_venta',
                    invoice_id: invoice_id,
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
     * TODO: Imprimir factura
     */

    function printer(invoice_id, detail, data, type) {
        console.log('imprimiendo.....')


        let file;
        if (type == "cash") {
            file = "factura_al_contado.php"
        } else if (type == "credit") {
            file = "factura_credito.php"
        }
        console.log(data)

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
                // 'public/tickets/' + file
            }
        });

    }





    // Agregar producto al detalle temporal y detalle de venta sin precio

    $("#add_item_free").on("click", () => {

        // Verificar a cual detalle insertar el producto

        let action;
        if (pageURL.includes("invoices/addpurchase")) {
            action = "agregar_detalle_temporal";
        } else if (pageURL.includes("invoices/edit")) {
            action = "agregar_detalle_venta";
        }

        // Validar tipo de item
        let description;
        let quantity;
        let discount;
        let piece_id;
        let product_id;
        let service_id;
        let variant_id
        let total_variant = 0;

        if ($('input:radio[name=tipo]:checked').val() == 'servicio') {

            quantity = 1;
            discount = $('#discount_service').val().replace(/,/g, "");
            service_id = $('#service').val();
            piece_id = 0;
            product_id = 0;
            description = $('#select2-service-container').attr('title')
            addItem();

        } else if ($('input:radio[name=tipo]:checked').val() == 'pieza') {

            piece_id = $('#piece').val();
            service_id = 0;
            product_id = 0;
            discount = $('#discount').val().replace(/,/g, "")
            quantity = $('#quantity').val();
            description = $('#select2-piece-container').attr('title')
            addItem();

        } else if ($('input:radio[name=tipo]:checked').val() == 'producto') {

            piece_id = 0;
            service_id = 0;
            discount = $('#discount').val().replace(/,/g, "")
            product_id = $('#product').val();;
            quantity = $('#quantity').val();
            description = $('#select2-product-container').attr('title')
            variant_id = $('#variant_id').val();
            $('.empty-variant').css("border", "1px solid #ced4da");
            total_variant = $('#total_variant').val();

            // Si el producto tiene variantes
            if (total_variant > 0) {

                // Si hay variante seleccionada
                if (variant_id.length == quantity) {

                    addItem()
                } else {
                    $('.empty-variant').css("border", "1px solid red");
                }

            } else {
                addItem();
            }
        }

        function assignVariants(detail_id, array) {

            let action2;
            if (pageURL.includes("invoices/addpurchase")) {
                action2 = "asignar_variantes_temporales";
            } else if (pageURL.includes("invoices/edit")) {
                action2 = "asignar_variantes";
            }

            array.forEach(element => {
                $.ajax({
                    url: SITE_URL + "services/invoices.php",
                    method: "post",
                    data: {
                        action: action2,
                        variant_id: element,
                        detail_id: detail_id,

                    },
                    success: function (res) {

                        if (res == "ready") {


                        } else {
                            mysql_error(res)
                        }
                    }
                });
            });

        }


        function addItem() {

            var taxes = ($('#price_out').val().replace(/,/g, "") * $('#taxes').val()) / 100 // Calcular impuestos

            $.ajax({
                url: SITE_URL + "services/invoices.php",
                method: "post",
                data: {
                    action: action,
                    invoice: $('#invoice_id').val(),
                    product_id: product_id,
                    piece_id: piece_id,
                    service_id: service_id,
                    description: description,
                    quantity: quantity,
                    discount: parseInt($('#price_out').val().replace(/,/g, "")) + parseInt(taxes),
                    taxes: taxes,
                    price: $('#price_out').val().replace(/,/g, "")

                },
                success: function (res) {

                    if (res > 0) {

                        calculateTotalInvoice()
                        reloadInvoiceDetail()
                        if (total_variant > 0) {
                            assignVariants(res, variant_id); // Asignar variantes al detalle temporal
                        }

                    } else if (res == "duplicate") {
                        mysql_error('Este ítem ya ha sido agregado al detalle');
                    } else {
                        mysql_error(res)
                    }
                }
            });
        }

    })





    /**
     * ! Imprimir factura de venta
     *  Imprimir la factura luego de ser facturada en la sección editar factura
     */


    $('#printer_inv').on('click', (e) => {
        e.preventDefault;

        data = {
            customer: $('#select2-customer-container').attr('title').trim(),
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
        }

        $.ajax({
            type: "post",
            url: PRINTER_SERVER + "factura_venta.php",
            data: {
                detail: $('#detail_inv').val(),
                data: data,
            },
            success: function (res) {


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


}); // Ready


// Agregar producto al detalle temporal / detalle de venta

function addDetailItem() {
    // Verificar a cual detalle insertar el producto

    let action = pageURL.includes("invoices/addpurchase") ? "agregar_detalle_temporal"
        : pageURL.includes("invoices/edit") ? "agregar_detalle_venta"
            : null;
    if (!action) return;

    const tipo = $('input:radio[name=tipo]:checked').val();

    let description, cost, quantity, discount, piece_id = 0, product_id = 0, service_id = 0, variant_id, total_variant = 0;

    if (tipo === 'servicio') {
        cost = $('#service_cost').val().replace(/,/g, "");
        quantity = 1;
        discount = $('#discount_service').val().replace(/,/g, "");
        service_id = $('#service').val();
        description = $('#select2-service-container').attr('title');
        addItem();

    } else if (tipo === 'pieza') {
        piece_id = $('#piece').val();
        discount = $('#discount').val().replace(/,/g, "");
        quantity = $('#quantity').val();
        description = $('#select2-piece-container').attr('title');
        cost = $('#piece_cost').val();
        addItem();
        resetModal();

    } else if (tipo === 'producto') {
        discount = $('#discount').val().replace(/,/g, "");
        product_id = $('#product').val();
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

    // asignar variantes al detalle temporal

    function assignVariants(detail_id, variants) {
        const action2 = pageURL.includes("invoices/addpurchase") ? "asignar_variantes_temporales"
            : pageURL.includes("invoices/edit") ? "asignar_variantes"
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
        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: action,
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
            errorCallback: (res) => mysql_error(error),
            verbose: true

        });
    }
}

// Eliminar item del detalle temporar / detalle de venta

function deleteInvoiceDetail(id) {

    function getDeleteAction(url) {
        if (url.includes("invoices/addpurchase")) return "eliminar_detalle_temporal";
        if (url.includes("invoices/edit")) return "eliminar_detalle_venta";
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

            (pageURL.includes('invoices/addpurchase')) ?
                dataTablesInstances['detailTemp'].ajax.reload()
                : dataTablesInstances['editInvoice'].ajax.reload();

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
            quantity: "1",
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

        $.ajax({
            type: "post",
            url: SITE_URL + "services/invoices.php",
            data: {
                action: "eliminar_detalle_cotizacion",
                id: index
            },
            success: function (res) {

                if (res == "ready") {
                    dataTablesInstances['detailTemp'].ajax.reload(); // Actualizar detalle
                    calculateTotalInvoice() // Cargar total de la cotizacion
                } else {
                    mysql_error(res)
                }

            }
        });
    }
}


// Crear detalle de cotizacion

function saveQuote() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/invoices.php",
        data: {
            action: "registrar_cotizaciones",
            customer_id: $("#customer").val(),
            total: $("#total_invoice").val().replace(/,/g, ""),
            date: $("#date").val(),
            observation: $("#observation").val()
        },
        success: function (res) {

            if (res > 0) {

                RegisterDetail(res)

            } else {
                mysql_error(res)
            }

        }
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
        $.ajax({
            type: "post",
            url: SITE_URL + "services/invoices.php",
            data: {
                action: "agregar_detalle_cotizacion",
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
                        dataTablesInstances['detailTemp'].ajax.reload(); // Actualizar detalle

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
                successCallback: () => dataTablesInstances['invoices'].ajax.reload(null, false),
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