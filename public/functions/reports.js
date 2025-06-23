$(document).ready(function () {

    $('#action').on('change', function () {

        if (this.value == "productos_vendidos" || this.value == "servicios_vendidos") {

            $("#col-dateq1").fadeIn(400)
            $("#col-dateq2").fadeIn(400)

            $("#query").attr("required", true)
            $("#dateq1").attr("required", true)
            $("#dateq2").attr("required", true)

        } else if (this.value == "serial_facturado") {

            $("#query").attr("required", true)
            $("#col-dateq1").fadeOut(200)
            $("#col-dateq2").fadeOut(200)

        }

    });

    /**
   * ! Reporte de ventas filtrado por fecha 
   */

    $('#date_query').change((e) => {
        e.preventDefault()

        $(location).attr('href', SITE_URL + 'src/excel/reporte-fecha.php?date=' + $('#date_query').val());

    })

    /**
    * Calcula el total esperado al cierre de caja.
    * Fórmula: initial_balance + cash_income + otros_ingresos - cash_expenses - withdrawals
    */
    function calculateExpectedTotal() {
        // Obtener los valores de cada campo (si está vacío se usa 0)
        const saldoInicial = parseFloat($('#initial_balance').val()) || 0;
        const ingresosEfectivo = parseFloat($('#cash_income').val().replace(/,/g, "")) || 0;
        const cash_expenses = parseFloat($('#cash_expenses').val().replace(/,/g, "")) || 0;
        const withdrawals = parseFloat($('#withdrawals').val().replace(/,/g, "")) || 0;
        const refunds = parseFloat($('#refund').val().replace(/,/g, "")) || 0;

        // Calcular el total esperado
        const totalEsperado = saldoInicial + ingresosEfectivo - cash_expenses - withdrawals - refunds;

        // Mostrar el resultado en el campo correspondiente
        $('#total_expected').val(format.format(totalEsperado));
    }

    // Calcular al abrir cierre de caja
    $('#cash_closing').on('click', () => {
        calculateExpectedTotal();
    })

    // Ejecutar el cálculo cuando se modifique cualquiera de los campos relacionados
    $('#initial_balance, #cash_income, #cash_expenses, #withdrawals, #refund').on('input', calculateExpectedTotal);


    // Calcular diferencia
    function updateDifference() {
        const valReal = parseFloat($('#current_total').val()) || 0;
        const valEsperado = parseFloat($('#total_expected').val().replace(/,/g, "")) || 0;

        const diferenciaValor = (valReal - valEsperado);
        const signo = diferenciaValor > 0 ? '+' : (diferenciaValor < 0 ? '-' : '');
        const diferenciaFormateada = signo + format.format(Math.abs(diferenciaValor));

        // Actualiza el campo con el valor formateado
        $('#total_difference').val(diferenciaFormateada);

        // Establece el color según el valor
        const $diffField = $('#total_difference');
        $diffField.removeClass('text-success text-danger');

        if (diferenciaValor > 0) {
            $diffField.addClass('text-success');
        } else if (diferenciaValor < 0) {
            $diffField.addClass('text-danger');
        }

        // Actualiza también el campo real con formato
        $('#real').val(format.format(valReal));
    }

    // Recalcular cuando se escriba en los campos relevantes
    $('#current_total,#initial_balance').on('input', updateDifference);
    $('#cash_expenses,#withdrawals, #refund').on('input', updateDifference);


  

}); // Ready

  // Generar cierre pdf
    function generateCashClosingPDF(id) {
        
        var width = 1000;
        var height = 800;

        // Centrar la ventana
        var x = parseInt((window.screen.width / 2) - (width / 2));
        var y = parseInt((window.screen.height / 2) - (height / 2));

        var url = SITE_URL + 'src/pdf/cierre_caja.php?id=' + id;
        window.open(url, 'ciere_caja', 'left=' + x + ',top=' + y + ',height=' + height + ',width=' + width + ',scrollball=yes,location=no')

    }

// Abrir caja

