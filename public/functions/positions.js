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
* Agregar posición
------------------------------------------*/

function AddPosition() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/positions.php",
        data: {
            reference: $('#reference').val(),
            comment: $('#position_comment').val(),
            action: 'agregar_posicion'
        },
        beforeSend: function () {
           
        },
        success: function (res) {

            if (res == "ready") {

                mysql_row_affected()
                $('input[type="text"]').val('');
                $('textarea').val('');
                $(".table").load(location.href + " .table");

            } else {
               mysql_error(res)
            }

        }
    });

}


/**
 * Actualizar Posición
----------------------------------- */

function UpdatePosition(position_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/positions.php",
        data: {
            position_id: position_id,
            reference: $('#reference').val(),
            comment: $('#position_comment').val(),
            action: 'actualizar_posicion'
        },
        beforeSend: function () {
         
        },
        success: function (res) {

            if (res == "ready") {

                $(".table").load(location.href + " .table");
                mysql_row_update()

            } else {
               mysql_error(res)
            }

        }
    });

}

/**
 * Eliminar Posición
 ----------------------------------*/

 function deletePosition(id) {

    alertify.confirm("Eliminar posición","¿Estas seguro que deseas borrar esta posición? ",
        function () {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/positions.php",
                data: {
                    position_id: id,
                    action: 'eliminar_posicion'
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