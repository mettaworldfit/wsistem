var pageURL = $(location).attr("pathname");
const format = new Intl.NumberFormat('en'); // Formato 0,000

function mysql_row_affected() {
    alertify.alert(`<div class='row-affected'>
    <i class='icon-success far fa-check-circle'></i>
    <p>Registrado exitosamente</p>
    </div>`).set('basic', true);
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



$(document).ready(function() {

        // Defaults 
        $("#credit-in-finish-or-receipt").hide()
        $("#credit-in-finish-or").hide()
        $("#cash-in-finish-or-receipt").hide()
        $("#cash-in-finish-or").hide()
        $('#cash-received').val('0.00')
        $('#cash-topay').val('0.00')
        $('#g_quantity').val('1')
        $('#save_spend').hide();


        // Auto cargar detalle del LocalStorage

        if (pageURL.includes("bills/addbills")) {

            $(function() {

                // Verificar
                if (localStorage.getItem("detalle_gasto")) {
                    SpendingLocalStorage = JSON.parse(localStorage.getItem("detalle_gasto"));

                    // Loop del detalle en localStorage 
                    SpendingLocalStorage.forEach((element, index) => {

                        let data = {
                            reason_id: element.reason_id,
                            name: element.name,
                            quantity: element.quantity,
                            taxes: element.taxes,
                            tax_value: element.tax_value,
                            value: element.value,
                            observation: element.observation,
                            total_price: element.total_price
                        }

                        ArrayBills.push(data); // Guardar de localStorage a ArrayBills

                        CalcBills(ArrayBills);

                        let taxes;
                        let tax_value;
                        if (element.taxes > 0) {
                            taxes = format.format(element.taxes)
                            tax_value = element.tax_value;
                        } else {
                            taxes = 0;
                            tax_value = 0;
                        }

                        var value = format.format(element.value);

                        // Insertar detalle
                        document.querySelector('#rows').innerHTML += `
                     <tr>
                         <td>${element.name}</td>
                         <td>${element.quantity}</td>
                         <td>${value}</td>
                         <td class="hide-cell">${taxes} - ${tax_value}%</td>
                         <td class="hide-cell">${element.observation}</td>
                         <td>${format.format(element.total_price)}</td>
                         <td>
                         <span class="action-delete" onClick="DeleteLocalstorage(${index});"><i class="fas fa-times"></i></span>
                         </td>
                     </tr>
                     `;

                    });
                }
            })
        }

        // Borrar todos los gastos del localstorage 

        $('#CancelBill').on('click', (e) => {
            CancelBills()
        });


        /**
         * Agregar orden 
         * carga la orden de compra en la factura a proveedores
         */

        $("#order").change(() => { add_purchase_order(); });

        /**
         * Ajustes para agregar piezas y productos
         * Esta función permite ocultar inputs mediante opción check
         */

        if (pageURL.includes("bills/add_order") || pageURL.includes("bills/edit_order") || pageURL.includes("bills/edit_invoice")) {
            //   invoice_total_or() // Cargar total orden de compra

            // Cambiar tipo de item a agregar

            $(".or_piece").hide();
            $("#piece").attr("required", false);

            $("input:radio[name=tipo]").change(function() {

                if ($(this).val() == "pieza") {

                    $(".or_piece").show();
                    $(".product").hide();

                    $("#product").attr("required", false);
                    $("#piece").attr("required", true);

                    $("#or_quantity").val("");
                    $("#or_price_out").val("");
                    $("#or_taxes").val("");
                    $("#or_discount").val("");
                    $("#observation_item").val("");

                } else if ($(this).val() == "producto") {

                    $(".product").show();
                    $(".or_piece").hide();

                    $("#product").attr("required", true);
                    $("#piece").attr("required", false);

                    $("#or_quantity").val("");
                    $("#or_price_out").val("");
                    $("#or_taxes").val("");
                    $("#or_discount").val("");
                    $("#observation_item").val("");
                }
            });
        }

        /**
         * Auto cargar detalle del LocalStorage
         */

        if (pageURL.includes("bills/add_order")) {
            $(function() {

                // Verificar
                if (localStorage.getItem("detalle_orden")) {
                    arrayLocalStorage = JSON.parse(localStorage.getItem("detalle_orden"));

                    // Loop del detalle en localStorage
                    arrayLocalStorage.forEach((element, index) => {
                        let data = {
                            item_id: element.product_id,
                            name: element.name,
                            quantity: element.quantity,
                            taxes: element.taxes,
                            tax_value: element.tax_value,
                            discount: element.discount,
                            price_out: element.price_out,
                            observation: element.observation,
                            total_price: element.total_price,
                        };

                        ArrayLists.push(data); // Guardar de localStorage a ArrayLists

                        clctotal(ArrayLists);

                        let taxes;
                        let tax_value;
                        let discount;
                        let price_out = format.format(element.price_out);
                        if (element.taxes > 0) {
                            taxes = format.format(element.taxes);
                            tax_value = element.tax_value;
                        } else {
                            taxes = 0;
                            tax_value = 0;
                        }
                        if (element.discount > 0) {
                            discount = element.discount;
                        } else {
                            discount = 0;
                        }

                        // Insertar detalle
                        document.querySelector("#rows").innerHTML += `
                       <tr>
                           <td>${element.name}</td>
                           <td>${element.quantity}</td>
                           <td>${price_out}</td>
                           <td>${taxes} - ${tax_value}%</td>
                           <td>${discount}</td>
                           <td>${element.observation}</td>
                           <td>${format.format(element.total_price)}</td>
                           <td>
                           <span class="action-delete" onClick="DeleteLocalstorage(${index});"><i class="fas fa-times"></i></span>
                           </td>
                       </tr>
                       `;
                    }); // Loop Array

                } // if condiction
            }); // function auto execute
        } // if condiction


        /**
         * TODO: Borrar todo el detalle del localStorage
         */

        $("#cancel_detail").on("click", (e) => {
            cancel_detail();
        });

        $("#cash-in-finish-or").on("click", (e) => {
            e.preventDefault();

            CASH_INV_PROVIDER()
        });

        $("#cash-in-finish-or-receipt").on("click", (e) => {
            e.preventDefault();

            data = {
                provider: $('#provider_cash').val(),
                method: $('#select2-cash-in-method-container').attr('title'),
                seller: $('#cash-in-seller').val(),
                subtotal: $('#in-subtotal').val().replace(/,/g, ""),
                discount: $('#in-discount').val().replace(/,/g, ""),
                taxes: $('#in-taxes').val().replace(/,/g, ""),
                total: $('#in-total').val().replace(/,/g, ""),
                order_id: $("#order").val()

            }

            CASH_INV_PROVIDER(true, data)
        });

        /**
         * TODO:  Crear factura proveedor al contado
         */

        function CASH_INV_PROVIDER(receipt = false, data = {}) {

            var total = $("#cash-topay").val().replace(/,/g, "");

            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    action: "factura_contado",
                    orden_id: $("#order").val(),
                    provider_id: $("#provider_id").val(),
                    payment_method: $("#cash-in-method").val(),
                    observation: $("#observation").val(),
                    total_invoice: total,
                    date: $("#cash-in-date").val(),
                },
                success: function(res) {

                    if (res > 0) {

                        if (receipt == true) {
                            printer(res, data, "cash");
                        }

                        document.querySelector("#rows").innerHTML = "";

                        // Vaciar campos
                        $("#in-subtotal").val("0");
                        $("#in-discount").val("0");
                        $("#in-taxes").val("0");
                        $("#in-total").val("0");

                        mysql_row_affected();

                        $("#cash-received").val(format.format(total));
                        $("#cash-pending").val("0.00");

                    } else {
                        mysql_error(res);
                    }
                },
            });

        }


        // Introducir monto de la factura a credito

        $("#credit-pay-or").on("keyup", (e) => {
            e.preventDefault();

            var pay = $("#credit-pay-or").val();
            $("#credit-received").val(format.format(pay));

            // Mostrar botón de facturar

            var pay = parseInt($("#credit-pay-or").val());
            var pending = parseInt($("#credit-pending").val().replace(/,/g, ""));

            if (pay <= pending) {
                $("#credit-in-finish-or").show();
                $("#credit-in-finish-or-receipt").show();
            } else {
                $("#credit-in-finish-or-receipt").hide();
                $("#credit-in-finish-or").hide();
            }
        });


        $("#credit-in-finish-or").on("click", (e) => {

            CREDIT_INV_PROVIDER();
        });

        $("#credit-in-finish-or-receipt").on("click", (e) => {

            data = {
                provider: $('#provider_cash').val(),
                method: $('#select2-cash-in-method-container').attr('title'),
                seller: $('#cash-in-seller').val(),
                subtotal: $('#in-subtotal').val().replace(/,/g, ""),
                discount: $('#in-discount').val().replace(/,/g, ""),
                taxes: $('#in-taxes').val().replace(/,/g, ""),
                total: $('#in-total').val().replace(/,/g, ""),
                order_id: $("#order").val()

            }

            CREDIT_INV_PROVIDER(true, data);
        });


        /**
         * TODO: Crear factura proveedor a crédito
         */


        function CREDIT_INV_PROVIDER(receipt = false, data = {}) {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    action: "factura_credito",
                    provider_id: $("#provider_id").val(),
                    orden_id: $("#order").val(),
                    payment_method: $("#credit-in-method").val(),
                    observation: $("#observation").val(),
                    total_invoice: $("#credit-topay").val().replace(/,/g, ""),
                    pay: $("#credit-pay-or").val(),
                    pending: $("#credit-pending").val().replace(/,/g, ""),
                    date: $("#credit-in-date").val(),
                },
                success: function(res) {

                    if (res > 0) {

                        document.querySelector("#rows").innerHTML = "";

                        if (receipt == true) {
                            printer(res, data, "credit");
                        }

                        mysql_row_affected();

                        // Vaciar campos
                        $("#in-subtotal").val("0");
                        $("#in-discount").val("0");
                        $("#in-taxes").val("0");
                        $("#in-total").val("0");
                        $("#credit-pending").val(format.format(pending)); // Imprimir valor pendiente en el modal

                        $("#credit-in-finish-or").hide();
                        $("#credit-in-finish-or-receipt").hide();
                    } else {

                        mysql_error(res);

                    }
                },
            });

        }


        /**
         * TODO: Imprimir factura
         */

        function printer(invoice_id, data, type) {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    action: 'obtener_detalle',
                    id: $("#order").val()
                },
                success: function(res) {
                    console.log(res)
                    print(invoice_id, res, data, type)

                }
            });

            function print(invoice_id, detail, data, type) {
                console.log('imprimiendo.....')

                // Tipo de documento a imprimir

                let file;
                if (type == "cash") {
                    file = "factura_cash_proveedor.php"
                } else if (type == "credit") {
                    file = "factura_credit_proveedor.php"
                }

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

                    }
                });

            }

        }


        /**
         * TODO: Calcular detalle de orden de compra
         */

        function CLC_DETAIL() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    action: "total_orden_compra",
                    id: $("#order").val(),
                },
                success: function(res) {
                    var data = JSON.parse(res);

                    var discount = format.format(data.descuentos);
                    var taxes = format.format(data.impuestos);
                    var subtotal = format.format(data.precio);
                    var total = format.format(
                        parseFloat(data.precio) +
                        parseFloat(data.impuestos) -
                        parseFloat(data.descuentos)
                    );

                    $("#in-total").val(total);
                    $("#in-subtotal").val(subtotal);
                    $("#in-taxes").val(taxes);
                    $("#in-discount").val(discount);
                },
            });

        }

        /**
         * TODO: Función para calcular total en la ventana editar orden
         */

        $(function() {
            if (pageURL.includes("bills/edit_order")) {
                CLC_DETAIL();
            }
        });





    }) // Ready



