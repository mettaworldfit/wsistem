$(document).ready(function () {

  const format = new Intl.NumberFormat('en'); // Formato 0,000
  var pageURL = $(location).attr("pathname");

  function mysql_row_affected() {
    alertify
      .alert(
        `<div class='row-affected'>
      <i class='icon-success far fa-check-circle'></i>
      <p>Registrado exitosamente</p>
      </div>`
      )
      .set("basic", true);
  }

  function mysql_row_update() {
    alertify
      .alert(
        `<div class='row-affected'>
      <i class='icon-success far fa-check-circle'></i>
      <p>Registro actualizado correctamente</p>
      </div>`
      )
      .set("basic", true);
  }

  function mysql_error(err) {
    alertify
      .alert(
        `<div class='error-info'>
      <i class='icon-error fas fa-exclamation-circle'></i> 
      <p>${err}</p>
      </div>`
      )
      .set("basic", true);
  }

  // Función para limpiar los campos

  function reset_input() {
    localStorage.removeItem("variantes");
    localStorage.removeItem("lista_de_precios");
    $('input[type="text"]').val("");
    $('input[type="password"]').val("");
    $('input[type="number"]').val("");

    $("#precioTotal").val("0.00");
    $("#product_quantity").val("1");
    $("#min_quantity").val("1");

    $("#variant_list").hide();
  }

  // Default Inputs

  if ($('input:radio[name=tipoproducto]:checked').val() == "novariante") {
    $(".variant").hide();
    $('.table_variant').hide();
    $('#product_history').hide()
  } else {
    $(".variant").show();
    $('#product_history').show()
    $('.table_variant').hide();
  }

  $('.table_variant').hide();
  $('#last_product_edit').hide() // Botón de editar último producto agregado


  $('input:radio[name=tipoproducto]').change(function () {

    if ($(this).val() == "variante") {

      $(".variant").fadeIn(400);
      $(".active").fadeOut(200);

    } else if ($(this).val() == "novariante") {
      $(".variant").fadeOut(400);
      $(".active").fadeIn(200);
      localStorage.removeItem('variantes');
      if (pageURL.includes("products/add")) {
        document.querySelector('#variant_list').innerHTML = ""; // Vaciar variante
      }
      $(".label-colour").css("color", "black")
    }

  })


  $("#product_quantity").val("1");
  $("#min_quantity").val("1");

  // Buscar producto por nombre

  $("#product").change(function () {
    var product_id = $(this).val();
    SearchProduct(product_id);
  });
 

  function SearchProduct(product_id) {
    $.ajax({
      url: SITE_URL + "services/products.php",
      method: "post",
      data: {
        product_id: product_id,
        action: "buscar_producto"
      },
      success: function (res) {
        var data = JSON.parse(res);

        $("#add_item_free").show()

        $("#select2-variant_id-container").empty();
        $("#product_id").val(data[0].IDproducto);
        $("#code").val(data[0].cod_producto);
        $("#stock").val(data[0].cantidad);
        $("#quantity").val(1);
        $("#locate").val(data[0].referencia);
        $("#price_out").val(format.format(data[0].precio_unitario));
        $("#taxes").val(format.format(data[0].impuesto));
        $("#quantity").removeAttr("disabled");
        $("#discount").removeAttr("disabled");


        // Incluir oferta
        if (data[0].oferta > 0) {
          var oferta = (data[0].precio_unitario * data[0].oferta) / 100;
          $("#discount").val(oferta);

          $("#discount").attr("disabled", true);
        } else {
          $("#discount").val("");
        }


        // Cargar lista de precios del producto
        if (data[0].valor_lista > 0) {
          product_price(data[0].IDproducto);
        }

        if (data[1].variante_total > 0) {
          $('#total_variant').val(data[1].variante_total) // total de variantes
          product_variants(data[0].IDproducto) // Buscar variantes del producto

         

        } else {
          $("#variant_id").attr("disabled", true)
                
         
        }

        clcTotalPrice(); // Calcular precios
      },
    });
  }

  // Buscar producto por barcode

  $("#code").keyup(function () {
    var product_code = $(this).val();

    if (product_code != "") {
      SearchProductCode(product_code);
    } else {
      SearchProductCode();
    }
  });

  
  function SearchProductCode(product_code) {

    $.ajax({
      url: SITE_URL + "services/products.php",
      method: "post",
      data: {
        product_code: product_code,
        action: "buscar_codigo_producto"
      },
      success: function (res) {

        var data = JSON.parse(res);

        $("#add_item_free").show()

        $("#select2-variant_id-container").empty();
        $("#product_id").val(data[0].IDproducto);
        $("#stock").val(data[0].cantidad);
        $("#quantity").val(1);
        $("#locate").val(data[0].referencia);
        $("#price_out").val(format.format(data[0].precio_unitario));
        $("#taxes").val(format.format(data[0].impuesto));
        $("#quantity").removeAttr("disabled");
        $("#discount").removeAttr("disabled");

        $('#select2-product-container').empty()
        $('#select2-product-container').append(data[0].nombre_producto)

        // Incluir oferta
        if (data[0].oferta > 0) {
          var oferta = (data[0].precio_unitario * data[0].oferta) / 100;
          $("#discount").val(oferta);

          $("#discount").attr("disabled", true);
        } else {
          $("#discount").val("");
        }


        // Cargar lista de precios del producto
        if (data[0].valor_lista > 0) {
          product_price(data[0].IDproducto);
        }

      
        if (data[1].variante_total > 0) {

          $('#total_variant').val(data[1].variante_total) // total de variantes
          product_variants(data[0].IDproducto) // Buscar variantes del producto
 
        } else {
          $("#variant_id").attr("disabled", true)
        
          
        }

        clcTotalPrice(); // Calcular precios
      },
    });
  }

  /**
   * TODO: Buscar variantes del producto
   * ! Muestra todas las variantes de los productos
   */
 
  function product_variants(product_id) {
    $.ajax({
      url: SITE_URL + "services/products.php",
      method: "post",
      data: {
        product_id: product_id,
        action: "buscar_variantes",
      },
      success: function (res) {

        $("#variant_id").attr("disabled", false)
        document.querySelector("#variant_id").innerHTML = ""; // Vaciar lista de variantes
        document.querySelector("#variant_id").innerHTML =
          '<option value="0" disabled>Seleccionar variante del producto</option>' + res;
        
      },

    });
  }



  /**
   * TODO: Buscar lista de precios
   * ! Calcula el precio de los productos
   */

  function product_price(product_id) {
    $.ajax({
      url: SITE_URL + "ajax/price_lists.php",
      method: "post",
      data: {
        product_id: product_id,
        action: "buscar_lista_de_producto",
      },
      success: function (res) {
        document.querySelector("#list_id").innerHTML = ""; // Vaciar lista de precios
        document.querySelector("#list_id").innerHTML =
          '<option value="0" selected>General</option>' + res;
      },
    });
  }

  // Cambiar precio del producto

  $("#list_id").change(function () {
    if ($(this).val() > 0) {
      $.ajax({
        url: SITE_URL + "ajax/price_lists.php",
        method: "post",
        data: {
          list_id: $(this).val(),
          product_id: $("#product_id").val(),
          action: "elegir_precio",
        },
        success: function (res) {
          var data = JSON.parse(res);
          $("#price_out").val(format.format(data.valor));
        },
      });
    } else {
      SearchProduct($("#product_id").val()); // Precio normal
    }
  });


  // Validar la cantidad del producto antes de agregar

  $("#quantity").keyup(function (e) {
    e.preventDefault();

    clcTotalPrice();

  });

  function clcTotalPrice() {
    var stock = parseFloat($("#stock").val());
    var quantity = parseFloat($("#quantity").val());


    // Si la cantidad es mayor al stock, se ocultará el botón de agregar
    if (quantity <= stock) {
      // Ocultar la cantidad si es menor que 1

      if ($("#quantity").val() < 0.1 || isNaN($("#quantity").val())) {
        $("#add_item").hide();
        $("#add_item_free").hide();
      } else {
        $("#add_item").show(); // Botón de ventana facturas de ventas
        $("#add_item_free").show();
      }
    } else {
      $("#add_item").hide(); // Botón de ventana facturas de ventas
      $("#add_item_free").hide();
    }


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

  // Agregar producto

  $("#createProduct").on("click", (e) => {
    e.preventDefault();

    console.log("crear producto")
    // Validar de los campos principales
    if ($("#product_name").val() != "" && $("#product_quantity").val() != "" && $("#inputPrice_out").val() != "") {


      $(".form-custom").css("border", "1px solid #ced4da");
      $(".form-check-label").css("color", "black")

      // Validar tipo de producto
      if ($('input:radio[name=tipoproducto]:checked').val() == "variante") {

        var MaxLength = $("#product_quantity").val();
        var VariantLength = Object.keys(ArrayVariant).length

        if (VariantLength == MaxLength) {

          addproduct(); // Crear producto

        } else {

          alertify.set("notifier", "position", "top-right");
          alertify.error("Debes completar las variantes del producto");
          $("#imei").css("border", "1px solid red");
          $("#colour").css("border", "1px solid red");
          $(".label-imei").css("color", "red");
          $(".label-colour").css("color", "red");

        }
      } else if ($('input:radio[name=tipoproducto]:checked').val() == "novariante") {
        addproduct();
      }



    } else {

      $(".form-custom").css("border", "1px solid #ced4da");
      $(".form-check-label").css("color", "black")
      alertify.set("notifier", "position", "top-right");
      alertify.error("Debes llenar los campos en rojo");

      if ($("#inputPrice_out").val() == "") {
        $("#inputPrice_out").css("border", "1px solid red");
        $(".label-price").css("color", "red")
      }

      if ($("#product_quantity").val() == "") {
        $("#product_quantity").css("border", "1px solid red");
        $('.label-cant').css("color", "red")
      }

      if ($("#product_name").val() == "") {
        $("#product_name").css("border", "1px solid red");
        $('.label-nomb').css("color", "red")
      }
    }

    function addproduct() {

      $.ajax({
        type: "post",
        url: SITE_URL + "services/products.php",
        data: {
          name: $("#product_name").val(),
          product_code: $("#product_code").val(),
          price_out: $("#inputPrice_out").val(),
          price_in: $("#inputPrice_in").val(),
          quantity: $("#product_quantity").val(),
          min_quantity: $("#min_quantity").val(),
          // Keys
          provider: $("#providerID").val(),
          tax: $("#tax").val(),
          brand: $("#brand").val(),
          offer: $("#offer").val(),
          category: $("#category").val(),
          position: $("#position").val(),
          warehouse: $("#warehouse").val(),
          action: "agregar_producto"
        },
        success: function (res) {

          if (res > 0) {

            if (localStorage.getItem("lista_de_precios")) {
              Assign_product_price(res);

            }

            if (localStorage.getItem("variantes")) {
              Assign_variant(res);
            }

            $('#last_product_edit').show()
            $('#last_product_edit').attr('href',SITE_URL+'/products/edit&id='+res) // botón para editar el ultimo producto agregado

            reset_input();
            mysql_row_affected();

          } else if (res == "duplicate") {

            mysql_error("El código del producto ya está siendo utilizado");

          } else if (res.includes("Error")) {

            mysql_error(res);
          }
        },
      });
    }

  });



  // Asignar precios a productos

  function Assign_product_price(product_id) {
    if (localStorage.getItem("lista_de_precios")) {
      arrayL = JSON.parse(localStorage.getItem("lista_de_precios"));
      arrayL.forEach((element, index) => {
        $.ajax({
          type: "post",
          url: SITE_URL + "ajax/price_lists.php",
          data: {
            action: "asignar_lista_de_precios",
            type: "producto",
            list_id: element.list_id,
            list_value: element.list_value,
            id: product_id,
          },
          success: function (res) {


          },
        });
      }); // Loop
    }
  }


  // Asignar variante a productos

  function Assign_variant(product_id) {
    if (localStorage.getItem("variantes")) {
      arr = JSON.parse(localStorage.getItem("variantes"));

      arr.forEach((element, index) => {

        $.ajax({
          type: "post",
          url: SITE_URL + "services/products.php",
          data: {
            action: "asignar_variante",
            colour_id: element.colour_id,
            provider_id: element.provider_id,
            imei: element.imei,
            serial: element.serial,
            box: element.box,
            cost: element.cost,
            id: product_id,
          },
          success: function (res) {
            console.log(res)

            if (res == "ready") {

              reset_input();
              mysql_row_affected();

            } else if (res == "duplicate") {

              alertify.set("notifier", "position", "top-right");
              alertify.error("Imei o Serial ya están siendo utilizado");


            } else if (res.includes("Error")) {

              mysql_error(res);
            }
          },
        });
      }); // Loop
    }
  }



  /**
   * ! Detalle lista de variantes
   */

  // ELiminar LocalStorage al recargar
  localStorage.removeItem('variantes');

  let ArrayVariant = [];

  $('.add_variant').on('click', (e) => {
    e.preventDefault();

    let data;

    if (!isNaN($('#imei').val())) {

      $("#imei").css("border", "1px solid #ced4da");
      $(".label-imei").css("color", "black")
      $(".label-colour").css("color", "black")

      data = {

        imei: $('#imei').val(),
        serial: $('#serial').val(),
        cost: $('#cost').val(),
        box: $('#box').val(),
        colour_id: $('#colour').val(),
        colour: $('#select2-colour-container').attr('title'),
        provider_id: $('#provider').val(),
        provider: $('#select2-provider-container').attr('title')

      }

    } else {

      $("#imei").css("border", "1px solid red");
      $(".label-imei").css("color", "red")
      alertify.set("notifier", "position", "top-right");
      alertify.error("Este campo solo permite números");
    }

    // Buscar coincidencia si existe la variante en el localStorage
    if (data.colour_id != 0 || data.imei != "" || data.serial != "") return FindAMatch(ArrayVariant);

    function FindAMatch(arr) {

      if (arr.length < 1) {

        arr.push(data); // Insertar datos al arreglo
        createDB(ArrayVariant); // crear el localstorage de las variantes 

      } else {

        let found = arr.find(element => element.imei == data.imei)
        let found2 = arr.find(element => element.serial == data.serial)

        if (found == undefined && found2 == undefined || data.serial == "" || data.imei == "") {

          if (ArrayVariant) {

            var MaxLength = $("#product_quantity").val();
            var VariantLength = Object.keys(ArrayVariant).length

            // Si la cantidad de variantes es menor a la cantidad del producto
            if (VariantLength < MaxLength) {
              arr.push(data);
              createDB(ArrayVariant);
            }
          }

        } else {

          $("#imei").css("border", "1px solid red");
          $(".label-imei").css("color", "red")
          $("#serial").css("border", "1px solid red");
          $(".label-serial").css("color", "red")
          alertify.set("notifier", "position", "top-right");
          alertify.error("El imei o serial ya han sido agregados");

        }

      }
    }

  })

  // Crear la base de datos en el localstorage
  function createDB(Arr) {

    localStorage.setItem('variantes', JSON.stringify(Arr));
    showDB(); // Mostrar DB

  }

  let variantLocalStorage;

  function showDB() {


    document.querySelector('#variant_list').innerHTML = ""; // Vaciar variantes
    $('#imei').val(''); // Vaciar campo imei
    $('#serial').val(''); // Vaciar campo serial
    $('#cost').val(''); // Vaciar campo costo

    if (localStorage.getItem("variantes")) {
      variantLocalStorage = JSON.parse(localStorage.getItem("variantes"));
    }


    let avg = 0;

    // Loop de las variantes del producto en localStorage 
    variantLocalStorage.forEach((element, index) => {

      // Calcular costo promedio del producto
      var quantity = $('#product_quantity').val()
      avg = parseInt(avg) + parseInt(element.cost)
      $('#inputPrice_in').val(avg / quantity)

      $('.table_variant').show(); // Mostrar tabla de variantes

      document.querySelector('#variant_list').innerHTML += `
      <tr>
        <td>${element.provider}</td>
        <td>${element.imei}</td>
        <td>${element.serial}</td>
        <td>${element.colour}</td>
        <td>${element.cost}</td>
        <td>${element.box}</td>
        <td> <span class="action-delete" id="delete"><i class="far fa-minus-square"></i></span></td>
      </tr>
        `;

    });

  }

  // Eliminar variantes del localStorage

  if (pageURL.includes("products/add")) {

    var variant = document.querySelector('#variant_list')

    if (variant != null) {

      variant.addEventListener('click', (e) => {
        e.preventDefault();

        if (e.path[1].id == "delete") {

          deleteDB(e.path[3].cells[1].innerHTML, e.path[3].cells[2].innerHTML);
        }

        function deleteDB(imei, serial) {
          let indexArray;

          // Loop de los services en localStorage 
          variantLocalStorage.forEach((element, index) => {

            if (element.imei == imei || element.serial == serial) {
              indexArray = index;

            }

          });

          ArrayVariant.splice(indexArray, 1);
          createDB(ArrayVariant)
        }

      })

    }
  }
  
  // Editar las variantes de un producto

  $('.add_variant_to_product').on('click', (e) => {
    e.preventDefault();

    let data = {
      imei: $('#imei').val(),
      serial: $('#serial').val(),
      cost: $('#cost').val(),
      box: $('#box').val(),
      colour_id: $('#colour').val(),
      colour: $('#select2-colour-container').attr('title'),
      provider_id: $('#provider').val(),
      provider: $('#select2-provider-container').attr('title'),
      product_id: $('#product_id').val()
    }


    // El valor de la lista tiene que ser mayor que 0
    if (!isNaN(data.imei) && data.imei != "" || !isNaN(data.serial) && data.serial != "" || data.colour_id > 0) {
      if (data.cost != '') {


        var rows = $('#variant_list tr').length;
        var quantity = $("#input_quantity").val();
console.log(rows)
        if (rows < quantity) {

          localStorage.setItem('variantes', JSON.stringify(data))

          $.ajax({
            type: "post",
            url: SITE_URL + "services/products.php",
            data: {
              action: "editar_variantes",
              product_id: data.product_id,
              imei: data.imei,
              serial: data.serial,
              box: data.box,
              cost: data.cost,
              colour_id: data.colour_id,
              provider_id: data.provider_id

            },
            success: function (res) {

              if (res > 0) {

                document.querySelector('#variant_list').innerHTML += `
              <tr>
                <td>${data.provider}</td>
                <td>${data.imei}</td>
                <td>${data.serial}</td>
                <td>${data.colour}</td>
                <td>${format.format(data.cost)}</td>
                <td>${data.box}</td>
                <td></td>
                <td> <span class="action-delete" onclick="deleteVariant('${res}','${data.cost}')"><i class="far fa-minus-square"></i></span></td>
               </tr>
                    `;

                CALC_COST() // Calcular costo promedio del producto
                editProduct()

              } else if (res == "duplicate") {

                $("#imei").css("border", "1px solid red");
                $(".label-imei").css("color", "red")
                $("#serial").css("border", "1px solid red");
                $(".label-serial").css("color", "red")
                alertify.set("notifier", "position", "top-right");
                alertify.error("El imei o serial ya están siendo ocupados");

              } else if (res.includes("Error")) {
                mysql_error(res)
              }
            }
          });
        }

      } else {
        $("#cost").css("border", "1px solid red");
        $(".label-cost").css("color", "red")
      }

    } else {


      alertify.set("notifier", "position", "top-right");
      alertify.error("Debes incluir un Serial, Imei o Color para agregar una variante");
    }

  });



  /**
   * ! Actualizar producto
   *  */

  $("#editProduct").on("click", (e) => {
    e.preventDefault()

    editProduct();

  })

  function editProduct() {

    // Validar de los campos principales
    if ($("#product_name").val() != "" && $("#input_quantity").val() != "" && $("#inputPrice_out").val() != "") {


      $(".form-custom").css("border", "1px solid #ced4da");
      $(".form-check-label").css("color", "black")

      // Validar tipo de producto
      if ($('input:radio[name=tipoproducto]:checked').val() == "variante") {

        var MaxLength = $("#input_quantity").val();
        var VariantLength = $('#variant_list tr').length;

        if (VariantLength == MaxLength) {

          update_product()


        } else {

          alertify.set("notifier", "position", "top-right");
          alertify.error("Debes completar las variantes de este producto");

        }

      } else if ($('input:radio[name=tipoproducto]:checked').val() == "novariante") {
        update_product()

      }



    } else {

      $(".form-custom").css("border", "1px solid #ced4da");
      $(".form-check-label").css("color", "black")
      alertify.set("notifier", "position", "top-right");
      alertify.error("Debes llenar los campos en rojo");

      if ($("#inputPrice_out").val() == "") {
        $("#inputPrice_out").css("border", "1px solid red");
        $(".label-price").css("color", "red")
      }

      if ($("#input_quantity").val() == "") {
        $("#input_quantity").css("border", "1px solid red");
        $('.label-cant').css("color", "red")
      }

      if ($("#product_name").val() == "") {
        $("#product_name").css("border", "1px solid red");
        $('.label-nomb').css("color", "red")
      }
    }

    function update_product() {

      $.ajax({
        type: "post",
        url: SITE_URL + "services/products.php",
        data: {
          name: $("#product_name").val(),
          product_id: $("#product_id").val(),
          product_code: $("#product_code").val(),
          price_out: $("#inputPrice_out").val(),
          price_in: $("#inputPrice_in").val(),
          quantity: $("#input_quantity").val(),
          min_quantity: $("#input_min_quantity").val(),
          // Keys
          provider: $("#provider").val(),
          tax: $("#tax").val(),
          brand: $("#brand").val(),
          offer: $("#offer").val(),
          category: $("#category").val(),
          position: $("#position").val(),
          warehouse: $("#warehouse").val(),
          action: "editar_producto",
        },
        success: function (res) {

          if (res == 'ready') {
            $('.radio-list').load(location.href + " .radio-list");
            mysql_row_update();
          } else if (res == "duplicate") {
            mysql_error("El código del producto ya está siendo utilizado");
          } else if (res.includes("Error")) {
            mysql_error(res);
          }
        },

      });
    }


  }


  // Calcular costo promedio del producto

  function CALC_COST() {


    let total;

    if ($('#average_cost').val() != '') {
      total = parseInt($('#average_cost').val()) // Total de costos anterior
    } else {
       total = 0;
    }


    let cost;

    if ($('#cost').val() != '') {
      cost = parseInt($('#cost').val())

    } else {
      cost = 0;
    }

    average_cost = parseInt(total) + parseInt(cost)

    $('#average_cost').val(average_cost) // Total de costos nuevo

    var quantity = $('#variant_list tr').length
    var avg = average_cost / quantity;

    console.log(total)
    console.log(average_cost)

    $('#inputPrice_in').val(avg) // Introducir costo promedio

  }




}); // Ready






// Eliminar variante de un producto

function deleteVariant(variant_id, cost) {

  $.ajax({
    type: "post",
    url: SITE_URL + "services/products.php",
    data: {
      action: "eliminar_variante",
      id: variant_id,
    },
    success: function (res) {

      if (res == "ready") {

        var total = parseInt($('#average_cost').val()) - parseInt(cost) // Total de costos anterior 
        $('#average_cost').val(total)

        $('#Detalle').load(location.href + " #Detalle");
        $('.radio-list').load(location.href + " .radio-list");

      } else {
        mysql_error(res);
      }
    },
  });
}


// Desactivar producto

function disableProduct(product_id) {
  alertify.confirm(
    "<i class='text-warning fas fa-exclamation-circle'></i> Desactivar producto",
    "¿Desea desactivar este producto? ",
    function () {
      $.ajax({
        type: "post",
        url: SITE_URL + "services/products.php",
        data: {
          product_id: product_id,
          action: "desactivar_producto",
        },
        success: function (res) {
          $("#example").load(" #example");
        },
      });
    },
    function () { }
  );
}

// Activar producto

function enableProduct(product_id) {

  alertify.confirm("Activar producto", "¿Desea activar este producto? ",
    function () {

      $.ajax({
        type: "post",
        url: SITE_URL + "services/products.php",
        data: {
          product_id: product_id,
          action: "activar_producto",
        },
        success: function (res) {
          $("#example").load(" #example");
        },
      });

    },
    function () { }
  );
}

// Eliminar producto

function deleteProduct(id) {

  alertify.confirm("Eliminar producto", "¿Estas seguro que deseas borrar este producto? ",
    function () {

      $.ajax({
        url: SITE_URL + "services/products.php",
        method: "post",
        data: {
          action: "eliminarProducto",
          product_id: id,
        },
        success: function (res) {

          if (res == "ready") {

            $("#example").load(" #example");

          } else {
            alertify.alert("<div class='error-info'><i class='text-danger fas fa-exclamation-circle'></i>" + " " + res + "</div>").set('basic', true);
          }

        }

      });

    },
    function () { }
  );
}

