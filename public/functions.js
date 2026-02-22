let basePath = '/';

if (window.location.hostname === 'localhost') {
    const pathParts = window.location.pathname.split('/');
    basePath = '/' + pathParts[1] + '/'; // Detecta el nombre
}

const SITE_URL = window.location.protocol + '//' + window.location.host + basePath;

let pageURL = $(location).attr("pathname");

/**
 * Intenta interpretar la respuesta del servidor como JSON.
 * Si no es JSON válido, analiza el texto plano para determinar
 * si representa un error según palabras clave.
 *
 * @param {string} response - Respuesta cruda devuelta por el servidor (AJAX).
 *
 * @returns {Object} Resultado normalizado de la respuesta.
 * @returns {boolean} returns.success - Indica si la respuesta se considera exitosa.
 * @returns {*} returns.data - Datos devueltos por el servidor (JSON, texto o número).
 * @returns {boolean|null} returns.error - Indica si la respuesta representa un error.
 *
 * @example
 * // JSON válido
 * handleJSONResponse('{"error":false,"message":"OK"}');
 *
 * @example
 * // Texto plano exitoso
 * handleJSONResponse('1');
 *
 * @example
 * // Texto plano con error
 * handleJSONResponse('Error al eliminar registro');
 */
function handleJSONResponse(response) {
    try {
        let parsed = JSON.parse(response);

        if (Array.isArray(parsed)) {
            return { success: true, data: parsed, error: null };
        }

        if (typeof parsed === 'object' && parsed !== null) {
            return { success: true, data: parsed, error: null };
        }

        return { success: false, data: "La respuesta no es un objeto válido.", error: null };


    } catch (e) {
        // Si no es JSON válido, devolver como error de texto plano

        const text = String(response).trim().toLowerCase();

        // Palabras clave que indican errores
        const errorKeywords = [
            'error', 'err', 'exception', 'duplicate', 'sql', 'warning'
        ];

        const isError = errorKeywords.some(keyword =>
            text.includes(keyword)
        );

        return {
            success: !isError,
            data: response,
            error: isError
        };

    }
}

/**
 * Envía una solicitud AJAX POST al backend.
 *
 * @param {Object} options - Opciones para la solicitud AJAX.
 * @param {string} options.url - Ruta relativa al archivo PHP que manejará la solicitud.
 * @param {Object} options.data - Objeto con los datos a enviar en la solicitud.
 * @param {Function} [options.successCallback] - Función a ejecutar si la respuesta es exitosa.
 * @param {Function} [options.errorCallback] - Función a ejecutar si hay un error en la respuesta.
 * @param {boolean} [options.verbose=false] - Si es true, se activan los logs en consola.
 */
function sendAjaxRequest({ url, data = {}, successCallback, errorCallback, verbose = false }) {
    const isFormData = data instanceof FormData;

    $.ajax({
        type: "POST",
        url: SITE_URL + url,
        data: data,
        processData: !isFormData,
        contentType: isFormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',

        success: function (res) {
            // Asegurarse de que la respuesta sea válida JSON (no texto plano)
            let data = handleJSONResponse(res);

            // Si la respuesta no tiene errores
            if (data && (data.error === false || data.error === null)) {
                successCallback?.(res);
            } else {
                // Si error es true, mandamos la respuesta al errorCallback
                errorCallback?.(data.message || res);
            }

            if (verbose) {
                console.group('%c[SERVIDOR]', 'color:#30b24c;font-weight:bold;');
                console.log("Respuesta del servidor:", data);
                console.groupEnd();
            }
        },
        error: function (xhr, status, error) {
            const msg = `Error HTTP: ${status} - ${error}`;

            if (verbose) {
                console.group('%c[SERVIDOR]', 'color:#df040e;font-weight:bold;');
                console.error(msg);
                console.groupEnd();
            }

            errorCallback?.(msg);
        }
    });
}

