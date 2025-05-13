$(document).ready(function() {

        $('.load').hide();
        $('.missing').hide();


        // Iniciar sesión

        $('#login').on('submit', (e) => {
            e.preventDefault();

            $.ajax({
                type: "post",
                url: SITE_URL + "services/users.php",
                data: {
                    user: $('#userName').val().toLowerCase(),
                    password: $('#userPassword').val(),
                    action: 'login'
                },
                beforeSend: function() {
                    $('#btn-txt').hide();
                    $('.load').show();
                },
                success: function(res) {

                    if (res == "approved") {
                        location.href = SITE_URL + "home/index";
                    } else {
                        $('.i').css('color', 'red');
                        $('.i').css('transition', '0.4s all ease');
                        $('.load').hide();
                        $('.missing').show();
                        $('#btn-txt').show();
                    }
                }
            });

        })


        // Cerrar sesión

        $('#logout').on('click', (e) => {
            e.preventDefault();

            $.ajax({
                type: "post",
                url: SITE_URL + "services/users.php",
                data: {
                    action: 'logout'
                },
                success: function(res) {

                    if (res == "ready") {
                        location.reload();
                    }
                }
            });

        })


    }) // Ready


function deleteUser(user_id) {

    alertify.confirm("Eliminar usuario", "¿Estas seguro que deseas borrar este usuario? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/users.php",
                data: {
                    action: "eliminar_usuario",
                    user_id: user_id
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


// Crear usuario

function AddUser() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/users.php",
        data: {
            username: $('#username').val(),
            name: $('#name').val(),
            lastname: $('#lastname').val(),
            rol: $('#rol').val(),
            password: $('#password').val(),
            action: "crear_usuario"
        },
        success: function(res) {

            if (res == "ready") {

                $('input[type="text"]').val('');
                $('input[type="password"]').val('');

                mysql_row_affected();

            } else if (res == "duplicate") {

                mysql_error('El nombre de usuario ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }


        }
    });
}

// Actualizar usuario

function UpdateUser(userId) {

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
        }
    });
}


// Desactivar usuario

function disableUser(userId) {
    alertify.confirm("<i class='text-warning fas fa-exclamation-circle'></i> Desactivar usuario", "¿Desea desactivar este usuario? ",
        function() {

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
        function() {}
    );
}

// Activar usuario

function enableUser(userId) {
    alertify.confirm("Activar usuario", "¿Desea activar este usuario? ",
        function() {

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
        function() {

        });
}