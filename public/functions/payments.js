var pageURL = $(location).attr("pathname");

function mysql_row_affected() {
  alertify.alert(`<div class='row-affected'>
    <i class='icon-success far fa-check-circle'></i>
    <p>Registrado exitosamente</p>
    </div>`).set('basic', true);
}

function mysql_row_update() {
  alertify.alert(`<div class='row-affected'>
    <i class='icon-success far fa-check-circle'></i>
    <p>Registro actualizado correctamente</p>
    </div>`).set('basic', true);
}


function mysql_error(err) {
  alertify.alert(`<div class='error-info'>
    <i class='icon-error fas fa-exclamation-circle'></i> 
    <p>${err}</p>
    </div>`).set('basic', true);
}

$(document).ready(function () {

  // Default
  $('#received').val('0.00')
  $('#topay').val('0.00')
  $('#pending').val('0.00')
  $('#add_payment').hide()
  $('#add_payment_print').hide()
  $('.pay').hide();

  // Elegir el tipo de factura

  $('.repair').hide()


  $('input:radio[name=tipo]').change(function () {
    if ($(this).val() == "venta") {

      $('.sale').show()
      $('.repair').hide()

    } else if ($(this).val() == "reparacion") {

      $('.repair').show()
      $('.sale').hide()
    }
  });

  // Cargar datos de la factura

  $('#invoiceFP').change(function () {
    var invoice_id = $('#invoiceFP').val();
    load_invoice(invoice_id, 'consultar_factura_proveedor')
  })

  $('#invoice').change(function () {
    var invoice_id = $('#invoice').val();
    load_invoice(invoice_id, 'consultar_factura_venta')
  })

  $('#invoiceRP').change(function () {
    var invoice_id = $('#invoiceRP').val();
    load_invoice(invoice_id, 'consultar_factura_reparacion')
  })


  function load_invoice(invoice_id, action) {

    $.ajax({
      type: "post",
      url: SITE_URL + "services/payments.php",
      data: {
        action: action,
        invoice_id: invoice_id
      },
      success: function (res) {

        var data = JSON.parse(res);

        $('.pay').show()
        $('#topay').val(format.format(data.total))


        let apellidos;

        if (data.apellidos != null) {
          apellidos = data.apellidos;
        } else {
          apellidos = "";
        }

        if (pageURL.includes("payments/add")) {

          $('#received').val(format.format(data.recibido))
          $('#pending').val(format.format(data.pendiente))

          $('#customer').val(data.nombre)
          $('#customer_id').val(data.cliente_id);

         

        } else if (pageURL.includes("bills/add_payment")) {

          $('#received').val(format.format(data.pagado))
          $('#pending').val(format.format(data.por_pagar))

          $('#provider').val(data.nombre_proveedor + " " + apellidos)
          $('#provider_id').val(data.proveedor_id);
        }

      }
    });
  }


  // Introducir monto

  $('#pay').on('keyup', (e) => {
    e.preventDefault();

    var pay = parseInt($('#pay').val());
    var pending = parseInt($('#pending').val().replace(/,/g, ""));

    if (pay <= pending) {
      $('#add_payment').show()
      $('#add_payment_print').show()
    } else {
      $('#add_payment').hide()
      $('#add_payment_print').hide()
    }

  })



  /**
   * TODO: Agregar pago
   */

  if (pageURL.includes("payments/add")) {

    $('#add_payment').on('click', (e) => {
      e.preventDefault();

      add_payment();
    })

    $('#add_payment_print').on('click', (e) => {
      e.preventDefault();

      data = {
        topay: $("#topay").val().replace(/,/g, ""),
        pending: $("#pending").val().replace(/,/g, ""),
        pay: $("#pay").val(),
        method: $('#select2-method-container').attr('title'),
        customer: $('#customer').val(),
        date: $('#date').val(),
        seller: $('#seller').val()
      }

      add_payment(true, data);
    })


    function add_payment(receipt = false, data = {}) {

      let invoice_id;
      let invoiceRP_id;

      if ($('input:radio[name=tipo]:checked').val() == 'reparacion') {
        invoiceRP_id = $('#invoiceRP').val();
        invoice_id = 0;
      } else if ($('input:radio[name=tipo]:checked').val() == 'venta') {
        invoice_id = $('#invoice').val();
        invoiceRP_id = 0;
      }

      $.ajax({
        type: "post",
        url: SITE_URL + "services/payments.php",
        data: {
          action: 'agregar_pago',
          invoice_id: invoice_id,
          invoiceRP_id: invoiceRP_id,
          customer_id: $('#customer_id').val(),
          comment: $('#observation').val(),
          method: $('#method').val(),
          received: $('#pay').val(),
          date: $('#date').val()
        },
        success: function (res) {
          console.log(res)
          if (res > 0) {

            if (receipt == true) {
              printer(invoice_id, invoiceRP_id, res, data);
            }

            if (invoiceRP_id > 0) {
              load_invoice(invoiceRP_id, 'consultar_factura_reparacion') // Volver a cargar los datos de la factura pagada
            } else if (invoice_id > 0) {
              load_invoice(invoice_id, 'consultar_factura_venta') // Volver a cargar los datos de la factura pagada
            }

            mysql_row_affected()

          } else {
            mysql_error(res)
          }

        }
      });

    }

    // función de imprimir
    function printer(invoice_id, invoiceRP_id, num_receipt, data) {
      console.log('imprimiendo.....')

      $.ajax({
        type: "post",
        url: PRINTER_SERVER + "recibo.php",
        data: {
          invoice_id: invoice_id,
          invoiceRP_id: invoiceRP_id,
          data: data,
          num_receipt: num_receipt
        },
        success: function (res) {
          console.log(res)

        }
      });

    }

  }


  /**
   * TODO: Pagar factura a un proveedor
   */

  if (pageURL.includes("bills/add_payment")) {
    $('#add_payment').on('click', (e) => {
      e.preventDefault();

      var invoiceFP = $('#invoiceFP').val();
      $.ajax({
        type: "post",
        url: SITE_URL + "services/payments.php",
        data: {
          action: 'agregar_pago_proveedor',
          invoice_id: invoiceFP,
          provider_id: $('#provider_id').val(),
          comment: $('#observation').val(),
          method: $('#method').val(),
          received: $('#pay').val()
        },
        success: function (res) {
          if (res == "ready") {

            load_invoice(invoiceFP, 'consultar_factura_proveedor')

            mysql_row_affected()

          } else {
            mysql_error(res)
          }

        }
      });

    })
  }



}) // Ready


// Eliminar pago

function deletePayment(id, factura_id, facturaRP_id) {
  alertify.confirm("Eliminar pago", "¿Estas seguro que deseas eliminar este pago? ",
    function () {

      $.ajax({
        url: SITE_URL + "services/payments.php",
        method: "post",
        data: {
          action: "eliminar_pago",
          invoice_id: factura_id,
          invoiceRP_id: facturaRP_id,
          id: id
        },
        success: function (res) {

          if (res == "ready") {

            $("#example").load(location.href + " #example");

          } else {
            mysql_error(res)
          }
        }
      });
    },
    function () {

    });
}


// Eliminar pago a factura de proveedores

function deletePaymentProvider(id) {
  alertify.confirm("Eliminar pago", "¿Estas seguro que deseas eliminar este pago? ",
    function () {

      $.ajax({
        url: SITE_URL + "services/payments.php",
        method: "post",
        data: {
          action: "eliminar_pago_factura_proveedor",
          id: id
        },
        success: function (res) {

          if (res == "ready") {

            $("#example").load(location.href + " #example");

          } else {
            mysql_error(res)
          }
        }
      });
    },
    function () {

    });
}