/* Eliminar gasto especifico del localstorage
 * Array auto cargado al recargar la pagina en la linea 47
 */

function DeleteLocalstorage(index) {

    ArrayBills.splice(index, 1);
    CreateLocalstorage(ArrayBills)
}

/**
 * Detalle de orden de compra
 ------------------------------------------------------------------*/

let ArrayBills = [];

function AddBills() {

    let tax_value;
    if ($('#g_taxes').val() == 0) {
        tax_value = 0;
    } else {
        tax_value = $('#g_taxes').val() / 100;
    }

    var total = $('#g_quantity').val() * $('#g_value').val().replace(/,/g, "");
    var taxes = total * tax_value;

    data = {
        reason_id: $('#reason').val(),
        name: $('#select2-reason-container').attr('title'),
        quantity: parseInt($('#g_quantity').val()),
        taxes: taxes,
        tax_value: $('#g_taxes').val(),
        value: $('#g_value').val().replace(/,/g, ""),
        total_price: total + taxes,
        observation: $('#observation_item').val()
    }

    // Buscar coincidencia si existe un "Motivo de gasto" en el localStorage
    if (data.reason_id > 0) return FindAMatch(ArrayBills);

    function FindAMatch(arr) {

        if (arr.length < 1) {

            arr.push(data); // Insertar datos al arreglo
            CreateLocalstorage(ArrayBills); // crear el localstorage del detalle

        } else {

            let found = arr.find(element => element.name == data.name)

            if (found == undefined) {

                arr.push(data);
                CreateLocalstorage(ArrayBills);
            }
        }
    } // Function

} // function AddBills()


