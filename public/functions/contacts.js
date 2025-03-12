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


    $('input:radio[name=contact]').change(function () {
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
        url: SITE_URL + "ajax/contacts.php",
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
        success: function (res) {

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
        url: SITE_URL + "ajax/contacts.php",
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
        success: function (res) {

            if (res == "ready") {

                $('input[type="text"]').val('');
                $('input[type="number"]').val('');

                mysql_row_affected();
                setTimeout('document.location.reload()',1100);

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
        url: SITE_URL + "ajax/contacts.php",
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
        success: function (res) {

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
        function () {

            $.ajax({
                url: SITE_URL + "ajax/contacts.php",
                method: "post",
                data: {
                    customer_id: id,
                    action: 'eliminar_cliente'
                },
                success: function (res) {

                    if (res == "ready") {

                        $(".table").load(location.href + " .table");

                    } else if (res.includes("Error")) {

                        mysql_error(res)
                    }

                }
            });

        },
        function () {

        });
}


// Actualizar proveedor

function UpdateProvider(proveedor_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "ajax/contacts.php",
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
        success: function (res) {

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
        function () {

            $.ajax({
                url: SITE_URL + "ajax/contacts.php",
                method: "post",
                data: {
                    proveedor_id: id,
                    action: 'eliminar_proveedor'
                },
                success: function (res) {

                    if (res == "ready") {

                        $(".table").load(location.href + " .table");

                    } else if (res.includes("Error")) {

                        mysql_error(res)
                    }

                }
            });

        },
        function () {

        });
}


// Eliminar bono

function deleteBond(id) {

    $.ajax({
        url: SITE_URL + "ajax/contacts.php",
        method: "post",
        data: {
            bond_id: id,
            action: 'eliminar_bono'
        },
        success: function (res) {

            if (res == "ready") {

                $(".table").load(location.href + " .table");

            } else if (res.includes("Error")) {

                mysql_error(res)
            }

        }
    });

}