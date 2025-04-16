var pageURL = $(location).attr("pathname");

function mysql_row_affected() {
    alertify.alert(`<div class='row-affected'>
    <i class='icon-success far fa-check-circle'></i>
    <p>Registrado exitosamente</p>
    </div>`).set('basic', true);
}

function cashback(data) {
    alertify.alert(`<div class='row-affected d-flex flex-column'>
    <h2>Devolver</h2> <br>` + "<h1 class='text-danger'> $" + data + `</h1></div>`).set('basic', true);
}

function mysql_row_update() {
    alertify.alert(`<div class='row-affected'>
    <i class='icon-success far fa-check-circle'></i>
    <p>Registro actualizado correctamente</p>
    </div>`).set('basic', true);
}


function mysql_error(err) {
    alertify.alert(`<div class='error-info'>
    <i class='icon-error fas fa-exclamation-circle'></i> 
    <p>${err}</p>
    </div>`).set('basic', true);
}

const format = new Intl.NumberFormat('en'); // Formato 0,000


// Total de la factura

function invoice_total(bonus = 0) {

    let action;

    // Verificar cual es el precio del detalle que va a mostrar la ventana

    if (pageURL.includes("invoices/addpurchase")) {

        action = 'precios_detalle_temp';
        LoadTotal(action, $("#invoice_id").val())

    } else if (pageURL.includes("invoices/edit_quote")) {

        action = 'total_cotizacion';
        LoadTotal(action, $("#quote_id").val())

    } else if (pageURL.includes("invoices/edit")) {

        action = 'precios_detalle_venta';
        LoadTotal(action, $("#invoice_id").val())
    }

    function LoadTotal(action, id) {

        $.ajax({
            type: "post",
            url: SITE_URL + "services/invoices.php",
            data: {
                action: action,
                id: id
            },
            success: function(res) {

                var data = JSON.parse(res);

                var discount = format.format(data.descuentos);
                var taxes = format.format(data.taxes);
                var subtotal = format.format(data.precios);
                var total = format.format(parseFloat(data.precios) + parseFloat(data.taxes) - parseFloat(data.descuentos));

                var total_price = total.replace(/,/g, "")

                $('#total_price').val(total_price)
                $('#in-subtotal').val(subtotal)
                $('#in-taxes').val(taxes)
                $('#in-discount').val(discount)

                if (total != 'NaN') {
                    $('#in-total').val(total)

                } else {
                    $('#in-total').val('0')
                }

                // Modal Factura al contado y actualizar datos
                $('#cash-received').val('0.00')
                if (pageURL.includes("invoices/edit")) {

                    $('#cash-topay').val(data.total)
                    $('#cash-pending').val(data.pendiente)
                    $('#cash-received').val(data.recibido)

                    $('#cash-topay2').val(data.total)
                    $('#cash-pending2').val(data.pendiente)
                    $('#cash-received2').val(data.recibido)

                } else {

                    if (total != 'NaN') {
                        if (bonus != 0 || bonus == '') {
                            $('#cash-bonus').val(format.format(bonus))

                            var totalXbonus = format.format(total.replace(/,/g, "") - bonus);

                            $('#cash-topay').val(totalXbonus)
                            $('#cash-pending').val(totalXbonus)

                        } else {
                            $('#cash-topay').val(total)
                            $('#cash-pending').val(total)
                            $('#cash-bonus').val('0.00')
                        }

                    } else {
                        $('#cash-topay').val('0.00')
                        $('#cash-pending').val('0.00')
                        $('#cash-bonus').val('0.00')
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
                    $('#cash-in-finish').show();
                    $('#cash-in-finish-receipt').show()
                } else {
                    $('#cash-in-finish').hide();
                    $('#cash-in-finish-receipt').hide()
                }

                // Validar campo ingresar monto factura a crédito

                if (total != 'NaN') {
                    $('.pay').show();
                } else {
                    $('.pay').hide();
                }

            }
        }); // Ajax

    } // function LoadTotal

}

function reload() {
    // Actualizar detalle y toda la página

    $('#Detalle').load(location.href + " #Detalle");
    $('#cash-in-finish-receipt').hide()
    $('#cash-in-finish').hide()

    $('#credit-in-finish').hide()
    $('#credit-in-finish-receipt').hide()

}

function reset_modal() {
    $("#add_item_free").hide()
    $("#add_item").hide()
    $("#code").val('');
    $("#piece_code").val('');
    $("#stock").val('');
    $("#quantity").val('');
    $("#price_out").val('');
    $("#select2-variant_id-container").empty();
    $("#variant_id").attr("disabled", true)
    $('#select2-product-container').empty()
    $('#select2-piece-container').empty()
}


$(document).ready(function() {

    // Default
    $("#SaveQuote").css("display", "none"); // Botón registrar cotización
    $('#last_invoice_edit').hide() // Botón de editar última factura
    $('#credit-in-finish').hide() // Botón de factura a crédito
    $('#credit-in-finish-receipt').hide() // Botón de factura a crédito con ticket
    $('#cash-in-finish-receipt').hide() // Botón de factura al contado con ticket
    $('#cash-in-finish').hide() // Botón de factura al contado

    if (pageURL.includes("invoices/addpurchase") || pageURL.includes("invoices/edit") || pageURL.includes("invoices/edit_quote") || pageURL.includes("invoices/quote")) {
        invoice_total() // Cargar total de la factura actual

        // Cambiar tipo de item a agregar

        $('.piece').hide()
        $('.service').hide()
        $('#piece_code').hide()
        $('#piece').attr('required', false)
        $('#rp_service').attr('required', false)
        $("#add_item_free").hide()

        $('input:radio[name=tipo]').change(function() {
            if ($(this).val() == "pieza") {

                $('.piece').show()
                $('.product').hide()

                $('#piece_code').show()
                $('.product-piece').show()
                $('.discount').show()
                $('#code').hide()
                $('.service').hide()
                $('#rp_service').attr('required', false)
                $('#product').attr('required', false)
                $('#piece').attr('required', true)

                $('#piece_code').val('')
                $('#stock').val('')
                $('#discount').val('')
                $('#quantity').val('')
                $('#price_out').val('')

                $('#select2-piece-container').empty(); // Vaciar description
                $('#select2-piece-container').append("Buscar piezas"); // agregar a description

            } else if ($(this).val() == "producto") {

                $('.product').show()
                $('.piece').hide()

                $('#piece_code').hide()
                $('.product-piece').show()
                $('.discount').show()
                $('#code').show()
                $('.service').hide()
                $('#rp_service').attr('required', false)
                $('#product').attr('required', true)
                $('#piece').attr('required', false)

                $('#code').val('')
                $('#stock').val('')
                $('#discount').val('')
                $('#quantity').val('')
                $('#price_out').val('')

                $('#select2-product-container').empty(); // Vaciar description
                $('#select2-product-container').append("Buscar productos"); // agregar a description

            } else if ($(this).val() == "servicio") {

                $("#add_item_free").hide()
                $('.product').hide()
                $('.piece').hide()
                $('.service').show()
                $('.discount').hide()
                $('#discount_service').show()

                $('.product-piece').hide()
                $('#product').attr('required', false)
                $('#piece').attr('required', false)
                $('#rp_service').attr('required', true)
                $('#quantity').attr('required', false)
                $('#discount').attr('disabled', false)
                $('#price_out').attr('disabled', false)

                $('#add_item').show();

                $('#code').val('')
                $('#stock').val('')
                $('#quantity').val('')
                $('#price_out').val('')

                $('#select2-service-container').empty(); // Vaciar description
                $('#select2-service-container').append("Buscar servicios"); // agregar a description

            }
        });

    }


    /**
     *! Facturas de ventas
     -----------------------------------------------------------*/

    // Crear factura al contado

    $('#cash-in-finish').on('click', (e) => {
        e.preventDefault();

        CASH_INV_FINISH();
    })


    $('#cash-in-finish-receipt').on('click', (e) => {
        e.preventDefault();

        CASH_INV_FINISH(true);

    })

    function CASH_INV_FINISH(receipt = false) {

        data = {
            customer: $('#select2-cash-in-customer-container').attr('title'),
            seller: $('#cash-in-seller').val(),
            payment_method: $('#select2-cash-in-method-container').attr('title'),
            subtotal: $('#in-subtotal').val().replace(/,/g, ""),
            discount: $('#in-discount').val().replace(/,/g, ""),
            taxes: $('#in-taxes').val().replace(/,/g, ""),
            total: $('#in-total').val().replace(/,/g, ""),
            bonus: $('#cash-bonus').val().replace(/,/g, ""),
            date: $('#cash-in-date').val(),
            observation: $('#observation').val()
        }


        $.ajax({
            type: "post",
            url: SITE_URL + "services/invoices.php",
            data: {
                action: "factura_contado",
                customer_id: $('#cash-in-customer').val(),
                payment_method: $('#cash-in-method').val(),
                description: $('#observation').val(),
                total_invoice: $('#cash-topay').val().replace(/,/g, ""),
                bonus: data.bonus,
                date: data.date
            },
            success: function(res) {
                if (res > 0) {

                    REGISTER_DETAIL_ON_CASH(res, data, receipt);
                    $('#cash-received').val($('#cash-topay').val())
                    $('#cash-pending').val('0.00')

                    $('#last_invoice_edit').show()
                    $('#last_invoice_edit').attr('href', SITE_URL + 'invoices/edit&id=' + res) // botón para editar la  última factura agregada


                } else {
                    mysql_error(res)
                }
            }
        });

        // Registrar detalle de factura al contado

        function REGISTER_DETAIL_ON_CASH(invoice_id, data, receipt) {
            $.ajax({
                type: "post",
                url: SITE_URL + "services/invoices.php",
                data: {
                    action: 'registrar_detalle_de_venta',
                    invoice_id: invoice_id,
                    date: $('#cash-in-date').val()
                },
                success: function(res) {

                    if (res != "") {

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

                        reload() // Actualizar datos


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


                    } else {
                        mysql_error(res)
                    }
                }
            });
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

    $("#include_bond").click(function() {
        if ($("#include_bond").is(':checked')) {

            // Aplicar bono
            var customer_id = $('#cash-in-customer').val()

            if (customer_id > 1) {
                $.ajax({
                    type: "post",
                    url: SITE_URL + "services/invoices.php",
                    data: {
                        action: "consultar_bono",
                        customer_id: customer_id
                    },
                    success: function(res) {
                        var data = JSON.parse(res);

                        if (data.valor > 0) {
                            invoice_total(data.valor) // aplicar bono a el total de la factura
                        }
                    }
                });

            } else {
                invoice_total()
            }
        } else {
            invoice_total()
        }
    });



    // Introducir monto

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
            success: function(res) {
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
                success: function(res) {

                    if (res != "") {

                        mysql_row_affected()
                        reload()

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
            success: function(res) {
                console.log(res)
                    // 'public/tickets/' + file
            }
        });

    }





    // Agregar producto al detalle temporal / detalle de venta sin precio

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

        function addVariant(detail_id, array) {

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
                    success: function(res) {

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
                success: function(res) {

                    if (res > 0) {

                        invoice_total()
                        reload()
                        if (total_variant > 0) {
                            addVariant(res, variant_id); // Asignar variantes al detalle temporal
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
            success: function(res) {


            }
        });

    })



    /**
     * ! Reporte de ventas filtrado por fecha 
     */

    $('#date_query').change((e) => {
        e.preventDefault()

        $(location).attr('href', SITE_URL + 'src/excel/detalle-ventas-dia.php?date=' + $('#date_query').val());

    })


    // Auto cargar detalle cotizacion desde LocalStorage

    if (pageURL.includes("invoices/quote")) {

        $(function() {

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
                     <td>${0} - ${0}%</td>
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

    $('#SendmailQuote').on('click', (e) => {
        e.preventDefault()

        var id = $('#quote_id').val()
        SendmailQuote(id)
    })


}); // Ready




// Agregar producto al detalle temporal / detalle de venta

function ADD_DETAIL_INVOICE() {
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
        discount = $('#discount_service').val().replace(/,/g, "")
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
        reset_modal()

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
                reset_modal()
            } else {
                $('.empty-variant').css("border", "1px solid red");
                $('.verify-quantity').css("border", "1px solid red");
            }

        } else {
            addItem();
            reset_modal()
        }
    }

    // asignar variantes al detalle temporal

    function addVariant(detail_id, array) {

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
                success: function(res) {

                    if (res == "ready") {


                    } else {
                        mysql_error(res)
                    }
                }
            });
        });

    }


    function addItem() {
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
                discount: discount,
                taxes: ($('#price_out').val().replace(/,/g, "") * $('#taxes').val()) / 100, // Calcular impuestos
                price: $('#price_out').val().replace(/,/g, "")

            },
            success: function(res) {

                if (res > 0) {

                    invoice_total()
                    reload()
                    if (total_variant > 0) {
                        addVariant(res, variant_id); // Asignar variantes al detalle temporal
                    }

                } else if (res == "duplicate") {
                    mysql_error('Este ítem ya ha sido agregado al detalle');
                } else {
                    mysql_error(res)
                }
            }
        });
    }

}