// Crear la base de datos en el localstorage
function CreateLocalstorage(Arr) {

    localStorage.setItem('detalle_gasto', JSON.stringify(Arr));
    showDB_spending(); // Mostrar DB

}

let SpendingLocalStorage;

function showDB_spending() {

    document.querySelector('#rows').innerHTML = ""; // Vaciar detalle

    if (localStorage.getItem("detalle_gasto")) {
        SpendingLocalStorage = JSON.parse(localStorage.getItem("detalle_gasto"));
    }

    // Loop del detalle en localStorage 
    SpendingLocalStorage.forEach((element, index) => {

        let taxes;
        let tax_value;
        if (element.taxes > 0) {
            taxes = format.format(element.taxes)
            tax_value = element.tax_value;
        } else {
            taxes = 0;
            tax_value = 0;
        }
        var value = format.format(element.value);

        document.querySelector('#rows').innerHTML += `
         <tr>
             <td>${element.name}</td>
             <td>${element.quantity}</td>
             <td>${value}</td>
             <td>${taxes} - ${tax_value}%</td>
             <td>${element.observation}</td>
             <td>${format.format(element.total_price)}</td>
             <td>
               <span class="action-delete" onClick="DeleteLocalstorage(${index});"><i class="fas fa-times"></i></span>
             </td>
         </tr>
         `;

    });

    CalcBills(SpendingLocalStorage);
}

