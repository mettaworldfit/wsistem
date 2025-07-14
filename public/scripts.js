navigator.serviceWorker && navigator.serviceWorker.register("../sw.js"); // Activacion del service worker
const PRINTER_SERVER = "http://localhost:81/tickets/"; // URL local de la impresora
const SITE_URL = window.location.protocol + '//' + window.location.host + '/'; // Raiz del sistema

let pageURL = $(location).attr("pathname");
const format = new Intl.NumberFormat('en'); // Formato 0,000

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
                $.ajax({
                    url: SITE_URL + ajaxUrl,
                    type: 'POST',
                    data: {
                        action: ajaxAction,
                        id: $.urlParam('id') || $.urlParam('o'),
                        ...data
                    },
                    dataType: 'json',
                    success: response => {
                        const json = typeof response === 'string'
                            ? JSON.parse(response)
                            : response;

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
 * Maneja una respuesta JSON de forma segura
 * @param {string} response - La respuesta del servidor (texto plano)
 * @returns {object} Objeto con estructura estandarizada: { success, data, error }
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

        return { success: false, data: null, error: "La respuesta no es un objeto válido." };

    } catch (e) {
        // Si no es JSON válido, devolver como error de texto plano
        return {
            success: false,
            data: null,
            data: response
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
function sendAjaxRequest({ url, data, successCallback, errorCallback, verbose = false }) {
    $.ajax({
        type: "post",
        url: SITE_URL + url,
        data,
        success: function (res) {

            let data = handleJSONResponse(res);

            if (verbose) {
                console.log("Datos devueltos por el servidor:", data);
            }

            if (Array.isArray(data) || data.success || res === "ready" || res > 0 || (!data.success && res != "duplicate" && !res.includes("Error"))) {
                // Ejecuta la función successCallback si fue pasada y está definida
                successCallback?.(res); // Devuelve la response

                if (verbose) {
                    console.log("Respuesta validada exitosamente:", res);
                }
            } else if (res === "duplicate") {
                mysql_error('Existen datos que ya están siendo utilizado');
            } else if (res.includes("Error")) {
                errorCallback ? errorCallback(res) : mysql_error(res);
            } else {
                errorCallback ? errorCallback(res) : mysql_error(res);
            }
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

$(document).ready(function () {

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


    // Menú Accordeon

    $(function () {
        var Accordion = function (el, multiple) {
            this.el = el || {};
            this.multiple = multiple || false;

            // Variables privadas
            var links = this.el.find(".link");

            // Evento
            links.on("click", {
                el: this.el,
                multiple: this.multiple,
            },
                this.dropdown
            );
        };

        Accordion.prototype.dropdown = function (e) {
            var $el = e.data.el;
            ($this = $(this)), ($next = $this.next());

            $next.slideToggle();
            $this.parent().toggleClass("open");

            if (!e.data.multiple) {
                $el.find(".submenu").not($next).slideUp().parent().removeClass("open");
            }
        };

        new Accordion($("#accordion"), false);
        new Accordion($("#accordion-movil"), false);

    });

    // Mantener el menu de accordion abierto

    $(function () {
        const menuMap = [
            { keywords: ["invoices/index", "invoices/edit", "invoices/addpurchase", "invoices/index_repair", "invoices/repair_edit", "payments/index", "payments/add", "invoices/quotes", "invoices/quote", "invoices/edit_quote", "invoices/orders", "invoices/add_order"], dropdown: "dropdown-1" },
            { keywords: ["bills"], dropdown: "dropdown-2" },
            { keywords: ["workshop"], dropdown: "dropdown-3" },
            { keywords: ["products", "inventory_control", "services/index", "services/add", "price_list", "categories", "taxes", "offers", "pieces", "warehouses", "positions", "brands"], dropdown: "dropdown-4" },
            { keywords: ["contacts"], dropdown: "dropdown-5" },
            { keywords: ["reports"], dropdown: "dropdown-6" },
        ];

        menuMap.forEach(({ keywords, dropdown }) => {
            if (keywords.some(keyword => pageURL.includes(keyword))) {
                $(`.${dropdown} ul.submenu`).css("display", "block");
                $(`.accordion .${dropdown}`).addClass("open");
            }
        });
    });



    /**
 * Valores de la sección agregar producto
 ----------------------------------------------*/

    $("#inputMinCantidad").val(1);
    $("#inputCantidad").val(1);

    /**
 * Activar librerías JavaScript
------------------------------------- */

    $(".search").select2();

    /**
 * Bootstrap4 PopOvers ?
 -----------------------------------*/

    $(function () {
        $(".example-popover").popover({
            container: "body",
        });
    });

    $(function () {
        $('[data-toggle="popover"]').popover();
    });

    $(".loader").hide(); // Loader

    // Menú2 desplegable

    $("#bar-menu").on("click", (e) => {
        e.preventDefault();

        $(".nav-container").slideToggle();
    });

    // User menú desplegable

    $(".user").on("click", (e) => {
        e.preventDefault();

        $(".nav-user").slideToggle();
    });

    // Atajos de tecla enter

    $("body").keyup(function (e) {
        if (e.keyCode == 13) {
            if (pageURL.includes("products/add")) {
                $("#createProduct").click();
            }
        }
    });

    // Notificacion de cantidad minima de productos
    setInterval(function () {
        $(".out-stock p").fadeTo(1200, 0.1).fadeTo(1200, 1);
    }, 1600);


    // Buscador global

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
            url: "services/home.php",
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
        url: 'services/invoices.php',
        action: 'index_facturas_ventas',
        columns: [
            'factura_venta_id', 'nombre', 'fecha_factura', 'total', 'recibido', 'pendiente', 'bono', 'nombre_estado', 'acciones'
        ],
        order: [[0, 'desc']],
        hiddenColumns: [3, 4, 5, 6]
    },
    {
        id: '#today',
        url: 'services/reports.php',
        action: 'index_ventas_hoy',
        columns: [
            'id', 'nombre', 'fecha', 'total', 'recibido', 'pendiente', 'estado', 'acciones'
        ],
        order: [[0, 'desc']],
    },
    {
        id: '#customers',
        url: 'services/contacts.php',
        action: 'index_clientes',
        columns: [
            'id', 'nombre', 'direccion', 'cedula', 'telefono', 'fecha', 'acciones'
        ],
        hiddenColumns: [2, 3]
    },
    {
        id: '#providers',
        url: 'services/contacts.php',
        action: 'index_proveedores',
        columns: [
            'id', 'nombre', 'correo', 'telefono', 'fecha', 'acciones'
        ],
        hiddenColumns: [0, 2]
    },
    {
        id: '#workshop',
        url: 'services/workshop.php',
        action: 'index_taller',
        columns: [
            'orden', 'nombre', 'equipo', 'fecha_entrada', 'fecha_salida', 'condicion', 'estado', 'acciones'
        ],
        order: [[0, 'desc']],
        hiddenColumns: [3, 4, 5]
    },
    {
        id: '#products',
        url: 'services/products.php',
        action: 'index_productos',
        columns: [
            'codigo', 'nombre', 'categoria', 'almacen', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
        ],
        hiddenColumns: [0, 2, 3, 5]
    },
    {
        id: '#invoicesrp',
        url: 'services/repair.php',
        action: 'index_facturas_reparacion',
        columns: [
            'id', 'nombre', 'fecha', 'total', 'recibido', 'pendiente', 'estado', 'acciones'
        ],
        order: [[0, 'desc']],
        hiddenColumns: [3, 4, 5]
    },
    {
        id: '#quotes',
        url: 'services/invoices.php',
        action: 'index_cotizaciones',
        columns: [
            'id', 'nombre', 'fecha', 'total', 'acciones'
        ],
        order: [[0, 'desc']],
        hiddenColumns: [3]
    },
    {
        id: '#payments',
        url: 'services/payments.php',
        action: 'index_pagos_facturas_ventas',
        columns: [
            'pago_id', 'factura_id', 'nombre', 'recibido', 'observacion', 'fecha', 'acciones'
        ],
        order: [[0, 'desc']],
        hiddenColumns: [0, 4, 5]
    },
    {
        id: '#ordersc',
        url: 'services/bills.php',
        action: 'index_ordenes_compras',
        columns: [
            'orden_id', 'proveedor', 'articulos', 'fecha', 'expiracion', 'estado', 'acciones'
        ],
        order: [[0, 'desc']],
        hiddenColumns: [2, 4, 5]
    },
    {
        id: '#invoicesp',
        url: 'services/bills.php',
        action: 'index_facturas_proveedores',
        columns: [
            'id', 'proveedor', 'fecha', 'total', 'pagado', 'por_pagar', 'estado', 'acciones'
        ],
        order: [[0, 'desc']],
        hiddenColumns: [0,]
    },
    {
        id: '#bills',
        url: 'services/bills.php',
        action: 'index_gastos',
        columns: [
            'id', 'proveedor', 'gastos', 'fecha', 'total', 'pagado', 'acciones'
        ],
        order: [[0, 'desc']],
        hiddenColumns: [1, 5]
    },
    {
        id: '#payments_providers',
        url: 'services/payments.php',
        action: 'index_pagos_proveedores',
        columns: [
            'pago_id', 'factura', 'proveedor', 'recibido', 'observacion', 'fecha', 'acciones'
        ],
        order: [[0, 'desc']],
    },
    {
        id: '#pieces',
        url: 'services/pieces.php',
        action: 'index_piezas',
        columns: [
            'id', 'nombre', 'categoria', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
        ],
        hiddenColumns: [0, 2, 4]
    },
    {
        id: '#minStockProduct',
        url: 'services/products.php',
        action: 'index_casi_agotados',
        columns: [
            'cod_producto', 'nombre', 'categoria', 'almacen', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
        ]
    },
    {
        id: '#services',
        url: 'services/services.php',
        action: 'index_servicios',
        columns: [
            'servicio_id', 'nombre_servicio', 'costo', 'precio', 'acciones'
        ]
    },
    {
        id: '#users',
        url: 'services/users.php',
        action: 'index_usuarios',
        columns: [
            'usuario_id', 'nombre', 'rol', 'estado', 'fecha', 'acciones'
        ],
        hiddenColumns: [0]
    },
    {
        id: '#brands',
        url: 'services/workshop.php',
        action: 'index_marcas',
        columns: [
            'nombre_marca', 'fecha', 'acciones'
        ]
    },
    {
        id: '#pricelists',
        url: 'services/price_lists.php',
        action: 'index_lista_precios',
        columns: [
            'id', 'nombre_lista', 'descripcion', 'acciones'
        ]
    },
    {
        id: '#warehouses',
        url: 'services/warehouses.php',
        action: 'index_almacen',
        columns: [
            'id', 'nombre_almacen', 'descripcion', 'fecha', 'acciones'
        ]
    },
    {
        id: '#categories',
        url: 'services/categories.php',
        action: 'index_categorias',
        columns: [
            'id', 'nombre_categoria', 'descripcion', 'fecha', 'acciones'
        ]
    },
    {
        id: '#positions',
        url: 'services/positions.php',
        action: 'index_posiciones',
        columns: [
            'id', 'referencia', 'fecha', 'acciones'
        ]
    },
    {
        id: '#offers',
        url: 'services/offers.php',
        action: 'index_ofertas',
        columns: [
            'id', 'nombre', 'valor', 'descripcion', 'fecha', 'acciones'
        ]
    },
    {
        id: '#taxs',
        url: 'services/taxes.php',
        action: 'index_impuestos',
        columns: [
            'id', 'nombre', 'valor', 'descripcion', 'fecha', 'acciones'
        ]
    },
    {
        id: '#inventory',
        url: 'services/products.php',
        action: 'index_valor_inventario',
        columns: [
            'codigo', 'nombre', 'cantidad', 'estado', 'precio_costo', 'total_costo'
        ]
    },
    {
        id: '#bonus',
        url: 'services/config.php',
        action: 'index_bonos',
        columns: [
            'id', 'cliente', 'valor', 'usuario', 'fecha', 'acciones'
        ],
        order: [[0, 'desc']],
    },

    // Cargar detalles
    {
        id: '#detailTemp',
        url: 'services/invoices.php',
        action: 'cargar_detalle_temporal',
        columns: ['descripcion', 'cantidad', 'precio', 'impuesto', 'descuento', 'importe', 'acciones'],
        hiddenColumns: [3],
        paging: false,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#editInvoice',
        url: 'services/invoices.php',
        action: 'cargar_detalle_facturas',
        columns: ['descripcion', 'cantidad', 'precio', 'impuesto', 'descuento', 'total', 'acciones'],
        hiddenColumns: [3],
        paging: false,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#addrepair',
        url: 'services/repair.php',
        action: 'cargar_ordenrp',
        columns: ['descripcion', 'cantidad', 'precio', 'descuento', 'total', 'acciones'],
        paging: false,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#editrepair',
        url: 'services/repair.php',
        action: 'cargar_facturarp',
        columns: ['descripcion', 'cantidad', 'precio', 'descuento', 'total', 'acciones'],
        paging: false,
        searching: false,
        ordering: false,
        info: false
    },
    {
        id: '#variantList',
        url: 'services/products.php',
        action: 'cargar_variantes',
        columns: getVariantTableColumns(),
        paging: false,
        searching: false,
        ordering: false,
        hideZeroRecordsMessage: true,
        info: false
    },
    {
        id: '#cashClosing',
        url: 'services/reports.php',
        action: 'index_cierre_caja',
        columns: ['id', 'cajero', 'total_real', 'gastos', 'diferencia', 'fecha_apertura', 'fecha_cierre', 'estado', 'acciones'],
        order: [[0, 'desc']],
        hiddenColumns: [0, 2, 3, 4, 7]
    },
    {
        id: '#orders',
        url: 'services/invoices.php',
        action: 'index_ordenes',
        columns: [
            'comanda_id','nombre','telefono','entrega','fecha','estado','orden','acciones'
        ],
        order: [[0, 'desc']]
    },
    {
        id: '#addorder',
        url: 'services/invoices.php',
        action: 'cargar_detalle_orden',
        columns: [
            'descripcion','cantidad','precio','impuesto','descuento','importe','acciones'
        ],
         hiddenColumns: [3],
        paging: false,
        searching: false,
        ordering: false,
        info: false
    },

    ];


    // Inicialización automática
    tableConfigs.forEach(config => {
        const { id, url, action, columns, hiddenColumns, ...rest } = config;

        const columnDefs = columns.map(col =>
            col === 'acciones'
                ? { data: col, orderable: false, searchable: false }
                : { data: col }
        );

        const tableId = id.replace('#', '');

        dataTablesInstances[tableId] = initCustomDataTable({
            selector: id,
            ajaxUrl: url,
            ajaxAction: action,
            columns: columnDefs,
            hiddenColumns: hiddenColumns,
            ...rest
        });
    });


}); // Ready
