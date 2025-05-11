navigator.serviceWorker && navigator.serviceWorker.register("../sw.js"); // Activacion del service worker
const PRINTER_SERVER = "http://localhost:81/tickets/"; // URL local de la impresora
const SITE_URL = window.location.protocol + '//' + window.location.host + '/'; // Raiz del sistema

let pageURL = $(location).attr("pathname");
const format = new Intl.NumberFormat('en'); // Formato 0,000

// Variable global para acceder a las instancias DataTable desde cualquier parte
const dataTablesInstances = {};

// Funcion para mostrar datos con DataTable 
function initCustomDataTable(selector, ajaxUrl, ajaxAction, columns, loadTime = 300) {
    return $(selector).DataTable({
        processing: false,
        serverSide: true,
        language: {
            lengthMenu: "_MENU_",
            zeroRecords: "Aún no tienes datos para mostrar",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Página no disponible",
            infoFiltered: "(Filtrado de _MAX_  registros)",
            search: "Buscar:",
            processing: "Buscando...",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "<i class='fas fa-caret-right'></i>",
                previous: "Anterior"
            }
        },
        ajax: function(data, callback, settings) {
            const $tbody = $(selector + ' tbody');
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

            setTimeout(() => {
                $.ajax({
                    url: SITE_URL + ajaxUrl,
                    type: 'POST',
                    data: {
                        action: ajaxAction,
                        ...data
                    },
                    dataType: 'json',
                    success: function(response) {
                        const json = typeof response === 'string' ? JSON.parse(response) : response;
                        callback(json);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en AJAX:", status, error);
                        console.error("Respuesta del servidor:", xhr.responseText);

                        $tbody.html(`
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
        columns: columns,
        initComplete: function() {}
    });
}

// Función genérica para enviar peticiones AJAX POST reutilizable
function sendAjaxRequest({ url, data, successCallback, errorCallback }) {
    $.ajax({
        type: "post",
        url: SITE_URL + url,
        data,
        success: function(res) {
            if (res || res === "ready" || res > 0) {
                // Ejecuta la función successCallback si fue pasada y está definida
                successCallback ?.(res); // Devuelve la response
            } else if (res === "duplicate") {
                mysql_error('Existen datos que ya están siendo utilizado');
            } else if (res.includes("Error")) {
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

$(document).ready(function() {

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

    $(function() {
        var Accordion = function(el, multiple) {
            this.el = el || {};
            this.multiple = multiple || false;

            // Variables privadas
            var links = this.el.find(".link");
            // Evento
            links.on(
                "click", {
                    el: this.el,
                    multiple: this.multiple,
                },
                this.dropdown
            );
        };

        Accordion.prototype.dropdown = function(e) {
            var $el = e.data.el;
            ($this = $(this)), ($next = $this.next());

            $next.slideToggle();
            $this.parent().toggleClass("open");

            if (!e.data.multiple) {
                $el.find(".submenu").not($next).slideUp().parent().removeClass("open");
            }
        };

        var accordion = new Accordion($("#accordion"), false);
    });

    // Mantener el menu de accordion abierto

    $(function () {
        const menuMap = [
            { keywords: ["invoices/index", "invoices/edit", "invoices/addpurchase", "invoices/index_repair", "invoices/repair_edit", "payments/index", "payments/add", "invoices/quotes", "invoices/quote", "invoices/edit_quote"], dropdown: "dropdown-1" },
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

    $(function() {
        $(".example-popover").popover({
            container: "body",
        });
    });

    $(function() {
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

    $("body").keyup(function(e) {
        if (e.keyCode == 13) {
            if (pageURL.includes("products/add")) {
                $("#createProduct").click();
            }
        }
    });

    // Notificacion de cantidad minima de productos
    setInterval(function() {
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

        $.ajax({
            type: "post",
            url: SITE_URL + "services/home.php",
            data: {
                action: 'buscador',
                search: q
            },
            success: function(res) {

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
                });
            }
        });

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
        initComplete: function() {

        }
    });

    table_default.column("0:visible").order("asc").draw();


    // Configuración de DataTable Server-Side para las tablas
    const tableConfigs = [{
            id: '#invoice',
            url: 'services/invoices.php',
            action: 'index_facturas_ventas',
            columns: [
                'factura_venta_id', 'nombre', 'fecha_factura', 'total', 'recibido', 'pendiente', 'bono', 'nombre_estado', 'acciones'
            ]
        },
        {
            id: '#today',
            url: 'services/reports.php',
            action: 'index_ventas_hoy',
            columns: [
                'id', 'nombre', 'fecha', 'total', 'recibido', 'pendiente', 'estado', 'acciones'
            ]
        },
        {
            id: '#customers',
            url: 'services/contacts.php',
            action: 'index_clientes',
            columns: [
                'id', 'nombre', 'direccion', 'cedula', 'telefono', 'fecha', 'acciones'
            ]
        },
        {
            id: '#providers',
            url: 'services/contacts.php',
            action: 'index_proveedores',
            columns: [
                'id', 'nombre', 'correo', 'telefono', 'fecha', 'acciones'
            ]
        },
        {
            id: '#workshop',
            url: 'services/workshop.php',
            action: 'index_taller',
            columns: [
                'orden', 'nombre', 'equipo', 'fecha_entrada', 'fecha_salida', 'condicion', 'estado', 'acciones'
            ]
        },
        {
            id: '#products',
            url: 'services/products.php',
            action: 'index_productos',
            columns: [
                'codigo', 'nombre', 'categoria', 'almacen', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
            ]
        },
        {
            id: '#invoicesrp',
            url: 'services/repair.php',
            action: 'index_facturas_reparacion',
            columns: [
                'id', 'nombre', 'fecha', 'total', 'recibido', 'pendiente', 'estado', 'acciones'
            ]
        },
        {
            id: '#quotes',
            url: 'services/invoices.php',
            action: 'index_cotizaciones',
            columns: [
                'id', 'nombre', 'fecha', 'total', 'acciones'
            ]
        },
        {
            id: '#payments',
            url: 'services/payments.php',
            action: 'index_pagos_facturas_ventas',
            columns: [
                'pago_id', 'factura_id', 'nombre', 'recibido', 'observacion', 'fecha', 'acciones'
            ]
        },
        {
            id: '#ordersc',
            url: 'services/bills.php',
            action: 'index_ordenes_compras',
            columns: [
                'orden_id', 'proveedor', 'articulos', 'fecha', 'expiracion', 'estado', 'acciones'
            ]
        },
        {
            id: '#invoicesp',
            url: 'services/bills.php',
            action: 'index_facturas_proveedores',
            columns: [
                'id', 'proveedor', 'fecha', 'total', 'pagado', 'por_pagar', 'estado', 'acciones'
            ]
        },
        {
            id: '#bills',
            url: 'services/bills.php',
            action: 'index_gastos',
            columns: [
                'id', 'proveedor', 'gastos', 'fecha', 'total', 'pagado', 'acciones'
            ]
        },
        {
            id: '#payments_providers',
            url: 'services/payments.php',
            action: 'index_pagos_proveedores',
            columns: [
                'pago_id', 'factura', 'proveedor', 'recibido', 'observacion', 'fecha', 'acciones'
            ]
        },
        {
            id: '#pieces',
            url: 'services/pieces.php',
            action: 'index_piezas',
            columns: [
                'id', 'nombre', 'categoria', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
            ]
        },
        {
            id: '#minStockProduct',
            url: 'services/products.php',
            action: 'index_casi_agotados',
            columns: [
                'cod_producto', 'nombre', 'categoria', 'almacen', 'cantidad', 'precio_costo', 'precio_unitario', 'acciones'
            ]
        }
        
    ];

    // Inicialización automática
    tableConfigs.forEach(({ id, url, action, columns }) => {
        const columnDefs = columns.map(col => (
            col === 'acciones' ? { data: col, orderable: false, searchable: false } : { data: col }
        ));
        const tableId = id.replace('#', '');
        dataTablesInstances[tableId] = initCustomDataTable(id, url, action, columnDefs);
    });


}); // Ready