// Calcular precio total

function CalcBills(arr) {

    let subtotal = 0;
    let taxes = 0;
    let total = 0;

    arr.forEach((element, index) => {

        subtotal = subtotal + (parseFloat(element.quantity) * element.value.replace(/,/g, ""));

        if (element.taxes > 0) return taxes = parseInt(element.taxes);
    });

    var sub = format.format(subtotal.toFixed(2));
    var tax = format.format(taxes.toFixed(2));
    total = format.format(subtotal + taxes);

    // Validar modal guardar gastos
    $('#cash-received').val('0.00')
    $('#cash-topay').val(total)
    if (total.replace(/,/g, "") > 0) {
        $('#save_spend').show();
    }

    document.querySelector('#price').innerHTML = ""; // Vaciar precios de la factura

    document.querySelector('#price').innerHTML += `
         <span><input type="text" class="invisible-input" value="${sub}" id="in-subtotal" disabled></span>
         <span><input type="text" class="invisible-input" value="${tax}" id="in-taxes" disabled></span>
         <span><input type="text" class="invisible-input" value="${total}" id="in-total" disabled></span>
         <input type="hidden" name="" value="${subtotal + taxes}" id="total_invoice">
     `;
} // function CalcBills()


// Borrar todo del localstorage

function CancelBills() {
    localStorage.removeItem('detalle_gasto'); // Vaciar localstorage
    ArrayBills = []; // Vaciar Arreglo
    CalcBills(ArrayBills); // Calcular precios
    $('#rows').load(location.href + " #rows"); // actualizar detalle
}


// guardar orden de compra

function SaveBills() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            action: "crear_orden_gasto",
            provider_id: $('#provider').val(),
            date: $('#date').val()
        },
        success: function(res) {

            if (res > 0) {
                AddOrderDetail(res) // Agregar detalle motivos de gastos
            } else {
                mysql_error(res)
            }

        }
    });

}

// Crear detalle de orden de compra

