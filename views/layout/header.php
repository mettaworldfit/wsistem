<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=0.9, user-scalable=no, shrink-to-fit=no">
  <meta name="title" content="Sistema de Control de Inventario">
  <meta name="description"
    content="Obtenga el control de su negocio desde cualquier lugar, vea estadísticas de ventas, stock, etc, “WS SYSTEM” es un Sistema de Ventas Web que te ayudará a obtener todos estos beneficios, desarrollado para tener el control total de un negocio en forma ordenada, sencilla y efectiva, posee los módulos necesarios con diferentes funciones que te permitirá administrar tu negocio." />
  <meta name="author" content="codevrd">
  <meta http-equiv="cache-control" content="no-cache">
  <meta http-equiv="expires" content="86400">
  <meta name="robots" content="noindex">

  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="application-name" content="cubic cloud">
  <meta name="apple-mobile-web-app-title" content="cubic cloud">
  <meta name="theme-color" content="#000000">
  <meta name="msapplication-navbutton-color" content="#000000">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

  <link rel="shortcut icon" href="<?= base_url ?>public/imagen/sistem/icon.ico" type="image/x-icon">

  <script src="<?= base_url ?>public/jquery/jquery.js"></script>
  <script src="<?= base_url ?>public/scripts.js"></script>


  <link rel="manifest" href="<?= base_url ?>manifest.json">
  <script src="<?= base_url ?>sw.js" type="text/javascript"></script>

  <title><?php echo isset($_SESSION['infoClient']) ? $_SESSION['infoClient']['company'] : "app.wsistems.com";  ?></title>


  <?php if (isset($_SESSION['admin']) || isset($_SESSION['identity'])) { ?>

    <link rel="stylesheet" href="<?= base_url ?>public/style.css" type="text/css">

  <?php } else { ?>

    <link rel="stylesheet" href="<?= base_url ?>public/login.css" type="text/css">

    <!-- Jquery Vegas -->
    <link rel="stylesheet" href="<?= base_url ?>public/jquery-vegas/vegas.min.css" type="text/css">
    <script src="<?= base_url ?>public/jquery-vegas/vegas.min.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/jquery-vegas/main.js" type="text/javascript"></script>

  <?php } ?>

  <!-- Scripts -->

  <script src="<?= base_url ?>public/functions/users.js" type="text/javascript"></script>
  <?php require_once "lazy_load.php"; ?>


  <!-- Material toast -->
  <link rel="stylesheet" href="<?= base_url ?>public/mdtoast/mdtoast.min.css" type="text/css">
  <script src="<?= base_url ?>public/mdtoast/mdtoast.min.js" type="text/javascript"></script>

  <!-- Font-Awesome -->

  <link rel="stylesheet" href="<?= base_url ?>public/font-awesome/all.min.css" type="text/css">
  <script src="<?= base_url ?>public/font-awesome/all.min.js" type="text/javascript"></script>

  <!-- Bootstrap4 -->

  <script src="<?= base_url ?>public/bootstrap4/popper.min.js" type="text/javascript"></script>
  <script src="<?= base_url ?>public/bootstrap4/bootstrap.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="<?= base_url ?>public/bootstrap4/bootstrap.min.css" type="text/css">

  <!-- Data Table -->

  <link rel="stylesheet" href="<?= base_url ?>public/datatable/dataTables.bootstrap4.min.css" type="text/css">

  <script src="<?= base_url ?>public/datatable/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="<?= base_url ?>public/datatable/dataTables.bootstrap4.min.js"></script>

  <!-- Select2 -->

  <link rel="stylesheet" href="<?= base_url ?>public/select2/select2.min.css" type="text/css">
  <script src="<?= base_url ?>public/select2/select2.full.min.js" type="text/javascript"></script>

  <!-- AlertifyJS CDN-->

  <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css" />

</head>

