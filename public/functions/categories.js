function mysql_row_affected () {
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


/**
 * Agregar categoría
 ------------------------------------*/

function AddCategorie() {

    $.ajax({
        type: "post",
        url: SITE_URL + "ajax/categories.php",
        data: {
            name: $('#category_name').val(),
            comment: $('#category_comment').val(),
            action: 'agregarCategoria'
        },
        beforeSend: function () {
           
        },
        success: function (res) {

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
        url: SITE_URL + "ajax/categories.php",
        data: {
            category_id: category_id,
            name: $('#category_name').val(),
            comment: $('#category_comment').val(),
            action: 'actualizar_categoria'
        },
        beforeSend: function () {
         
        },
        success: function (res) {

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

    alertify.confirm("Eliminar categoría","¿Estas seguro que deseas borrar esta categoría? ",
        function () {

            $.ajax({
                type: "post",
                url: SITE_URL + "ajax/categories.php",
                data: {
                    category_id: id,
                    action: 'eliminar_categoria'
                },
                beforeSend: function () {
                
                },
                success: function (res) {
                    
                    if (res == "ready") {
                       
                        $(".table").load(location.href + " .table");

                    } else {
                       mysql_error(res)
                    }
                   

                }
            });

        },
        function () {

        });
}