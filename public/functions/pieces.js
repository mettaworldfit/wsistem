// Función para limpiar los campos

function reset_input() {

    localStorage.removeItem('lista_de_precios');
    $('input[type="text"]').val('');
    $('input[type="password"]').val('');
    $('input[type="number"]').val('');

    $('.list').hide();

    $('.offer').hide();
    $('.provider').hide();
    $("#precioTotal").val("0.00");
    $("#piece_quantity").val("1");
    $("#min_quantity").val("1");

}

$(document).ready(function() {

    $("#piece_quantity").val("1");

    // Buscar pieza por nombre

    $('#piece').change(function() {

        var piece_id = $(this).val();
        SearchPiece(piece_id);

    });

    function SearchPiece(piece_id) {

        $.ajax({
            url: SITE_URL + "services/pieces.php",
            method: "post",
            data: {
                piece_id: piece_id,
                action: "buscar_pieza"
            },
            success: function(res) {

                var data = JSON.parse(res);

                // Cargar lista de precios de la pieza

                if (data.valor_lista > 0) {
                    piece_price(data.IDpieza)
                }

                $("#add_item_free").show()

                $('#piece_id').val(data.IDpieza)
                $('#piece_code').val(data.cod_pieza)
                $('#stock').val(data.cantidad)
                $("#locate").val(data.referencia);
                $('#quantity').val(1);
                $('#price_out').val(format.format(data.precio_unitario))
                $('#quantity').removeAttr('disabled');
                $('#discount').removeAttr('disabled');

                // Incluir oferta
                if (data.oferta > 0) {
                    var oferta = data.precio_unitario * data.oferta / 100;
                    $('#discount').val(oferta);

                    $('#discount').attr("disabled", true);
                } else {
                    $('#discount').val('');
                }

                clcTotalPrice_rp() // Calcular precios

            }
        });
    }


    // Buscar piezas por barcode

    $('#piece_code').keyup(function() {
        var piece_code = $(this).val();

        if (piece_code != '') {
            SearchPieceCode(piece_code);
        } else {
            SearchPieceCode();
        }
    });

    function SearchPieceCode(piece_code) {

        $.ajax({
            url: SITE_URL + "services/pieces.php",
            method: "post",
            data: {
                piece_code: piece_code,
                action: "buscar_codigo_pieza"
            },
            success: function(res) {

                var data = JSON.parse(res);

                $("#add_item_free").show()

                // SearchPiece(data.IDpieza)
                $('#select2-piece-container').attr('title', data.nombre_pieza);
                $('#select2-piece-container').empty(); // Vaciar description
                $('#select2-piece-container').append(data.nombre_pieza); // agregar a description


                $('#piece_id').val(data.IDproducto)
                $('#stock').val(data.cantidad)
                $("#locate").val(data.referencia);
                $('#quantity').val(1);
                $('#price_out').val(format.format(data.precio_unitario))
                $('#quantity').removeAttr('disabled');
                $('#discount').removeAttr('disabled');

                // Incluir oferta
                if (data.oferta > 0) {
                    var oferta = data.precio_unitario * data.oferta / 100;
                    $('#discount').val(oferta);

                    $('#discount').attr("disabled", true);
                } else {
                    $('#discount').val('');
                }

                piece_price(data.IDproducto) // Cargar lista de precios del producto
                clcTotalPrice_rp() // Calcular precios

            }
        });
    }


    // Buscar lista de precios 

    function piece_price(piece_id) {
        $.ajax({
            url: SITE_URL + "ajax/price_lists.php",
            method: "post",
            data: {
                piece_id: piece_id,
                action: 'buscar_lista_de_pieza'
            },
            success: function(res) {

                document.querySelector('#piece_list_id').innerHTML = ""; // Vaciar lista de precios
                document.querySelector('#piece_list_id').innerHTML = '<option value="0" selected>General</option>' + res;

            }
        });
    }

    // Cambiar precio 

    $('#piece_list_id').change(function() {
        if ($(this).val() > 0) {
            $.ajax({
                url: SITE_URL + "ajax/price_lists.php",
                method: "post",
                data: {
                    list_id: $(this).val(),
                    piece_id: $('#piece_id').val(),
                    action: 'elegir_precio_pieza'
                },
                success: function(res) {

                    var data = JSON.parse(res);
                    $('#price_out').val(format.format(data.valor))
                }
            });

        } else {
            SearchPiece($('#piece_id').val()); // Precio normal
        }
    });



    // Validar la cantidad del piezas antes de agregar

    $('#quantity').keyup(function(e) {
        e.preventDefault();

        clcTotalPrice_rp();

    })

    function clcTotalPrice_rp() {

        var stock = parseFloat($('#stock').val());
        var quantity = parseFloat($('#quantity').val());

        // Si la cantidad es mayor al stock, se ocultará el botón de agregar
        if (quantity <= stock) {

            // Ocultar la cantidad si es menor que 1

            if ($('#quantity').val() < 0.1 || isNaN($('#quantity').val())) {
                $('#rp_add_item').hide(); // Botón de ventana detalle de ordenes de reparaciones
                $('#add_item').hide(); // Botón de ventana facturas de ventas


            } else {
                $('#rp_add_item').show(); // Botón de ventana detalle de ordenes de reparaciones
                $('#add_item').show(); // Botón de ventana facturas de ventas

            }

        } else {
            $('#rp_add_item').hide(); // Botón de ventana detalle de ordenes de reparaciones
            $('#add_item').hide(); // Botón de ventana facturas de ventas

        }
    }

    // Agregar pieza

    $("#createPiece").on("click", (e) => {
        e.preventDefault();

        AddPiece();

    });

    function AddPiece() {

        $.ajax({
            type: "post",
            url: SITE_URL + "services/pieces.php",
            data: {
                name: $("#piece_name").val(),
                piece_code: $("#piece_code").val(),
                price_out: $("#inputPrice_out").val(),
                price_in: $("#inputPrice_in").val(),
                quantity: $("#piece_quantity").val(),
                min_quantity: $("#min_quantity").val(),
                // Keys
                provider: $('#provider').val(),
                brand: $("#brand").val(),
                offer: $("#offer").val(),
                category: $("#category").val(),
                position: $("#position").val(),
                warehouse: $("#warehouse").val(),
                action: "agregar_pieza",
            },
            success: function(res) {

                if (res > 0) {

                    if (localStorage.getItem("lista_de_precios")) {
                        Assign_piece_price(res);
                    } else {

                        reset_input()

                        mysql_row_affected();
                    }

                } else if (res == "duplicate") {

                    mysql_error('El código de la pieza ya está siendo utilizado');

                } else if (res.includes("Error")) {
                    mysql_error(res)
                }

            }
        });

    }


    // Asignar precios a una pieza

    function Assign_piece_price(piece_id) {

        if (localStorage.getItem("lista_de_precios")) {

            arrayL = JSON.parse(localStorage.getItem("lista_de_precios"));

            arrayL.forEach((element, index) => {

                $.ajax({
                    type: "post",
                    url: SITE_URL + "ajax/price_lists.php",
                    data: {
                        action: "asignar_lista_de_precios",
                        type: "pieza",
                        list_id: element.list_id,
                        list_value: element.list_value,
                        id: piece_id

                    },
                    success: function(res) {

                        if (res == "ready") {

                            reset_input()

                            mysql_row_affected()

                        } else {
                            mysql_error(res)
                        }
                    }
                });

            }); // Loop
        }
    }




    // Editar pieza

    $("#editPiece").on("click", (e) => {
        e.preventDefault()

        editPiece();

    })

    function editPiece() {


        $.ajax({
            type: "post",
            url: SITE_URL + "services/pieces.php",
            data: {
                action: "editar_pieza",
                name: $("#piece_name").val(),
                piece_id: $("#piece_id").val(),
                piece_code: $("#input_piece_code").val(),
                price_out: $("#inputPrice_out").val(),
                price_in: $("#inputPrice_in").val(),
                quantity: $("#input_quantity").val(),
                min_quantity: $("#input_min_quantity").val(),
                // Keys
                provider: $("#provider").val(),
                brand: $("#brand").val(),
                offer: $("#offer").val(),
                category: $("#category").val(),
                position: $("#position").val(),
                warehouse: $("#warehouse").val()

            },
            success: function(res) {


                if (res > 0) {
                    mysql_row_update();
                } else if (res == "duplicate") {
                    mysql_error("El código del producto ya está siendo utilizado");
                } else if (res.includes("Error")) {
                    mysql_error(res);
                }
            },

        });

    }

}); // Ready


