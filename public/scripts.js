navigator.serviceWorker && navigator.serviceWorker.register("../sw.js"),
  $(document).ready(function () {
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
    window.addEventListener("online", e),
      window.addEventListener("offline", e),
      $(function () {
        var e = function (e, n) {
          (this.el = e || {}),
            (this.multiple = n || !1),
            this.el
              .find(".link")
              .on(
                "click",
                { el: this.el, multiple: this.multiple },
                this.dropdown
              );
        };
        e.prototype.dropdown = function (e) {
          var n = e.data.el;
          ($this = $(this)),
            ($next = $this.next()),
            $next.slideToggle(),
            $this.parent().toggleClass("open"),
            e.data.multiple ||
              n
                .find(".submenu")
                .not($next)
                .slideUp()
                .parent()
                .removeClass("open");
        };
        new e($("#accordion"), !1);
      }),
      $(function () {
        var e = function (e, n) {
          (this.el = e || {}),
            (this.multiple = n || !1),
            this.el
              .find(".link")
              .on(
                "click",
                { el: this.el, multiple: this.multiple },
                this.dropdown
              );
        };
        e.prototype.dropdown = function (e) {
          var n = e.data.el;
          ($this = $(this)),
            ($next = $this.next()),
            $next.slideToggle(),
            $this.parent().toggleClass("open"),
            e.data.multiple ||
              n
                .find(".submenu")
                .not($next)
                .slideUp()
                .parent()
                .removeClass("open");
        };
        new e($("#accordion-movil"), !1);
      }),
      $(function () {
        var e = $(location).attr("pathname");
        e.includes("invoices/index") ||
        e.includes("invoices/edit") ||
        e.includes("invoices/addpurchase") ||
        e.includes("invoices/index_repair") ||
        e.includes("invoices/repair_edit") ||
        e.includes("payments/index") ||
        e.includes("payments/add")
          ? ($(".dropdown-1 ul.submenu").css("display", "block"),
            $(".accordion .dropdown-1").addClass("open"))
          : e.includes("bills")
          ? ($(".dropdown-2 ul.submenu").css("display", "block"),
            $(".accordion .dropdown-2").addClass("open"))
          : e.includes("workshop") || e.includes("addrepair")
          ? ($(".dropdown-3 ul.submenu").css("display", "block"),
            $(".accordion .dropdown-3").addClass("open"))
          : e.includes("products") ||
            e.includes("inventory_control") ||
            e.includes("services/index") ||
            e.includes("services/add") ||
            e.includes("price_list") ||
            e.includes("categories") ||
            e.includes("taxes") ||
            e.includes("offers") ||
            e.includes("pieces") ||
            e.includes("warehouses") ||
            e.includes("positions")
          ? ($(".dropdown-4 ul.submenu").css("display", "block"),
            $(".accordion .dropdown-4").addClass("open"))
          : e.includes("contacts")
          ? ($(".dropdown-5 ul.submenu").css("display", "block"),
            $(".accordion .dropdown-5").addClass("open"))
          : e.includes("reports") &&
            ($(".dropdown-6 ul.submenu").css("display", "block"),
            $(".accordion .dropdown-6").addClass("open"));
      }),
      $("#inputMinCantidad").val(1),
      $("#inputCantidad").val(1),
      $("#example")
        .DataTable({
          language: {
            lengthMenu: "_MENU_",
            zeroRecords: "Aún no tienes datos para mostrar",
            info: "_PAGE_ de _PAGES_",
            infoEmpty: "Página no disponible",
            infoFiltered: "(Filtrado de _MAX_  registros)",
          },
        })
        .column("0:visible")
        .order("asc")
        .draw(),
      $(".search").select2(),
      $(function () {
        $(".example-popover").popover({ container: "body" });
      }),
      $(function () {
        $('[data-toggle="popover"]').popover();
      }),
      $(".loader").hide(),
      $("#bar-menu").on("click", (e) => {
        e.preventDefault(), $(".nav-container").slideToggle();
      }),
      $(".user").on("click", (e) => {
        e.preventDefault(), $(".nav-user").slideToggle();
      }),
      $("body").keyup(function (e) {
        13 == e.keyCode &&
          pageURL.includes("products/add") &&
          $("#createProduct").click();
      });
    setInterval(function () {
      $(".out-stock p").fadeTo(500, 0.1).fadeTo(500, 1);
    }, 1600);
  });
