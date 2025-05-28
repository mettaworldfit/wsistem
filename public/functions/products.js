function editProduct() {
    // Obtener valores de los campos del formulario
    const name = $("#product_name").val();
    const quantity = $("#input_quantity").val();
    const priceOut = $("#inputPrice_out").val();
    const tipo = $('input:radio[name=tipoproducto]:checked').val(); // Tipo de producto (variante o no)

    resetFieldStyles(); // Restaurar estilos visuales a estado normal (sin errores)

    // Validar que los campos requeridos estén llenos
    if (!name || !quantity || !priceOut) {
        validateRequiredFields(name, quantity, priceOut); // Aplicar estilo rojo a campos vacíos
        return alertify.error("Debes llenar los campos en rojo"); // Mostrar mensaje de error
    }

    // Validar que la cantidad de variantes coincida con la cantidad ingresada
    if (tipo === "variante" && $('#variant_list tr').length != quantity) {
        return alertify.error("Debes completar las variantes de este producto");
    }

    // Enviar datos al servidor para actualizar el producto
    sendAjaxRequest({
        url: "services/products.php", // Archivo PHP que procesa la acción
        data: {
            name,
            product_id: $("#product_id").val(),
            product_code: $("#product_code").val(),
            price_out: priceOut,
            price_in: $("#inputPrice_in").val(),
            quantity,
            min_quantity: $("#input_min_quantity").val(),
            provider: $("#provider").val(),
            tax: $("#tax").val(),
            brand: $("#brand").val(),
            offer: $("#offer").val(),
            category: $("#category").val(),
            position: $("#position").val(),
            warehouse: $("#warehouse").val(),
            action: "editar_producto", // Acción que se ejecutará en el backend
        },
        successCallback: () => {
            // Si la respuesta es exitosa, actualizar parte del HTML y mostrar mensaje
            $('.radio-list').load(location.href + " .radio-list");
            mysql_row_update(); // Función personalizada que indica éxito
        },
        errorCallback: (res) => {
            // Manejo de errores personalizados según el contenido de la respuesta
            if (res === "duplicate") {
                mysql_error("El código del producto ya está siendo utilizado");
            } else {
                mysql_error(res); // Mostrar cualquier otro error
            }
        }
    });
}

// Restaura estilos de los campos de entrada a su estado original
function resetFieldStyles() {
    $(".form-custom").css("border", "1px solid #ced4da");
    $(".form-check-label").css("color", "black");
}

// Aplica estilo rojo a los campos obligatorios que están vacíos
function validateRequiredFields(name, quantity, priceOut) {
    if (!name) {
        $("#product_name").css("border", "1px solid red");
        $('.label-nomb').css("color", "red");
    }
    if (!quantity) {
        $("#input_quantity").css("border", "1px solid red");
        $('.label-cant').css("color", "red");
    }
    if (!priceOut) {
        $("#inputPrice_out").css("border", "1px solid red");
        $(".label-price").css("color", "red");
    }
}

// Calcular costo promedio del producto
function calculateAverageProductCost() {
    const previousCost = parseInt($('#average_cost').val()) || 0;
    const newCost = parseInt($('#cost').val()) || 0;
    const updatedTotalCost = previousCost + newCost;

    $('#average_cost').val(updatedTotalCost); // Actualizar total de costos

    const variantCount = $('#variant_list tr').length || 1; // Evitar división por 0
    const averageCost = updatedTotalCost / variantCount;

    $('#inputPrice_in').val(averageCost.toFixed(2)); // Establecer costo promedio con 2 decimales
}

// Desactivar producto

function disableProduct(product_id) {
    alertify.confirm(
        "<i class='text-warning fas fa-exclamation-circle'></i> Desactivar producto",
        "¿Desea desactivar este producto? ",
        function () {
            sendAjaxRequest({
                url: "services/products.php",
                data: {
                    product_id: product_id,
                    action: "desactivar_producto",
                },
                successCallback: () => dataTablesInstances['products'].ajax.reload() // Actualizar datatable
            })
        },
        function () { }
    );
}

// Activar producto

