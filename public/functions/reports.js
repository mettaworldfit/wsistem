let dt_today; // Global

$(document).ready(function() {

    dt_today = $('#today').DataTable({
        processing: false, // Oculta el spinner interno de DataTables
        serverSide: true,
        language: {
            lengthMenu: "_MENU_",
            zeroRecords: "Aún no tienes datos para mostrar",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Página no disponible",
            infoFiltered: "(Filtrado de _MAX_  registros)",
            search: "Buscar:", // Cambia el texto
            processing: "Buscando...",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "<i class='fas fa-caret-right'></i>",
                previous: "Anterior"
            }
        },
        ajax: function(data, callback, settings) {
            // Mostrar loader
            const $tbody = $('#today tbody');
            $tbody.html(`
            <tr>
                <td colspan="100%">
                    <div class="spinner-container">
                        <div class="spinner"></div>
                        <div style="margin-top: 10px;">Cargando datos...</div>
                    </div>
                </td>
            </tr>
        `);

            // Simular retardo de 900ms antes de hacer la llamada AJAX real
            setTimeout(() => {
                $.ajax({
                    url: SITE_URL + 'services/reports.php',
                    type: 'POST',
                    data: {
                        action: 'index_ventas_hoy',
                        ...data // Importante: esto pasa los datos de paginación, búsqueda, etc.

                    },
                    dataType: 'json',
                    success: function(response) {
                        const json = typeof response === 'string' ? JSON.parse(response) : response;
                        callback(json);

                    }
                });
            }, 300);
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'fecha' },
            { data: 'total' },
            { data: 'recibido' },
            { data: 'pendiente' },
            { data: 'estado' },
            { data: 'acciones', orderable: false, searchable: false }
        ],
        initComplete: function() {

        }

    });


    $('#action').on('change', function() {

        if (this.value == "productos_vendidos" || this.value == "servicios_vendidos") {

            $("#col-dateq1").fadeIn(400)
            $("#col-dateq2").fadeIn(400)

            $("#query").attr("required", true)
            $("#dateq1").attr("required", true)
            $("#dateq2").attr("required", true)

        } else if (this.value == "imei_facturado") {

            $("#query").attr("required", true)
            $("#col-dateq1").fadeOut(200)
            $("#col-dateq2").fadeOut(200)

        }

    });



}); // Ready


function Query() {

    if ($('#action').val() == "productos_vendidos") {
        PV(); // Productos vendidos
    } else if ($('#action').val() == "piezas_vendidas") {
        PZ(); // Piezas vendidas
    } else if ($('#action').val() == "servicios_vendidos") {
        SV(); // Servicios vendidos
    } else if ($('#action').val() == "imei_facturado") {
        IMF() // Imei facturados
    }



}

// Funciones de consultas

function PV() {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/reports.php",
        data: {
            query: $('#query').val(),
            dateq1: $('#dateq1').val(),
            dateq2: $('#dateq2').val(),
            action: $('#action').val()
        },
        beforeSend: function() {

        },
        success: function(res) {

            var data = JSON.parse(res);

            document.querySelector("#queryResult").innerHTML = ""; // Vaciar campos de resultado
            document.querySelector("#queryResult").innerHTML = ` 
      <table class="queryTable" id="echoQuery" style="width: 100%; margin-top: 2.5em; 
      background: #f1f1f1; border-radius: 7px;">
            <thead>
                <tr>
                    <td style=" padding: 2px 10px; font-size: 13px;">Nombre producto</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Cantidad</td>
                      <td style=" padding: 2px 10px; font-size: 13px;">Costo total</td>
                      <td style=" padding: 2px 10px; font-size: 13px;">Ganancias</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Total</td>
                </tr>
            </thead>
        </table>`;

            var tr = "<tr>";
            $(data).each(function(index, element) {

                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.nombre_producto + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.cantidad + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.costo) + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.ganancia) + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.total) + "</td>";
                tr += "</tr>";
            });
            $('#echoQuery').append(tr);

        },
    });
}


function PZ() {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/reports.php",
        data: {
            query: $('#query').val(),
            dateq1: $('#dateq1').val(),
            dateq2: $('#dateq2').val(),
            action: $('#action').val()
        },
        beforeSend: function() {

        },
        success: function(res) {

            var data = JSON.parse(res);

            document.querySelector("#queryResult").innerHTML = ""; // Vaciar campos de resultado
            document.querySelector("#queryResult").innerHTML = ` 
      <table class="queryTable" id="echoQuery" style="width: 100%; margin-top: 2.5em; 
      background: #f1f1f1; border-radius: 7px;">
            <thead>
                <tr>
                    <td style=" padding: 2px 10px; font-size: 13px;">Nombre pieza</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Cantidad</td>
                      <td style=" padding: 2px 10px; font-size: 13px;">Costo total</td>
                      <td style=" padding: 2px 10px; font-size: 13px;">Ganancias</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Total</td>
                </tr>
            </thead>
        </table>`;

            var tr = "<tr>";
            $(data).each(function(index, element) {

                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.nombre_pieza + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.cantidad + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.costo) + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.ganancia) + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.total) + "</td>";
                tr += "</tr>";
            });
            $('#echoQuery').append(tr);

        },
    });
}


function SV() {

    $.ajax({
        type: "post",
        url: SITE_URL + "services/reports.php",
        data: {
            query: $('#query').val(),
            dateq1: $('#dateq1').val(),
            dateq2: $('#dateq2').val(),
            action: $('#action').val()
        },
        beforeSend: function() {

        },
        success: function(res) {

            var data = JSON.parse(res);

            document.querySelector("#queryResult").innerHTML = ""; // Vaciar campos de resultado
            document.querySelector("#queryResult").innerHTML = ` 
      <table class="queryTable" id="echoQuery" style="width: 100%; margin-top: 2.5em; 
      background: #f1f1f1; border-radius: 7px;">
            <thead>
                <tr>
                    <td style=" padding: 2px 10px; font-size: 13px;">Nombre servicio</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Cantidad</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Total</td>
                </tr>
            </thead>
        </table>`;

            var tr = "<tr>";
            $(data).each(function(index, element) {

                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.nombre_servicio + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.cantidad + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + format.format(element.total) + "</td>";
                tr += "</tr>";
            });
            $('#echoQuery').append(tr);

        },
    });
}

function IMF() {
    $.ajax({
        type: "post",
        url: SITE_URL + "services/reports.php",
        data: {
            query: $('#query').val(),
            action: $('#action').val()
        },
        beforeSend: function() {

        },
        success: function(res) {

            var data = JSON.parse(res);

            document.querySelector("#queryResult").innerHTML = ""; // Vaciar campos de resultado
            document.querySelector("#queryResult").innerHTML = ` 
      <table class="queryTable" id="echoQuery" style="width: 100%; margin-top: 2.5em; 
      background: #f1f1f1; border-radius: 7px;">
            <thead>
                <tr>
                    <td style=" padding: 2px 10px; font-size: 13px;">Factura_id</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Cliente</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Imei</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Costo</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Entrada</td>
                    <td style=" padding: 2px 10px; font-size: 13px;">Facturado_el</td>
                </tr>
            </thead>
        </table>`;

            var tr = "<tr>";
            $(data).each(function(index, element) {

                tr += "<td style='padding: 2px 10px; font-size: 13px;'> FT-00" + element.factura_venta_id + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.nombre + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.imei + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.costo_unitario + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.fecha_entrada + "</td>";
                tr += "<td style='padding: 2px 10px; font-size: 13px;'>" + element.fecha + "</td>";
                tr += "</tr>";
            });
            $('#echoQuery').append(tr);

        },
    });
}