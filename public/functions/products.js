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
    var table = $('#variantList').DataTable();
    var info = table.page.info();

    var totalFilas = info.recordsTotal || 0;
    console.log("Total registros del server:", totalFilas);

    if (tipo === "variante" && totalFilas != quantity) {
        alertify.error("Debes completar las variantes de este producto");
        return;
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
        successCallback: (res) => {

            if (res.includes("ready")) {
            // Si la respuesta es exitosa, actualizar parte del HTML y mostrar mensaje
            $('.radio-head').load(window.location.href + ' .radio-head > *');
            
            notifyAlert("Actualización exitosa")

             } else if (res.includes("Duplicate")) {
                 notifyAlert("El código del producto ya está siendo utilizado","error");
            } else {
                console.error(res);
                notifyAlert("Ha ocurrido un error", "error")
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

    const variantCount = $('#variantList tbody tr').length || 1; // Evitar división por 0
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
                successCallback: () => dataTablesInstances['products'].ajax.reload(null, false) // Actualizar datatable
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
                successCallback: () => dataTablesInstances['products'].ajax.reload(null, false), // Actualizar datatable
                errorCallback: (err) => mysql_error(err)
            })

        },
        function () { }
    );
}

// Funcion para agregar variantes a un producto
function addVariantDb() {

    // Recolectar datos del formulario
    const data = {
        flavor: $('#flavor').val(),
        serial: $('#serial').val(),
        cost: $('#cost').val(),
        type: $('input[name="tipovariante"]:checked').val(),
        box: $('#box').val(),
        colour_id: $('#colour').val(),
        colour: $('#select2-colour-container').attr('title'),
        provider_id: $('#provider').val(),
        provider: $('#select2-provider-container').attr('title'),
        product_id: $('#product_id').val()
    };

    const tipo = $('input[name="tipovariante"]:checked').val();

    function validateVariantFields(tipo, data) {
        let isValid = true;

        // Validar serial si es dispositivo
        if (tipo === 'dispositivo') {
            const serial = data.serial.trim();
            const isValidSerial = /^[A-Za-z0-9]+$/.test(serial);

            $("#serial").css("border", isValidSerial ? "1px solid #ced4da" : "1px solid red");
            $(".label-serial").css("color", isValidSerial ? "black" : "red");

            if (!isValidSerial) {
                alertify.set("notifier", "position", "top-right");
                alertify.error("Debes incluir un serial válido para una variante de tipo dispositivo. Solo se permiten letras y números, sin espacios ni símbolos.");
                isValid = false;
            }
        }

        // Validar sabor si es producto
        if (tipo === 'producto') {
            const flavor = data.flavor.trim();
            const isValidFlavor = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/.test(flavor) && flavor !== "";

            $("#flavor").css("border", isValidFlavor ? "1px solid #ced4da" : "1px solid red");
            $(".label-flavor").css("color", isValidFlavor ? "black" : "red");

            if (!isValidFlavor) {
                alertify.set("notifier", "position", "top-right");
                alertify.error("El campo sabor es obligatorio y solo puede contener letras");
                isValid = false;
            }
        }

        // Validar costo (para cualquier tipo)
        const cost = data.cost.toString().trim();
        const isValidCost = /^[0-9]+(\.[0-9]+)?$/.test(cost);

        $("#cost").css("border", isValidCost ? "1px solid #ced4da" : "1px solid red");
        $(".label-cost").css("color", isValidCost ? "black" : "red");

        if (!isValidCost) {
            alertify.set("notifier", "position", "top-right");
            alertify.error("El campo costo es obligatorio y debe ser un número válido (entero o decimal).");
            isValid = false;
        }

        return isValid;
    }

    const isValid = validateVariantFields(tipo, data);
    if (!isValid) return;

    // Validar que no se haya excedido la cantidad de variantes

    const totalQuantity = $("#input_quantity").val();

    // Actualizar el DataTable con la nueva variante (agregarla a la tabla antes de validar)
    var table = $('#variantList').DataTable();
    var info = table.page.info();
    var totalFilas = info.recordsTotal || 0; // Total de registros en DataTable

    console.log("Total registros del server:", totalFilas);
    console.log("Total cantidad:", totalQuantity);

    if (totalFilas >= totalQuantity) return;

    // Enviar la solicitud AJAX para agregar la variante

    sendAjaxRequest({
        url: "services/products.php",
        data: {
            action: "agregar_variantes",
            product_id: data.product_id,
            serial: data.serial,
            flavor: data.flavor,
            type: data.type,
            box: data.box,
            cost: data.cost,
            colour_id: data.colour_id,
            provider_id: data.provider_id
        },
        successCallback: (res) => {

            if (res > 0) {

                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0'); // Mes: 01–12
                const day = String(today.getDate()).padStart(2, '0'); // Día: 01–31

                const formattedDate = `${year}-${month}-${day}`;

                // Agregar la nueva fila a DataTable
                var newRow = `
                <tr>
                    <td>${data.provider}</td>
                    <td>${data.type === 'dispositivo' ? data.serial : data.flavor}</td>
                    <td>${data.type === 'dispositivo' ? data.colour : ''}</td>
                    <td>${format.format(data.cost)}</td>
                    <td>${data.box}</td>
                    <td>${formattedDate}</td>
                    <td>
                        <span class="action-danger btn-action" onclick="deleteVariantDb('${res}')">
                            <i class="fas fa-backspace"></i>
                        </span>
                    </td>
                </tr>
                `;

                $('#variantList tbody').append(newRow);
                $('#variantList').DataTable().draw();


                setTimeout(function () {
                    calculateAverageProductCost(); // Recalcular el costo promedio
                    editProduct(); // Editar producto tras agregar variante
                    toggleVariantFieldsListener(); // Actualizar tipo de variante
                }, 500);

            } else if (res === "duplicate") {
                // Mostrar errores por duplicación
                $("#serial").css("border", "1px solid red");
                $(".label-serial").css("color", "red");
                alertify.error("El serial ya están siendo ocupado");
            } else if (res.includes("Error")) {
                mysql_error(res); // Otros errores del servidor
            }
        },
        errorCallback: (res) => {
            mysql_error("Error de red o del servidor: " + res); // Fallback para errores AJAX
        }
    });

};

