// Actualizar configuración de bonos

function UpdateBond_config() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/config.php",
        data: {
            min: $('#min_invoice').val(),
            value: $('#bonus_value').val(),
            status: $('#status').val(),
            action: 'actualizar_bono_config'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_update()

            } else {
                mysql_error(res)
            }

        }
    });

}

// Actualizar ajustes de la facturacion electronica

function ConfigElectronicInv() {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/config.php",
        data: {
            company: $('#company').val(),
            logo: $('#logo').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            host: $('#host').val(),
            port: $('#port').val(),
            smtps: $('#smtps').val(),
            facebook: $('#facebook').val(),
            whatsapp: $('#whatsapp').val(),
            instagram: $('#instagram').val(),
            action: 'ajustes_factura_electronica'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_update()

            } else {
                mysql_error(res)
            }

        }
    });
}

// Actualizar ajustes de la facturacion electronica

function ConfigPDF() {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/config.php",
        data: {
            logo: $('#logo').val(),
            slogan: $('#slogan').val(),
            address: $('#address').val(),
            tel: $('#tel').val(),
            policy: $('#policy').val(),
            title: $('#title').val(),
            action: 'ajustes_factura_pdf'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_update()

            } else {
                mysql_error(res)
            }

        }
    });
}