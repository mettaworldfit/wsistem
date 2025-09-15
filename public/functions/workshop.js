function addOrdenRepair() {
    
    sendAjaxRequest({
        url: "services/workshop.php",
        data: {
            action: 'agregar_orden_reparacion',
            customer_id: $('#or_customer_id').val(),
            device: $('#device').val(),
            serie: $('#serie').val(),
            observation: $('#observation_repair').val(),
            imei: $('#imei').val()
        },
        successCallback: (res) => {
            assignConditionToOrder(res);

            $('input[type="text"]').val('');
            $('input[type="number"]').val('');
            dataTablesInstances['workshop'].ajax.reload(null, false);

            window.location.href = SITE_URL + 'invoices/addrepair&id=' + res
        },
        errorCallback: (res) => mysql_error(res)
    });
}

// Asignar condiciones a la orden

function assignConditionToOrder(ordenId) {

    const array = $('#condition_id').val()

    array.forEach(element => {
        sendAjaxRequest({
            url: "services/workshop.php",
            data: {
                action: "asignar_condiciones",
                condition_id: element,
                orden_id: ordenId

            },
            successCallback: () => mysql_row_affected(),
            errorCallback: (res) => mysql_error(res)
        })
    }); // Loop
}

// Actualizar estado de la orden
function updateOrderStatus(selectElement) {
    // Obtener valores de la opción seleccionada
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const status_id = selectedOption.value;
    const order_id = selectedOption.getAttribute('order_id');

    // Validación
    if (!status_id || !order_id) {
        console.warn("Faltan datos para actualizar el estado.");
        return;
    }

    // Determinar URL según la página actual
    const url = pageURL.includes("invoices/orders")
        ? "services/invoices.php"
        : "services/workshop.php";

    // Enviar AJAX
    sendAjaxRequest({
        url: url,
        data: {
            status: status_id,
            order_id: order_id,
            action: 'actualizar_estado_orden'
        },
        successCallback: () => {
            const tableKey = pageURL.includes("invoices/orders") ? 'orders' : 'workshop';
            if (dataTablesInstances[tableKey]) {
                dataTablesInstances[tableKey].ajax.reload(null, false);
            }
        },
        errorCallback: (res) => mysql_error(res)
    });
}



// Eliminar orden de reparación

function deleteRepairOrder(id) {

    alertify.confirm("Eliminar orden", "¿Estas seguro que deseas eliminar esta orden? ",
        function () {

            sendAjaxRequest({
                url: "services/workshop.php",
                data: {
                    id: id,
                    action: 'eliminar_orden'
                },
                successCallback: () => dataTablesInstances['workshop'].ajax.reload(null, false),
                errorCallback: (res) => mysql_error("Ha ocurrido un error inesperado")

            });
        },
        function () {

        });
}


// Crear condición de reparación 

function addRepairCondition() {
    sendAjaxRequest({
        url: "services/workshop.php",
        data: {
            condition: $('#condition').val(),
            action: 'crear_condicion'
        },
        successCallback: () => {
            mysql_row_affected()
            setTimeout('document.location.reload()', 1000);
        },
        errorCallback: (res) => mysql_error(res)
    });
}


// Agregar dispositivo
function addDevice() {
    sendAjaxRequest({
        url: "services/workshop.php",
        data: {
            brand: $('#brand_id').val(),
            device: $('#nom_device').val(),
            model: $('#num_device').val(),
            action: 'crear_equipo'
        },
        successCallback: () => {
            mysql_row_affected()
            setTimeout('document.location.reload()', 1000);
        },
        errorCallback: (res) => mysql_error(res)
    });
}

// Agregar marca

function addBrand() {
    sendAjaxRequest({
        url: "services/workshop.php",
        data: {
            name: $('#brand_name').val(),
            action: 'crear_marca'
        },
        successCallback: () => mysql_row_affected(),
        errorCallback: (res) => mysql_error(res)
    });

}

// Actualizar marca
function updateBrand(brandId) {
    sendAjaxRequest({
        url: "services/workshop.php",
        data: {
            name: $('#brand_name').val(),
            id: brandId,
            action: 'actualizar_marca'
        },
        successCallback: () => mysql_row_affected(),
        errorCallback: (res) => mysql_error(res)
    });
}

// Eliminar marca
function deleteBrand(id) {
    alertify.confirm("Eliminar marca", "¿Estas seguro que deseas eliminar esta marca? ",
        function () {

            sendAjaxRequest({
                url: "services/workshop.php",
                data: {
                    id: id,
                    action: 'eliminar_marca'
                },
                successCallback: () => dataTablesInstances['brands'].ajax.reload(null, false),
                errorCallback: (res) => mysql_error(res)
            });
        },
        function () {

        });
}

$(document).ready(function () {

/**
 * Evento para imprimir la orden de reparación.
 * Escucha el click en el botón con id "printer_order" y envía los datos al servidor de impresión.
 */
$('#printer_order').on('click', (e) => {
    e.preventDefault();

    console.log('imprimiendo.....');

    // Muestra notificación de impresión en pantalla
    mdtoast('imprimiendo ticket...', {
        interaction: true,
        interactionTimeout: 1500,
        position: "bottom right"
    });

    /**
     * Información general de la orden de reparación.
     * Los valores numéricos se limpian de comas para facilitar su uso en el servidor.
     * @type {{subtotal: string, discount: string, total: string, observation: string, order_id: string}}
     */
    const data = {
        subtotal: $('#in-subtotal').val().replace(/,/g, ""),
        discount: $('#in-discount').val().replace(/,/g, ""),
        total: $('#in-total').val().replace(/,/g, ""),
        observation: $('#observation').val(),
        order_id: $('#orden_id').val()
    };

    // Envía la información de la orden de reparación al servidor de impresión
    $.ajax({
        type: "post",
        url: PRINTER_SERVER + "factura_ordenrp.php",
        data: {
            detail: $('#detail_order').val(),
            device: $('#device_info').val(),
            condition: $('#conditions').val(),
            info: data
        },
        /**
         * Callback de éxito que maneja la respuesta del servidor de impresión.
         * @param {string} res - Respuesta del servidor (estado de la impresión).
         */
        success: function (res) {
            console.log("respuesta:", res);
        }
    });
});



    // Buscar dispositivo

    $('#device').change(function () {
        sendAjaxRequest({
            url: "services/workshop.php",
            data: {
                device_id: $('#device').val(),
                action: 'buscar_equipo'
            },
            successCallback: (res) => {
                var data = JSON.parse(res);

                $('#brand').val(data.nombre_marca)
                $('#model').val(data.modelo)
            },
            errorCallback: (res) => mysql_error(res)
        });
    }) // Function

}) // Ready