$(document).ready(function() {


    // Aplicar descuento

    $('#discount_service').keyup(function(e) {
        e.preventDefault();

        var price_service = parseInt($('#price_out').val().replace(/,/g, ""));
        var discount = $('#discount_service').val();

        // Validar que el descuento no sea mayor que el precio del piezas
        if (discount <= price_service) {
            $('#rp_add_item').show(); // Botón de ventana detalle de ordenes de reparaciones
            $('#add_item').show(); // Botón de ventana facturas de ventas

        } else {

            $('#rp_add_item').hide(); // Botón de ventana detalle de ordenes de reparaciones
            $('#add_item').hide(); // Botón de ventana facturas de ventas

        }
    })


    // Buscar servicio por nombre

    $("#service").change(function() {
        var service_id = $(this).val();
        SearchService(service_id);
    });


    function SearchService(service_id) {

        $.ajax({
            url: SITE_URL + "services/services.php",
            method: "post",
            data: {
                service_id: service_id,
                action: "buscar_servicios"
            },
            success: function(res) {
                var data = JSON.parse(res);

                $("#price_out").val('')
                $("#price_out").attr("disabled", true);

                if (data.precio > 0) {

                    $("#price_out").val(format.format(data.precio));

                } else {

                    $("#price_out").attr("disabled", false);
                }

            }

        })

    }

}); // Ready


/**
* Agregar servicio
------------------------------------------*/

function AddService() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/services.php",
        data: {
            name: $('#service_name').val(),
            price: $('#service_price').val(),
            action: 'agregar_servicio'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected()
                $('input[type="text"]').val('');
                $('input[type="number"]').val('');
                $(".table").load(location.href + " .table");

            } else if (res == "duplicate") {

                mysql_error('El nombre de este servicio ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }

        }
    });

}

/**
 * Actualizar servicio
----------------------------------- */

function UpdateService(service_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/services.php",
        data: {
            service_id: service_id,
            name: $('#service_name').val(),
            price: $('#service_price').val(),
            action: 'actualizar_servicio'
        },
        success: function(res) {

            if (res == "ready") {

                $(".table").load(location.href + " .table");
                mysql_row_update()

            } else if (res == "duplicate") {

                mysql_error('El nombre de este servicio ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }

        }
    });

}

/**
 * Eliminar servicio
 ----------------------------------*/

function deleteService(id) {

    alertify.confirm("Eliminar servicio", "¿Estas seguro que deseas borrar este servicio? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/services.php",
                data: {
                    service_id: id,
                    action: 'eliminar_servicio'
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