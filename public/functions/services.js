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

   /**============================================================= 
   * FUNCIONES Y ACCIONES EN LAS VENTAS SECCION SERVICIOS
   ===============================================================*/

     // Funcion que maneja y muestra los inputs en las ventanas
    function handleServiceModal() {
        const tipo = $('input[name="tipo"]:checked').val();

        // Limpiar campos comunes
        $('#code, #piece_code, #stock, #discount, #quantity, #service_quantity, #price_out,#totalPriceService').val('');

        if (tipo === "servicio") {
            // Mostrar campos relacionados con servicios
            $('.service').show();
            $('.product, .piece, .product-piece').hide();
            $('.discount').hide();

            $('#discount_service').show();
            $('#add_item_free').hide();

            // Modal total
            $("#totalPriceService").show();
            $("#totalPricePiece, #totalPriceProduct").hide();

            // Volver a cargar imagen
            $('.item-img').load(window.location.href + ' .item-img > *');

            // Requerimientos
            $('#service').attr('required', true);
            $('#product, #piece').attr('required', false);
            $('#quantity').attr('required', false);
            $('#discount, #price_out').attr('disabled', false);

            // Mostrar botón para agregar servicio
            $('#add_item').show();

            // Placeholder de Select2
            $('#select2-service-container').html("Buscar servicios");
        }
    }

    handleServiceModal(); // Inicializador
    $('input[name="tipo"]').on('change', handleServiceModal);


    // Buscar servicio por nombre
    $("#service").on("change", function () {
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

    /**
    * Calcula y muestra el total de un servicio en el modal de detalle.
    *
    * La función:
    * - Verifica que el tipo seleccionado sea "servicio"
    * - Obtiene la cantidad, precio base y descuentos
    * - Determina el precio final según lista de precios o precio directo
    * - Calcula subtotal, descuento y total
    * - Actualiza el total en el DOM
    *
    * @param {number} [price_out=0] - Precio externo opcional (por ejemplo, desde una lista de precios).
    *                                 Si es mayor a 0, tiene prioridad sobre el precio del producto.
    *
    * @returns {void} No retorna ningún valor, solo actualiza el HTML.
    */
    function calculateDetailModalTotalService(price_out = 0) {
        // Obtener cantidad (default 1)
        var quantity = parseFloat($("#service_quantity").val()) || 1;

        // Obtener descuento (0 si vacío o NaN)
        var discount = parseFloat($("#discount_service").val() || $("#discount").val()) || 0;

        // Precio externo (valor manual de price_out)
        let priceOutValue = parseFloat(price_out) || 0;

        // Determinar precio (prioridad: externo > data-price > input price_out)
        let price = priceOutValue > 0
            ? priceOutValue
            : parseFloat($("#service option:selected").data("price")) || parseFloat($('#price_out').val()) || 0;

        // Calcular subtotal y total
        var subtotal = quantity * price;
        var total = subtotal - discount;

        // Mostrar total con 2 decimales
        $("#totalPriceService").text(total.toFixed(2));
    }


    // Cuando cambie servicio, recalcular
    $("#service").on("change", function () {
        calculateDetailModalTotalService();
    });

    // Detectar cambios en cantidad, descuentos y servicio
    $("#service_quantity, #discount_service, #discount, #service").on("input change", calculateDetailModalTotalService);

    // Si editan manualmente el precio
    $("#price_out").on("keyup", function () {
        calculateDetailModalTotalService($(this).val());
    });


}); // Ready