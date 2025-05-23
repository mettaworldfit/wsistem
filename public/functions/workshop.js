$(document).ready(function() {


        /**
         * TODO: Imprimir orden de reparación
         */


        $('#printer_order').on('click', (e) => {
                e.preventDefault;

                data = {
                    subtotal: $('#in-subtotal').val().replace(/,/g, ""),
                    discount: $('#in-discount').val().replace(/,/g, ""),
                    total: $('#in-total').val().replace(/,/g, ""),
                    observation: $('#observation').val(),
                    order_id: $('#orden_id').val()
                }

                $.ajax({
                    type: "post",
                    url: PRINTER_SERVER + "factura_ordenrp.php",
                    data: {
                        detail: $('#detail_order').val(),
                        device: $('#device_info').val(),
                        condition: $('#conditions').val(),
                        info: data
                    },
                    success: function(res) {
                        console.log('Imprimiendo ticket')

                    }
                }); // Ajax
            }) // Function


        // Buscar dispositivo

        $('#device').change(function() {
                $.ajax({
                    type: "post",
                    url: SITE_URL + "services/workshop.php",
                    data: {
                        device_id: $('#device').val(),
                        action: 'buscar_equipo'
                    },
                    success: function(res) {

                        var data = JSON.parse(res);

                        $('#brand').val(data.nombre_marca)
                        $('#model').val(data.modelo)
                    }
                }); // Ajax
            }) // Function


    }) // Ready




function add_ordenRP() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/workshop.php",
        data: {
            action: 'agregar_orden_reparacion',
            customer_id: $('#customer_id').val(),
            device: $('#device').val(),
            serie: $('#serie').val(),
            observation: $('#observation').val(),
            imei: $('#imei').val()

        },
        success: function(res) {

            if (res > 0) {

                Assign_condition(res);
                //   GenerateOrderPDF(res)

                $('input[type="text"]').val('');
                $('input[type="number"]').val('');
                $(".table").load(location.href + " .table");

                window.location.href = SITE_URL + 'invoices/addrepair&id=' + res

            } else {
                mysql_error(res)
            }
        }
    });

}

// Generar factura pdf

function GenerateOrderPDF(order) {

    var width = 1000;
    var height = 800;

    // Centar la ventana
    var x = parseInt((window.screen.width / 2) - (width / 2));
    var y = parseInt((window.screen.height / 2) - (height / 2));

    var url = SITE_URL + 'src/pdf/generar_order_rp.php?o=' + order;
    window.open(url, 'Factura', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

}


// Asignar condiciones a la orden

function Assign_condition(orden_id) {

    array = $('#condition_id').val()

    array.forEach(element => {

        $.ajax({
            type: "post",
            url: SITE_URL + "services/workshop.php",
            data: {
                action: "asignar_condiciones",
                condition_id: element,
                orden_id: orden_id

            },
            success: function(res) {

                if (res == "ready") {

                    mysql_row_affected()

                } else {
                    mysql_error(res)
                }
            }
        });

    }); // Loop
}



// Actualizar estado de la orden

function elegirEstado(el) { // recibimos por parametro el elemento select

    // obtenemos la opción seleccionada .
    var workshop_id = $('option:selected', el).attr('workshop_id');
    var status_id = $('option:selected', el).attr('value');

    $.ajax({
        type: "post",
        url: SITE_URL + "services/workshop.php",
        data: {
            status: status_id,
            workshop_id: workshop_id,
            action: 'actualizar_estado_orden'
        },
        success: function(res) {

            if (res == "ready") {

                dataTablesInstances['workshop'].ajax.reload(); // Actualizar datatable

            } else {
                mysql_error(res)
            }

        }
    });
}

// Eliminar orden de reparación

function deleteOrden(id) {

    alertify.confirm("Eliminar orden", "¿Estas seguro que deseas eliminar esta orden? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/workshop.php",
                data: {
                    id: id,
                    action: 'eliminar_orden'
                },
                success: function(res) {

                    if (res == "ready") {

                        dataTablesInstances['workshop'].ajax.reload(); // Actualizar datatable

                    } else {
                        mysql_error(res)
                    }

                }
            });
        },
        function() {

        });
}


// Crear condición de reparación 

function AddCondition() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/workshop.php",
        data: {
            condition: $('#condition').val(),
            action: 'crear_condicion'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected()
                setTimeout('document.location.reload()', 1100);

            } else {
                mysql_error(res)
            }

        }
    });
}


function AddDevice() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/workshop.php",
        data: {
            brand: $('#brand_id').val(),
            device: $('#nom_device').val(),
            model: $('#num_device').val(),
            action: 'crear_equipo'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected()
                setTimeout('document.location.reload()', 1100);

            } else {
                mysql_error(res)
            }

        }
    });

}



function AddBrand() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/workshop.php",
        data: {
            name: $('#brand_name').val(),
            action: 'crear_marca'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected()

            } else {
                mysql_error(res)
            }

        }
    });

}


function UpdateBrand(id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/workshop.php",
        data: {
            name: $('#brand_name').val(),
            id: id,
            action: 'actualizar_marca'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected()

            } else {
                mysql_error(res)
            }

        }
    });

}

// Eliminar marca

function deleteBrand(id) {

    alertify.confirm("Eliminar marca", "¿Estas seguro que deseas eliminar esta marca? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/workshop.php",
                data: {
                    id: id,
                    action: 'eliminar_marca'
                },
                success: function(res) {

                    if (res == "ready") {

                        mysql_row_affected()
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