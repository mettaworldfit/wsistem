<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=0.9, user-scalable=si, shrink-to-fit=no">
  <meta name="title" content="wsistems | Plataforma de Control de inventario, Ventas y Gestión Empresarial">
  <meta name="description" content="Administra tu negocio desde cualquier lugar. Visualiza ventas, inventario, estadísticas y reportes en tiempo real. WSistems es un sistema de gestión completo, moderno y fácil de usar, diseñado para ofrecerte control total y una operación más ordenada y eficiente." />
  <meta name="author" content="codevrd">
  <meta http-equiv="cache-control" content="no-cache">
  <meta http-equiv="expires" content="86400">
  <meta name="robots" content="noindex">

  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="application-name" content="wsistems">
  <meta name="apple-mobile-web-app-title" content="wsistems">
  <meta name="theme-color" content="#000000">
  <meta name="msapplication-navbutton-color" content="#000000">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

  <link rel="shortcut icon" href="<?= base_url ?>public/imagen/sistem/icon.ico" type="image/x-icon">

  <!-- CORE -->
  <script src="<?= base_url ?>public/vendor/jquery/jquery.js"></script>
  <script src="<?= base_url ?>public/scripts.js?v=<?= APP_VERSION ?>"></script>
  <link rel="manifest" href="<?= base_url ?>manifest.json">
  <script src="<?= base_url ?>sw.js" type="text/javascript"></script>

  <!-- Vegas -->
  <script src="<?= base_url ?>public/vendor/vegas/vegas.min.js"></script>
  <link rel="stylesheet" href="<?= base_url ?>public/vendor/vegas/vegas.min.css">

  <title><?php echo isset($_SESSION['infoClient']) ? $_SESSION['infoClient']['company'] : "app.wsistems.com";  ?></title>


  <?php if (isset($_SESSION['admin']) || isset($_SESSION['identity'])) { ?>

    <link rel="stylesheet" href="<?= base_url ?>public/style.css?v=<?= APP_VERSION ?>" type="text/css">

  <?php } else { ?>

    <link rel="stylesheet" href="<?= base_url ?>public/login.css?v=<?= APP_VERSION ?>" type="text/css">

  <?php } ?>

  <!-- Lazy loading -->
  <?php require_once "includes/lazy_load.php"; ?>

  <!-- HTML5-QRcode -->
  <script src="https://unpkg.com/html5-qrcode"></script>

  <!-- Font-Awesome -->
  <link rel="preload" href="<?= base_url ?>public/vendor/font-awesome/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <script src="<?= base_url ?>public/vendor/font-awesome/all.min.js" type="text/javascript"></script>

  <!-- Material toast -->
  <link rel="stylesheet" href="<?= base_url ?>public/vendor/mdtoast/mdtoast.min.css" type="text/css">
  <script src="<?= base_url ?>public/vendor/mdtoast/mdtoast.min.js" type="text/javascript"></script>

  <!-- Bootstrap4 -->
  <script src="<?= base_url ?>public/vendor/bootstrap4/popper.min.js" type="text/javascript"></script>
  <script src="<?= base_url ?>public/vendor/bootstrap4/bootstrap.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="<?= base_url ?>public/vendor/bootstrap4/bootstrap.min.css" type="text/css">

  <!-- Data Table -->
  <link rel="stylesheet" href="<?= base_url ?>public/vendor/datatable/dataTables.bootstrap4.min.css" type="text/css">
  <script src="<?= base_url ?>public/vendor/datatable/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="<?= base_url ?>public/vendor/datatable/dataTables.bootstrap4.min.js"></script>

  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url ?>public/vendor//select2/select2.min.css" type="text/css">
  <script src="<?= base_url ?>public/vendor/select2/select2.full.min.js" type="text/javascript"></script>

  <!-- Alertify -->
  <link rel="stylesheet" href="<?= base_url ?>public/vendor/alertify/css/alertify.min.css">
  <link rel="stylesheet" href="<?= base_url ?>public/vendor/alertify/css/themes/default.min.css">
  <script src="<?= base_url ?>public/vendor/alertify/alertify.min.js"></script>

  <!-- QZ Tray -->
  <script src="<?= base_url ?>public/vendor/qz/qz-tray.js"></script>

</head>

