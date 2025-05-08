/**
 * Agregar categoría
 ------------------------------------*/

function AddCategorie() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/categories.php",
        data: {
            name: $('#category_name').val(),
            comment: $('#category_comment').val(),
            action: 'agregarCategoria'
        },
        beforeSend: function() {

        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_affected()

                $('input[type="text"]').val('');
                $('input[type="number"]').val('');
                $('textarea').val('');
                $(".table").load(location.href + " .table");

            } else {
                mysql_error(res)
            }

        }
    });

}

/**
 * Actualizar categoría
 */

function UpdateCategorie(category_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/categories.php",
        data: {
            category_id: category_id,
            name: $('#category_name').val(),
            comment: $('#category_comment').val(),
            action: 'actualizar_categoria'
        },
        beforeSend: function() {

        },
        success: function(res) {

            if (res == "ready") {
                mysql_row_update()
                $(".table").load(location.href + " .table");

            } else {
                mysql_error(res)
            }

        }
    });

}

/**
 * Eliminar categoria
 ----------------------------------*/

function deleteCategory(id) {

    alertify.confirm("Eliminar categoría", "¿Estas seguro que deseas borrar esta categoría? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/categories.php",
                data: {
                    category_id: id,
                    action: 'eliminar_categoria'
                },
                beforeSend: function() {

                },
                success: function(res) {

                    if (res == "ready") {

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