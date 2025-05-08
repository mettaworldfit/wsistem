$(document).ready(function() {

    //  Defaults

    var pageURL = $(location).attr("pathname");
    const format = new Intl.NumberFormat('en'); // Formato 0,000


    // ELiminar LocalStorage al recargar
    localStorage.removeItem('lista_de_precios');

    // Detalle lista de precio

    let ArrayLists = [];

    $('.add_price_list').on('click', (e) => {
        e.preventDefault();

        let data = {

            list_id: $('#price_list').val(),
            name: $('#select2-price_list-container').attr('title'),
            list_value: $('#list_value').val()

        }

        // Buscar coincidencia si existe la lista en el localStorage
        if (data.list_id > 0 && data.list_value > 0) return FindAMatch(ArrayLists);

        function FindAMatch(arr) {

            if (arr.length < 1) {

                arr.push(data); // Insertar datos al arreglo
                createDB(ArrayLists); // crear el localstorage de las listas 

            } else {

                let found = arr.find(element => element.name == data.name)

                if (found == undefined) {

                    arr.push(data);
                    createDB(ArrayLists);
                }

            }
        }

    })

    // Crear la base de datos en el localstorage
    function createDB(Arr) {

        localStorage.setItem('lista_de_precios', JSON.stringify(Arr));
        showDB(); // Mostrar DB

    }


    let arrayLocalStorage;

    function showDB() {

        document.querySelector('#list').innerHTML = ""; // Vaciar detalle
        $('#list_value').val('');

        if (localStorage.getItem("lista_de_precios")) {
            arrayLocalStorage = JSON.parse(localStorage.getItem("lista_de_precios"));
        }

        // Loop de las listas de precios en localStorage 
        arrayLocalStorage.forEach((element, index) => {

            var list_value = format.format(element.list_value);

            document.querySelector('#list').innerHTML += `
          <div class="form-group col-sm-6 list">
              <input class="form-custom col-sm-12" type="text" name="" value="${element.name}" disabled>
          </div>

          <div class="form-group col-sm-6 list">
              <input class="form-custom col-sm-10" type="text" name="" value="${list_value}" list_name="${element.name}" disabled>
              <span class="action-delete"><i class="far fa-minus-square"></i></span>
          </div>
        `;

        });

    }


    // Eliminar lista de precio del localStorage

    if (pageURL.includes("products/add") || pageURL.includes("pieces/add")) {

        var list = document.querySelector('#list')

        if (list != null) {

            list.addEventListener('click', (e) => {
                e.preventDefault();

                if (e.path) {

                    deleteDB(e.path[2].childNodes[1].attributes[4].nodeValue)


                } else if (e.path) {

                    deleteDB(e.path[3].childNodes[1].attributes[4].nodeValue);

                }

                function deleteDB(name) {
                    let indexArray;

                    // Loop de los services en localStorage 
                    arrayLocalStorage.forEach((element, index) => {

                        if (element.name == name) {
                            indexArray = index;

                        }

                    });

                    ArrayLists.splice(indexArray, 1);
                    createDB(ArrayLists)
                }

            })

        }
    }

    // Editar listas de precios de un producto

    $('.add_list_to_product').on('click', (e) => {
        e.preventDefault();

        var product_id = $('#product_id').val()
        add_list_to_db(product_id, "editar_lista_de_precio_de_un_producto")
    });

    // Editar listas de precios de una pieza

    $('.add_list_to_piece').on('click', (e) => {
        e.preventDefault();

        var piece_id = $('#piece_id').val()
        add_list_to_db(piece_id, "editar_lista_de_precio_de_una_pieza")
    });


    function add_list_to_db(id, action) {

        let data = {
            list_value: $('#list_value').val(),
            list_name: $('#select2-price_list-container').attr('title'),
            list_id: $('#price_list').val(),
        }

        localStorage.setItem('lista_de_precios', JSON.stringify(data))


        // El valor de la lista tiene que ser mayor que 0
        if (data.list_value > 0) {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/price_lists.php",
                data: {
                    action: action,
                    id: id,
                    list_id: data.list_id,
                    list_value: data.list_value

                },
                success: function(res) {

                    if (res > 0) {

                        document.querySelector('#list').innerHTML += `
                        
            <div class="form-group col-sm-6 list">
                <input class="form-custom col-sm-12" type="text" name="" value="${data.list_name}" identity="${data.list_id}" disabled>
            </div>

            <div class="form-group col-sm-6 list">
                <input class="form-custom col-sm-10" type="text" name="" value="${format.format(data.list_value)}" identity="${data.list_id}" disabled>
                <span class="action-delete" onclick="deleteList('${res}','${data.list_id}')" identity="${data.list_id}"><i class="far fa-minus-square"></i></span>
            </div>         
          `;

                    } else if (res.includes("Error")) {
                        mysql_error(res)
                    }
                }
            });

        }
    }


}); // Ready



// Agregar lista de precios

function AddList() {

    var list_name = $('#list_name').val();
    var list_comment = $('#list_comment').val();

    $.ajax({
        type: "post",
        url: SITE_URL + "services/price_lists.php",
        data: {
            list_name: list_name,
            list_comment: list_comment,
            action: 'agregar_lista'
        },
        success: function(res) {

            if (res == "ready") {

                $('input[type="text"]').val('');

                mysql_row_affected();

            } else if (res == "duplicate") {

                mysql_error('El nombre de esta lista ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }

        }
    });
}

// Actualizar lista de precios

function UpdateList(list_id) {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/price_lists.php",
        data: {
            list_id: list_id,
            list_name: $('#list_name').val(),
            list_comment: $('#list_comment').val(),
            action: 'actualizar-lista'
        },
        success: function(res) {

            if (res == "ready") {

                mysql_row_update()

            } else if (res == "duplicate") {

                mysql_error('El nombre de esta lista ya está siendo utilizado');

            } else if (res.includes("Error")) {
                mysql_error(res)
            }
        }
    });

}

// Eliminar lista de precios

function deletePriceList(id) {
    alertify.confirm("Eliminar lista de precio", "¿Estas seguro que deseas borrar esta lista? ",
        function() {

            $.ajax({
                type: "post",
                url: SITE_URL + "services/price_lists.php",
                data: {
                    id: id,
                    action: 'eliminar_lista'
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


// Eliminar lista de precio de un producto / pieza

function deleteList(id, list_id) {

    let type;

    if (pageURL.includes("products/edit")) {
        type = "producto";
    } else if (pageURL.includes("pieces/edit")) {
        type = "pieza";

    }

    console.log(type)
    $.ajax({
        type: "post",
        url: SITE_URL + "services/price_lists.php",
        data: {
            action: "desasignar_lista_de_precio",
            id: id,
            type: type,
        },
        success: function(res) {
            if (res == "ready") {
                $("input[identity=" + list_id + "]").hide();
                $("span[identity=" + list_id + "]").hide();
            } else {
                mysql_error(res);
            }
        },
    });
}