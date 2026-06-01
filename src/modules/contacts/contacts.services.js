$(document).ready(function() {

    $('input:radio[name=contact]').change(function() {
        if ($(this).val() == "cliente") {
            $('#cod_client').slideToggle('fast');

        } else {
            $('#cod_client').hide();
        }
    });

}); // Ready

function AddContact() {
    sendAjaxRequest({
        url: "src/modules/contacts/contacts.repository.php",
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
        successCallback: () => {
            $('input[type="text"], input[type="number"]').val('');
            mysql_row_affected();
        }
    });
}

function AddContactModal() {
    sendAjaxRequest({
        url: "src/modules/contacts/contacts.repository.php",
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
        successCallback: () => {
            $('input[type="text"], input[type="number"]').val('');
            mysql_row_affected();
            setTimeout(() => location.reload(), 900);
         
        },verbose: false
    });
}

function UpdateCustomer(customer_id) {
    sendAjaxRequest({
        url: "src/modules/contacts/contacts.repository.php",
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
        successCallback: mysql_row_affected
    });
}

function deleteCustomer(id) {
    alertify.confirm("Eliminar cliente", "¿Estas seguro que deseas eliminar este cliente?",
        function() {
            sendAjaxRequest({
                url: "src/modules/contacts/contacts.repository.php",
                data: {
                    customer_id: id,
                    action: 'eliminar_cliente'
                },
                successCallback: () => dataTablesInstances['customers'].ajax.reload(null, false)
            });
        },
        function() {});
}

function UpdateProvider(proveedor_id) {
    sendAjaxRequest({
        url: "src/modules/contacts/contacts.repository.php",
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
        successCallback: mysql_row_affected
    });
}

function deleteProveedor(id) {
    alertify.confirm("Eliminar proveedor", "¿Estas seguro que deseas eliminar este proveedor?",
        function() {
            sendAjaxRequest({
                url: "src/modules/contacts/contacts.repository.php",
                data: {
                    proveedor_id: id,
                    action: 'eliminar_proveedor'
                },
                successCallback: () => dataTablesInstances['providers'].ajax.reload(null, false)
            });
        },
        function() {});
}

function deleteBond(id) {
    sendAjaxRequest({
        url: "src/modules/contacts/contacts.repository.php",
        data: {
            bond_id: id,
            action: 'eliminar_bono'
        },
        successCallback: () => $(".table").load(location.href + " .table")
    });
}