// Desactivar pieza

function disablePiece(pieceId) {
    alertify.confirm(
        "<i class='text-warning fas fa-exclamation-circle'></i> Desactivar pieza","¿Desea desactivar esta pieza? ",
        function() {
            sendAjaxRequest({
                url: "services/pieces.php",
                data: {
                    piece_id: pieceId,
                    action: "desactivar_pieza",
                },
                successCallback: () =>  dataTablesInstances['pieces'].ajax.reload()
            });
        },
        function() {}
    );
}

// Activar pieza

function enablePiece(pieceId) {
    alertify.confirm("Activar pieza","¿Desea activar esta pieza? ",
        function() {
            sendAjaxRequest({
                url: "services/pieces.php",
                data: {
                    piece_id: pieceId,
                    action: "activar_pieza",
                },
                successCallback: () =>  dataTablesInstances['pieces'].ajax.reload()
            });
           
        },
        function() {}
    );
}



// Eliminar pieza

function deletePiece(pieceId) {

    alertify.confirm("Eliminar pieza", "¿Estas seguro que deseas borrar esta pieza? ",
        function() {

            sendAjaxRequest({
                url: "services/pieces.php",
                data: {
                    action: "eliminarPieza",
                    pieza_id: pieceId,
                },
                successCallback: (res) => {
                    if (res === "ready") {
                        dataTablesInstances['pieces'].ajax.reload()
                    } else {
                        alertify.alert("<div class='error-info'><i class='text-danger fas fa-exclamation-circle'></i>" + " " + res + "</div>").set('basic', true);
                    }
                }
            });
        },
        function() {}
    );
}