function enableProduct(product_id) {

    alertify.confirm("Activar producto", "¿Desea activar este producto? ",
        function () {

            sendAjaxRequest({
                url: "services/products.php",
                data: {
                    product_id: product_id,
                    action: "activar_producto",
                },
                successCallback: () => dataTablesInstances['products'].ajax.reload(), // Actualizar datatable
                errorCallback: (err) => mysql_error(err)
            })

        },
        function () { }
    );
}

// Eliminar producto

function deleteProduct(id) {

    alertify.confirm("Eliminar producto", "¿Estas seguro que deseas borrar este producto? ",
        function () {

            sendAjaxRequest({
                url: "services/products.php",
                data: {
                    action: "eliminarProducto",
                    product_id: id
                },
                successCallback: () => {
                    dataTablesInstances['products'].ajax.reload(); // Actualizar datatable
                },
                errorCallback: (res) => {
                    alertify.alert(
                        "<div class='error-info'><i class='text-danger fas fa-exclamation-circle'></i> " + res + "</div>"
                    )
                        .set('basic', true);
                }
            });
        },
        function () { }
    );
}


// Funcion para agregar variantes a un producto
function addVariantDb() {

    // Recolectar datos del formulario
    const data = {
        imei: $('#imei').val(),
        serial: $('#serial').val(),
        cost: $('#cost').val(),
        box: $('#box').val(),
        colour_id: $('#colour').val(),
        colour: $('#select2-colour-container').attr('title'),
        provider_id: $('#provider').val(),
        provider: $('#select2-provider-container').attr('title'),
        product_id: $('#product_id').val()
    };

    // Validación inicial: debe tener IMEI, Serial o un Color válido
    const hasValidIdentifier = (!isNaN(data.imei) && data.imei) ||
        (!isNaN(data.serial) && data.serial) ||
        data.colour_id > 0;

    if (!hasValidIdentifier) {
        alertify.error("Debes incluir un Serial, Imei o Color para agregar una variante");
        return;
    }

    // Validar que el costo esté presente
    if (!data.cost) {
        $('#cost').css("border", "1px solid red");
        $('.label-cost').css("color", "red");
        return;
    }

    // Validar que no se haya excedido la cantidad de variantes
    const currentVariants = $('#variant_list tr').length;
    const totalQuantity = $("#input_quantity").val();
    if (currentVariants >= totalQuantity) return;

    // Guardar variante en localStorage (opcional según uso posterior)
    localStorage.setItem('variantes', JSON.stringify(data));

    // Enviar la solicitud AJAX para agregar la variante

    sendAjaxRequest({
        url: "services/products.php",
        data: {
            action: "agregar_variantes",
            product_id: data.product_id,
            imei: data.imei,
            serial: data.serial,
            box: data.box,
            cost: data.cost,
            colour_id: data.colour_id,
            provider_id: data.provider_id
        },
        successCallback: (res) => {

            if (res > 0) {
                // Agregar nueva fila a la tabla de variantes
                $('#variant_list').append(`
                        <tr>
                            <td>${data.provider}</td>
                            <td>${data.imei}</td>
                            <td>${data.serial}</td>
                            <td>${data.colour}</td>
                            <td>${format.format(data.cost)}</td>
                            <td>${data.box}</td>
                            <td></td>
                            <td><span class="action-delete" onclick="deleteVariant('${res}','${data.cost}')"><i class="far fa-minus-square"></i></span></td>
                        </tr>
                    `);

                calculateAverageProductCost(); // Recalcular el costo promedio
                editProduct(); // Editar producto tras agregar variante

            } else if (res === "duplicate") {
                // Mostrar errores por duplicación
                $("#imei, #serial").css("border", "1px solid red");
                $(".label-imei, .label-serial").css("color", "red");
                alertify.error("El imei o serial ya están siendo ocupados");
            } else if (res.includes("Error")) {
                mysql_error(res); // Otros errores del servidor
            }
        },
        errorCallback: (err) => {
            mysql_error("Error de red o del servidor: " + err); // Fallback para errores AJAX
        }
    });

};

let ArrayVariant = []; // Arreglo de las variantes
// ELiminar LocalStorage al recargar
localStorage.removeItem('variantes');