function AddOrderDetail(order_id) {

    if (localStorage.getItem("detalle_gasto")) {
        arrayL = JSON.parse(localStorage.getItem("detalle_gasto"));
        arrayL.forEach((element, index) => {


            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    action: "detalle_gasto",
                    order_id: order_id,
                    reason_id: element.reason_id,
                    quantity: element.quantity,
                    taxes: element.taxes,
                    value: element.value,
                    observation: element.observation,
                },
                success: function(res) {

                    if (res == "ready") {

                    } else {
                        mysql_error(res)
                    }

                }
            });
        }); // Loop

        // función de imprimir

        alertify.confirm("Imprimir ticket", "¿Desea imprimir un ticket? ",
            function() {

                data = {
                    provider: $('#select2-provider-container').attr('title'),
                    by: $("#emisor").val(),
                    date: $("#date").val(),
                    subtotal: $('#in-subtotal').val().replace(/,/g, ""),
                    taxes: $('#in-taxes').val().replace(/,/g, ""),
                    total: $('#in-total').val().replace(/,/g, ""),
                    observation: $('#observation').val()
                }

                $.ajax({
                    type: "post",
                    url: PRINTER_SERVER + "gastos.php",
                    data: {
                        order_id: order_id,
                        data: data,
                        detail: localStorage.getItem("detalle_gasto")
                    },
                    success: function(res) {
                        console.log(res)
                        register_spending(order_id) // Registrar gastos

                    }
                });

            },
            function() {
                register_spending(order_id) // Registrar gastos
            }).set({ labels: { ok: 'Imprimir', cancel: 'No imprimir' }, padding: true });

    }


}

/**
 * Registrar gastos
 */


function register_spending(order_id) {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            action: "registrar_gasto",
            order_id: order_id,
            provider_id: $('#provider').val(),
            observation: $('#observation').val(),
            total_invoice: $('#cash-topay').val().replace(/,/g, ""),
            date: $('#date').val()
        },
        success: function(res) {
            if (res == "ready") {

                document.querySelector('#rows').innerHTML = "";
                $('#in-subtotal').val('0.00')
                $('#in-taxes').val('0.00')
                $('#in-total').val('0.00')
                $('#observation').val('')
                $('#cash-received').val($('#cash-topay').val())
                $('#cash-topay').val('0.00')
                $(".table").load(location.href + " .table");

                CancelBills()

                mysql_row_affected()
            } else {
                mysql_error(res)
            }

        }
    });
}

// Eliminar gasto

function deleteSpending(id) {
    alertify.confirm("Eliminar gasto", "¿Estas seguro que deseas eliminar este gasto? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    action: "eliminar_gasto",
                    id: id
                },
                success: function(res) {

                    if (res == "ready") {
                        $(".table").load(location.href + " .table");
                    } else {
                        mysql_error(res)
                    }

                }
            });
        },
        function() {

        });
}



/**
 * TODO: Recalcular detalle
 * ! Recalcular el detalle al recarga la pagina
 */

function RECLC_DETAIL(order_id) {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            action: "total_orden_compra",
            id: order_id,
        },
        success: function(res) {
            $("#in-total").val("");
            $("#in-subtotal").val("");
            $("#in-taxes").val("");
            $("#in-discount").val("");

            var data = JSON.parse(res);

            var discount = format.format(data.descuentos);
            var taxes = format.format(data.impuestos);
            var subtotal = format.format(data.precio);
            var total = format.format(
                parseFloat(data.precio) +
                parseFloat(data.impuestos) -
                parseFloat(data.descuentos)
            );

            $("#in-total").val(total);
            $("#in-subtotal").val(subtotal);
            $("#in-taxes").val(taxes);
            $("#in-discount").val(discount);
        },
    });
}

/**
 * TODO: Agregar ítem al detalle de la orden de compra
 * ! Para editar las ordenes de compras luego de creadas
 */

function ADD_ITEM_DETAIL_ORDER() {
    let piece_id;
    let product_id;
    let tax_value;
    var order_id = $("#order").val();
    console.log(order_id)

    if ($("input:radio[name=tipo]:checked").val() == "pieza") {
        piece_id = $("#piece").val();
        product_id = 0;
    } else if ($("input:radio[name=tipo]:checked").val() == "producto") {
        piece_id = 0;
        product_id = $("#product").val();
    }

    if ($("#or_taxes").val() == 0) {
        tax_value = 0;
    } else {
        var price = $("#or_quantity").val() * $("#or_price_out").val();
        var result = (price * $("#or_taxes").val()) / 100;
        tax_value = result;
    }

    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            action: "agregar_detalle_orden_de_compra",
            order_id: order_id,
            product_id: product_id,
            piece_id: piece_id,
            quantity: $("#or_quantity").val(),
            taxes: tax_value,
            discount: $("#or_discount").val(),
            price: $("#or_price_out").val(),
            observation: $("#observation_item").val(),
        },
        success: function(res) {
            if (res == "ready") {
                $("#Detalle").load(location.href + " #Detalle");
                RECLC_DETAIL(order_id); // Recalcular detalle
            } else if (res == "duplicate") {
                mysql_error("Este ítem ya ha sido agregado al detalle");
            } else {
                mysql_error(res);
            }
        },
    });
}

