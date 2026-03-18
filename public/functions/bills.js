import * as qz from "/public/test.js?v=1.0.1";

// import * as qz from "../test.js";

$(document).ready(function () {

    // Defaults 
    $("#credit-in-finish-or-receipt").hide()
    $("#credit-in-finish-or").hide()
    $("#cash-in-finish-or-receipt").hide()
    $("#cash-in-finish-or").hide()
    $('#cash-received').val('0.00')
    $('#cash-topay').val('0.00')
    $('#g_quantity').val('1')
    $('#save_spend').hide();

    /**============================================================= 
     * CRUD GASTOS
    ===============================================================*/

    let ArrayBills = [];
    let ArrayBillsLocalStorage;

    $('#formAddBill').on('submit', function (e) {
        e.preventDefault()

        const quantity = Number($('#g_quantity').val());
        const value = Number($('#g_value').val().replace(/,/g, ""));
        const tax_value = Number($('#g_taxes').val()) / 100 || 0;

        const total = quantity * value;
        const taxes = total * tax_value;

       const data = {
            reason_id: $('#reason').val(),
            name: $('#select2-reason-container').attr('title'),
            quantity: parseInt($('#g_quantity').val()),
            taxes: taxes,
            tax_value: $('#g_taxes').val(),
            value: $('#g_value').val().replace(/,/g, ""),
            total_price: total + taxes,
            observation: $('#observation_item').val()
        }

        ArrayBills.push(data); // Insertar datos al arreglo
        createLocalStorage(ArrayBills); // crear el localstorage del detalle

        // Buscar coincidencia si existe un "Motivo de gasto" en el localStorage
        // if (data.reason_id > 0) return FindAMatch(ArrayBills);

        // function FindAMatch(arr) {

        //     if (arr.length < 1) {

        //         arr.push(data); // Insertar datos al arreglo
        //         createLocalStorage(ArrayBills); // crear el localstorage del detalle

        //     } else {

        //         let found = arr.find(element => element.name == data.name)

        //         if (found == undefined) {

        //             arr.push(data);
        //             createLocalStorage(ArrayBills);
        //         }
        //     }
        // } // Function

    })


    // Crear detalle en el localstorage
    function createLocalStorage(Arr) {
        localStorage.setItem('detalle_gasto', JSON.stringify(Arr));
        loadLocalStorage(); // Mostrar DB
    }

    function loadLocalStorage() {

        const rows = document.querySelector('#rows');
        rows.innerHTML = "";

        const data = localStorage.getItem("detalle_gasto");
        if (data) {
            ArrayBillsLocalStorage = JSON.parse(data);
        }

        let html = "";

        ArrayBillsLocalStorage.forEach((element, index) => {

            const taxes = element.taxes > 0 ? format.format(element.taxes) : 0;
            const tax_value = element.taxes > 0 ? element.tax_value : 0;
            const value = format.format(element.value);

            html += `
        <tr>
            <td>${element.name}</td>
            <td>${element.quantity}</td>
            <td>${value}</td>
            <td class="hide-cell">${taxes} - ${tax_value}%</td>
            <td class="hide-cell note-width">${element.observation}</td>
            <td>${format.format(element.total_price)}</td>
            <td>
                <span class="action-delete erase_bill" data-id="${index}">
                    <i class="fas fa-backspace"></i>
                </span>
            </td>
        </tr>`;
        });

        rows.innerHTML = html;

        calcBills(ArrayBillsLocalStorage);
    }

    // Calcular total del detalle 
    function calcBills(arr) {

        let subtotal = 0;
        let taxes = 0;
        let total = 0;

        arr.forEach((element) => {

            const quantity = Number(element.quantity);
            const value = Number(element.value);

            subtotal += quantity * value;

            if (element.taxes > 0) {
                taxes += Number(element.taxes);
            }

        });

        var sub = format.format(subtotal.toFixed(2));
        var tax = format.format(taxes.toFixed(2));
        total = format.format(subtotal + taxes);

        // Validar modal guardar gastos
        $('#cash-received').val('0.00')
        $('#cash-topay').val(total)
        if (total.replace(/,/g, "") > 0) {
            $('#save_spend').show();
        }

        document.querySelector('#price').innerHTML = ""; // Vaciar precios de la factura

        document.querySelector('#price').innerHTML += `
         <span><input type="text" class="invisible-input" value="${sub}" id="in-subtotal" disabled></span>
         <span><input type="text" class="invisible-input" value="${tax}" id="in-taxes" disabled></span>
         <span><input type="text" class="invisible-input" value="${total}" id="in-total" disabled></span>
         <input type="hidden" name="" value="${subtotal + taxes}" id="total_invoice">
     `;
    }


    // Auto cargar detalle del LocalStorage
    if (pageURL.includes("bills/addbills")) {

        $(function () {

            const data = localStorage.getItem("detalle_gasto");
            if (!data) return;

            const bills = JSON.parse(data);
            let rowsHTML = '';

            bills.forEach((e, index) => {

                ArrayBills.push(e);

                const taxes = e.taxes > 0 ? format.format(e.taxes) : 0;
                const tax_value = e.taxes > 0 ? e.tax_value : 0;
                const value = format.format(e.value);

                rowsHTML += `
            <tr>
                <td>${e.name}</td>
                <td>${e.quantity}</td>
                <td>${value}</td>
                <td class="hide-cell">${taxes} - ${tax_value}%</td>
                <td class="hide-cell">${e.observation}</td>
                <td>${format.format(e.total_price)}</td>
                <td>
                  <span class="action-delete erase_bill" data-id="${index}">
                    <i class="fas fa-backspace"></i>
                  </span>
                </td>
            </tr>`;
            });

            $('#rows').html(rowsHTML);

            calcBills(ArrayBills);

        });
    }

    // Eliminar gasto del detalle
    $(document).on('click', '.erase_bill', function () {

        const id = $(this).data('id');

        ArrayBills.splice(id, 1);
        createLocalStorage(ArrayBills)
    })

    // Borrar todos los gastos del localstorage 
    $('#CancelBill').on('click', (e) => {
        cancelBills()
    });

    function cancelBills() {
        localStorage.removeItem('detalle_gasto'); // Vaciar localstorage
        ArrayBills = []; // Vaciar Arreglo
        calcBills(ArrayBills); // Calcular precios
        $('#rows').load(location.href + " #rows"); // actualizar detalle
    }

    // Eliminar gasto
    $(document).on('click', '.delete_bill', function () {

        const id = $(this).data('id');

        alertify.confirm("Eliminar gasto", "¿Estas seguro que deseas eliminar este gasto? ",
            function () {

                sendAjaxRequest({
                    url: "services/bills.php",
                    data: {
                        action: "eliminar_gasto",
                        id: id
                    },
                    successCallback: (res) => {
                        dataTablesInstances['bills'].ajax.reload() // Recargar datatable

                    },
                    errorCallback: (err) => {
                        console.error(err);
                        notifyAlert("Ha ocurrido un error", "error")
                    }
                })
            },
            function () {

            });
    })


    /**============================================================= 
     * REGISTRAR E IMPRIMIR GASTOS
    ===============================================================*/

    // Crear orden gastos
    $('#formSaveBills').on('submit', async function (e) {
        e.preventDefault();

        try {
            // 1️⃣ Crear orden
            const order_id = await sendAjaxPromise({
                url: SITE_URL + "services/bills.php",
                data: {
                    action: "crear_orden_gasto",
                    provider_id: $('#provider').val(),
                    origin: $('#origin').val(),
                    date: $('#date').val()
                }
            });

            //2️⃣ Agregar detalles
            await addOrderDetail(order_id);

            // 3️⃣ Registrar gasto
            await registerBill(order_id);

            // 4️⃣ Éxito final
            notifyAlert("Registro guardado correctamente", "success");

            printerBill(order_id) 

        } catch (err) {
            console.error(err);
            notifyAlert("Ha ocurrido un error inesperado", "error");
        }
    });

    // Registrar el detalle a la orden
    async function addOrderDetail(order_id) {
        if (!localStorage.getItem("detalle_gasto")) return;

        const arrayL = JSON.parse(localStorage.getItem("detalle_gasto"));

        for (const element of arrayL) {
            const res = await sendAjaxPromise({
                type: "POST",
                url: SITE_URL + "services/bills.php",
                data: {
                    action: "detalle_gasto",
                    order_id: order_id,
                    reason_id: element.reason_id,
                    quantity: element.quantity,
                    taxes: element.taxes,
                    value: element.value,
                    observation: element.observation,
                }
            });

            if (res !== "ready") {
                throw new Error(`Error al registrar detalle: ${res}`);
            }
        }
    }

    // Registrar gastos
    async function registerBill(order_id) {
        const res = await sendAjaxPromise({
            type: "POST",
            url: SITE_URL + "services/bills.php",
            data: {
                action: "registrar_gasto",
                order_id: order_id,
                provider_id: $('#provider').val(),
                observation: $('#observation').val(),
                total_invoice: $('#cash-topay').val().replace(/,/g, ""),
                date: $('#date').val()
            }
        });

        if (res !== "ready") {
            throw new Error(`Error al registrar gasto: ${res}`);
        }

        // Limpiar formulario y recargar tabla
        document.querySelector('#rows').innerHTML = "";
        $('#in-subtotal').val('0.00');
        $('#in-taxes').val('0.00');
        $('#in-total').val('0.00');
        $('#observation').val('');
        $('#cash-received').val($('#cash-topay').val());
        $('#cash-topay').val('0.00');
        $(".table").load(location.href + " .table");

        cancelBills();
    }

    // Función para envolver AJAX en una promesa
    function sendAjaxPromise(options) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: options.type || "POST",
                dataType: options.dataType || "text",
                url: options.url,
                data: options.data,

                success: (data) => {
                    resolve(data);
                },

                error: (xhr, status, error) => {
                    console.error("ERROR:", xhr.responseText);
                    reject({
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                }
            });
        });
    }

    // función de imprimir
    function printerBill(order_id) {
        sendAjaxRequest({
            url: "services/bills.php",
            data: {
                action: 'datos_impresion',
                id: order_id
            },
            successCallback: (res) => {
                try {
                    var data = JSON.parse(res)
                    console.log(data)

                    qz.gastos(data[0], data[1]);
                } catch (error) {
                    console.log(error)
                }
            },
            errorCallback: (err) => {
                console.error(err)
            }
        })
    }

    /**============================================================= 
    * LEER Y ESCRIBIR NUEVOS GASTOS
    ===============================================================*/

    // Crear motivo de gastos
    $('#formNewBill').on('submit', function (e) {
        e.preventDefault()

        let formData = new FormData(this);
        formData.append('action', 'agregar_motivo')

        sendAjaxRequest({
            url: "services/bills.php",
            data: formData,
            successCallback: () => {
                notifyAlert("Registro guardado correctamente", "success")
            },
            errorCallback: (err) => {
                console.error(err)
                notifyAlert("Ha ocurrido un erro inesperado", "error")
            },

        })
    })

    // Cargar motivos de gastos
    function initBillsSelect2(selector) {
        const $select = $(selector);

        $select.select2({
            placeholder: 'Selecciona el gasto',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: SITE_URL + 'services/bills.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        action: 'lista_gastos',
                        q: params.term || ''
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results.map(e => ({
                            id: e.id,
                            text: e.nombre
                        }))
                    };
                }
            }
        });
    }

    initBillsSelect2('#reason')


}) // Ready