// Funcion para agregar variantes al local storage
function addVariantLocalStorage() {

    function validateIMEI(imei) {
        const isValid = !isNaN(imei) && imei.trim() !== "";

        $("#imei").css("border", isValid ? "1px solid #ced4da" : "1px solid red");
        $(".label-imei").css("color", isValid ? "black" : "red");

        if (!isValid) {
            alertify.set("notifier", "position", "top-right");
            alertify.error("Este campo solo permite números");
        }

        return isValid;
    }

    function getFormData() {
        return {
            imei: $('#imei').val().trim(),
            serial: $('#serial').val().trim(),
            cost: $('#cost').val().trim(),
            box: $('#box').val().trim(),
            colour_id: $('#colour').val(),
            colour: $('#select2-colour-container').attr('title'),
            provider_id: $('#provider').val(),
            provider: $('#select2-provider-container').attr('title')
        };
    }

    function handleVariantData() {
        const imei = $('#imei').val();
        if (validateIMEI(imei)) {
            $(".label-colour").css("color", "black");
            const data = getFormData();
            // Aquí puedes usar `data` para enviar o procesar
            return data;
        }
        return null;
    }

    const data = handleVariantData();
    if (data) {
        // Continúa con el flujo si el IMEI es válido

        // Buscar coincidencia si existe la variante en el localStorage
        if (data.colour_id || data.imei || data.serial) {
            return findMatch(ArrayVariant, data);
        }

        function findMatch(arr, data) {
            const hasImei = arr.some(item => item.imei === data.imei && data.imei !== "");
            const hasSerial = arr.some(item => item.serial === data.serial && data.serial !== "");

            if (!hasImei && !hasSerial) {
                const max = parseInt($("#product_quantity").val());
                if (arr.length < max) {
                    arr.push(data); // Insertar datos al arreglo
                    createVariantDb(arr); // Pasa el arreglo actualizado
                } else {
                    alertify.error("Has alcanzado la cantidad máxima permitida.");
                }
            } else {
                $("#imei, #serial").css("border", "1px solid red");
                $(".label-imei, .label-serial").css("color", "red");
                alertify.set("notifier", "position", "top-right");
                alertify.error("El IMEI o serial ya han sido agregados");
            }
        }
    }
}

// Crear la base de datos en el localstorage
function createVariantDb(arr, storageKey = "variantes") {
    if (!Array.isArray(arr)) {
        console.error("El argumento debe ser un arreglo.");
        return;
    }

    try {
        localStorage.setItem(storageKey, JSON.stringify(arr));
        renderVariantDb(); // Mostrar DB
        console.log(`Base de datos creada en localStorage con la clave "${storageKey}"`);
    } catch (error) {
        console.error("Error al guardar en localStorage:", error);
    }
}

function renderVariantDb(storageKey = "variantes", outputSelector = "#variant_list") {

    const quantity = parseInt($("#product_quantity").val()) || 1;

    // Limpiar la tabla y los inputs
    document.querySelector(outputSelector).innerHTML = ""; // Vaciar variantes
    $("#imei, #serial, #cost").val("");

    const data = localStorage.getItem(storageKey);
    if (!data) return;

    const table = JSON.parse(data);
    let totalCost = 0;

    // Loop de las variantes del producto en localStorage 
    table.forEach((element, index) => {

        // Calcular costo promedio del producto
        totalCost += parseFloat(element.cost) || 0;

        $('.table_variant').show(); // Mostrar tabla si esta oculta

        document.querySelector(outputSelector).innerHTML += `
            <tr>
                <td>${element.provider}</td>
                <td>${element.imei}</td>
                <td>${element.serial}</td>
                <td>${element.colour}</td>
                <td>${element.cost}</td>
                <td>${element.box}</td>
                <td> <span class="action-delete" onClick="deleteVariantLocalStorage(${index});"><i class="far fa-minus-square"></i></span></td>
            </tr>
        `;

    });

    // Actualizar costo promedio
    const avg = (totalCost / quantity).toFixed(2);
    $("#inputPrice_in").val(avg);

}

// Eliminar variante del localstorage
function deleteVariantLocalStorage(index) {
    ArrayVariant.splice(index, 1);
    createVariantDb(ArrayVariant)
}

