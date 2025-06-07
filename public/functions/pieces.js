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

$(document).ready(function () {

    // Inputs por defecto
    $("#piece_quantity").val("1");

    // Rellenar el formulario con los datos de la pieza seleccionada
    function populatePieceFormFields(data) {
        if (!Array.isArray(data)) return;

        const piece = data;

        $("#add_item_free").show();

        $('#piece_id').val(piece.IDpieza);
        $('#stock').val(piece.cantidad);
        $('#locate').val(piece.referencia);
        $('#quantity').val(1).removeAttr('disabled');
        $('#price_out').val(format.format(piece.precio_unitario));
        $('#discount').removeAttr('disabled');

        // Aplicar oferta si existe
        if (piece.oferta > 0) {
            const oferta = piece.precio_unitario * piece.oferta / 100;
            $('#discount').val(oferta).attr('disabled', true);
        } else {
            $('#discount').val('');
        }

        // Cargar lista de precios si tiene
        if (piece.valor_lista > 0) {
            loadPiecePrice(piece.IDpieza);
        }
    }


    // Buscar pieza por nombre    
    $("#piece").change(function () {
        const pieceId = $('#piece_id').val();
        if (pieceId) {
            fetchPiece(pieceId);
        }
    });

    function fetchPiece(piece_id) {

        sendAjaxRequest({
            url: "services/pieces.php",
            data: {
                piece_id: piece_id,
                action: "buscar_pieza"
            },
            successCallback: (res) => {

                var data = JSON.parse(res);
                $('#piece_code').val(data.cod_pieza)

                populatePieceFormFields(data)
                validatePieceQuantity() // Calcular precios

            },
            errorCallback: (res) => mysql_error(res)
        });
    }


    // Buscar piezas por barcode
    $("#piece_code").on("keyup", function () {
        const pieceCode = $(this).val().trim();
        fetchPieceCode(pieceCode || null);
    });

    function fetchPieceCode(piece_code) {

        sendAjaxRequest({
            url: "services/pieces.php",
            data: {
                piece_code: piece_code,
                action: "buscar_codigo_pieza"
            },
            successCallback: (res) => {
                var data = JSON.parse(res);

                $('#select2-piece-container').attr('title', data.nombre_pieza);
                $('#select2-piece-container').empty(); // Vaciar description
                $('#select2-piece-container').append(data.nombre_pieza); // agregar a description

                populatePieceFormFields(data)
                validatePieceQuantity() // Calcular precios
            },
            errorCallback: (res) => mysql_error(res)
        });
    }


    // Cargar las listas de precios de la pieza

    function loadPiecePrice(pieceId) {
        sendAjaxRequest({
            url: "services/price_lists.php",
            data: {
                piece_id: pieceId,
                action: 'buscar_lista_de_pieza'
            },
            successCallback: (res) => {
                let data = JSON.parse(res);

                document.querySelector('#piece_list_id').innerHTML = ""; // Vaciar lista de precios
                document.querySelector('#piece_list_id').innerHTML = '<option value="0" selected>General</option>' + data.options;

            }
        })
    }

    // Cambiar precio del pieza

    $('#piece_list_id').change(function () {
        const pieceId = $('#piece_id').val();
        if ($(this).val() > 0) {

            sendAjaxRequest({
                url: "services/price_lists.php",
                data: {
                    list_id: $(this).val(),
                    piece_id: pieceId,
                    action: 'elegir_precio_pieza'
                },
                successCallback: (res) => {
                    var data = JSON.parse(res);
                    $('#price_out').val(format.format(data[0].valor))
                }
            });

        } else {
            fetchPiece(pieceId); // Precio normal
        }
    });



    // Validar la cantidad del piezas antes de agregar
    $('#quantity').keyup(function (e) {
        e.preventDefault();

        validatePieceQuantity();
    })


    // Validar cantidad y stock de las piezas
    function validatePieceQuantity() {

        const stock = parseFloat($('#stock').val());
        const quantity = parseFloat($('#quantity').val());

        const buttons = $('#rp_add_item, #add_item');
        const isValidQuantity = !isNaN(quantity) && quantity >= 0.1;

        // Mostrar botones solo si la cantidad es válida y menor o igual al stock
        if (quantity <= stock && isValidQuantity) {
            buttons.show();
        } else {
            buttons.hide();
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
            success: function (res) {

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
                    url: SITE_URL + "services/price_lists.php",
                    data: {
                        action: "asignar_lista_de_precios",
                        type: "pieza",
                        list_id: element.list_id,
                        list_value: element.list_value,
                        id: piece_id

                    },
                    success: function (res) {

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
            success: function (res) {


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
        "<i class='text-warning fas fa-exclamation-circle'></i> Desactivar pieza", "¿Desea desactivar esta pieza? ",
        function () {
            sendAjaxRequest({
                url: "services/pieces.php",
                data: {
                    piece_id: pieceId,
                    action: "desactivar_pieza",
                },
                successCallback: () => dataTablesInstances['pieces'].ajax.reload()
            });
        },
        function () { }
    );
}

// Activar pieza

function enablePiece(pieceId) {
    alertify.confirm("Activar pieza", "¿Desea activar esta pieza? ",
        function () {
            sendAjaxRequest({
                url: "services/pieces.php",
                data: {
                    piece_id: pieceId,
                    action: "activar_pieza",
                },
                successCallback: () => dataTablesInstances['pieces'].ajax.reload()
            });

        },
        function () { }
    );
}



// Eliminar pieza

function deletePiece(pieceId) {

    alertify.confirm("Eliminar pieza", "¿Estas seguro que deseas borrar esta pieza? ",
        function () {

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
        function () { }
    );
}