// Total de la factura
export function calculateTotalInvoice(bonus = 0) {
    // Determinar acción según la URL
    let action, invoice_id, order_id;

    if (pageURL.includes("invoices/addpurchase")) {
        action = 'precios_detalle_temp';
    } else if (pageURL.includes("invoices/edit_quote")) {
        action = 'total_cotizacion';
        invoice_id = $("#quote_id").val();
    } else if (pageURL.includes("invoices/edit")) {
        action = 'precios_detalle_venta';
        invoice_id = $("#invoice_id").val();
    } else if (pageURL.includes("invoices/add_order") || pageURL.includes("invoices/pos")) {
        action = 'precios_ordenes_ventas';
        order_id = $("#order_id").val();
    }

    // Cargar totales según acción
    loadInvoiceTotals(action, invoice_id, order_id);

    // Función para cargar totales de la factura
    function loadInvoiceTotals(action, invoice_id, order_id) {
        if (!action) return; // si no hay action, salir

        sendAjaxRequest({
            url: "services/invoices.php",
            data: { action, invoice_id, order_id },
            successCallback: (res) => {

                const data = JSON.parse(res)[0];

                // 1. Obtener los valores crudos de precios, impuestos y descuentos
                const rawPrice = parseFloat(data.precios) || 0;
                const rawTaxes = parseFloat(data.taxes || 0);
                const rawDiscount = parseFloat(data.descuentos || 0);

                // 2. Cálculo del valor total
                const totalValue = rawPrice + rawTaxes - rawDiscount;

                // 3. Formateo de los valores de descuento, impuestos y subtotal
                const subtotal = format.format(data.precios);
                const discount = format.format(data.descuentos || 0);
                const taxes = format.format(data.taxes || 0);
                const total = isNaN(totalValue) ? '0.00' : format.format(totalValue);

                // 4. Eliminar las comas del total para usarlo en cálculos o almacenamiento
                const totalRaw = total.replace(/,/g, "");

                // Asignar valores al formulario principal
                $('#total_price').val(totalRaw);
                $('#in-subtotal').val(subtotal);
                $('#in-taxes').val(taxes);
                $('#in-discount').val(discount);
                $('#in-total').val(total);

                // Inicializar valores comunes
                $('#cash-received').val('0.00');
                $('#credit-received').val('0.00');

                // Insertar en el POS (Punto de venta)
                if (discount.replace(/,/g, "") > 0) {
                    $('#pos-discount').css('display', 'flex')
                    $('#pos-subtotal').css('display', 'flex')
                }

                if (taxes.replace(/,/g, "") > 0) {
                    $('#pos-taxes').css('display', 'flex')
                    $('#pos-subtotal').css('display', 'flex')
                }

                $('.pos-subtotal').text('$' + subtotal);
                $('.pos-discount').text('$' + discount);
                $('.pos-taxes').text('$' + taxes);
                $('.pos-total').text('$' + total);

                // Total del pos
                $('#total_pos').val(totalRaw);

                // Modal Factura Editar
                if (pageURL.includes("invoices/edit")) {
                    setCashModal(data.total, data.pendiente, data.recibido);
                } else {
                    setCashModalWithBonus(totalRaw);
                }

                // Modal Factura a crédito
                setCreditModal(totalRaw);

                // Botones y validaciones
                toggleElementsByTotal(totalValue);
            },
            verbose: false
        })
    }

    // Establecer valores en el modal de factura al contado (edit)
    function setCashModal(total, pending, received) {
        $('#cash-topay, #cash-topay2').val(total);
        $('#cash-pending, #cash-pending2').val(pending);
        $('#cash-received, #cash-received2').val(received);
    }

    // Establecer valores en el modal de factura al contado con bono
    function setCashModalWithBonus(total) {
        const bonusValue = parseFloat(bonus) || 0;
        const totalAfterBonus = (parseFloat(total) - bonusValue).toFixed(2);

        $('#cash-bonus').val(format.format(bonusValue));
        $('#cash-topay, #cash-pending').val(format.format(totalAfterBonus));
    }

    // Establecer valores en el modal de factura a crédito
    function setCreditModal(total) {
        $('#credit-topay, #credit-pending').val(format.format(total));
    }

    // Mostrar/ocultar elementos según el total
    function toggleElementsByTotal(total) {
        const isValid = !isNaN(total) && total > 0;
        $('#cash-in-finish, #cash-in-finish-receipt').toggle(isValid);
        $('.pay').toggle(isValid);
    }
}


