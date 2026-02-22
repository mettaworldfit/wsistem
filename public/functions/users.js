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
            }
        })
    })

    // Cerrar sesión
    $('#logout, #logout-movil').on('click', (e) => {
        e.preventDefault();

        sendAjaxRequest({
            url: "services/users.php",
            data: {
                action: 'logout'
            },
            successCallback: () => {
                // Redirigir al usuario
                window.location.href = SITE_URL + "users/login";
            },
            errorCallback: (error) => {
                console.error("Error al cerrar sesión:", error);
            }
        })
    })

    /**============================================================= 
    * C.R.U.D USUARIOS
    ===============================================================*/

    // Crear usuario
    $('#formUser').on('submit', function (e) {
        e.preventDefault()

        let formData = new FormData(this)
        formData.append("action", "crear_usuario")

        sendAjaxRequest({
            url: "services/users.php",
            data: formData,
            successCallback: (res) => {
                $('input[type="text"]').val('');
                $('input[type="password"]').val('');

                notifyAlert("Guardado correctamente", "success", 3000);
            },
            errorCallback: (err) => {
                console.log('%c[USERS]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        })
    })


    // Actualizar usuario
    $('#editUser').on('submit', function (e) {
        e.preventDefault()

        let formData = new FormData(this)
        formData.append("action", "actualizar_usuario")

        sendAjaxRequest({
            url: "services/users.php",
            data: formData,
            successCallback: (res) => {
                notifyAlert("Registro actualizado")
            },
            errorCallback: (err) => {
                console.log('%c[USERS]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        });
    })

    // Eliminar usuario
    $(document).on('click', '.erase_user', function () {

        const userId = $(this).data('id');
        const name = $(this).data('name');

        alertify.confirm("Eliminar usuario", "¿Estas seguro que deseas eliminar el usuario " + name + "?",
            function () {

                sendAjaxRequest({
                    url: "services/users.php",
                    data: {
                        action: "eliminar_usuario",
                        user_id: userId
                    },
                    successCallback: () => dataTablesInstances['users'].ajax.reload(null, false),
                    errorCallback: (err) => {
                        console.log('%c[USERS]', 'color:#b51717;font-weight:bold;', err)
                        notifyAlert(err, "error", 3000)
                    }
                })
            },
            function () {

            });
    })

    // Desactivar usuario
    $(document).on('click', '.disabled_user', function () {

        const userId = $(this).data('id');

        alertify.confirm("<i class='text-warning fas fa-exclamation-circle'></i> Desactivar usuario", "¿Desea desactivar este usuario? ",
            function () {

                sendAjaxRequest({
                    url: "services/users.php",
                    data: {
                        user_id: userId,
                        action: "desactivar_usuario",
                    },
                    successCallback: (res) => {
                        dataTablesInstances['users'].ajax.reload(null, false);
                    },
                    errorCallback: (err) => {
                        console.log('%c[USERS]', 'color:#b51717;font-weight:bold;', err)
                        notifyAlert(err, "error", 3000)
                    }
                });
            },
            function () { }
        );
    })

    // Activar usuario
    $(document).on('click', '.enable_user', function () {
        const userId = $(this).data('id');

        alertify.confirm("Activar usuario", "¿Desea activar este usuario? ",
            function () {

                sendAjaxRequest({
                    url: "services/users.php",
                    data: {
                        user_id: userId,
                        action: "activar_usuario",
                    },
                    successCallback: (res) => {
                        dataTablesInstances['users'].ajax.reload(null, false);
                    },
                    errorCallback: (err) => {
                        console.log('%c[USERS]', 'color:#b51717;font-weight:bold;', err)
                        notifyAlert(err, "error", 3000)
                    }
                });
            },
            function () {

            });
    })


}) // Ready