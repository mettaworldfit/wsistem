navigator.serviceWorker && navigator.serviceWorker.register("../sw.js");

$(document).ready(function () {

  // Alerta de cuando se pierde la conexión a internet
  function e() {
    navigator.onLine
      ? mdtoast("Conexión establecida", {
          interaction: !0,
          interactionTimeout: 1500,
          position: "bottom right",
          actionText: "OK!",
        })
      : mdtoast("Conexión pérdida", {
          interaction: !0,
          position: "bottom right",
          actionText: "OK!",
        });
  }
  window.addEventListener("online", e);
  window.addEventListener("offline", e);

  /**
   *  Menú Accordeon
   * ------------------------------------
   */

  $(function () {
    var Accordion = function (el, multiple) {
      this.el = el || {};
      this.multiple = multiple || false;

      // Variables privadas
      var links = this.el.find(".link");
      // Evento
      links.on(
        "click",
        {
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

    var accordion = new Accordion($("#accordion"), false);
  });

  /**
* Verificar la página actual
--------------------------------------------*/

  $(function () {
    var pageURL = $(location).attr("pathname");

    if (
      pageURL.includes("invoices/index") ||
      pageURL.includes("invoices/edit") ||
      pageURL.includes("invoices/addpurchase") ||
      pageURL.includes("invoices/index_repair") ||
      pageURL.includes("invoices/repair_edit") ||
      pageURL.includes("payments/index") ||
      pageURL.includes("payments/add")
    ) {
      $(".dropdown-1 ul.submenu").css("display", "block");
      $(".accordion .dropdown-1").addClass("open");
    } else if (pageURL.includes("expenses")) {
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

  var table = $("#example").DataTable({
    language: {
      lengthMenu: "_MENU_",
      zeroRecords: "Aún no tienes datos para mostrar",
      info: "_PAGE_ de _PAGES_",
      infoEmpty: "Página no disponible",
      infoFiltered: "(Filtrado de _MAX_  registros)",
    },
  });

  table.column("0:visible").order("asc").draw();

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
  
});
