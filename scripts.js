navigator.serviceWorker && navigator.serviceWorker.register("../sw.js"); // Activacion del service worker
const PRINTER_SERVER = "http://localhost:81/tickets/"; // URL local de la impresora

let basePath = '/';

if (window.location.hostname === 'localhost') {
    const pathParts = window.location.pathname.split('/');
    basePath = '/' + pathParts[1] + '/'; // Detecta el nombre
}

const SITE_URL = window.location.protocol + '//' + window.location.host + basePath;

// URL de la API
const API_URL = window.location.hostname === 'localhost'
    ? 'http://localhost:3001/'
    : 'https://ws.wsistems.com/';

let pageURL = $(location).attr("pathname");
const format = new Intl.NumberFormat('en'); // Formato 0,000

let toastTimeout = null;
/**
 * 
 * @param {any} response - respuesta del mensaje
 * @param {string} type - tipo de notificacion
 * @param {int} duration  - duracion de la notificacion
 */
function notifyAlert(message, type = 'success', duration = 3000) {

    // Evitar llamadas simultáneas
    if (toastTimeout) {
        clearTimeout(toastTimeout);
        toastTimeout = null;
    }

    try {
        mdtoast(message, {
            type,
            duration,
            position: 'bottom right'
        });
    } catch (e) {
        console.warn('Toast error:', e);
    }

    // Bloqueo temporal (anti-spam)
    toastTimeout = setTimeout(() => {
        toastTimeout = null;
    }, duration);
}


// Variable global para acceder a las instancias DataTable desde cualquier parte
const dataTablesInstances = {};

/**
 * Obtiene el valor de un parámetro en la URL.
 * @param {string} name - Nombre del parámetro.
 * @returns {string|null} Valor decodificado del parámetro o null si no existe.
 */
$.urlParam = function (name) {
    const results = new RegExp(`[?&]${name}=([^&#]*)`).exec(window.location.href);
    return results ? decodeURIComponent(results[1]) : null;
};

/**
 * Agrega un comando de atajo de teclado para redirigir a una página específica.
 * 
 * @param {string} keyCombo La combinación de teclas para activar el comando, en formato 'Ctrl+F'.
 * @param {string} url La URL a la que redirigir al usuario cuando se active el comando.
 * @param {boolean} [preventDefault=true] Si se debe prevenir el comportamiento predeterminado del navegador (por ejemplo, la búsqueda).
 */
function addKeyboardCommand(keyCombo, url, preventDefault = true) {
    // Obtiene la tecla que se usará junto a Ctrl (por ejemplo "s" en "ctrl+s")
    const key = keyCombo.toLowerCase().replace('ctrl+', '');

    // Escucha el evento global
    $(document).keydown(function (e) {
        // Ignorar si el foco está dentro de un input, textarea o select
        if ($(e.target).is('input, textarea, select')) {
            return;
        }

        // Verifica si se presionó Ctrl y la tecla correcta
        if (e.ctrlKey && e.key.toLowerCase() === key) {
            if (preventDefault) e.preventDefault(); // Evita acción del navegador
            window.location.href = url; // Redirige a la URL indicada
        }
    });
}


/**
 * Agrega un comando de atajo de teclado para abrir una modal específica.
 * 
 * @param {string} keyCombo La combinación de teclas para activar el comando, en formato 'Ctrl+F'.
 * @param {string} modalId El ID de la modal que se desea abrir.
 * @param {boolean} [preventDefault=true] Si se debe prevenir el comportamiento predeterminado del navegador (por ejemplo, la búsqueda).
 */
function addKeyboardCommandForModal(keyCombo, modalId, preventDefault = true) {
    // Extrae la tecla (por ejemplo "n" en "ctrl+n")
    const key = keyCombo.toLowerCase().replace('ctrl+', '');

    $(document).keydown(function (e) {
        // Evita activar el comando si el foco está en un campo editable
        if ($(e.target).is('input, textarea, select')) {
            return;
        }

        // Verifica si se presionó Ctrl y la tecla indicada
        if (e.ctrlKey && e.key.toLowerCase() === key) {
            if (preventDefault) e.preventDefault(); // Evita comportamiento del navegador
            $('#' + modalId).modal('show'); // Muestra el modal Bootstrap
        }
    });
}


/**
 * Inicializa una tabla DataTable personalizada con carga de datos vía AJAX.
 *
 * @param {Object} config - Objeto de configuración.
 * @param {string} config.selector - Selector CSS del contenedor de la tabla (por ejemplo, "#miTabla").
 * @param {string} config.ajaxUrl - Ruta relativa del endpoint al que se hará la solicitud AJAX.
 * @param {string} config.ajaxAction - Acción que se enviará como parte de los datos del request POST.
 * @param {Array} config.columns - Definición de las columnas del DataTable (coincide con el formato requerido por DataTables).
 * @param {number} [config.loadTime=300] - Tiempo en milisegundos para mostrar el spinner antes de iniciar la petición AJAX.
 * @param {boolean} [config.hideZeroRecordsMessage=false] - Ocultar mensaje "sin registros" cuando no hay datos.
 * @param {Array<number>} [config.hiddenColumns=[]] - Índices de columnas a ocultar con clase CSS.
 * @param {Object} [config.ajaxParams={}] - Parámetros extras que se agregarán a la petición AJAX.
 * @param {any} [config.options] - Otras opciones opcionales compatibles con DataTables.
 *
 * @returns {DataTable|null} Instancia de DataTable o null si hay parámetros inválidos.
 */
