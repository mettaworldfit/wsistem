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

    // Aplicar descuento con cantidad
    $('#discount_service').keyup(function (e) {
        e.preventDefault();

        // Obtener valores numéricos
        const price = parseFloat($('#price_out').val().replace(/,/g, '')) || 0;
        const quantity = parseFloat($('#service_quantity').val()) || 1;
        const discount = parseFloat($('#discount_service').val()) || 0;

        // Calcular precio total con cantidad
        const total = price * quantity;

        // Validar que el descuento no supere el total
        if (discount <= total) {
            $('#rp_add_item').show(); // Botón de orden de reparación
            $('#add_item').show();    // Botón de factura
        } else {
            $('#rp_add_item').hide();
            $('#add_item').hide();
        }
    });


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

                $('#service_quantity').val('1');
                const priceOut = $("#price_out");
                const cost = $("#service_cost");

                // Reiniciar y deshabilitar los campos por defecto
                priceOut.val('').prop("disabled", true);
                // cost.val('').prop("disabled", true);

                // Si el precio es válido y mayor que cero, se formatea y se asigna
                if (Number(data.precio) > 0) {
                    priceOut.val(format.format(data.precio));
                } else {
                    // Si el precio es 0 o no existe, se habilita el campo para edición manual
                    priceOut.prop("disabled", false);
                }

                // // Si el costo es válido y mayor que cero, se formatea y se asigna
                if (Number(data.costo) > 0) {
                    cost.val(format.format(data.costo));
                    // $('#cost-field').hide()
                } else {
                    // Si el costo es 0 o no existe, se habilita el campo para edición manual
                    // cost.prop("disabled", false);
                    // $('#cost-field').show()
                    cost.val(0)
                }
            }
        });
    }



    /**
        * calculateDetailModalTotalService
        * --------------------------
        * Esta función calcula el total dentro del modal de agregar detalle.
        * - Obtiene la cantidad introducida por el usuario.
        * - Obtiene el precio unitario del producto seleccionado.
        * - Obtiene el porcentaje de descuento (si aplica).
        * - Calcula el subtotal (cantidad * precio).
        * - Aplica el descuento en base al porcentaje.
        * - Muestra el total en el campo correspondiente.
        */
    function calculateDetailModalTotalService(price_out = 0) {
        var quantity = parseFloat($("#service_quantity").val()) || 1; // por defecto 1
        var discount = $("#discount_service").val();
      
      // Convertir price_out a número
        let priceOutValue = parseFloat(price_out) || 0;

        // Usar priceOutValue si es mayor a 0, sino el precio del producto
        let price = priceOutValue > 0
            ? priceOutValue
            : parseFloat($("#service option:selected").data("price")) || $('#price_out').val();

        var subtotal = quantity * price;

        // Calcular el total
        var total = subtotal - discount;

        $("#totalPrice").val(total.toFixed(2));
    }

    // Cada vez que cambie servicio, setea también el descuento automático
    $("#service").on("change", function () {
        calculateDetailModalTotalService();
    });

    // Detectar cambios en todos los campos del modal de servicios
    $("#service_quantity, #discount_service, #service").on("input change", calculateDetailModalTotalService);


     $("#price_out").on("keyup", function () { 

        calculateDetailModalTotalService($(this).val());
     })

}); // Ready