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

function UpdateUser(user_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/users.php",
        data: {
            id: user_id,
            username: $('#username').val(),
            name: $('#name').val(),
            lastname: $('#lastname').val(),
            rol: $('#rol').val(),
            password: $('#password').val(),
            action: "actualizar_usuario"
        },
        beforeSend: function() {

        },
        success: function(res) {

            if (res == "ready") {
                mysql_row_update()

            } else if (res == "duplicate") {

                mysql_error('El nombre de usuario ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }



        }
    });
}


// Desactivar usuario

function disableUser(user_id) {
    alertify.confirm("<i class='text-warning fas fa-exclamation-circle'></i> Desactivar usuario", "¿Desea desactivar este usuario? ",
        function() {
            $.ajax({
                type: "post",
                url: SITE_URL + "services/users.php",
                data: {
                    user_id: user_id,
                    action: "desactivar_usuario",
                },
                beforeSend: function() {

                },
                success: function(res) {

                    if (res == "ready") {

                        $("#example").load(" #example");

                    } else {
                        mysql_error(res)
                    }


                },
            });
        },
        function() {}
    );
}

// Activar usuario

function enableUser(user_id) {
    alertify.confirm("Activar usuario", "¿Desea activar este usuario? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/users.php",
                data: {
                    user_id: user_id,
                    action: "activar_usuario",
                },
                beforeSend: function() {
                    $(".loader").show();
                },
                success: function(res) {

                    if (res == "ready") {

                        $("#example").load(" #example");

                    } else {
                        mysql_error(res)
                    }

                    $(".loader").hide();

                },
            });
        },
        function() {

        });
}