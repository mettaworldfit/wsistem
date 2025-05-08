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

let dt_customers; // Declarada globalmente

$(document).ready(function() {

    dt_customers = $('#customers').DataTable({
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
            const $tbody = $('#customer tbody');
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
                    url: SITE_URL + 'services/contacts.php',
                    type: 'POST',
                    data: {
                        action: 'index_clientes',
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
            { data: 'id' },
            { data: 'nombre' },
            { data: 'direccion' },
            { data: 'cedula' },
            { data: 'telefono' },
            { data: 'fecha' },
            { data: 'acciones', orderable: false, searchable: false }
        ],
        initComplete: function() {

        }

    });


    $('input:radio[name=contact]').change(function() {
        if ($(this).val() == "cliente") {
            $('#cod_client').slideToggle('fast');

        } else {
            $('#cod_client').hide();
        }
    });




}); // Ready


// Agregar cliente

function AddContact() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/contacts.php",
        data: {
            name: $('#name').val(),
            lastname: $('#lastname').val(),
            address: $('#select2-address-container').attr('title'),
            identity: $('#identity').val(),
            tel1: $('#tel1').val(),
            tel2: $('#tel2').val(),
            email: $('#email').val(),
            type: $('input:radio[name=contact]:checked').val(),
            action: 'crear_contacto'

        },
        success: function(res) {

            if (res == "ready") {

                $('input[type="text"]').val('');
                $('input[type="number"]').val('');

                mysql_row_affected();

            } else if (res == "duplicate") {

                mysql_error('El RNC o Cédula del cliente ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }

        }
    });

}

// Agregar cliente

function AddContactModal() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/contacts.php",
        data: {
            name: $('#name').val(),
            lastname: $('#lastname').val(),
            address: $('#select2-address-container').attr('title'),
            identity: $('#identity').val(),
            tel1: $('#tel1').val(),
            tel2: $('#tel2').val(),
            email: $('#email').val(),
            type: "cliente",
            action: 'crear_contacto'

        },
        success: function(res) {

            if (res == "ready") {

                $('input[type="text"]').val('');
                $('input[type="number"]').val('');

                mysql_row_affected();
                setTimeout('document.location.reload()', 1100);

            } else if (res == "duplicate") {

                mysql_error('El RNC o Cédula del cliente ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }

        }
    });

}



// Actualizar cliente

function UpdateCustomer(customer_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/contacts.php",
        data: {
            id: customer_id,
            name: $('#name').val(),
            lastname: $('#lastname').val(),
            address: $('#select2-address-container').attr('title'),
            identity: $('#identity').val(),
            tel1: $('#tel1').val(),
            tel2: $('#tel2').val(),
            email: $('#email').val(),
            action: 'actualizar_cliente'

        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected();

            } else if (res == "duplicate") {

                mysql_error('El RNC o Cédula del cliente ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }

        }
    });
}


// Eliminar cliente

function deleteCustomer(id) {

    alertify.confirm("Eliminar cliente", "¿Estas seguro que deseas eliminar este cliente? ",
        function() {

            $.ajax({
                url: SITE_URL + "services/contacts.php",
                method: "post",
                data: {
                    customer_id: id,
                    action: 'eliminar_cliente'
                },
                success: function(res) {

                    if (res == "ready") {

                        dt_customers.ajax.reload();

                    } else if (res.includes("Error")) {

                        mysql_error(res)
                    }

                }
            });

        },
        function() {

        });
}


// Actualizar proveedor

function UpdateProvider(proveedor_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/contacts.php",
        data: {
            id: proveedor_id,
            name: $('#name').val(),
            lastname: $('#lastname').val(),
            address: $('#select2-address-container').attr('title'),
            tel1: $('#tel1').val(),
            tel2: $('#tel2').val(),
            email: $('#email').val(),
            action: 'actualizar_proveedor'

        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected();

            } else if (res.includes("Error")) {
                mysql_error(res)
            }

        }
    });
}

// Eliminar proveedor

function deleteProveedor(id) {

    alertify.confirm("Eliminar proveedor", "¿Estas seguro que deseas eliminar este proveedor? ",
        function() {

            $.ajax({
                url: SITE_URL + "services/contacts.php",
                method: "post",
                data: {
                    proveedor_id: id,
                    action: 'eliminar_proveedor'
                },
                success: function(res) {

                    if (res == "ready") {

                        $(".table").load(location.href + " .table");

                    } else if (res.includes("Error")) {

                        mysql_error(res)
                    }

                }
            });

        },
        function() {

        });
}


// Eliminar bono

function deleteBond(id) {

    $.ajax({
        url: SITE_URL + "services/contacts.php",
        method: "post",
        data: {
            bond_id: id,
            action: 'eliminar_bono'
        },
        success: function(res) {

            if (res == "ready") {

                $(".table").load(location.href + " .table");

            } else if (res.includes("Error")) {

                mysql_error(res)
            }

        }
    });

}