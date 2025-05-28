// Crear lista de precios

function addList() {
    var list_name = $('#list_name').val();
    var list_comment = $('#list_comment').val();

    sendAjaxRequest({
        url: "services/price_lists.php",
        data: {
            list_name: list_name,
            list_comment: list_comment,
            action: 'agregar_lista'
        },
        successCallback: (res) => {
            $('input[type="text"]').val('');
            mysql_row_affected();
        }, 
        errorCallback: (res) => mysql_error(res)
    })
}

// Actualizar lista de precios

function updateList(listId) {
    sendAjaxRequest({
        url: "services/price_lists.php",
        data: {
            list_id: listId,
            list_name: $('#list_name').val(),
            list_comment: $('#list_comment').val(),
            action: 'actualizar-lista'
        },
        successCallback: (res) => {
            mysql_row_update()
        },
        errorCallback: (res) => mysql_error(res)
    });
}

// Eliminar lista de precios

function deletePriceList(listId) {
    alertify.confirm("Eliminar lista de precio", "¿Estas seguro que deseas borrar esta lista? ",
        function () {
            sendAjaxRequest({
                url: "services/price_lists.php",
                data: {
                    id: listId,
                    action: 'eliminar_lista'
                },
                successCallback: () => dataTablesInstances['pricelists'].ajax.reload(),
                errorCallback: (res) => mysql_error(res)
            })
        },
        function () {

        });
}

// Editar listas de precios de un producto

function addPriceListsDb(listId, outputSelector = "#priceList", storageKey = "lista_de_precios") {

    function getAction(url) {
        if (url.includes("products/edit")) return "editar_lista_de_precio_de_un_producto";
        if (url.includes("pieces/edit")) return "editar_lista_de_precio_de_una_pieza";
        return null;
    }

    const action = getAction(pageURL);

    const data = {
        list_value: $('#list_value').val(),
        list_name: $('#select2-price_list-container').attr('title'),
        list_id: $('#price_list').val(),
    }

    // Guardar lista en localStorage
    localStorage.setItem(storageKey, JSON.stringify(data));

    // Solo ejecutar si el valor de la lista es mayor que 0
    if (data.list_value <= 0) return;

    sendAjaxRequest({
        url: "services/price_lists.php",
        data: {
            action: action,
            id: listId,
            list_id: data.list_id,
            list_value: data.list_value
        },
        successCallback: (res) => {
            document.querySelector(outputSelector).innerHTML += `
                 
        <div class="form-group col-sm-6 list">
            <input class="form-custom col-sm-12" type="text" name="" value="${data.list_name}" identity="${data.list_id}" disabled>
        </div>

        <div class="form-group col-sm-6 list">
            <input class="form-custom col-sm-10" type="text" name="" value="${format.format(data.list_value)}" identity="${data.list_id}" disabled>
            <span class="action-delete" onclick="deleteItemPriceList('${res}')" ><i class="far fa-minus-square"></i></span>
        </div>`;
        },
        errorCallback: (res) => mysql_error(res)
    });
}

// Detalle lista de precio

let ArrayLists = [];

function addPriceListLocalStorage() {
    let data = {
        list_id: $('#price_list').val(),
        name: $('#select2-price_list-container').attr('title'),
        list_value: $('#list_value').val()
    }

    // Buscar coincidencia si existe la variante en el localStorage
    if (data.list_id > 0 && data.list_value > 0) {
        return findMatch(ArrayLists, data);
    }

    function findMatch(arr, data) {
        // Si el arreglo está vacío o no contiene un elemento con el mismo nombre, se agrega
        if (arr.length === 0 || !arr.some(element => element.name === data.name)) {
            arr.push(data);           // Agregar el nuevo objeto
            createPriceListsDb(arr);     // Actualizar el almacenamiento local
        }
    }

}


// Crear la base de datos en el localstorage
function createPriceListsDb(arr, storageKey = "lista_de_precios") {

    if (!Array.isArray(arr)) {
        console.error("El argumento debe ser un arreglo.");
        return;
    }

    try {
        localStorage.setItem(storageKey, JSON.stringify(arr));
        renderPriceListsDb(); // Mostrar DB
        console.log(`Base de datos creada en localStorage con la clave "${storageKey}"`);
    } catch (error) {
        console.error("Error al guardar en localStorage:", error);
    }
}


let arrayLocalStorage;

function renderPriceListsDb(storageKey = "lista_de_precios", outputSelector = "#priceList") {

    // Limpiar la tabla y los inputs
    document.querySelector(outputSelector).innerHTML = ""; // Vaciar detalle
    $('#list_value').val('');

    const data = localStorage.getItem(storageKey);
    if (!data) return;

    const table = JSON.parse(data);

    // Loop de las listas de precios en localStorage 
    table.forEach((element, index) => {

        var listValue = format.format(element.list_value);

        document.querySelector(outputSelector).innerHTML += `
        <div class="form-group col-sm-6 list">
            <input class="form-custom col-sm-12" type="text" name="" value="${element.name}" disabled>
        </div>

        <div class="form-group col-sm-6 list">
            <input class="form-custom col-sm-10" type="text" name="" value="${listValue}" list_name="${element.name}" disabled>
            <span class="action-delete" onclick="deletePriceListsLocalStorage(${index});"><i class="far fa-minus-square"></i></span>
        </div>
      `;

    });
}

// Eliminar lista de precio del localStorage

function deletePriceListsLocalStorage(index) {
    ArrayLists.splice(index, 1);
    createPriceListsDb(ArrayLists)
}

// Eliminar lista de precio de un producto / pieza

function deleteItemPriceList(listId) {

    function getEditType(url) {
        if (url.includes("products/edit")) return "producto";
        if (url.includes("pieces/edit")) return "pieza";
        return null;
    }
    
    const type = getEditType(pageURL);
    
    sendAjaxRequest({
        url: "services/price_lists.php",
        data: {
            action: "desasignar_lista_de_precio",
            id: listId,
            type: type,
        },
        successCallback: (res) => {
         
            $('#priceList').load(location.href + " #priceList")
        },
        errorCallback: (res) => mysql_error(res)
    });
}

$(document).ready(function () {

    // ELiminar LocalStorage al recargar
    localStorage.removeItem('lista_de_precios');

}); // Ready