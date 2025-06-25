// Agregar servicio

function addService() {
    sendAjaxRequest({
        url: "services/services.php",
        data: {
            name: $('#service_name').val(),
            cost: $('#service_cost').val(),
            price: $('#service_price').val(),
            action: 'agregar_servicio'
        },
        successCallback: () => {
            mysql_row_affected()
            $('input[type="text"]').val('');
            $('input[type="number"]').val('');
            $(".table").load(location.href + " .table");
        },
        errorCallback: (res) => mysql_error(res)
    });
}

// Actualizar servicio

function updateService(serviceId) {
    sendAjaxRequest({
        url: "services/services.php",
        data: {
            service_id: serviceId,
            name: $('#service_name').val(),
            cost: $('#service_cost').val(),
            price: $('#service_price').val(),
            action: 'actualizar_servicio'
        },
        successCallback: () => {
            $(".table").load(location.href + " .table");
            mysql_row_update();
        },
        errorCallback: (res) => mysql_error(res)
    })
}

// Eliminar servicio

function deleteService(serviceId) {
    alertify.confirm("Eliminar servicio", "¿Estas seguro que deseas borrar este servicio? ",
        function () {
            sendAjaxRequest({
                url: "services/services.php",
                data: {
                    service_id: serviceId,
                    action: 'eliminar_servicio'
                },
                successCallback: () => dataTablesInstances['services'].ajax.reload(),
                errorCallback: (res) => mysql_error(res),
                verbose: true
            });
        },
        function () {

        });
}

$(document).ready(function () {

    // Aplicar descuento

    $('#discount_service').keyup(function (e) {
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
    $("#service, #rp_service").on("change", function () {
        const service_id = $(this).val();

        // Validar que haya un valor antes de hacer la petición
        if (service_id) {
            fetchService(service_id);
        }
    });


    function fetchService(serviceId) {
        sendAjaxRequest({
            url: "services/services.php",
            data: {
                service_id: serviceId,
                action: "buscar_servicios"
            },
            successCallback: (res) => {
                const data = JSON.parse(res)[0];

                const priceOut = $("#price_out");
                const cost = $("#service_cost");

                // Reiniciar y deshabilitar los campos por defecto
                priceOut.val('').prop("disabled", true);
                cost.val('').prop("disabled", true);

                // Si el precio es válido y mayor que cero, se formatea y se asigna
                if (Number(data.precio) > 0) {
                    priceOut.val(format.format(data.precio));
                } else {
                    // Si el precio es 0 o no existe, se habilita el campo para edición manual
                    priceOut.prop("disabled", false);
                }

                // Si el costo es válido y mayor que cero, se formatea y se asigna
                if (Number(data.costo) > 0) {
                    cost.val(format.format(data.costo));
                    $('#cost-field').hide()
                } else {
                    // Si el costo es 0 o no existe, se habilita el campo para edición manual
                    cost.prop("disabled", false);
                    $('#cost-field').show()
                    cost.val(0)
                }
            }
        });
    }


}); // Ready