function initCustomDataTable({
    selector,
    ajaxUrl,
    ajaxAction,
    columns,
    loadTime = 300,
    hideZeroRecordsMessage = false,
    hiddenColumns = [], // índices a ocultar con clase
    ajaxParams = {}, // Parametros
    ...options
}) {
    if (!selector || !ajaxUrl || !ajaxAction || !Array.isArray(columns)) {
        console.error('initCustomDataTable: parámetros inválidos');
        return null;
    }

    const $tbody = () => $(`${selector} tbody`);
    const $thead = () => $(`${selector} thead`);

    // Aplicar clase 'hide-cell' a los <th> correspondientes
    $(document).ready(() => {
        hiddenColumns.forEach(index => {
            $thead().find("th").eq(index).addClass("hide-cell");
        });
    });

    return $(selector).DataTable({
        serverSide: true,
        processing: false,
        language: {
            lengthMenu: "_MENU_",
            pageLength: 100,
            zeroRecords: hideZeroRecordsMessage ? "" : "Aún no tienes datos para mostrar",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Página no disponible",
            infoFiltered: "(Filtrado de _MAX_ registros)",
            search: "Buscar:",
            processing: "Buscando...",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "<i class='fas fa-caret-right'></i>",
                previous: "Anterior"
            }
        },
        ajax: (data, callback) => {
            $tbody().html(`
                <tr>
                    <td colspan="100%">
                        <div class="spinner-container">
                            <div class="spinner"></div>
                            <div style="margin-top: 10px;">Cargando datos...</div>
                        </div>
                    </td>
                </tr>
            `);

            setTimeout(() => {

                const extraParams = typeof ajaxParams === 'function' ?
                    ajaxParams() :
                    ajaxParams;

                $.ajax({
                    url: SITE_URL + ajaxUrl,
                    type: 'POST',
                    data: {
                        action: ajaxAction,
                        id: $.urlParam('id') || $.urlParam('o'), // parámetros url
                        ...extraParams, // parámetros personalizados
                        ...data // parámetros internos de DataTables

                    },
                    dataType: 'json',
                    success: response => {

                        const json = typeof response === 'string' ?
                            JSON.parse(response) :
                            response;

                        callback(json);

                        if (hideZeroRecordsMessage && json.data.length === 0) {
                            setTimeout(() => {
                                $tbody().empty();
                            }, 50);
                        }
                    },
                    error: (xhr, status, error) => {
                        console.error("Error en AJAX:", status, error);
                        console.error("Respuesta del servidor:", xhr.responseText);

                        $tbody().html(`
                            <tr>
                                <td colspan="100%">
                                    <div class="error-message" style="color: red; padding: 20px; text-align: center;">
                                        Error al cargar los datos. Por favor, intenta nuevamente.
                                    </div>
                                </td>
                            </tr>
                        `);
                        callback({
                            data: [],
                            recordsTotal: 0,
                            recordsFiltered: 0
                        });
                    }
                });
            }, loadTime);
        },
        columns,
        createdRow: function (row, data, dataIndex) {
            hiddenColumns.forEach(index => {
                $(row).find('td').eq(index).addClass('hide-cell');
            });
        },
        ...options
    });
}

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


function loadTables(tableConfigs) {
    tableConfigs.forEach(config => {
        const { id, url, action, columns, hiddenColumns, params, ...rest } = config;

        const columnDefs = columns.map(col =>
            col === 'acciones' ?
                { data: col, orderable: false, searchable: false } :
                { data: col }
        );

        const tableId = id.replace('#', '');

        dataTablesInstances[tableId] = initCustomDataTable({
            selector: id,
            ajaxUrl: url,
            ajaxAction: action,
            columns: columnDefs,
            hiddenColumns: hiddenColumns,
            ajaxParams: params,
            ...rest
        });
    });
}