/**
 * TODO: Eliminar ítem de la orden de compra
 */

function DELETE_ITEM_ORDER_PRCHSE(detail_id, order_id) {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            action: "eliminar_item_de_la_orden",
            detail_id: detail_id,
        },
        success: function(res) {
            if (res == "ready") {
                $("#Detalle").load(location.href + " #Detalle");
                RECLC_DETAIL(order_id);
            } else {
                mysql_error(res);
            }
        },
    });
}

/**
 * Detalle de orden de compra
 ------------------------------------------------------------------*/

let ArrayLists = [];

function add_detail_order() {
    let data;

    if ($("input:radio[name=tipo]:checked").val() == "producto") {
        let tax_value;
        if ($("#or_taxes").val() == 0) {
            tax_value = 0;
        } else {
            tax_value = $("#or_taxes").val() / 100;
        }

        var total =
            $("#or_quantity").val() * $("#or_price_out").val().replace(/,/g, "");
        var taxes = total * tax_value;

        data = {
            type_item: $("input:radio[name=tipo]:checked").val(),
            item_id: $("#product").val(),
            name: $("#select2-product-container").attr("title"),
            quantity: parseInt($("#or_quantity").val()),
            discount: $("#or_discount").val().replace(/,/g, ""),
            taxes: taxes,
            tax_value: $("#or_taxes").val(),
            price_out: $("#or_price_out").val().replace(/,/g, ""),
            total_price: total + taxes - $("#or_discount").val().replace(/,/g, ""),
            observation: $("#observation_item").val(),
        };
    } else if ($("input:radio[name=tipo]:checked").val() == "pieza") {
        let tax_value;
        if ($("#or_taxes").val() == 0) {
            tax_value = 0;
        } else {
            tax_value = $("#or_taxes").val() / 100;
        }

        var total =
            $("#or_quantity").val() * $("#or_price_out").val().replace(/,/g, "");
        var taxes = total * tax_value;

        data = {
            type_item: $("input:radio[name=tipo]:checked").val(),
            item_id: $("#piece").val(),
            name: $("#select2-piece-container").attr("title"),
            quantity: $("#or_quantity").val(),
            discount: parseInt($("#or_discount").val().replace(/,/g, "")),
            taxes: taxes,
            tax_value: $("#or_taxes").val(),
            price_out: $("#or_price_out").val().replace(/,/g, ""),
            total_price: total + taxes - $("#or_discount").val().replace(/,/g, ""),
            observation: $("#observation_item").val(),
        };
    }

    // Buscar coincidencia si existe un producto o pieza en el localStorage
    if (data.item_id > 0) return FindAMatch(ArrayLists);

    function FindAMatch(arr) {
        if (arr.length < 1) {
            arr.push(data); // Insertar datos al arreglo
            createDB(ArrayLists); // crear el localstorage del detalle
        } else {
            let found = arr.find((element) => element.name == data.name);

            if (found == undefined) {
                arr.push(data);
                createDB(ArrayLists);
            }
        }
    }
}

// Crear la base de datos en el localstorage
function createDB(Arr) {
    localStorage.setItem("detalle_orden", JSON.stringify(Arr));
    showDB(); // Mostrar DB
}

let arrayLocalStorage;

