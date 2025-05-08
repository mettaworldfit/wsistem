navigator.serviceWorker && navigator.serviceWorker.register("../sw.js"); // Activacion del service worker
const PRINTER_SERVER = "http://localhost:81/tickets/"; // URL local de la impresora
const SITE_URL = window.location.protocol + '//' + window.location.host + '/'; // Raiz del sistema
let pageURL = $(location).attr("pathname");
let datatable; //Variable declarada globalmente
const format = new Intl.NumberFormat('en'); // Formato 0,000

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
                        console.log(json)
                        console.log(selector)
                    }
                });
            }, loadTime);
        },
        columns: columns,
        initComplete: function() {}
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
    function e() {
        navigator.onLine ?
            mdtoast("Conexión establecida", {
                interaction: !0,
                interactionTimeout: 1500,
                position: "bottom right",
                actionText: "OK!",
            }) :
            mdtoast("Conexión pérdida", {
                interaction: !0,
                position: "bottom right",
                actionText: "OK!",
            });
    }
    window.addEventListener("online", e);
    window.addEventListener("offline", e);

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

    $(function() {

        if (
            pageURL.includes("invoices/index") ||
            pageURL.includes("invoices/edit") ||
            pageURL.includes("invoices/addpurchase") ||
            pageURL.includes("invoices/index_repair") ||
            pageURL.includes("invoices/repair_edit") ||
            pageURL.includes("payments/index") ||
            pageURL.includes("payments/add") ||
            pageURL.includes("invoices/quotes") ||
            pageURL.includes("invoices/quote") ||
            pageURL.includes("invoices/edit_quote")
        ) {
            $(".dropdown-1 ul.submenu").css("display", "block");
            $(".accordion .dropdown-1").addClass("open");
        } else if (pageURL.includes("bills")) {
            $(".dropdown-2 ul.submenu").css("display", "block");
            $(".accordion .dropdown-2").addClass("open");
        } else if (pageURL.includes("workshop")) {
            $(".dropdown-3 ul.submenu").css("display", "block");
            $(".accordion .dropdown-3").addClass("open");
        } else if (
            pageURL.includes("products") ||
            pageURL.includes("inventory_control") ||
            pageURL.includes("services/index") ||
            pageURL.includes("services/add") ||
            pageURL.includes("price_list") ||
            pageURL.includes("categories") ||
            pageURL.includes("taxes") ||
            pageURL.includes("offers") ||
            pageURL.includes("pieces") ||
            pageURL.includes("warehouses") ||
            pageURL.includes("positions") ||
            pageURL.includes("brands")
        ) {
            $(".dropdown-4 ul.submenu").css("display", "block");
            $(".accordion .dropdown-4").addClass("open");
        } else if (pageURL.includes("contacts")) {
            $(".dropdown-5 ul.submenu").css("display", "block");
            $(".accordion .dropdown-5").addClass("open");
        } else if (pageURL.includes("reports")) {
            $(".dropdown-6 ul.submenu").css("display", "block");
            $(".accordion .dropdown-6").addClass("open");
        }
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

    // Cargar datos del index de factura ventas

    datatable = initCustomDataTable('#invoice', 'services/invoices.php', 'index_facturas_ventas', [
        { data: 'factura_venta_id' },
        { data: 'nombre' },
        { data: 'fecha_factura' },
        { data: 'total' },
        { data: 'recibido' },
        { data: 'pendiente' },
        { data: 'bono' },
        { data: 'nombre_estado' },
        { data: 'acciones', orderable: false, searchable: false },

    ]);

    datatable = initCustomDataTable('#today', 'services/reports.php', 'index_ventas_hoy', [
        { data: 'id' },
        { data: 'nombre' },
        { data: 'fecha' },
        { data: 'total' },
        { data: 'recibido' },
        { data: 'pendiente' },
        { data: 'estado' },
        { data: 'acciones', orderable: false, searchable: false }
    ]);

    datatable = initCustomDataTable('#customers', 'services/contacts.php', 'index_clientes', [
        { data: 'id' },
        { data: 'nombre' },
        { data: 'direccion' },
        { data: 'cedula' },
        { data: 'telefono' },
        { data: 'fecha' },
        { data: 'acciones', orderable: false, searchable: false }
    ]);

    datatable = initCustomDataTable('#providers', 'services/contacts.php', 'index_proveedores', [
        { data: 'id' },
        { data: 'nombre' },
        { data: 'correo' },
        { data: 'telefono' },
        { data: 'fecha' },
        { data: 'acciones', orderable: false, searchable: false }
    ]);

    datatable = initCustomDataTable('#workshop', 'services/workshop.php', 'index_taller', [
        { data: 'orden' },
        { data: 'nombre' },
        { data: 'equipo' },
        { data: 'fecha_entrada' },
        { data: 'fecha_salida' },
        { data: 'condicion' },
        { data: 'estado' },
        { data: 'acciones', orderable: false, searchable: false },

    ]);

    datatable = initCustomDataTable('#products', 'services/products.php', 'index_productos', [
        { data: 'codigo' },
        { data: 'nombre' },
        { data: 'categoria' },
        { data: 'almacen' },
        { data: 'cantidad' },
        { data: 'precio_costo' },
        { data: 'precio_unitario' },
        { data: 'acciones', orderable: false, searchable: false }
    ]);


    // $.ajax({
    //     type: "post",
    //     url: SITE_URL + "services/contacts.php",
    //     data: {
    //         action: "index_clientes",

    //     },
    //     success: function(res) {

    //         console.log(res)

    //     }
    // });


}); // Ready