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
* Agregar Almacen
------------------------------------------*/

function AddWarehouse() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/warehouses.php",
        data: {
            name: $('#warehouse_name').val(),
            comment: $('#warehouse_comment').val(),
            action: 'agregar_almacen'
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
 * Actualizar Almacen
----------------------------------- */

function UpdateWarehouse(warehouse_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/warehouses.php",
        data: {
            warehouse_id: warehouse_id,
            name: $('#warehouse_name').val(),
            comment: $('#warehouse_comment').val(),
            action: 'actualizar_almacen'
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
 * Eliminar Almacen
 ----------------------------------*/

 function deleteWarehouse(id) {

    alertify.confirm("Eliminar almacen","¿Estas seguro que deseas borrar este almacen? ",
        function () {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/warehouses.php",
                data: {
                    warehouse_id: id,
                    action: 'eliminar_almacen'
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