<body>

  <?php if (isset($_SESSION['admin']) || isset($_SESSION['identity'])): ?>

    <section class="contenido">

      <!-- Manú -->

      <header class="admin-bar clearfix">

        <div class="container-logo">
          <!-- Logo -->
          <!-- <img src="<?= base_url ?>public/imagen/sistem/" alt="" class="logo"> -->
        </div>

        <section class="content-bar">
          <div class="admin-left">
            <span id="bar-menu"><i class="fas fa-bars"></i></span>

            <nav class="nav-container">
              <ul>
                <li><a href="<?= base_url ?>invoices/addpurchase">Nueva venta</a></li>
                <li><a href="<?= base_url ?>invoices/addrepair">Nueva reparación</a></li>
                <li><a href="<?= base_url ?>products/add">Nuevo producto</a></li>
                <li><a href="<?= base_url ?>pieces/add">Nueva pieza</a></li>
                <li><a href="<?= base_url ?>contacts/add">Nuevo contacto</a></li>
                <li><a href="<?= base_url ?>payments/add">Nuevo pago</a></li>

              </ul>
            </nav>


            <!-- menú movil -->
            <div id="menuToggle">
              <input type="checkbox" />
              <span></span>
              <span></span>
              <span></span>

              <ul id="accordion-movil" class="accordion menu-movil">
                <li>
                  <div class="link"><a href="<?= base_url ?>home/index"><i class="mr-3 fas fa-home"></i>Inicio</a></div>
                  </li>

                  <li class="dropdown-1">
                    <div class="link"><i class="mr-3 fas fa-arrow-circle-down"></i>Ingresos <i
                        class="fas fa-chevron-down"></i></div>
                    <ul class="submenu">
                      <li class="page"><a href="<?= base_url ?>invoices/index">Facturas de ventas</a> <a
                          href="<?= base_url ?>invoices/addpurchase"><i class="fas fa-plus-circle"></i></a></li>
                      <li><a href="<?= base_url ?>invoices/index_repair">Facturas de reparaciones</a> </li>
                      <li class="page"><a href="<?= base_url ?>payments/index">Pagos</a> <a
                          href="<?= base_url ?>payments/add"><i class="fas fa-plus-circle"></i></a></li>
                    </ul>
                  </li>

                  <li class="dropdown-2">
                    <div class="link"><i class="mr-3 fas fa-arrow-circle-up"></i>Egresos <i
                        class="fas fa-chevron-down"></i></div>
                    <ul class="submenu ">
                      <li class="page"><a href="<?= base_url ?>bills/invoices">Facturas de proveedores</a> <a
                          href="<?= base_url ?>bills/addinvoice"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>bills/bills">Gastos</a> <a
                          href="<?= base_url ?>bills/addbills"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>bills/payments">Pagos</a> <a
                          href="<?= base_url ?>bills/add_payment"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>bills/orders">Órdenes de compras</a> <a
                          href="<?= base_url ?>bills/add_order"><i class="fas fa-plus-circle"></i></a></li>

                    </ul>
                  </li>

                  <li class="dropdown-3">
                    <div class="link"><i class="mr-3 fas fa-tools"></i>Servicios <i class="fas fa-chevron-down"></i></div>
                    <ul class="submenu ">
                      <li><a href="<?= base_url ?>workshop/index">Órdenes de servicios</a> </li>

                    </ul>
                  </li>

                  <li class="dropdown-4">
                    <div class="link"><i class="mr-3 fas fa-box"></i>Inventario <i class="fas fa-chevron-down"></i></div>
                    <ul class="submenu ">
                      <li class="page"><a href="<?= base_url ?>products/index">Productos</a> <a
                          href="<?= base_url ?>products/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>pieces/index">Piezas</a> <a
                          href="<?= base_url ?>pieces/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>services/index">Servicios</a> <a
                          href="<?= base_url ?>services/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li><a href="<?= base_url ?>inventory_control/inventory">Valor de inventario</a></li>
                      <li class="page"><a href="<?= base_url ?>warehouses/index">Almacenes</a> <a
                          href="<?= base_url ?>warehouses/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>categories/index">Categorías</a> <a
                          href="<?= base_url ?>categories/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>price_lists/index">Listas de precios</a> <a
                          href="<?= base_url ?>price_lists/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>brands/index">Marcas</a> <a
                          href="<?= base_url ?>brands/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>taxes/index">Impuestos</a> <a
                          href="<?= base_url ?>taxes/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>positions/index">Posiciones</a> <a
                          href="<?= base_url ?>positions/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>offers/index">Ofertas</a> <a
                          href="<?= base_url ?>offers/add"><i class="fas fa-plus-circle"></i></a></li>

                    </ul>
                  </li>

                  <li class="dropdown-5">
                    <div class="link"><i class="mr-3 fas fa-address-book"></i>Contactos <i
                        class="fas fa-chevron-down"></i></div>
                    <ul class="submenu">
                      <li class="page"><a href="<?= base_url ?>contacts/customers">Clientes</a> <a
                          href="<?= base_url ?>contacts/add"><i class="fas fa-plus-circle"></i></a></li>
                      <li class="page"><a href="<?= base_url ?>contacts/providers">Proveedores</a> <a
                          href="<?= base_url ?>contacts/add"><i class="fas fa-plus-circle"></i></a></li>
                    </ul>
                  </li>

                  <li class="dropdown-6">
                    <div class="link"><i class="mr-3 fas fa-chart-bar"></i>Reportes <i class="fas fa-chevron-down"></i>
                    </div>
                    <ul class="submenu">
                      <li class="page"><a href="<?= base_url ?>reports/day">Ventas del día</a></li>
                      <li class="page"><a href="<?= base_url ?>reports/querys">Consultas</a></li>

                      <!-- <li class="page"><a href="<?= base_url ?>reports/month">Ventas del mes</a> </li>
                <li class="page"><a href="<?= base_url ?>reports/date">Reporte de fecha</a> </li> -->
                      <li class="page"><a href="<?= base_url ?>reports/spendings">Reporte de gastos</a></li>
                      <li class="page"><a href="<?= base_url ?>reports/pending">Cuentas por cobrar</a> </li>

                    </ul>
                  </li>

                  <li>
                    <div class="link"><a href="<?= base_url ?>users/index"><i class="mr-3 fas fa-users"></i>Usuarios</a>
                    </div>
                  </li>

                  <?php if ($_SESSION['identity']->nombre_rol == 'administrador'): ?>
                    <li>
                      <div class="link"><a href="<?= base_url ?>config/index"><i
                            class="mr-3 fas fa-cog"></i>Configuración</a></div>
                    </li>
                  <?php endif; ?>
              </ul>
            </div>


          </div>


          <div class="admin-right">
            <div class="content-bar-info">


              <div class="out-stock">
                <a href="<?= base_url ?>products/stock" title="Casi agotados">
                <?php 

                   $stock = Help::MinStockProduct();
                   $className = "";

                   if($stock < 10) {
                      $className .= "alert-notify alert-notify-xs";
                   } elseif ($stock > 99) {
                    $className .= "alert-notify alert-notify-lg";
                    $stock = "99+"; // Nuevo stock
                   } else {
                    $className .= "alert-notify alert-notify-md";
                   }
                
                ?>

                  <span class="<?= $className ?>">
                  <i class="fas fa-inbox"></i>
                     <p><?= $stock; ?></p>
                  </span>

                </a>
              </div>



              <div class="user">
                <span><i class="fas fa-user-circle"></i></span>
                <span><?= $_SESSION['identity']->nombre ?></span>
              </div>

            </div>

            <nav class="nav-user">
              <ul class="user-menu">
                <li><a href="<?= base_url ?>users/edit&id=<?= $_SESSION['identity']->usuario_id ?>">Perfil</a></li>
                <li id="logout"><a href="#">Cerrar sesión</a></li>
              </ul>
            </nav>
          </div>

        </section>

      </header>

      <!-- Nav-sidebar -->

      <aside class="sidebar clearfix">
        <nav class="app-menu">

          <ul id="accordion" class="accordion">
            <li>
              <div class="link"><a href="<?= base_url ?>home/index"><i class="mr-3 fas fa-home"></i>Inicio</a></div>
            </li>

            <li class="dropdown-1">
              <div class="link"><i class="mr-3 fas fa-arrow-circle-down"></i>Ingresos <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="submenu">
                <li class="page"><a href="<?= base_url ?>invoices/index">Facturas de ventas</a> <a
                    href="<?= base_url ?>invoices/addpurchase"><i class="fas fa-plus-circle"></i></a></li>
                <li><a href="<?= base_url ?>invoices/index_repair">Facturas de reparaciones</a> </li>
                <li class="page"><a href="<?= base_url ?>invoices/quotes">Cotizaciones</a> <a
                href="<?= base_url ?>invoices/quote"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>payments/index">Pagos</a> <a
                    href="<?= base_url ?>payments/add"><i class="fas fa-plus-circle"></i></a></li>
              </ul>
            </li>

            <li class="dropdown-2">
              <div class="link"><i class="mr-3 fas fa-arrow-circle-up"></i>Egresos <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="submenu ">
                <li class="page"><a href="<?= base_url ?>bills/invoices">Facturas de proveedores</a> <a
                    href="<?= base_url ?>bills/addinvoice"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>bills/bills">Gastos</a> <a
                    href="<?= base_url ?>bills/addbills"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>bills/payments">Pagos</a> <a
                    href="<?= base_url ?>bills/add_payment"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>bills/orders">Órdenes de compras</a> <a
                    href="<?= base_url ?>bills/add_order"><i class="fas fa-plus-circle"></i></a></li>

              </ul>
            </li>

            <li class="dropdown-3">
              <div class="link"><i class="mr-3 fas fa-tools"></i>Servicios <i class="fas fa-chevron-down"></i></div>
              <ul class="submenu ">
                <li><a href="<?= base_url ?>workshop/index">Órdenes de servicios</a> </li>

              </ul>
            </li>

            <li class="dropdown-4">
              <div class="link"><i class="mr-3 fas fa-box"></i>Inventario <i class="fas fa-chevron-down"></i></div>
              <ul class="submenu ">
                <li class="page"><a href="<?= base_url ?>products/index">Productos</a> <a
                    href="<?= base_url ?>products/add"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>pieces/index">Piezas</a> <a href="<?= base_url ?>pieces/add"><i
                      class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>services/index">Servicios</a> <a
                    href="<?= base_url ?>services/add"><i class="fas fa-plus-circle"></i></a></li>
                <li><a href="<?= base_url ?>inventory_control/inventory">Valor de inventario</a></li>
                <li class="page"><a href="<?= base_url ?>warehouses/index">Almacenes</a> <a
                    href="<?= base_url ?>warehouses/add"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>categories/index">Categorías</a> <a
                    href="<?= base_url ?>categories/add"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>price_lists/index">Listas de precios</a> <a
                    href="<?= base_url ?>price_lists/add"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>brands/index">Marcas</a> <a href="<?= base_url ?>brands/add"><i
                      class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>taxes/index">Impuestos</a> <a href="<?= base_url ?>taxes/add"><i
                      class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>positions/index">Posiciones</a> <a
                    href="<?= base_url ?>positions/add"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>offers/index">Ofertas</a> <a href="<?= base_url ?>offers/add"><i
                      class="fas fa-plus-circle"></i></a></li>

              </ul>
            </li>

            <li class="dropdown-5">
              <div class="link"><i class="mr-3 fas fa-address-book"></i>Contactos <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="submenu">
                <li class="page"><a href="<?= base_url ?>contacts/customers">Clientes</a> <a
                    href="<?= base_url ?>contacts/add"><i class="fas fa-plus-circle"></i></a></li>
                <li class="page"><a href="<?= base_url ?>contacts/providers">Proveedores</a> <a
                    href="<?= base_url ?>contacts/add"><i class="fas fa-plus-circle"></i></a></li>
              </ul>
            </li>

            <li class="dropdown-6">
              <div class="link"><i class="mr-3 fas fa-chart-bar"></i>Reportes <i class="fas fa-chevron-down"></i></div>
              <ul class="submenu">
                <li class="page"><a href="<?= base_url ?>reports/day">Ventas del día</a></li>
                <li class="page"><a href="<?= base_url ?>reports/querys">Consultas</a></li>
                <!-- <li class="page"><a href="<?= base_url ?>reports/month">Ventas del mes</a> </li>
                <li class="page"><a href="<?= base_url ?>reports/date">Reporte de fecha</a> </li> -->
                <li class="page"><a href="<?= base_url ?>reportss/pending">Cuentas por cobrar</a> </li>

              </ul>
            </li>

            <li>
              <div class="link"><a href="<?= base_url ?>users/index"><i class="mr-3 fas fa-users"></i>Usuarios</a></div>
            </li>

            <?php if ($_SESSION['identity']->nombre_rol == 'administrador'): ?>
              <li>
                <div class="link"><a href="<?= base_url ?>config/index"><i class="mr-3 fas fa-cog"></i>Configuración</a>
                </div>
              </li>
            <?php endif; ?>





          </ul>


        </nav>
      </aside>



      <div class="main wrap">
        <main>


        <?php endif; ?>