function showDB() {
    document.querySelector("#rows").innerHTML = ""; // Vaciar detalle

    if (localStorage.getItem("detalle_orden")) {
        arrayLocalStorage = JSON.parse(localStorage.getItem("detalle_orden"));
    }

    // Loop del detalle en localStorage
    arrayLocalStorage.forEach((element, index) => {
        let taxes;
        let tax_value;
        let discount;
        let price_out = format.format(element.price_out);
        if (element.taxes > 0) {
            taxes = format.format(element.taxes);
            tax_value = element.tax_value;
        } else {
            taxes = 0;
            tax_value = 0;
        }

        if (element.discount > 0) {
            discount = element.discount;
        } else {
            discount = 0;
        }

        document.querySelector("#rows").innerHTML += `
           <tr>
               <td>${element.name}</td>
               <td>${element.quantity}</td>
               <td>${price_out}</td>
               <td>${taxes} - ${tax_value}%</td>
               <td>${discount}</td>
               <td class="note-width">${element.observation}</td>
               <td>${format.format(element.total_price)}</td>
               <td>
                 <span class="action-delete" onClick="DeleteLocalstorage(${index});"><i class="fas fa-times"></i></span>
               </td>
           </tr>
           `;
    });

    clctotal(arrayLocalStorage);
}

// Calcular precio total

function clctotal(arr) {
    let subtotal = 0;
    let discount = 0;
    let taxes = 0;
    let total = 0;

    arr.forEach((element, index) => {
        subtotal =
            subtotal +
            parseFloat(element.quantity) * element.price_out.replace(/,/g, "");

        if (element.discount > 0) {
            discount = discount + parseInt(element.discount);
        }
        if (element.taxes > 0) {
            taxes = parseInt(element.taxes);
        }
    });

    var sub = format.format(subtotal.toFixed(2));
    var disc = format.format(discount);
    var tax = format.format(taxes.toFixed(2));
    total = format.format(subtotal + taxes - discount);

    document.querySelector("#price").innerHTML = ""; // Vaciar precios de la factura

    document.querySelector("#price").innerHTML += `
           <span>${sub}</span>
           <span>${disc}</span>
           <span>${tax}</span>
           <span>${total}</span>
           <input type="hidden" name="" value="${subtotal + taxes - discount
      }" id="total_invoice">
       `;
}


// Crear orden de compra

function save_order() {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            action: "crear_orden_compra",
            provider: $("#provider").val(),
            observation: $("#observation").val(),
            date: $("#date").val(),
            expiration: $("#expiration").val(),
        },
        success: function(res) {
            if (res > 0) {
                add_detailOR(res);
                mysql_row_affected();
            } else {
                mysql_error(res);
            }
        },
    });
}

// Crear detalle de orden de compra

function add_detailOR(order_id) {
    if (localStorage.getItem("detalle_orden")) {
        arrayL = JSON.parse(localStorage.getItem("detalle_orden"));
        arrayL.forEach((element, index) => {
            let product_id;
            let piece_id;

            if (element.type_item == "producto") {
                product_id = element.item_id;
                piece_id = 0;
            } else if (element.type_item == "pieza") {
                product_id = 0;
                piece_id = element.item_id;
            }

            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    action: "agregar_detalle_orden_de_compra",
                    order_id: order_id,
                    product_id: product_id,
                    piece_id: piece_id,
                    quantity: element.quantity,
                    taxes: element.taxes,
                    discount: element.discount,
                    price: element.price_out,
                    observation: element.observation,
                },
                success: function(res) {
                    if (res == "ready") {} else {
                        mysql_error(res);
                    }
                },
            });
        }); // Loop

        cancel_detail(); // Borrar todo del localstorage
    }
}

// Eliminar orden de compra

function deleteOrderC(id) {
    alertify.confirm(
        "Eliminar orden",
        "¿Estas seguro que deseas eliminar esta orden? ",
        function() {
            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    id: id,
                    action: "eliminar_orden",
                },
                success: function(res) {
                    if (res == "ready") {
                        $(".table").load(location.href + " .table");
                    } else {
                        mysql_error(res);
                    }
                },
            });
        },
        function() {}
    );
}

// Agregar orden de compra a la factura de proveedores

function add_purchase_order() {

    var order_id = $("#order").val();
    cal_invoice_provider(order_id);

    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            id: order_id,
            action: "agregar_orden_a_factura",
        },
        success: function(res) {

            if (res != "") {

                document.querySelector("#rows").innerHTML = "";
                document.querySelector("#rows").innerHTML += res;

                $("#cash-in-finish-or-receipt").show()
                $("#cash-in-finish-or").show()
            }
        },
    });

}

/**
 * TODO: Calcular total de factura de proveedores
 */