let ArrayVariant = []; // Arreglo de las variantes
// ELiminar LocalStorage al recargar
localStorage.removeItem('variantes');

// Funcion para agregar variantes al local storage
function addVariantLocalStorage() {

    function validateSerial(serial) {
        const isValid = /^[A-Za-z0-9]+$/.test(serial.trim());

        $("#serial").css("border", isValid ? "1px solid #ced4da" : "1px solid red");
        $(".label-serial").css("color", isValid ? "black" : "red");

        if (!isValid) {
            alertify.set("notifier", "position", "top-right");
            alertify.error("El campo serial solo permite letras y números, sin espacios ni símbolos");
        }

        return isValid;
    }

    function validateFlavor(flavor) {
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
        const isValid = regex.test(flavor.trim());

        $("#flavor").css("border", isValid ? "1px solid #ced4da" : "1px solid red");
        $(".label-sabor").css("color", isValid ? "black" : "red");

        if (!isValid) {
            alertify.set("notifier", "position", "top-right");
            alertify.error("El campo sabor solo permite letras y espacios. No se permiten números ni caracteres especiales.");
        }

        return isValid;
    }

    function validateCost(cost) {
        const isValid = !isNaN(cost) && cost.trim() !== "";

        $("#cost").css("border", isValid ? "1px solid #ced4da" : "1px solid red");
        $(".label-costo").css("color", isValid ? "black" : "red");

        if (!isValid) {
            alertify.set("notifier", "position", "top-right");
            alertify.error("El campo costo solo permite números");
        }

        return isValid;
    }

    function getFormData() {
        const tipo = $('input[name="tipovariante"]:checked').val();

        const data = {
            flavor: $('#flavor').val() || "",
            serial: $('#serial').val().trim(),
            cost: $('#cost').val().trim(),
            box: $('#box').val().trim(),
            colour_id: $('#colour').val(),
            colour: $('#select2-colour-container').attr('title'),
            provider_id: $('#provider').val(),
            provider: $('#select2-provider-container').attr('title'),
            type: tipo
        };

        return data;
    }

    function handleVariantData() {
        const tipo = $('input[name="tipovariante"]:checked').val();
        const serial = $('#serial').val();
        const flavor = $('#flavor').val();
        const cost = $('#cost').val();

        let isValid = true;
        console.log(tipo)

        if (tipo === "product") {

            isValid = validateFlavor(flavor);
        } else if (tipo === "device") {
            isValid = validateSerial(serial);
        }

        // Si costo tiene algo, validarlo
        if (cost.trim() !== "") {
            isValid = isValid && validateCost(cost);
        }

        if (isValid) {
            $(".label-colour").css("color", "black");
            const data = getFormData();
            return data;
        }

        return null;
    }


    const data = handleVariantData();

    if (data) {

        // Buscar coincidencia si existe la variante en el localStorage
        if (data.serial) {
            return findMatch(ArrayVariant, data);
        } else {

            const max = parseInt($("#product_quantity").val());
            if (ArrayVariant.length < max) {
                ArrayVariant.push(data) // Insertar datos al arreglo
                createVariantDb(ArrayVariant); // Pasa el arreglo actualizado
            }

        }

        function findMatch(arr, data) {
            const hasSerial = arr.some(item => item.serial === data.serial && data.serial !== "");

            if (!hasSerial) {
                const max = parseInt($("#product_quantity").val());
                if (arr.length < max) {
                    arr.push(data); // Insertar datos al arreglo
                    createVariantDb(arr); // Pasa el arreglo actualizado
                } else {
                    alertify.error("Has alcanzado la cantidad máxima permitida.");
                }
            } else {
                $("#serial").css("border", "1px solid red");
                $(".label-serial").css("color", "red");
                alertify.set("notifier", "position", "top-right");
                alertify.error("El serial ya han sido agregados");
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
    $("#serial").val("");

    const data = localStorage.getItem(storageKey);
    if (!data) return;

    const table = JSON.parse(data);
    let totalCost = 0;

    $('.table_variant').show(); // Mostrar tabla si esta oculta

    // Loop de las variantes del producto en localStorage 
    table.forEach((element, index) => {

        // Calcular costo promedio del producto
        totalCost += parseFloat(element.cost) || 0;

        let rowHTML = `
        <tr>
            <td>${element.provider}</td>
            ${element.type === 'dispositivo' ? `
                <td>${element.serial}</td>
                <td>${element.colour}</td>
                <td>${element.cost}</td>
                <td>${element.box}</td>
            ` : `
                <td>${element.flavor}</td>
                <td>${element.cost}</td>
            `}
            <td><span class="action-danger btn-action" onClick="deleteVariantLocalStorage(${index});"><i class="fas fa-backspace"></i></span></td>
        </tr>
        `;

        document.querySelector(outputSelector).innerHTML += rowHTML;

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
            var newPreviousCost = parseInt($('#average_cost').val()) - parseInt(cost);
            $('#average_cost').val(newPreviousCost)
            $('#cost').val('')
            calculateAverageProductCost();

            dataTablesInstances['variantList'].ajax.reload()

            // Actualizar tipo de producto
            $('.radio-head').load(window.location.href + ' .radio-head > *');

        },
        errorCallback: mysql_error
    });
}

// Actualizar el tipo de variante
function toggleVariantFieldsListener() {

    // Reset inicial
    $('.productField').hide();
    localStorage.removeItem('variantes');

    // Función que muestra/oculta según el tipo seleccionado
    function updateVariantView(tipo) {
        localStorage.removeItem('variantes');

        if (tipo === 'dispositivo') {
            $('.productField').fadeOut(200);
            $('.deviceField').fadeIn(500);
        } else {
            $('.deviceField').fadeOut(200);
            $('.productField').fadeIn(500);
        }
    }

    // Ejecutar al cargar
    const initialTipo = $('input[name="tipovariante"]:checked').val();
    updateVariantView(initialTipo);

    // Listener para cambios
    $('input[name="tipovariante"]').on('change', function () {

        const tipo = $(this).val();
        $('#cost').val('0');
        $('#flavor, #serial').val('');
        updateVariantView(tipo);
    });
}

$(document).ready(function () {

    /**============================================================= 
    * FUNCIONES Y ACCIONES EN LAS VENTAS SECCION PRODUCTOS
    ===============================================================*/

    // Funcion que maneja y muestra los inputs en las ventanas
    function handleProductModal() {
        const tipo = $('input[name="tipo"]:checked').val();

        // Limpiar campos comunes
        $('#code, #piece_code, #stock, #discount, #quantity, #service_quantity, #price_out, #totalPriceProduct').val('');

        if (tipo === "producto") {

            $('.product').show();
            $('.piece, .service').hide();
            $('#piece_code').hide();
            $('.product-piece, .discount').show();

            $('#code').show().focus();

            $("#totalPriceProduct").show();
            $("#totalPricePiece, #totalPriceService").hide();

            $('.item-img').load(window.location.href + ' .item-img > *');

            $('#service').attr('required', false);
            $('#product').attr('required', true);
            $('#piece').attr('required', false);

            $('#select2-product-container').html("Buscar productos");
        }
    }

    handleProductModal(); // Inicializador

    $('input[name="tipo"]').on('change', handleProductModal);

    /**============================================================= 
    * FUNCIONES PARA MOSTRAR PRODUCTOS
    ===============================================================*/

    // Inicializar el tipo de variante
    toggleVariantFieldsListener();

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

            const value = $('input[name="tipovariante"]:checked').val();

            // Ocultar tipos variantes
            if (value === "dispositivo") {
                $('#radioProduct').closest('.radio-item').hide();
            } else {
                $('#radioProduct').closest('.radio-item').show();
                $('#radioDevice').closest('.radio-item').hide();
            }

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


        // Mostrar imagen
        const quantity = parseFloat(product.cantidad);
        const formatQuantity = quantity % 1 === 0 ? quantity.toString() : quantity;

        const productImage = product.imagen && product.imagen !== ""
            ? `<img src="${SITE_URL}public/uploads/${product.imagen}" 
                                onerror="this.onerror=null; this.src='${SITE_URL}public/imagen/sistem/no-imagen.png';" 
                                alt="Imagen del producto">
                                <span id="stock">${formatQuantity} inv</span>`
            : `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags-icon lucide-tags">
                                <path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z" />
                                <path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193" />
                                <circle cx="10.5" cy="6.5" r=".5" fill="currentColor" />
                            </svg>
                            <span id="stock">${formatQuantity} inv</span>`;

        $('.item-img').html(productImage)

        const discountRate = parseFloat(product.oferta) || 0;
        const unitPrice = parseFloat(product.precio_unitario) || 0;
        const productCost = parseFloat(product.precio_costo) || 0;

        // Mostrar botón para agregar producto libre
        $("#add_item_free").show();

        // Rellenar campos del formulario con los datos del producto
        $("#select2-variant_id-container").empty();
        $("#product_id").val(product.IDproducto);
        $("#product").val(product.IDproducto);
        //  $("#code").val(product.cod_producto);
        $("#stock").val(product.cantidad);
        $("#quantity").val(1);
        $("#locate").val(product.referencia);
        $("#price_out").val(format.format(unitPrice));
        $("#totalPriceProduct").text(unitPrice.toFixed(2))
        $("#product_cost").val(productCost);
        $("#taxes").val(format.format(product.impuesto));
        $("#quantity").removeAttr("disabled");
        $("#discount").removeAttr("disabled");

        // Aplicar descuento si hay una oferta
        if (discountRate > 0) {
            const discountAmount = (unitPrice * discountRate) / 100;
            $("#discount").val(discountAmount).prop("disabled", true);
        } else {
            $("#discount").prop("disabled", false);
        }

        // Cargar lista de precios si existe un valor
        if (parseFloat(product.valor_lista) > 0) {
            loadProductPrice(product.IDproducto);
        }

        // Manejar variantes del producto
        const hasVariants = parseInt(variantsInfo.variante_total) > 0;
        const isQuotePage = pageURL.includes("invoices/quote");

        if (hasVariants && !isQuotePage && !pageURL.includes('invoices/quote')) {
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
                $("#code").val(data[0].cod_producto)

                applyProductOptions(data); // Aplicar opciones
                validateProductQuantity(); // Calcular precios
            }, verbose: true
        });
    }

    // Buscar producto por barcode
    $("#code").on("keydown", function (e) {

        if (e.key === "Enter") {
            e.preventDefault();

            fetchProductCode($(this).val().trim());

            // Limpiar para siguiente escaneo
            $(this).val("");
        }
    }).on('input', function () {
        const productCode = $(this).val().trim();

        if (productCode.length < 3) return;

        fetchProductCode(productCode);
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
            },verbose: true
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

    /**============================================================= 
    * FUNCIONES, ACCIONES Y VALIDACION DE PRECIO
    ===============================================================*/

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

                    calculateDetailModalTotalProduct($("#price_out").val().replace(/,/g, '')); // recalcular total con nuevo precio
                }
            })

        } else {
            // Volver a cargar el producto
            fetchProduct(productId);
            calculateDetailModalTotalProduct(parseFloat($("#product option:selected").data("price"))); // recalcular total con nuevo precio

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

    /**
    * Calcula y actualiza el total del producto dentro del modal de detalle.
    *
    * La función toma en cuenta:
    * - Cantidad del producto
    * - Precio base del producto
    * - Precio proveniente de lista o precio externo
    * - Descuento por porcentaje o monto manual
    *
    * Actualiza automáticamente los campos:
    * - #discount (si el descuento es porcentual)
    * - #totalPriceProduct
    *
    * @param {number} [price_out=0] - Precio externo opcional (por ejemplo, precio enviado desde una lista)
    * @returns {void}
    */
    function calculateDetailModalTotalProduct(price_out = 0) {
        const tipo = $('input[name="tipo"]:checked').val();

        if (tipo == "producto") {

            var quantity = parseFloat($("#quantity").val()) || 1;
            var discountPercent = parseFloat($("#product option:selected").data("discount")) || 0;
            const listId = parseInt($('#list_id').val()) || 0;
            const priceOutValue = parseFloat(price_out) || 0;
            const priceOutInput = parseFloat($('#price_out').val().replace(/,/g, "")) || 0;
            const productPrice = parseFloat($('#product option:selected').data('price')) || 0;

            const price = (priceOutValue > 0)
                ? (listId > 0 ? priceOutInput : priceOutValue)
                : productPrice;

            var subtotal = quantity * price;

            let discountAmount = discountPercent > 0
                ? subtotal * (discountPercent / 100)
                : parseFloat($("#discount").val()) || 0;

            // Actualizar el campo de descuento solo si es porcentual
            if (discountPercent > 0) {
                $("#discount").val(discountAmount);
            }

            var total = subtotal - discountAmount;

            // Mostrar el total con dos decimales
            $("#totalPriceProduct").text(total.toFixed(2));
        }
    }


    // Cada vez que cambie producto, setea también el descuento automático
    // Producto: solo setea el descuento
    $(document)
        .off("change", "#product")
        .on("change", "#product", function () {
            var discount = $("#product option:selected").data("discount") || 0;
            $("#discount").val(discount);
        });

    // Cálculo centralizado (UNA sola vez)
    $(document)
        .off("input change", "#quantity, #discount, #product")
        .on("input change", "#quantity, #discount, #product", calculateDetailModalTotalProduct);


    /**============================================================= 
    * FUNCIONES CRUD DEL PRODUCTO
    ===============================================================*/

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

                    uploadImage(res) // Subir imagen
                    
                    $('#last_product_edit').show().attr('href', `${SITE_URL}/products/edit&id=${res}`);
                    resetProductForm();
                    mysql_row_affected();

                } else if (res.includes("Duplicate")) {
                    notifyAlert("El código del producto ya está siendo utilizado","error");
                } else if (res.includes("Error")) {
                    console.error(res)
                    notifyAlert('Ha ocurrido un error',"error")
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
                        type: variant.type,
                        flavor: variant.flavor,
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

    // Eliminar producto
    $(document).on('click', '.btn-delete-product', function () {
        const productId = $(this).data('id');
        const productName = $(this).data('name');

        alertify.confirm("Eliminar producto", "¿Estas seguro que deseas eliminar " + productName + " ?",
            function () {

                unsetImagen(productId) // Eliminar imagen

                sendAjaxRequest({
                    url: "services/products.php",
                    data: {
                        action: "eliminarProducto",
                        product_id: productId
                    },
                    successCallback: () => {
                        dataTablesInstances['products'].ajax.reload(null, false); // Actualizar datatable
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
    });

    /**============================================================= 
    * MANEJO DE IMAGEN
    ===============================================================*/

    // Eliminar imagen al cambiar o eliminar el producto 
    function unsetImagen(productId) {
        // Verificar si existe la imagen antes de guardar
        sendAjaxRequest({
            url: "services/products.php",
            data: {
                action: "borrar_imagen",
                product_id: productId
            },
            successCallback: (res) => {
                try {
                    var data = JSON.parse(res);
                    console.log(data)

                } catch (error) {
                    console.error("Error en respuesta del servidor ", error)
                }
            }
        });
    }

    // Cambiar imagen
    $('#product_image').change(function () {
        var productId = $("#product_id").val();

        // Verificamos si el ID del producto es válido (mayor que 0 y no vacío)
        if (productId && productId > 0) {

            unsetImagen(productId) // Elimina la imagen anterior
            uploadImage(productId); // Guardar imagen
        }
    });

    // Subir imagen
    function uploadImage(productId) {
        var formData = new FormData();
        var fileInput = $('#product_image')[0];  // Capturamos el input de la imagen
        var file = fileInput.files[0];  // Tomamos el archivo de la imagen

        // Verificamos si el archivo es válido
        if (file) {
            formData.append('product_image', file);
            formData.append('action', 'subir_imagen');
            formData.append('product_id', productId);

            $.ajax({
                url: SITE_URL + 'services/products.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    try {
                        var data = JSON.parse(response)

                        if (data.success) {
                            notifyAlert(data.success[1])
                            console.log(data)
                        } else {
                            notifyAlert(data.error, 'error')
                        }

                    } catch (error) {
                        notifyAlert('Error de respuesta al subir la imagen', 'error')
                    }
                },
                error: function (e) {
                    notifyAlert(e, 'error')
                }
            });
        }
    }


    // Cargar la imagen subida
    $('#product_image').on('change', function (e) {
        var reader = new FileReader();
        reader.onload = function (event) {
            $('#product-content-img').empty();
            $('#product-content-img').html('<img src="' + event.target.result + '" alt="Vista previa de la imagen" />');
        };
        reader.readAsDataURL(this.files[0]);
    });



}); // Ready