/**
 * Muestra una alerta con el monto de devolución en estilo limpio y con animación de latido solo en el valor.
 * Usa Alertify.js.
 * 
 * @param {number|string} data - Monto a devolver.
 */
export function cashBack(data, timeout = 10000) {
    const alert = alertify.alert(
        `
    <div class="cashback-modal">
      <div class="cashback-header">
        <i class="fa fa-hand-holding-usd cashback-icon"></i>
        <span class="cashback-title">Cambio a devolver</span>
      </div>
      <div class="cashback-body">
        <span class="cashback-currency">$</span>
        <span class="cashback-amount">${parseFloat(data).toFixed(2)}</span>
      </div>
    </div>
    `
    ).set({
        basic: true,
        movable: false,
        closable: true,
        transition: 'fade'
    });

    // ⏱ Cerrar automáticamente
    setTimeout(() => { alert.close(); }, timeout);
}


/**============================================================= 
* WEBSOCKET
===============================================================*/

// Variable global para almacenar la conexión WebSocket
let ws = null;
let wsConnected = false;
let wsURL;

// Función para inicializar la conexión WebSocket
export function initWebSocket() {
    // Si ya existe una conexión, no la volvemos a crear
    if (ws !== null) {
        console.log('Conexión WebSocket ya establecida.');
        return ws;  // Retornamos la conexión existente
    }



    if (location.hostname === 'localhost' || location.hostname === '127.0.0.1') {
        // DESARROLLO LOCAL
        wsURL = 'ws://127.0.0.1:3001';
    } else {
        // PRODUCCIÓN
        const protocol = location.protocol === 'https:' ? 'wss://' : 'ws://';
        wsURL = protocol + 'ws.wsistems.com' + '/ws/';
    }

    // Crear la nueva conexión WebSocket
    ws = new WebSocket(wsURL);

    // Configurar eventos de WebSocket
    ws.onopen = () => {
        console.group('%c[WEBSOCKET]', 'color:#007bff;font-weight:bold;');
        console.log('Conexión establecida con', wsURL);
        console.groupEnd();
        wsConnected = true;
    };

    ws.onclose = () => {
        console.group('%c[WEBSOCKET]', 'color:#df040e;font-weight:bold;');
        console.log('No se pudo establecer la conexión con', wsURL);
        console.groupEnd();
        wsConnected = false;
    };

    ws.onerror = () => {
        wsConnected = false;
    };

    return ws;  // Retornamos la conexión WebSocket para su uso global
}

// Estado de la conexión
export function isWebSocketConnected() {
    return wsConnected;  // Retorna si la conexión está activa
}

/**============================================================= 
* FUNCIONES WEBSOCKET
===============================================================*/

/**
 * Obtiene el valor más reciente del total de ventas mediante una solicitud AJAX y actualiza el DOM.
 * 
 * Esta función realiza una solicitud AJAX al servidor para obtener el valor actualizado del total de ventas.
 * Una vez que se recibe la respuesta, se actualiza el contenido del elemento `#total-purchase` con el 
 * nuevo total y también se actualiza el atributo `data-title` con el valor formateado a dos decimales.
 * 
 * @function getUpdatedTotal
 * @returns {void} 
 */
export function getUpdatedTotal() {
    console.log("ejecutando")
    sendAjaxRequest({
        url: "services/home.php",
        data: {
            action: "total_vendido"
        },
        successCallback: (res) => {
            const data = JSON.parse(res)[0];

            // Actualizamos el contenido del span con el nuevo valor del total de ventas
            $('#total-purchase').html(`$${format.format(data.total)}`);

            // Actualizamos el atributo 'data-title' con el valor total, formateado a 2 decimales
            $('#total-purchase').attr('data-title', parseFloat(data.total).toFixed(2));
        }
    })
}