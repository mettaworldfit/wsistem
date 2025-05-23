/**
* Agregar Oferta
------------------------------------------*/

function AddOffer() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/offers.php",
        data: {
            name: $('#offer_name').val(),
            comment: $('#offer_comment').val(),
            value: $('#offer_value').val(),
            action: 'agregar_oferta'
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
 * Actualizar Oferta
----------------------------------- */

function UpdateOffer(offer_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/offers.php",
        data: {
            offer_id: offer_id,
            name: $('#offer_name').val(),
            comment: $('#offer_comment').val(),
            value: $('#offer_value').val(),
            action: 'actualizar_oferta'
        },
        success: function(res) {

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
 * Eliminar Oferta
 ----------------------------------*/

function deleteOffer(id) {

    alertify.confirm("Eliminar oferta", "¿Estas seguro que deseas borrar esta oferta? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/offers.php",
                data: {
                    offer_id: id,
                    action: 'eliminar_oferta'
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