$(document).ready(function () {

    // Valores de la sección agregar producto
    $("#inputMinCantidad").val(1);
    $("#inputCantidad").val(1);

    // Atajos de tecla enter
    $("body").keyup(function (e) {
        if (e.keyCode == 13) {
            if (pageURL.includes("products/add")) {
                $("#createProduct").click();
            }
        }
    });

    // Notificaciones del admin bar
    setInterval(function () {
        $(".out-stock p").fadeTo(1200, 0.1).fadeTo(1200, 1);
        $(".num-order p").fadeTo(1200, 0.1).fadeTo(1200, 1);
    }, 1600);

    // Alerta de cuando se pierde la conexión a internet
    function handleConnectionChange() {
        const isOnline = navigator.onLine;
        const message = isOnline ? "Conexión establecida" : "Conexión perdida";

        mdtoast(message, {
            interaction: true,
            interactionTimeout: 1500,
            position: "bottom right",
            actionText: "OK!"
        });
    }

    // Escuchar cambios en el estado de conexión
    window.addEventListener("online", handleConnectionChange);
    window.addEventListener("offline", handleConnectionChange);

    /*
    | ------------------------------------------------------
    | STEPS LIST CERTIFICACION DGII
    | ------------------------------------------------------
    */

    function showStep(stepId) {
        // Ocultar todos los steps
        $('.approval-content .step').hide();
        // Remover clase active de todos
        $('.approval-content .step').removeClass('active');

        // Mostrar el step seleccionado
        $('#' + stepId).show();
        // Agregar clase active al step visible
        $('#' + stepId).addClass('active');
    }

    $('.steps-list li').on('click', function () {
        // Obtener el step
        const step = $(this).data('step');

        // Quitar active de todos los li y agregar al seleccionado
        $('.steps-list li').removeClass('active');
        $(this).addClass('active');

        // Mostrar el contenido correspondiente
        showStep('step' + step);
    });


    /*
    | ------------------------------------------------------
    | MENU ACCORDEON SIDEBAR
    | ------------------------------------------------------
    */

    $(function () {
        // Función de Acordeón
        var Accordion = function (el, multiple) {
            this.el = el || {};
            this.multiple = multiple || false;

            // Variables privadas
            var links = this.el.find(".link");

            // Evento de clic para abrir y cerrar los submenús
            links.on("click", {
                el: this.el,
                multiple: this.multiple,
            }, this.dropdown);
        };

        Accordion.prototype.dropdown = function (e) {
            var $el = e.data.el;
            var $this = $(this),
                $next = $this.next();

            $next.slideToggle();
            $this.parent().toggleClass("open");

            // Si no es un menú múltiple, cierra los otros submenús
            if (!e.data.multiple) {
                $el.find(".submenu").not($next).slideUp().parent().removeClass("open");
            }
        };

        // Inicializar el acordeón en ambos menús
        new Accordion($("#accordion"), false);
        new Accordion($("#accordion-movil"), false);

        // Mantener el menú abierto según la URL
        const menuMap = [
            { keywords: ["invoices/index", "invoices/edit", "invoices/addpurchase", "invoices/index_repair", "invoices/repair_edit", "payments/index", "payments/add", "invoices/quotes", "invoices/quote", "invoices/edit_quote", "invoices/orders", "invoices/add_order"], dropdown: "dropdown-1" },
            { keywords: ["bills"], dropdown: "dropdown-2" },
            { keywords: ["workshop"], dropdown: "dropdown-1" },
            { keywords: ["products", "services/index", "services/add", "price_list", "categories", "taxes", "offers", "pieces", "warehouses", "positions", "brands"], dropdown: "dropdown-3" },
            { keywords: ["contacts"], dropdown: "dropdown-4" },
            { keywords: ["reports"], dropdown: "dropdown-5" },
        ];

        menuMap.forEach(({ keywords, dropdown }) => {
            if (keywords.some(keyword => pageURL.includes(keyword))) {
                $(`.${dropdown} ul.submenu`).css("display", "block");
                $(`.accordion .${dropdown}`).addClass("open");
            }
        });

        // Cerrar el menú cuando se haga clic fuera de él
        $(document).click(function (event) {
            // Verificar si el clic no es dentro del menú o del botón de toggle
            if (!$(event.target).closest('#accordion-movil').length &&
                !$(event.target).closest('#menuToggle').length &&
                !$(event.target).closest('.menu-movil').length) {

                // Cierra el menú y desmarca el checkbox
                $('#accordion-movil').removeClass('open');
                $('#menuToggle input').prop('checked', false);
            }
        });

        // Prevenir que el clic dentro del menú o del botón de toggle lo cierre
        $('#accordion-movil').click(function (event) {
            event.stopPropagation();
        });
        $('#menuToggle').click(function (event) {
            event.stopPropagation();
        });
    });


    /**============================================================= 
    * MENU RAPIDO
    ===============================================================*/

    // Menú rápido (desliza desde el lado derecho)
    $("#bar-menu").on("click", (e) => {
        e.preventDefault();

        $(".nav-container").toggleClass("open");
    });

    // Cerrar el menú si se hace clic fuera de él
    $(document).on("click", function (event) {
        if (!$(event.target).closest(".nav-container, #bar-menu").length) {
            $(".nav-container").removeClass("open");
        }
    });


    /**============================================================= 
    * BUSCADOR GLOBAL
    ===============================================================*/

    const result = document.getElementById('search_result');

    $('#keyword').on('keyup', (e) => {
        e.preventDefault();

        const format = new Intl.NumberFormat('en'); // Formato 0,000
        const q = $('#keyword').val().trim();

        if (q.length < 0) {
            result.innerHTML = '';
            return;
        }

        sendAjaxRequest({
            url: "src/modules/home/home.repository.php",
            data: {
                action: 'buscador',
                search: q
            },
            successCallback: (res) => {
                var data = JSON.parse(res)

                result.innerHTML = '';
                data.forEach(item => {
                    const li = document.createElement('li');
                    const a = document.createElement('a');

                    if (item.tipo == "Cliente") {
                        a.textContent = `${item.tipo}: ${item.nombre} ${item.apellidos}`;
                        a.href = SITE_URL + `contacts/edit_customer&id=${item.id}`;
                    } else if (item.tipo == "Producto") {
                        a.textContent = `${item.tipo}: ${item.nombre} $${format.format(item.precio)}`;
                        a.href = SITE_URL + `products/edit&id=${item.id}`;
                    } else if (item.tipo == "Pieza") {
                        a.textContent = `${item.tipo}: ${item.nombre} $${format.format(item.precio)}`;
                        a.href = SITE_URL + `pieces/edit&id=${item.id}`;
                    } else if (item.tipo == "Proveedor") {
                        a.textContent = `${item.tipo}: ${item.nombre}`;
                        a.href = SITE_URL + `contacts/edit_provider&id=${item.id}`;
                    } else if (item.tipo == "Factura_venta") {
                        a.textContent = `${item.tipo}: FT-00${item.id} ${item.nombre} ${item.apellidos}`;
                        a.href = SITE_URL + `invoices/edit&id=${item.id}`;
                    } else if (item.tipo == "Orden_reparacion") {
                        a.textContent = `${item.tipo}: OR-00${item.id} ${item.nombre} ${item.apellidos}`;
                        a.href = SITE_URL + `invoices/addrepair&id=${item.id}`;
                    } else if (item.tipo == "Factura_reparacion") {
                        a.textContent = `${item.tipo}: RP-00${item.id} ${item.nombre} ${item.apellidos}`;
                        a.href = SITE_URL + `invoices/repair_edit&o=${item.orden_id}&f=${item.id}`;
                    }

                    a.style.textDecoration = "none"; // Opcional: quitar subrayado
                    a.style.color = "#333"; // Opcional: color del texto

                    li.appendChild(a);
                    result.appendChild(li);
                })
            }
        })
    });


    /**============================================================= 
    * LISTA DE TABLAS DATATABLE
    ===============================================================*/

    // obtener las columnas de las variantes
    function getVariantTableColumns() {
        const tipo = $('input[name="tipovariante"]:checked').val();

        const deviceColumns = ['proveedor', 'serial', 'color', 'costo', 'caja', 'entrada', 'acciones'];
        const productColumns = ['proveedor', 'sabor', 'costo', 'entrada', 'acciones'];

        return tipo === 'dispositivo' ? deviceColumns : productColumns;
    }

    // Configuración de DataTable Server-Side para las tablas
    const tableConfigs = [{
        id: '#invoices',
        url: 'src/modules/invoices/invoices.repository.php',
        action: 'index_facturas_ventas',
        columns: [
            'factura_venta_id', 'nombre', 'fecha_factura', 'total', 'recibido', 'pendiente', 'bono', 'nombre_estado', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [3, 4, 5, 6]
    },
    {
        id: '#today',
        url: 'src/modules/reports/reports.repository.php',
        action: 'index_ventas_hoy',
        columns: [
            'id', 'nombre', 'fecha', 'total', 'recibido', 'pendiente', 'estado', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
    },
    {
        id: '#customers',
        url: 'src/modules/contacts/contacts.repository.php',
        action: 'index_clientes',
        columns: [
            'id', 'nombre', 'direccion', 'cedula', 'telefono', 'fecha', 'acciones'
        ],
        hiddenColumns: [2, 3]
    },
    {
        id: '#providers',
        url: 'src/modules/contacts/contacts.repository.php',
        action: 'index_proveedores',
        columns: [
            'id', 'nombre', 'correo', 'telefono', 'fecha', 'acciones'
        ],
        hiddenColumns: [0, 2]
    },
    {
        id: '#workshop',
        url: 'src/modules/workshop/workshop.repository.php',
        action: 'index_taller',
        columns: [
            'orden', 'nombre', 'equipo', 'fecha_entrada', 'fecha_salida', 'condicion', 'estado', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [3, 4, 5]
    },
    {
        id: '#products',
        url: 'src/modules/products/products.repository.php',
        action: 'index_productos',
        columns: [
            'codigo', 'nombre', 'categoria', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
        ],
        hiddenColumns: [0, 2, 4]
    },
    {
        id: '#invoicesrp',
        url: 'src/modules/repair/repair.repository.php',
        action: 'index_facturas_reparacion',
        columns: [
            'id', 'nombre', 'fecha', 'total', 'recibido', 'pendiente', 'estado', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [3, 4, 5]
    },
    {
        id: '#quotes',
        url: 'src/modules/invoices/invoices.repository.php',
        action: 'index_cotizaciones',
        columns: [
            'id', 'nombre', 'fecha', 'total', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [3]
    },
    {
        id: '#payments',
        url: 'src/modules/payments/payments.repository.php',
        action: 'index_pagos_facturas_ventas',
        columns: [
            'pago_id', 'factura_id', 'nombre', 'recibido', 'observacion', 'fecha', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [0, 4, 5]
    },
    {
        id: '#ordersc',
        url: 'src/modules/bills/bills.repository.php',
        action: 'index_ordenes_compras',
        columns: [
            'orden_id', 'proveedor', 'articulos', 'fecha', 'expiracion', 'estado', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [2, 4, 5]
    },
    {
        id: '#invoicesp',
        url: 'src/modules/bills/bills.repository.php',
        action: 'index_facturas_proveedores',
        columns: [
            'id', 'proveedor', 'fecha', 'total', 'pagado', 'por_pagar', 'estado', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [0,]
    },
    {
        id: '#bills',
        url: 'src/modules/bills/bills.repository.php',
        action: 'index_gastos',
        columns: [
            'id', 'proveedor', 'gastos', 'fecha', 'total', 'pagado', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [1, 5]
    },
    {
        id: '#payments_providers',
        url: 'src/modules/payments/payments.repository.php',
        action: 'index_pagos_proveedores',
        columns: [
            'pago_id', 'factura', 'proveedor', 'recibido', 'observacion', 'fecha', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
    },
    {
        id: '#pieces',
        url: 'src/modules/pieces/pieces.repository.php',
        action: 'index_piezas',
        columns: [
            'id', 'nombre', 'categoria', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
        ],
        hiddenColumns: [0, 2, 4]
    },
    {
        id: '#minStockProduct',
        url: 'src/modules/products/products.repository.php',
        action: 'index_casi_agotados',
        columns: [
            'cod_producto', 'nombre', 'categoria', 'almacen', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
        ]
    },
    {
        id: '#services',
        url: 'src/modules/services/services.repository.php',
        action: 'index_servicios',
        columns: [
            'servicio_id', 'nombre_servicio', 'costo', 'precio', 'acciones'
        ]
    },
    {
        id: '#users',
        url: 'src/modules/users/users.repository.php',
        action: 'index_usuarios',
        columns: [
            'usuario_id', 'nombre', 'rol', 'estado', 'fecha', 'acciones'
        ],
        hiddenColumns: [0]
    },
    {
        id: '#brands',
        url: 'src/modules/workshop/workshop.repository.php',
        action: 'index_marcas',
        columns: [
            'nombre_marca', 'fecha', 'acciones'
        ]
    },
    {
        id: '#pricelists',
        url: 'src/modules/price_lists/price_lists.repository.php',
        action: 'index_lista_precios',
        columns: [
            'id', 'nombre_lista', 'descripcion', 'acciones'
        ]
    },
    {
        id: '#warehouses',
        url: 'src/modules/warehouses/warehouses.repository.php',
        action: 'index_almacen',
        columns: [
            'id', 'nombre_almacen', 'descripcion', 'fecha', 'acciones'
        ]
    },
    {
        id: '#categories',
        url: 'src/modules/categories/categories.repository.php',
        action: 'index_categorias',
        columns: [
            'id', 'nombre_categoria', 'descripcion', 'fecha', 'acciones'
        ]
    },
    {
        id: '#positions',
        url: 'src/modules/positions/positions.repository.php',
        action: 'index_posiciones',
        columns: [
            'id', 'referencia', 'fecha', 'acciones'
        ]
    },
    {
        id: '#offers',
        url: 'src/modules/offers/offers.repository.php',
        action: 'index_ofertas',
        columns: [
            'id', 'nombre', 'valor', 'descripcion', 'fecha', 'acciones'
        ]
    },
    {
        id: '#taxs',
        url: 'src/modules/taxes/taxes.repository.php',
        action: 'index_impuestos',
        columns: [
            'id', 'nombre', 'valor', 'descripcion', 'fecha', 'acciones'
        ]
    },
    {
        id: '#bonus',
        url: 'src/modules/config/config.repository.php',
        action: 'index_bonos',
        columns: [
            'id', 'cliente', 'valor', 'usuario', 'fecha', 'acciones'
        ],
        order: [
            [0, 'desc']
        ],
    },

    // Cargar detalles
    {
        id: '#detailTemp',
        url: 'src/modules/invoices/invoices.repository.php',
        action: 'cargar_detalle_temporal',
        columns: ['descripcion', 'cantidad', 'precio', 'impuesto', 'descuento', 'importe', 'acciones'],
        hiddenColumns: [3],
        paging: true,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#editInvoice',
        url: 'src/modules/invoices/invoices.repository.php',
        action: 'cargar_detalle_facturas',
        columns: ['descripcion', 'cantidad', 'precio', 'impuesto', 'descuento', 'total', 'acciones'],
        hiddenColumns: [3],
        paging: true,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#addrepair',
        url: 'src/modules/repair/repair.repository.php',
        action: 'cargar_ordenrp',
        columns: ['descripcion', 'cantidad', 'precio', 'descuento', 'total', 'acciones'],
        paging: true,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#editrepair',
        url: 'src/modules/repair/repair.repository.php',
        action: 'cargar_facturarp',
        columns: ['descripcion', 'cantidad', 'precio', 'descuento', 'total', 'acciones'],
        paging: true,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#variantList',
        url: 'src/modules/products/products.repository.php',
        action: 'cargar_variantes',
        columns: getVariantTableColumns(),
        paging: true,
        searching: true,
        ordering: false,
        hideZeroRecordsMessage: true,
        info: false
    },
    {
        id: '#cashClosing',
        url: 'src/modules/reports/reports.repository.php',
        action: 'index_cierre_caja',
        columns: ['id', 'cajero', 'total_real', 'gastos', 'diferencia', 'fecha_apertura', 'fecha_cierre', 'estado', 'acciones'],
        order: [
            [0, 'desc']
        ],
        hiddenColumns: [0, 2, 3, 4, 7]
    },
    {
        id: '#orders',
        url: 'src/modules/invoices/invoices.repository.php',
        action: 'index_ordenes',
        columns: [
            'comanda_id', 'nombre', 'telefono', 'entrega', 'fecha', 'estado', 'orden', 'acciones'
        ],
        order: [
            [0, 'desc']
        ]
    },
    {
        id: '#addorder',
        url: 'src/modules/invoices/invoices.repository.php',
        action: 'cargar_detalle_orden',
        columns: [
            'descripcion', 'cantidad', 'precio', 'impuesto', 'descuento', 'importe', 'acciones'
        ],
        hiddenColumns: [3],
        paging: false,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#customer_history',
        url: 'src/modules/contacts/contacts.repository.php',
        action: 'historial_cliente',
        columns: [
            'factura_id', 'item', 'cantidad', 'precio', 'descuento', 'total', 'fecha'
        ],
        hiddenColumns: [1, 2, 4, 5, 6],
        paging: true,
        searching: false,
        ordering: true,
        info: false
    },
    {
        id: '#labels',
        url: 'src/modules/config/config.repository.php',
        action: 'cargar_etiquetas',
        columns: ['id', 'nombre', 'ancho', 'alto', 'impresora', 'acciones'],
        paging: true,
        searching: true,
        ordering: true,
        info: false
    },
    {
        id: '#printers',
        url: 'src/modules/config/config.repository.php',
        action: 'cargar_printers',
        columns: ['usuario', 'printer', 'tipo', 'lenguaje', 'tamaño', 'acciones'],
        paging: true,
        searching: true,
        ordering: true,
        info: false
    },
    {
        id: '#ecfs',
        url: 'src/modules/ecf/ecf.repository.php',
        action: 'index_facturas_emitidas',
        columns: ['id', 'nombre_cliente', 'creado_en', 'monto_total', 'encf', 'vendedor', 'estado', 'acciones'],
        order: [
            [0, 'desc']
        ]
    }

    ];


    // Hacer autofocus al abrir una modal

    $('#add_detail').on('shown.bs.modal', function () {
        $('#code').trigger('focus');
    });

    $('#modalCashOpening').on('shown.bs.modal', function () {
        $('#cash_initial').trigger('focus');
    });

    $('#modalCashClosing').on('shown.bs.modal', function () {
        $('#current_total').trigger('focus');
    });

    $('#credit_invoice').on('shown.bs.modal', function () {
        $('#credit-pay').trigger('focus');
    });

    /**============================================================= 
    * CARGAR FECHA ACTUAL EN INPUTS
    ===============================================================*/

    $(function () {

        // Función que obtiene la fecha desde el servidor vía AJAX
        function getFechaServidor(callback) {
            $.ajax({
                url: SITE_URL + "src/modules/config/config.repository.php",
                method: "POST", // Usualmente se usa POST, si es GET entonces cambialo.
                data: {
                    action: 'fecha_actual'
                },
                success: function (res) {
                    const data = JSON.parse(res); // Convertir JSON a objeto
                    callback(data); // Pasar el objeto con ambas fechas
                },
                error: function (xhr, status, error) {
                    console.error("Error al obtener la fecha del servidor:", error);
                }
            });
        }

        // CASH MODAL
        $(document).on('shown.bs.modal', '#cash_invoice', function () {
            getFechaServidor(function (data) {
                $('#cash-in-date').val(data.fecha); // Asignar solo la fecha (YYYY-MM-DD)
            });
        });

        // CREDIT MODAL
        $(document).on('shown.bs.modal', '#credit_invoice', function () {
            getFechaServidor(function (data) {
                $('#credit-in-date').val(data.fecha); // Asignar solo la fecha (YYYY-MM-DD)
            });
        });

        // CREDIT MODAL POS
        $(document).on('shown.bs.modal', '#pos-credit', function () {
            getFechaServidor(function (data) {
                $('#modal-date').val(data.fecha); // Asignar solo la fecha (YYYY-MM-DD)
            });
        });

        // CIERRE DE CAJA
        $(document).on('shown.bs.modal', '#modalCashClosing', function () {
            getFechaServidor(function (data) {
                console.log("Fecha y hora del servidor (Cierre de Caja):", data.fecha_completa); // Fecha completa
                var fechaFormateada = data.fecha_completa.replace(' ', 'T').slice(0, 16); // Reemplazar espacio por T y recortar a YYYY-MM-DDTHH:MM
                $('#closing_date').val(fechaFormateada); // Asignar el valor formateado al input
            });
        });

        // APERTURA DE CAJA
        $(document).on('shown.bs.modal', '#modalCashOpening', function () {
            getFechaServidor(function (data) {
                console.log("Fecha y hora del servidor (Abrir caja):", data.fecha_completa); // Fecha completa
                var fechaFormateada = data.fecha_completa.replace(' ', 'T').slice(0, 16); // Reemplazar espacio por T y recortar a YYYY-MM-DDTHH:MM
                $('#opening').val(fechaFormateada); // Asignar el valor formateado al input
            });
        });

    });

    /**============================================================= 
    * LECTOR DE CODIGO DE BARRA 
    ===============================================================*/

    let scanner = null;
    let scanning = false;

    $('#scannerProduct, #scannerExplorer, #scannerPos').on('click', function () {

        if (scanning) return;

        if (!scanner) {
            scanner = new Html5Qrcode("reader");
        }

        $('#scanner-overlay').css('display', 'flex');

        setTimeout(() => {

            scanning = true;

            scanner.start({ facingMode: "environment" }, // Este es el objeto correcto con solo una clave
                {
                    fps: 15,
                    qrbox: (vw, vh) => {
                        const width = Math.min(vw * 0.9, 480); // Ajustar tamaño dinámico
                        return { width, height: width / 2 };
                    },
                    disableFlip: true,
                    formatsToSupport: [
                        Html5QrcodeSupportedFormats.CODE_128,
                        Html5QrcodeSupportedFormats.EAN_13,
                        Html5QrcodeSupportedFormats.EAN_8,
                        Html5QrcodeSupportedFormats.UPC_A,
                        Html5QrcodeSupportedFormats.CODE_39
                    ]
                },
                (decodedText) => {

                    // Evitar doble lectura
                    if (!scanning) return;

                    // Insertar código
                    if (pageURL.includes("products/add") || pageURL.includes("products/edit")) {
                        $('#product_code').val(decodedText).trigger('change');
                    } else if (pageURL.includes("invoices/pos")) {
                        $('#search-input').val(decodedText).trigger('change');
                    } else {
                        $('#keyword').val(decodedText).trigger('change');
                    }

                    // Vibración (móvil)
                    navigator.vibrate?.(100);

                    stopScanner();
                },
                () => { }
            ).catch(err => {
                console.error("Error cámara:", err);
                alert("Error cámara:", err)
                scanning = false;
            });

        }, 200);
    });

    function stopScanner() {
        if (!scanner || !scanning) return;

        scanning = false;

        scanner.stop().then(() => {
            scanner.clear();
            $('#scanner-overlay').hide();
        }).catch(() => {
            $('#scanner-overlay').hide();
        });
    }

    // BOTÓN SALIR
    $('#closeScanner').on('click', function () {
        stopScanner();
    });

    /**============================================================= 
   * EXPIRACION DEL PLAN
   ===============================================================*/

    // Función para obtener y mostrar el progreso del plan
    function renderPlanProgress(fecha_inicio, fecha_fin) {
        if (!fecha_inicio || !fecha_fin) {
            document.getElementById('plan-progress-container').innerHTML = '';
            return null;
        }

        const inicio = new Date(fecha_inicio + 'T00:00:00');
        const fin = new Date(fecha_fin + 'T00:00:00');

        if (isNaN(inicio.getTime()) || isNaN(fin.getTime())) {
            document.getElementById('plan-progress-container').innerHTML = '';
            return null;
        }

        const hoy = new Date();

        const totalDias = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));

        let diasConsumidos = Math.ceil((hoy - inicio) / (1000 * 60 * 60 * 24));
        diasConsumidos = Math.max(0, Math.min(diasConsumidos, totalDias));

        let diasRestantes = Math.max(0, totalDias - diasConsumidos);

        const porcentaje = (diasConsumidos / totalDias) * 100;

        let color = 'var(--color-primary)';
        if (diasRestantes <= 3) color = '#e53935';
        else if (diasRestantes <= 7) color = '#fbc02d';

        const mesesPlan = `${inicio.toLocaleString('default', { month: 'short' })} – ${fin.toLocaleString('default', { month: 'short', year: 'numeric' })}`;

        const html = `
        <div class="plan-progress-container">
            <div class="plan-progress-text">
                Expira - <span class="dias-restantes">${diasRestantes}</span> días &nbsp;|&nbsp;
                <span class="meses-plan">${mesesPlan}</span>
            </div>
            <div class="plan-progress">
                <div class="plan-progress-bar" style="width: ${porcentaje}%; background: ${color};"></div>
            </div>
        </div>
    `;

        document.getElementById('plan-progress-container').innerHTML = html;

        return diasRestantes;
    }

    function fetchPlanExpiracion() {
        const lastFetch = localStorage.getItem('lastFetch'); // Fecha del último fetch
        const now = new Date();
        const currentTime = now.getTime();
        const twelveHours = 1000 * 60 * 60; // 1 horas en milisegundos

        // Verificar si han pasado más de 12 horas desde la última consulta
        if (!lastFetch || currentTime - lastFetch > twelveHours) {
            // Si han pasado más de 12 horas, hacer una consulta y actualizar lastFetch
            sendAjaxRequest({
                url: 'src/modules/home/home.repository.php',
                data: { action: 'plan_expiracion' },
                successCallback: (res) => {
                    const data = JSON.parse(res); // Datos de las fechas

                    // Guardar los datos en localStorage
                    localStorage.setItem('planData', JSON.stringify(data)); // Guardamos las fechas de inicio y fin
                    localStorage.setItem('lastFetch', currentTime); // Guardamos la fecha del último fetch

                    // Renderizar la barra de progreso
                    const diasRestantes = renderPlanProgress(data[0], data[1]);

                    checkPlanExpired(diasRestantes);
                },
                errorCallback: (err) => {
                    console.error("Error en la consulta:", err);
                }
            });
        } else {
            // Si no han pasado 12 horas, usamos los datos almacenados en localStorage
            const storedData = JSON.parse(localStorage.getItem('planData'));
            if (storedData) {
                // Renderizamos el progreso usando los datos guardados
                renderPlanProgress(storedData[0], storedData[1]);
            } else {
                console.error("No hay datos almacenados en localStorage.");
            }
        }
    }


    function checkPlanExpired(diasRestantes) {
        if (diasRestantes > 0) return;
        notifyAlert('⚠️ Su plan ha expirado. Renueve el servicio.', 'error', 6000);
    }

    // Llamar a la función para obtener la expiración del plan
    fetchPlanExpiracion();


    /**============================================================= 
    * INICIAR FUNCIONES
    ===============================================================*/

    // Inicializar Vegas
    $(".sidebar-right").vegas({
        slides: [
            { src: SITE_URL + "public/imagen/img/img1.jpg" },
            { src: SITE_URL + "public/imagen/img/img2.jpg" },
            { src: SITE_URL + "public/imagen/img/img3.jpg" },
        ],
        transition: ["fade", "blur", "fade2", "blur2"],
    });

    // Inicializar select2
    $(".search").select2();

    // Bootstrap4 PopOvers
    $(function () {
        $(".example-popover").popover({
            container: "body",
        });
    });

    $(function () {
        $('[data-toggle="popover"]').popover();
    });

    // Inicializar datos de tablas Datatable
    var table_default = $("#example").DataTable({
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
        initComplete: function () {

        }
    });

    table_default.column("0:visible").order("asc").draw();

    loadTables(tableConfigs) // Cargar todas las tablas

    // Agregar el comando Ctrl+M para redirigir a la página de inicio
    addKeyboardCommand('Ctrl+M', SITE_URL + '/home/index');

    // Agregar el comando Ctrl+F para redirigir a la página de facturación
    addKeyboardCommand('Ctrl+F', SITE_URL + '/invoices/addpurchase');

    // Agregar el comando Ctrl+I para redirigir a la página de órdenes de venta
    addKeyboardCommand('Ctrl+I', SITE_URL + '/invoices/orders');

    // Agregar el comando Ctrl+T para redirigir a la página de ordenes de servicio
    addKeyboardCommand('Ctrl+T', SITE_URL + 'workshop/index');

    // Agregar el comando Ctrl+D para redirigir a la página de ventas del dia
    addKeyboardCommand('Ctrl+D', SITE_URL + '/reports/day');

    // Agregar el comando Ctrl+E para redirigir a la página crear contacto
    addKeyboardCommand('Ctrl+E', SITE_URL + '/contacts/add&type=1');

    // Agregar el comando Ctrl+Q para redirigir a la página consultas
    addKeyboardCommand('Ctrl+Q', SITE_URL + '/reports/querys');

    // Agregar el comando Ctrl+G para agregar gastos
    addKeyboardCommand('Ctrl+G', SITE_URL + 'bills/addbills');

    // Agregar el comando Ctrl+S para agregar servicio
    addKeyboardCommand('Ctrl+S', SITE_URL + '/services/add');

    // Agregar el comando Ctrl+P para agregar servicio
    addKeyboardCommand('Ctrl+P', SITE_URL + '/products/add');

    // Abrir modals
    addKeyboardCommandForModal('Ctrl+O', 'modalComanda');
    addKeyboardCommandForModal('Ctrl+A', 'orden');

    addKeyboardCommandForModal('Ctrl+1', 'cash_invoice');
    addKeyboardCommandForModal('Ctrl+1', 'create_device');
    addKeyboardCommandForModal('Ctrl+1', 'update_data_invoice');

    addKeyboardCommandForModal('Ctrl+2', 'credit_invoice');
    addKeyboardCommandForModal('Ctrl+2', 'create_condition');

    addKeyboardCommandForModal('Ctrl+3', 'add_detail');
    addKeyboardCommandForModal('Ctrl+0', 'create_customer');


/* ===== QZ-TRAY VERBOSE MODE ===== */
const QZ_VERBOSE = true;

function qzLog(...args) {
    if (!QZ_VERBOSE) return;
    console.log('%c[QZ]', 'color:#1976d2;font-weight:bold;', ...args);
}

function qzWarn(...args) {
    if (!QZ_VERBOSE) return;
    console.warn('%c[QZ]', 'color:#f9a825;font-weight:bold;', ...args);
}

function qzError(...args) {
    if (!QZ_VERBOSE) return;
    console.error('%c[QZ]', 'color:#d32f2f;font-weight:bold;', ...args);
}

/* ===== SEGURIDAD QZ-TRAY | CERTIFICADO ===== */

console.log('Connection ready')

qz.security.setCertificatePromise(function (resolve, reject) {

    qzLog('Solicitando certificado…');

    fetch(SITE_URL + "public/printing/get-cert.php", {
        cache: 'no-store'
    })
        .then(res => {
            qzLog('HTTP status certificado:', res.status);
            if (!res.ok) throw new Error('Cert not loaded');
            return res.text();
        })
        .then(cert => {

            qzLog('Certificado recibido');
            qzLog('Longitud:', cert.length);
            qzLog('BEGIN:', cert.slice(0, 40));
            qzLog('END:', cert.slice(-40));

            // Validación dura
            if (
                !cert.includes('-----BEGIN CERTIFICATE-----') ||
                !cert.includes('-----END CERTIFICATE-----')
            ) {
                throw new Error('Contenido NO es un certificado X509');
            }

            qzLog('Certificado X509 válido ✔');
            resolve(cert);
        })
        .catch(err => {
            qzError('❌ Error certificado:', err);
            reject(err);
        });
});

/* ===== SEGURIDAD QZ-TRAY | FIRMA ===== */

qz.security.setSignatureAlgorithm('SHA512');
qz.security.setSignaturePromise(function (toSign) {

    return function (resolve, reject) {

        qzLog('Solicitud de firma enviada');
        qzLog('Payload:', toSign);

        fetch(SITE_URL + 'public/printing/sign.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request: toSign })
        })
            .then(res => {
                qzLog('HTTP status firma:', res.status);
                if (!res.ok) throw new Error('Firma no generada');
                return res.text();
            })
            .then(signature => {

                qzLog('Firma recibida');
                qzLog('Longitud firma:', signature.length);

                resolve(signature.trim());
            })
            .catch(err => {
                qzError('❌ Error firma:', err);
                reject(err);
            });
    };
});

/* ===== CONEXION ===== */
qz.websocket.connect()
    .then(() => qz.printers.find())
    .then(printers => {

        const $select = $('#impresoraSelect');
        $select.empty().append('<option value=""></option>');

        printers.forEach(printer => {
            $select.append(
                $('<option>', { value: printer, text: printer })
            );
        });

        const defaultPrinter = 'POS-80';
        if (printers.includes(defaultPrinter)) {
            $select.val(defaultPrinter).trigger('change');
        }
    })
    .catch(err => {
        console.error('QZ Tray error:', err);
    });

}); // Ready