<body>

  <?php if (isset($_SESSION['admin']) || isset($_SESSION['identity'])): ?>

    <?php require_once 'includes/sidebar.php'; ?>

    <!-- ================ HEADER ==================== -->

    <header class="admin-bar clearfix">
      <section class="content-bar">
        <div class="admin-left">

          <!-- Menu rapido -->
          <nav class="nav-container">
            <ul>
              <li><a href="<?= base_url ?>invoices/addpurchase">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/bill.png" alt="">
                  Factura de venta</a></li>

              <li data-toggle="modal" data-target="#modalComanda"><a href="#">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/sort.png" alt="">
                  Orden de venta</a></li>

              <li data-toggle="modal" data-target="#orden"><a href="#">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/clipboard.png" alt="">
                  Orden de reparación</a></li>

              <li><a href="<?= base_url ?>invoices/quote">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/advice.png" alt="">
                  Cotización</a></li>

              <li><a href="<?= base_url ?>payments/add">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/pay.png" alt="">
                  Pagar factura</a></li>

              <li><a href="<?= base_url ?>bills/addbills">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/expense.png" alt="">
                  Agregar gasto</a></li>

              <li><a href="<?= base_url ?>products/add">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/add-item.png" alt="">
                  Agregar producto</a></li>

              <li><a href="<?= base_url ?>services/add">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/service.png" alt="">
                  Agregar servicio</a></li>

              <li><a href="<?= base_url ?>reports/querys">
                  <img class="nav-icon" src="<?= base_url ?>/public/imagen/icons/analytics.png" alt="">
                  Consultas</a></li>
            </ul>
          </nav>


          <div id="menuToggle">
            <input type="checkbox" />
            <span></span>
            <span></span>
            <span></span>

            <ul id="accordion-movil" class="accordion menu-movil">

              <!-- user-section -->
              <div class="user-section">
                <div class="user-info">
                  <div class="user-icon">
                    <i class="fas fa-user"></i>
                  </div>

                  <div class="user-details">
                    <span class="username"><?= $_SESSION['identity']->nombre ?></span>
                  </div>
                </div>

                <div class="user-actions">
                  <a href="<?= base_url ?>users/edit&id=<?= $_SESSION['identity']->usuario_id ?>">Perfil</a>
                  <a href="#" id="logout-movil"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
              </div>

              <?php
              // Mostrar las secciones del menú
              foreach ($menu_sections as $section) {
                display_menu_item($section);
              }
              ?>

            </ul>
          </div>

          <!-- buscador -->
          <div class="explorer">
            <input type="text" class="form-custom" name="" placeholder="Buscar producto, contactos, ordenes y facturas" id="keyword">

            <!-- Botón scanner -->
            <button type="button" class="scanner-svg" id="scannerExplorer">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-scan-barcode-icon lucide-scan-barcode">
                <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                <path d="M8 7v10" />
                <path d="M12 7v10" />
                <path d="M17 7v10" />
              </svg>
            </button>

            <!-- Overlay del scanner -->
            <div id="scanner-overlay">
              <div id="reader"></div>
              <button id="closeScanner">✕</button>
            </div>

            <ul id="search_result"></ul>
          </div>

          <!-- Menu rapido icon -->
          <span id="bar-menu" data-title="Menu"><i class="fas fa-th"></i></span>
        </div> <!-- admin-left -->


        <div class="admin-right">
          <div class="content-bar-info">

            <div class="num-order">
              <a href="<?= base_url ?>" data-title="Ordenes de ventas">
                <?php
                $order = Help::numOrderAlert();
                $className = "";

                if ($order < 10) {
                  $className .= "alert-notify alert-notify-xs alert-num-order";
                } elseif ($order > 99) {
                  $className .= "alert-notify alert-notify-lg alert-num-order";
                  $order = "99+";
                } else {
                  $className .= "alert-notify alert-notify-md alert-num-order";
                }
                ?>
                <span class="<?= $className ?>">
                  <i class="fas fa-clipboard-list"></i>
                  <p><?= $order; ?></p>
                </span>
              </a>
            </div>

            <!-- fuera de stock -->
            <div class="out-stock">
              <a href="<?= base_url ?>products/stock" data-title="Casi agotados">
                <?php
                $stock = Help::minStockProductAlert();
                $className = "";

                if ($stock < 10) {
                  $className .= "alert-notify alert-notify-xs alert-out-stock";
                } elseif ($stock > 99) {
                  $className .= "alert-notify alert-notify-lg alert-out-stock";
                  $stock = "99+"; // Nuevo stock
                } else {
                  $className .= "alert-notify alert-notify-md alert-out-stock";
                }
                ?>
                <span class="<?= $className ?>">
                  <i class="fas fa-inbox"></i>
                  <p><?= $stock; ?></p>
                </span>
              </a>
            </div>
          </div>

        </div> <!-- admin-right -->
      </section> <!-- content-bar -->
    </header>


    <!-- ================ SIDEBAR (APP-MENU) ==================== -->

    <aside class="sidebar clearfix">

      <!-- HEADER (usuario) -->
      <div class="sidebar-header">

        <div class="user-header">
          <div class="user-avatar">
            <?= strtoupper(substr($_SESSION['identity']->nombre, 0, 1)) ?>
          </div>

          <div class="user-info">
            <span class="user-name">
              <?= $_SESSION['identity']->nombre ?>
            </span>
            <span class="user-role">
              <?= $_SESSION['identity']->nombre_rol ?>
            </span>
          </div>

          <!-- BOTÓN CERRAR SESIÓN -->
          <a href="<?= base_url ?>users/logout" class="user-logout" id="logout">
            <i class="fas fa-sign-out-alt"></i>
          </a>
        </div>

      </div>

      <!-- MENU -->
      <nav class="sidebar-menu">
        <ul id="accordion" class="accordion">
          <?php
          foreach ($menu_sections as $section) {
            display_menu_item($section);
          }
          ?>
        </ul>
      </nav>

      <!-- FOOTER (FUERA del nav) -->
      <div class="sidebar-footer">
        <ul class="accordion">
          <?php
          foreach ($sidebar_footer as $item) {
            display_menu_item($item, true);
          }
          ?>
        </ul>
      </div>


    </aside>

    <!-- ================ CONTENIDO ==================== -->

    <div class="main wrap">
      <main>

        <?php require_once 'includes/modal-global.php'; ?> <!-- Modals goblales -->
      <?php endif; ?><!-- verificar sesion activa -->