function cal_invoice_provider(order_id) {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            id: order_id,
            action: "calc_factura",
        },
        success: function(res) {
            var data = JSON.parse(res);

            var discount = format.format(data.descuentos);
            var taxes = format.format(data.impuestos);
            var subtotal = format.format(data.subtotal);
            var total = format.format(
                parseFloat(data.subtotal) +
                parseFloat(data.impuestos) -
                parseFloat(data.descuentos)
            );

            $("#in-subtotal").val(subtotal);
            $("#in-taxes").val(taxes);
            $("#in-discount").val(discount);

            $("#provider_cash").val(data.proveedor);
            $("#provider_credit").val(data.proveedor);
            $("#provider").val(data.proveedor);
            $("#provider_id").val(data.proveedor_id);
            $("#expiration_order").val(data.expiracion);
            $("#date_order").val(data.fecha);

            if (total != "NaN") {
                $("#in-total").val(total);
            } else {
                $("#in-total").val("0");
            }

            // Modal Factura al contado
            $("#cash-received").val("0.00");
            if (total != "NaN") {
                $("#cash-topay").val(total);
                $("#cash-pending").val(total);
            } else {
                $("#cash-topay").val("0.00");
                $("#cash-pending").val("0.00");
            }

            // Modal Factura a crédito

            $("#credit-received").val("0.00");
            if (total != "NaN") {
                $("#credit-topay").val(total);
                $("#credit-pending").val(total);
            } else {
                $("#credit-topay").val("0.00");
                $("#credit-pending").val("0.00");
            }

            // Validar botón de procesar venta al contado

            if (total != "NaN") {
                $("#credit-in-finish_or").show();
            } else {
                $("#credit-in-finish_or").hide();
            }

            // Validar campo ingresar monto factura a crédito

            if (total != "NaN") {
                $(".pay").show();
            } else {
                $(".pay").hide();
            }
        },
    });
}

/**
 * TODO: Cargar detalle de factura de proveedores
 * ! Esta función se carga al momento de editar una factura de proveedores
 */

$(function() {
    if (pageURL.includes("bills/edit_invoice")) {
        var order_id = $("#order").val();
        cal_invoice_provider(order_id);
    }
});

/**
 * TODO: Eliminar ítem del detalle de orden de compra desde la factura de proveedores
 */

function deleteDetailOrderC(id) {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            id: id,
            action: "eliminar_detalle",
        },
        success: function(res) {

            if (res == "ready") {
                document.querySelector("#rows").innerHTML = "";
                add_purchase_order();
            } else {
                mysql_error();
            }
        },
    });
}

// Eliminar factura de proveedores

function deleteInvoiceFP(id, order_id) {

    alertify.confirm(
        "Eliminar factura",
        "¿Estas seguro que deseas eliminar esta factura? ",
        function() {
            $.ajax({
                type: "post",
                url: SITE_URL + "services/bills.php",
                data: {
                    id: id,
                    order_id: order_id,
                    action: "eliminar_factura_proveedor",
                },
                success: function(res) {
                    if (res == "ready") {
                        $(".table").load(location.href + " .table");
                    } else {
                        mysql_error(res);
                    }
                },
            });
        },
        function() {}
    );

}

// Actualizar orden de compra

function update_order(id) {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            order_id: id,
            provider_id: $("#provider").val(),
            date: $("#date").val(),
            expiration: $("#expiration").val(),
            observation: $("#observation").val(),
            action: "actualizar_orden",
        },
        success: function(res) {
            if (res == "ready") {
                mysql_row_update();
            } else {
                mysql_error(res);
            }
        },
    });
}

/**
 * TODO: Actualizar factura de proveedores
 */

function update_invoice(id) {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/bills.php",
        data: {
            invoice_id: id,
            provider_id: $("#provider").val(),
            date: $("#date").val(),
            observation: $("#observation").val(),
            action: "actualizar_factura",
        },
        success: function(res) {
            if (res == "ready") {
                mysql_row_update();
            } else {
                mysql_error(res);
            }
        },
    });
}


// Borrar todas las ordenes de compra del localstorage 

function cancel_detail() {

    localStorage.removeItem("detalle_orden"); // Vaciar localstorage
    ArrayLists = []; // Vaciar Arreglo
    clctotal(ArrayLists); // Calcular precios
    $("#rows").load(location.href + " #rows"); // actualizar detalle
}