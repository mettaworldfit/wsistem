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

let dt_workshop; // Declarada globalmente

$(document).ready(function() {

        // index ordenes de servicios
        dt_workshop = $('#workshop').DataTable({
            processing: false, // Oculta el spinner interno de DataTables
            serverSide: true,
            language: {
                lengthMenu: "_MENU_",
                zeroRecords: "Aún no tienes datos para mostrar",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Página no disponible",
                infoFiltered: "(Filtrado de _MAX_  registros)",
                search: "Buscar:", // Cambia el texto
                processing: "Buscando...",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "<i class='fas fa-caret-right'></i>",
                    previous: "Anterior"
                }
            },
            ajax: function(data, callback, settings) {
                // Mostrar loader
                const $tbody = $('#workshop tbody');
                $tbody.html(`
                <tr>
                    <td colspan="100%">
                        <div class="spinner-container">
                            <div class="spinner"></div>
                            <div style="margin-top: 10px;">Cargando datos...</div>
                        </div>
                    </td>
                </tr>
            `);

                // Simular retardo de 900ms antes de hacer la llamada AJAX real
                setTimeout(() => {
                    $.ajax({
                        url: SITE_URL + 'services/workshop.php',
                        type: 'POST',
                        data: {
                            action: 'index_taller',
                            ...data // Importante: esto pasa los datos de paginación, búsqueda, etc.

                        },
                        dataType: 'json',
                        success: function(response) {
                            const json = typeof response === 'string' ? JSON.parse(response) : response;
                            callback(json);
                        }
                    });
                }, 300);
            },
            columns: [
                { data: 'orden' },
                { data: 'nombre' },
                { data: 'equipo' },
                { data: 'fecha_entrada' },
                { data: 'fecha_salida' },
                { data: 'condicion' },
                { data: 'estado' },
                { data: 'acciones', orderable: false, searchable: false },

            ],
            initComplete: function() {

            }

        });


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
                dt_workshop.ajax.reload();

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

                        dt_workshop.ajax.reload();

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