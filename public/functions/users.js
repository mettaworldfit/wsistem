function deleteUser(user_id) {

    alertify.confirm("Eliminar usuario", "¿Estas seguro que deseas borrar este usuario? ",
        function () {

            sendAjaxRequest({
                url: "services/users.php",
                data: {
                    action: "eliminar_usuario",
                    user_id: user_id
                },
                successCallback: () => dataTablesInstances['users'].ajax.reload(),
                errorCallback: (res) => mysql_error(res)
            })
        },
        function () {

        });
}


// Crear usuario

function addUser() {

    sendAjaxRequest({
        url: "services/users.php",
        data: {
            username: $('#username').val(),
            name: $('#name').val(),
            lastname: $('#lastname').val(),
            rol: $('#rol').val(),
            password: $('#password').val(),
            action: "crear_usuario"
        },
        successCallback: (res) => {
            $('input[type="text"]').val('');
            $('input[type="password"]').val('');

            mysql_row_affected();
        },
        errorCallback: (res) => mysql_error(res)
    })
}

// Actualizar usuario

function updateUser(userId) {

    sendAjaxRequest({
        url: "services/users.php",
        data: {
            id: userId,
            username: $('#username').val(),
            name: $('#name').val(),
            lastname: $('#lastname').val(),
            rol: $('#rol').val(),
            password: $('#password').val(),
            action: "actualizar_usuario"
        },
        successCallback: (res) => {
            if (res == "ready") {
                mysql_row_affected()
            }
        },
        errorCallback: (res) => mysql_error(res)
    });
}


// Desactivar usuario

function disableUser(userId) {
    alertify.confirm("<i class='text-warning fas fa-exclamation-circle'></i> Desactivar usuario", "¿Desea desactivar este usuario? ",
        function () {

            sendAjaxRequest({
                url: "services/users.php",
                data: {
                    user_id: userId,
                    action: "desactivar_usuario",
                },
                successCallback: (res) => {
                    if (res === "ready") {
                        dataTablesInstances['users'].ajax.reload();
                    }
                }
            });
        },
        function () { }
    );
}

// Activar usuario

function enableUser(userId) {
    alertify.confirm("Activar usuario", "¿Desea activar este usuario? ",
        function () {

            sendAjaxRequest({
                url: "services/users.php",
                data: {
                    user_id: userId,
                    action: "activar_usuario",
                },
                successCallback: (res) => {
                    if (res === "ready") {

                        dataTablesInstances['users'].ajax.reload();
                    }
                }
            });
        },
        function () {

        });
}

$(document).ready(function () {

    $('.load, .missing').hide();

    // Iniciar sesión

    $('#login').on('submit', (e) => {
        e.preventDefault();

        sendAjaxRequest({
            url: "services/users.php",
            data: {
                user: $('#userName').val().toLowerCase(),
                password: $('#userPassword').val(),
                action: 'login'
            },
            successCallback: (res) => {
                if (res === "approved") {
                    location.href = SITE_URL + "home/index";
                } else {
                    $('.i').css({ color: 'red', transition: '0.4s all ease' });
                    $('.load').hide();
                    $('.missing, #btn-txt').show();
                }
            },
            verbose: true
        })
    })

    // Cerrar sesión

    $('#logout').on('click', (e) => {
        e.preventDefault();

        sendAjaxRequest({
            url: "services/users.php",
            data: {
                action: 'logout'
            },
            successCallback: () => location.reload()
        })
    })

})