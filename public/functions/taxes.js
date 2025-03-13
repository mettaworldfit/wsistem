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

$(document).ready(function () {

    /**
      *  Calcular impuesto al recargar la ventada
      *  Esto se utiliza si el producto ya está creado
      * */
    var pageURL = $(location).attr("pathname");

    if (pageURL.includes("products/add")) {

        $("#precioTotal").val("0.00"); // Evita sustituir el precio total al editar el producto

        if ($("#tax").val() != "Vacío") {
            search_tax($("#select2-tax-container").attr("title"));
        }
    }

    // Incluir ofertas

    $("#include_offer").change(function () {
        if (this.checked) {
            $('.offer').slideToggle('fast');
        } else {
            $('.offer').hide();
        }
    });

    // Incluir Proveedor

    $("#include_provider").change(function () {
        if (this.checked) {
            $('.provider').slideToggle();
        } else {
            $('.provider').hide();
        }
    });


    // Función para calcular impuesto

    function search_tax(tax = "") {
        $.ajax({
            type: "post",
            url: SITE_URL + "services/taxes.php",
            data: {
                action: "buscarImpuesto",
                tax: tax,
            },
            success: function (res) {
                var data = JSON.parse(res);

                var tax_value = (data.valor / 100);
                calculate_final_price(tax_value);
            },
        });
    }

    /**
     * Calcular el impuesto al abrir la ventana "Editar producto"
     */

    // if (pageURL.includes("products/edit")) {
    //   var tax = $("#select2-tax-container").attr("title");

    //   if($('#tax').val() > 0) return search_tax(tax);
    // }

    // Calcular al cambiar impuesto 

    $("#tax").change(function () {

        var tax = $("#select2-tax-container").attr("title");

        if (tax != "Vacío") {

            search_tax(tax); // Precio con Impuestos
        } else {
            calculate_final_price(); // Precio sin Impuestos

        }
    });

    // Calcular precio al escribir

   if (pageURL.includes("products/add") || pageURL.includes("products/edit")) {

        $("#inputPrice_out").keyup(function (e) {
            e.preventDefault();

            calculate_final_price();
        });

    } else if (pageURL.includes("pieces/add") || pageURL.includes("pieces/edit")) {

        $("#inputPrice_out").keyup(function (e) {
            e.preventDefault();

            var price_out = $("#inputPrice_out").val();
      
            $("#FinalPrice_out").val(price_out);
            $("#precioTotal").val(format.format(price_out) + ".00");
        });
    }

    function calculate_final_price(tax = 0) {

        const format = new Intl.NumberFormat("en");

        var tax_selected = $("#select2-tax-container").attr("title");

        if (tax > 0) {
            var price_out = $("#inputPrice_out").val();

            if (price_out != "") {
                var product_price = price_out * tax;
                price = product_price.toFixed(2);

                $("#FinalPrice_out").val(price);
                $("#precioTotal").val(format.format(price));

            }
        } else if (tax_selected != "Vacío") {
            search_tax(tax_selected);
        } else {
            var price_out = $("#inputPrice_out").val();
      
            $("#FinalPrice_out").val(price_out);
            $("#precioTotal").val(format.format(price_out) + ".00");
         
          }
    }


    



}); // Ready


/**
* Agregar impuesto
------------------------------------------*/

function AddTax() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/taxes.php",
        data: {
            name: $('#tax_name').val(),
            comment: $('#tax_comment').val(),
            value: $('#tax_value').val(),
            action: 'agregar_impuesto'
        },
        beforeSend: function () {

        },
        success: function (res) {

            if (res == "ready") {

                mysql_row_affected()
                $('input[type="text"]').val('');
                $('input[type="number"]').val('');
                $('textarea').val('');
                $(".table").load(location.href + " .table");

            } else {
                mysql_error(res)
            }

        }
    });

}

/**
 * Actualizar Impuesto
----------------------------------- */

function UpdateTax(tax_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/taxes.php",
        data: {
            tax_id: tax_id,
            name: $('#tax_name').val(),
            comment: $('#tax_comment').val(),
            value: $('#tax_value').val(),
            action: 'actualizar_impuesto'
        },
        beforeSend: function () {

        },
        success: function (res) {

            if (res == "ready") {

                $(".table").load(location.href + " .table");
                mysql_row_update()

            } else {
                mysql_error(res)
            }

        }
    });

}

/**
 * Eliminar Impuesto
 ----------------------------------*/

function deleteTax(id) {

    alertify.confirm("Eliminar impuesto", "¿Estas seguro que deseas borrar este impuesto? ",
        function () {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/taxes.php",
                data: {
                    tax_id: id,
                    action: 'eliminar_impuesto'
                },
                beforeSend: function () {

                },
                success: function (res) {

                    if (res == "ready") {

                        $(".table").load(location.href + " .table");

                    } else {
                        mysql_error(res)
                    }


                }
            });
        },
        function () {

        });
}


