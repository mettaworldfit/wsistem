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

$(document).ready(function () {

    /**============================================================= 
    * FUNCIONES Y ACCIONES EN LAS VENTAS SECCION PIEZAS
    ===============================================================*/

    // Funcion que maneja y muestra los inputs en las ventanas
    function handlePieceModal() {
        const tipo = $('input[name="tipo"]:checked').val();

        // Limpiar campos comunes
        $('#code, #piece_code, #stock, #discount, #quantity, #service_quantity, #price_out, #totalPricePiece').val('');

        if (tipo === "pieza") {

            $('.piece').show();
            $('.product, .service').hide();
            $('.product-piece, .discount').show();

            $('#piece_code').show().focus();
            $('#code').hide();

            // Modal total
            $("#totalPricePiece").show();
            $("#totalPriceProduct, #totalPriceService").hide();

            // Volver a cargar imagen
            $('.item-img').load(window.location.href + ' .item-img > *');

            // Requerimientos
            $('#service, #product').attr('required', false);
            $('#piece').attr('required', true);

            // Placeholder de Select2
            $('#select2-piece-container').html("Buscar piezas");
        }
    }

    handlePieceModal(); // Inicializador

    $('input[name="tipo"]').on('change', handlePieceModal);


    // Inputs por defecto
    $("#piece_quantity").val("1");

    // Rellenar el formulario con los datos de la pieza seleccionada
    function populatePieceFormFields(data) {

        if (!Array.isArray(data)) return;

        const piece = data[0];
        const unitPrice = parseFloat(piece.precio_unitario) || 0;


        // Mostrar imagen
        const quantity = parseFloat(piece.cantidad);
        const formatQuantity = quantity % 1 === 0 ? quantity.toString() : quantity;

        const pieceImage = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags-icon lucide-tags">
                                <path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z" />
                                <path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193" />
                                <circle cx="10.5" cy="6.5" r=".5" fill="currentColor" />
                            </svg>
                            <span id="stock">${formatQuantity} inv</span>`;

        $('.item-img').html(pieceImage)

        $("#add_item_free").show();

        $('#piece_id').val(piece.IDpieza);
        $('#stock').val(piece.cantidad);
        $('#locate').val(piece.referencia);
        $('#quantity').val(1).removeAttr('disabled');
        $('#price_out').val(format.format(unitPrice));
        $("#totalPricePiece").text(unitPrice.toFixed(2));
        $("#piece_cost").val(piece.precio_costo);
        $('#discount').removeAttr('disabled');

        // Aplicar oferta si existe
        if (piece.oferta > 0) {
            const oferta = unitPrice * piece.oferta / 100;
            $('#discount').val(oferta).attr('disabled', true);
        }

        // Cargar lista de precios si tiene
        if (piece.valor_lista > 0) {
            loadPiecePrice(piece.IDpieza);
        }
    }


    // Buscar pieza por nombre    
    $("#piece").change(function () {
        const pieceId = $(this).val()
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
                $('#piece_code').val(data[0].cod_pieza)

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

                    calculateDetailModalTotalPiece($("#price_out").val().replace(/,/g, '')); // recalcular total con nuevo precio

                }
            });

        } else {
            fetchPiece(pieceId); // Precio normal
            calculateDetailModalTotalPiece(parseFloat($("#piece option:selected").data("price"))); // recalcular total con nuevo precio

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




    /**
 * Calcula y muestra el total de una pieza en el modal de detalle.
 *
 * La función:
 * - Verifica que el tipo seleccionado sea "pieza"
 * - Obtiene la cantidad, precio base y descuentos
 * - Determina el precio final según lista de precios o precio directo
 * - Calcula subtotal, descuento y total
 * - Actualiza el total en el DOM
 *
 * @param {number} [price_out=0] - Precio externo opcional (por ejemplo, desde una lista de precios).
 *                                 Si es mayor a 0, tiene prioridad sobre el precio del producto.
 *
 * @returns {void} No retorna ningún valor, solo actualiza el HTML.
 */
    function calculateDetailModalTotalPiece(price_out = 0) {
        const tipo = $('input[name="tipo"]:checked').val();

        if (tipo == "pieza") {
            var quantity = parseFloat($("#quantity").val()) || 1; // por defecto 1
            var discountPercent = parseFloat($("#piece option:selected").data("discount")) || 0;

            // Obtener valores de manera consistente
            const listId = parseInt($('#piece_list_id').val()) || 0;
            const priceOutValue = parseFloat(price_out) || $('#piece_list_id').val();
            const priceOutInput = parseFloat($('#price_out').val().replace(/,/g, "")) || 0;
            const piecePrice = parseFloat($('#piece option:selected').data('price')) || 0;

            // Determinar el precio final
            const price = (priceOutValue > 0)
                ? (listId > 0 ? priceOutInput : priceOutValue)
                : piecePrice;

            var subtotal = quantity * price;

            let discountAmount = discountPercent > 0
                ? subtotal * (discountPercent / 100) // Calcular descuento en base al porcentaje
                : parseFloat($("#discount").val()) || 0; // Introducirlo manualmente

            // Mostrar el nuevo descuento solo si es mayor a 0
            if (discountPercent > 0) {
                $("#discount").val(discountAmount);
            }

            // Calcular el total
            var total = subtotal - discountAmount;

            $("#totalPricePiece").text(total.toFixed(2));
        }
    }

    // Cada vez que cambie la pieza, setea también el descuento automático

    $(document)
        .off("change", "#piece")
        .on("change", "#piece", function () {
            var discount = $("#piece option:selected").data("discount") || 0;
            $("#discount").val(discount);
        });

    // Cálculo centralizado
    $(document)
        .off("input change", "#quantity, #discount, #piece")
        .on("input change", "#quantity, #discount, #piece", calculateDetailModalTotalPiece);



}); // Ready