// Eliminar variante de un producto
function deleteVariantDb(variant_id, cost) {
    sendAjaxRequest({
        url: "services/products.php",
        data: {
            action: "eliminar_variante",
            id: variant_id
        },
        successCallback: () => {

            // Recargar la tabla de variantes y recalcular el costo promedio
            $('#Detalle').load(location.href + " #Detalle", () => {

                var newPreviousCost = parseInt($('#average_cost').val()) - parseInt(cost);
                $('#average_cost').val(newPreviousCost)
                $('#cost').val('')
                calculateAverageProductCost();
            });

            $('.radio-list').load(location.href + " .radio-list");
        },
        errorCallback: mysql_error
    });
}

$(document).ready(function () {

    const format = new Intl.NumberFormat('en'); // Formato 0,000

    // Función para limpiar los campos
    function resetProductForm() {
        // Limpiar localStorage
        ["variantes", "lista_de_precios"].forEach(key => localStorage.removeItem(key));

        // Limpiar campos de texto, contraseña y número
        $('input[type="text"], input[type="password"], input[type="number"]').val("");

        // Establecer valores por defecto
        $("#totalPrice").val("0.00");
        $("#product_quantity, #min_quantity").val("1");

        // Ocultar la tabla de variantes
        $("#variant_list").hide();
    }

    // Funcion para mostrar interfaz de variantes y normal

    function toggleVariantUI(isVariant) {
        if (isVariant) {
            $(".variant").fadeIn(400);
            $(".active").fadeOut(200);
            $('#product_history').show();
        } else {
            $(".variant").fadeOut(400);
            $(".active").fadeIn(200);
            $('#product_history').hide();
            localStorage.removeItem('variantes');
            if (pageURL.includes("products/add")) {
                $('#variant_list').empty(); // Vaciar variante
            }
            $(".label-colour").css("color", "black");
        }
        $('.table_variant').hide();
        $('#last_product_edit').hide(); // Botón de editar último producto agregado
        $("#totalPrice").val("0.00");
    }

    // Inicialización
    const initialType = $('input:radio[name=tipoproducto]:checked').val();
    toggleVariantUI(initialType !== "novariante");

    // Evento de cambio
    $('input:radio[name=tipoproducto]').change(function () {
        toggleVariantUI($(this).val() === "variante");
    });

    // Inicializar valores por defecto
    $("#product_quantity, #min_quantity").val("1");

    // funcion para aplicar opciones del detalle
    function applyProductOptions(data) {
        if (!Array.isArray(data) || data.length < 2) return;
        
        const product = data[0];
        const variantsInfo = data[1];

        const discountRate = parseFloat(product.oferta) || 0;
        const unitPrice = parseFloat(product.precio_unitario) || 0;

        // Mostrar botón para agregar producto libre
        $("#add_item_free").show();

        // Rellenar campos del formulario con los datos del producto
        $("#select2-variant_id-container").empty();
        $("#product_id").val(product.IDproducto);
        $("#code").val(product.cod_producto);
        $("#stock").val(product.cantidad);
        $("#quantity").val(1);
        $("#locate").val(product.referencia);
        $("#price_out").val(format.format(unitPrice));
        $("#taxes").val(format.format(product.impuesto));
        $("#quantity").removeAttr("disabled");
        $("#discount").removeAttr("disabled");

        // Aplicar descuento si hay una oferta
        if (discountRate > 0) {
            const discountAmount = (unitPrice * discountRate) / 100;
            $("#discount").val(discountAmount).prop("disabled", true);
        } else {
            $("#discount").val("").prop("disabled", false);
        }
  
        // Cargar lista de precios si existe un valor
        if (parseFloat(product.valor_lista) > 0) {
            loadProductPrice(product.IDproducto);
        }

        // Manejar variantes del producto
        const hasVariants = parseInt(variantsInfo.variante_total) > 0;
        const isQuotePage = pageURL.includes("invoices/quote");

        if (hasVariants && !isQuotePage) {
            $("#total_variant").val(variantsInfo.variante_total);
            loadProductVariants(product.IDproducto);
        } else {
            $("#variant_id").prop("disabled", true);
        }
    }

    // Evento que muestra el precio del produucto final
    $("#inputPrice_out").on("keyup", function () {
        $("#totalPrice").val(format.format($(this).val()));
    });

    // Buscar producto por nombre

    $("#product").change(function () {
        const productId = $(this).val();
        if (productId) {
            fetchProduct(productId);
        }
    });

    function fetchProduct(product_id) {

        sendAjaxRequest({
            url: "services/products.php",
            data: {
                product_id: product_id,
                action: "buscar_producto"
            },
            successCallback: (res) => {

                var data = JSON.parse(res);

                applyProductOptions(data); // Aplicar opciones
                validateProductQuantity(); // Calcular precios
            }
        });
    }

    // Buscar producto por barcode

    $("#code").on("keyup", function () {
        const productCode = $(this).val().trim();
        fetchProductCode(productCode || null);
    });

    function fetchProductCode(product_code) {

        sendAjaxRequest({
            url: "services/products.php",
            data: {
                product_code: product_code,
                action: "buscar_codigo_producto"
            },
            successCallback: (res) => {

                var data = JSON.parse(res);

                applyProductOptions(data); // Aplicar opciones
                $('#select2-product-container').empty()
                $('#select2-product-container').append(data[0].nombre_producto)

                validateProductQuantity(); // Calcular precios   
            }
        })
    }

    // Muestra todas las variantes de los productos

    function loadProductVariants(productId) {
        sendAjaxRequest({
            url: "services/products.php",
            data: {
                product_id: productId,
                action: "buscar_variantes",
            },
            successCallback: (res) => {
                
                $("#variant_id").attr("disabled", false)
                document.querySelector("#variant_id").innerHTML = ""; // Vaciar lista de variantes
                document.querySelector("#variant_id").innerHTML =
                    '<option value="0" disabled>Seleccionar variante del producto</option>' + res;

            }
        });
    }

    // Cargar las listas de precios del producto

    function loadProductPrice(productId) {
        sendAjaxRequest({
            url: "services/price_lists.php",
            data: {
                product_id: productId,
                action: "buscar_lista_de_producto",
            },
            successCallback: (res) => {
                let data = JSON.parse(res);

                document.querySelector("#list_id").innerHTML = ""; // Vaciar lista de precios
                document.querySelector("#list_id").innerHTML =
                    '<option value="0" selected>General</option>' + data.options;
            },
            errorCallback: (res) => mysql_error(res)
        })
    }

    // Cambiar precio del producto

    $("#list_id").change(function () {
        const productId = $("#product_id").val()

        if ($(this).val() > 0) {

            sendAjaxRequest({
                url: "services/price_lists.php",
                data: {
                    list_id: $(this).val(),
                    product_id: productId,
                    action: "elegir_precio",
                },
                successCallback: (res) => {
                    var data = JSON.parse(res);
                    $("#price_out").val(format.format(data[0].valor));
                }
            })

        } else {
            // Volver a cargar el producto
            fetchProduct(productId); 
        }
    });


    // Validar la cantidad del producto antes de agregar

    $("#quantity").keyup(function (e) {
        e.preventDefault();

        validateProductQuantity();
    });

    // Validar cantidad y stock 
    function validateProductQuantity() {
        const stock = parseFloat($("#stock").val()) || 0;
        const quantity = parseFloat($("#quantity").val()) || 0;

        const isValidQuantity = quantity >= 0.1 && quantity <= stock;

        $("#add_item").toggle(isValidQuantity);
        $("#add_item_free").toggle(isValidQuantity);
    }


    // Aplicar descuento

    $("#discount").keyup(function (e) {
        e.preventDefault();

        var price = parseInt(
            $("#quantity").val() * $("#price_out").val().replace(/,/g, "")
        );
        var discount = $("#discount").val();

        // Validar que el descuento no sea mayor que el precio del piezas
        if (discount <= price) {
            $("#rp_add_item").show(); // Botón de ventana detalle de ordenes de reparaciones
            $("#add_item").show(); // Botón de ventana facturas de ventas
        } else {
            $("#rp_add_item").hide(); // Botón de ventana detalle de ordenes de reparaciones
            $("#add_item").hide(); // Botón de ventana facturas de ventas
        }
    });

    // Crear producto

    $("#createProduct").on("click", (e) => {
        e.preventDefault();

        const name = $("#product_name").val();
        const quantity = $("#product_quantity").val();
        const priceOut = $("#inputPrice_out").val();
        const productType = $('input:radio[name=tipoproducto]:checked').val();

        if (name && quantity && priceOut) {
            resetFieldStyles();

            if (productType === "variante") {
                const maxVariants = quantity;
                const currentVariants = Object.keys(ArrayVariant).length;

                if (currentVariants == maxVariants) {
                    createProduct();
                } else {
                    highlightVariantError();
                }
            } else {
                createProduct();
            }

        } else {
            showMissingFieldsError(name, quantity, priceOut);
        }
    });

    function createProduct() {

        sendAjaxRequest({
            url: "services/products.php",
            data: {
                name: $("#product_name").val(),
                product_code: $("#product_code").val(),
                price_out: $("#inputPrice_out").val(),
                price_in: $("#inputPrice_in").val(),
                quantity: $("#product_quantity").val(),
                min_quantity: $("#min_quantity").val(),
                provider: $("#providerID").val(),
                tax: $("#tax").val(),
                brand: $("#brand").val(),
                offer: $("#offer").val(),
                category: $("#category").val(),
                position: $("#position").val(),
                warehouse: $("#warehouse").val(),
                action: "agregar_producto"
            },
            successCallback: (res) => {
                console.log(res)
                if (res > 0) {
                    if (localStorage.getItem("lista_de_precios")) assignProductPrice(res);
                    if (localStorage.getItem("variantes")) assignProductVariant(res);

                    $('#last_product_edit').show().attr('href', `${SITE_URL}/products/edit&id=${res}`);
                    resetProductForm();
                    mysql_row_affected();

                } else if (res === "duplicate") {
                    mysql_error("El código del producto ya está siendo utilizado");
                } else if (res.includes("Error")) {
                    mysql_error(res);
                }
            }
        })

    }

    function resetFieldStyles() {
        $(".form-custom").css("border", "1px solid #ced4da");
        $(".form-check-label").css("color", "black");
    }

    function highlightVariantError() {
        alertify.set("notifier", "position", "top-right");
        alertify.error("Debes completar las variantes del producto");
        $("#imei, #colour").css("border", "1px solid red");
        $(".label-imei, .label-colour").css("color", "red");
    }

    function showMissingFieldsError(name, quantity, priceOut) {
        resetFieldStyles();
        alertify.set("notifier", "position", "top-right");
        alertify.error("Debes llenar los campos en rojo");

        if (!priceOut) $("#inputPrice_out").css("border", "1px solid red").siblings(".label-price").css("color", "red");
        if (!quantity) $("#product_quantity").css("border", "1px solid red").siblings(".label-cant").css("color", "red");
        if (!name) $("#product_name").css("border", "1px solid red").siblings(".label-nomb").css("color", "red");
    }

    // Asignar precios a productos

    function assignProductPrice(productId) {
        if (localStorage.getItem("lista_de_precios")) {
            const priceListArray = JSON.parse(localStorage.getItem("lista_de_precios"));

            priceListArray.forEach(({ list_id, list_value }) => {
                sendAjaxRequest({
                    url: "services/price_lists.php",
                    data: {
                        action: "asignar_lista_de_precios",
                        type: "producto",
                        list_id,
                        list_value,
                        id: productId
                    },
                    successCallback: (res) => {

                        console.log(`Lista de precio ${list_id} asignada con éxito.`);
                    }
                });
            });
        }

    }

    // Asignar variante a productos

    function assignProductVariant(productId) {
        if (localStorage.getItem("variantes")) {
            const variantArrayList = JSON.parse(localStorage.getItem("variantes"));

            variantArrayList.forEach(variant => {

                sendAjaxRequest({
                    url: "services/products.php",
                    data: {
                        action: "agregar_variantes",
                        colour_id: variant.colour_id,
                        provider_id: variant.provider_id,
                        imei: variant.imei,
                        serial: variant.serial,
                        box: variant.box,
                        cost: variant.cost,
                        product_id: productId,
                    },
                    successCallback: (res) => {
                        console.log(res)

                        if (res == "ready") {

                            resetProductForm();
                            mysql_row_affected();

                        } else if (res == "duplicate") {

                            alertify.set("notifier", "position", "top-right");
                            alertify.error("Imei o Serial ya están siendo utilizado");

                        } else if (res.includes("Error")) {

                            mysql_error(res);
                        }
                    }
                });
            }) // Loop
        }
    }

}); // Ready
