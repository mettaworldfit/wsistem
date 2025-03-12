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



// Actualizar configuración de bonos

function UpdateBond_config() {

    $.ajax({
        type: "post",
        url: SITE_URL + "ajax/config.php",
        data: {
           min: $('#min_invoice').val(),
           value: $('#bonus_value').val(),
           status: $('#status').val(),
           action: 'actualizar_bono_config'
        },
        success: function (res) {

            if (res == "ready") {

                mysql_row_update()

            } else {
               mysql_error(res)
            }

        }
    });

}