// Eliminar item del detalle temporar / detalle de venta

function DELETE_DETAIL_INVOICE(id) {

    // Verificar de que detalle se eliminará el producto
    let action;

    if (pageURL.includes("invoices/addpurchase")) {
        action = "eliminar_detalle_temporal";
    } else if (pageURL.includes("invoices/edit")) {
        action = "eliminar_detalle_venta";
    }

    $.ajax({
        url: SITE_URL + "services/invoices.php",
        method: "post",
        data: {
            action: action,
            id: id
        },
        success: function(res) {
            if (res == "ready") {
                invoice_total()
                $('#Detalle').load(location.href + " #Detalle");

            } else {
                mysql_error(res)
            }
        }
    });

}

// Eliminar factura

function deleteInvoice(id) {

    alertify.confirm("Eliminar factura", "¿Estas seguro que deseas eliminar esta factura? ",
        function() {

            $.ajax({
                url: SITE_URL + "services/invoices.php",
                method: "post",
                data: {
                    action: "eliminar_factura",
                    id: id
                },
                success: function(res) {

                    if (res == "ready") {

                        $("#example").load(location.href + " #example");


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
        success: function(res) {
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
        // reset_modal()

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
             <td>${0} - ${0}%</td>
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

// Eliminar item del detalle

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
            success: function(res) {

                if (res == "ready") {
                    $('#Detalle').load(location.href + " #Detalle"); // Actualizar detalle
                    invoice_total() // Cargar total de la cotizacion
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
            total: $("#total_invoice").val(),
            date: $("#date").val(),
            observation: $("#observation").val()
        },
        success: function(res) {

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
            success: function(res) {

                if (res == "ready") {

                    if (onDb == true) {
                        invoice_total() // Cargar total de la cotizacion
                        $('#Detalle').load(location.href + " #Detalle"); // Actualizar detalle

                    } else if (onDb != true) {
                        CancelQuote() // Borrar todo del localstorage
                        GenerateQuotePDF(res) // Generar PDF
                        if ($("#sendMail").is(':checked')) return SendmailQuote(res); // Enviar mail
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
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/invoices.php",
                data: {
                    action: "eliminar_cotizacion",
                    id: id
                },
                success: function(res) {

                    if (res == "ready") {

                        mysql_row_affected()
                        $('#example').load(location.href + " #example");

                    } else {
                        mysql_error(res)
                    }

                }
            });
        },
        function() {

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

    $.ajax({
        type: "post",
        url: SITE_URL + "services/invoices.php",
        data: {
            action: "actualizar_cotizaciones",
            quote_id: id,
            customer_id: $("#customer").val(),
            date: $("#date").val(),
            observation: $("#observation").val()
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected()

            } else {
                mysql_error(res)
            }

        }
    });

} // function


// Generar Email de la cotizacion

function SendmailQuote(invoice) {

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