function cashOpening() {

    let OpeningDateRaw = $('#opening').val(); // "2025-06-17T10:17"
    let localDate = new Date(OpeningDateRaw);

    // Formatear a "YYYY-MM-DD HH:MM:SS"
    let formattedOpeningDate = localDate.getFullYear() + '-' +
        String(localDate.getMonth() + 1).padStart(2, '0') + '-' +
        String(localDate.getDate()).padStart(2, '0') + ' ' +
        String(localDate.getHours()).padStart(2, '0') + ':' +
        String(localDate.getMinutes()).padStart(2, '0') + ':' +
        '00'; // segundos fijos

    data = {
        action: "abrir_caja",
        initial_balance: parseFloat($('#cash_initial').val()) || 0,
        opening_date: formattedOpeningDate,
    }

    sendAjaxRequest({
        url: "services/reports.php",
        data: data,
        successCallback: (res) => {
            $('.float-right').load(window.location.href + ' .float-right > *');
            setTimeout(() => location.reload(), 900);
            mysql_row_affected()
        },
        errorCallback: (res) => mysql_error(res),
        verbose: true
    })

}

// Cierre de caja
function cashClosing() {

    let closingDateRaw = $('#closing_date').val(); // "2025-06-17T10:17"
    let localDate = new Date(closingDateRaw);

    // Formatear a "YYYY-MM-DD HH:MM:SS"
    let formattedClosingDate = localDate.getFullYear() + '-' +
        String(localDate.getMonth() + 1).padStart(2, '0') + '-' +
        String(localDate.getDate()).padStart(2, '0') + ' ' +
        String(localDate.getHours()).padStart(2, '0') + ':' +
        String(localDate.getMinutes()).padStart(2, '0') + ':' +
        '00'; // segundos fijos

    const data = {
        action: "cierre_caja",
        user_id: $('#user_id').val(),
        closing_date: formattedClosingDate, // closing_date
        initial_balance: parseFloat($('#initial_balance').val()) || 0,
        cash_income: parseFloat($('#cash_income').val().replace(/,/g, "")) || 0,
        card_income: parseFloat($('#card_income').val().replace(/,/g, "")) || 0,
        transfer_income: parseFloat($('#transfer_income').val().replace(/,/g, "")) || 0,
        check_income: parseFloat($('#check_income').val().replace(/,/g, "")) || 0,
        cash_expenses: parseFloat($('#cash_expenses').val().replace(/,/g, "")) || 0,
        external_expenses: parseFloat($('#external_expenses').val().replace(/,/g, "")) || 0,
        withdrawals: parseFloat($('#withdrawals').val()) || 0,
        refunds: parseFloat($('#refund').val().replace(/,/g, "")) || 0,
        total: parseFloat($('#total').val().replace(/,/g, "")) || 0,
        current_total: $('#current_total').val(),
        notes: $('#notes').val() || ""
    };

    sendAjaxRequest({
        url: "services/reports.php",
        data: data,
        successCallback: (res) => {
            $('.float-left').load(window.location.href + ' .float-left > *');
            mysql_row_affected()
        },
        errorCallback: (res) => mysql_error(res),
        verbose: true
    })
}

// Eliminar cierre de caja

function deleteCashClosing(id) {
    alertify.confirm("Eliminar cierre de caja", "¿Estas seguro que deseas eliminar el cierre? ",
        function () {
            sendAjaxRequest({
                url: "services/reports.php",
                data: {
                    action: "eliminar_cierre",
                    id: id
                },
                successCallback: (res) => {
                    dataTablesInstances['cashClosing'].ajax.reload()

                },
                errorCallback: (res) => mysql_error(res),
            })
        },
        function () { }
    );
}


function Query() {

    if ($('#action').val() == "productos_vendidos") {
        PV(); // Productos vendidos
    } else if ($('#action').val() == "piezas_vendidas") {
        PZ(); // Piezas vendidas
    } else if ($('#action').val() == "servicios_vendidos") {
        SV(); // Servicios vendidos
    } else if ($('#action').val() == "serial_facturado") {
        IMF() // Serial facturados
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
        beforeSend: function () {

        },
        success: function (res) {

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
            $(data).each(function (index, element) {

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
        beforeSend: function () {

        },
        success: function (res) {

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
            $(data).each(function (index, element) {

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
        beforeSend: function () {

        },
        success: function (res) {

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
            $(data).each(function (index, element) {

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
        beforeSend: function () {

        },
        success: function (res) {

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
            $(data).each(function (index, element) {

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