<?php


class Help
{


   public static function getDailyProfit()
   {

      $db = Database::connect();

      $query = "SELECT SUM(ganancia) AS total_ganancias FROM (

  -- Ganancia Productos en facturas de ventas hoy
  SELECT SUM(
    (f.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_facturas_ventas d
  INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
  INNER JOIN (
    SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_facturas_ventas
    WHERE fecha = CURDATE()
    GROUP BY factura_venta_id
  ) ft ON ft.factura_venta_id = f.factura_venta_id
  INNER JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
  INNER JOIN productos p ON p.producto_id = dp.producto_id
  WHERE d.fecha = CURDATE()

  UNION ALL

  -- Ganancia Piezas en facturas de ventas hoy
  SELECT SUM(
    (f.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_facturas_ventas d
  INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
  INNER JOIN (
    SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_facturas_ventas
    WHERE fecha = CURDATE()
    GROUP BY factura_venta_id
  ) ft ON ft.factura_venta_id = f.factura_venta_id
  INNER JOIN detalle_ventas_con_piezas_ dp ON dp.detalle_venta_id = d.detalle_venta_id
  INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
  WHERE d.fecha = CURDATE()

  UNION ALL

  -- Ganancia Piezas en órdenes de reparación hoy
  SELECT SUM(
    (frp.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_ordenRP d
  INNER JOIN facturasRP frp ON frp.orden_rp_id = d.orden_rp_id
  INNER JOIN (
    SELECT orden_rp_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_ordenRP
    WHERE fecha = CURDATE()
    GROUP BY orden_rp_id
  ) ft ON ft.orden_rp_id = frp.orden_rp_id
  INNER JOIN detalle_ordenRP_con_piezas dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
  INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
  WHERE d.fecha = CURDATE()

  UNION ALL

  -- Ganancia Servicios en facturas de ventas hoy
  SELECT SUM(
    (f.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_facturas_ventas d
  INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
  INNER JOIN (
    SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_facturas_ventas
    WHERE fecha = CURDATE()
    GROUP BY factura_venta_id
  ) ft ON ft.factura_venta_id = f.factura_venta_id
  INNER JOIN detalle_ventas_con_servicios ds ON ds.detalle_venta_id = d.detalle_venta_id
  INNER JOIN servicios s ON s.servicio_id = ds.servicio_id
  WHERE d.fecha = CURDATE()

  UNION ALL

  -- Ganancia Servicios en órdenes de reparación hoy
  SELECT SUM(
    (frp.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_ordenRP d
  INNER JOIN facturasRP frp ON frp.orden_rp_id = d.orden_rp_id
  INNER JOIN (
    SELECT orden_rp_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_ordenRP
    WHERE fecha = CURDATE()
    GROUP BY orden_rp_id
  ) ft ON ft.orden_rp_id = frp.orden_rp_id
  INNER JOIN detalle_ordenRP_con_servicios dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
  INNER JOIN servicios s ON s.servicio_id = dp.servicio_id
  WHERE d.fecha = CURDATE()

) ganancias_dia;";


      return $db->query($query)->fetch_object()->total_ganancias;
   }


   public static function getMonthProfit()
   {
      $query = "SELECT SUM(ganancia) AS total_ganancias FROM (

  -- Ganancia Productos en facturas de ventas
  SELECT SUM(
    (f.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_facturas_ventas d
  INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
  INNER JOIN (
    SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_facturas_ventas
    WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
    GROUP BY factura_venta_id
  ) ft ON ft.factura_venta_id = f.factura_venta_id
  INNER JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
  INNER JOIN productos p ON p.producto_id = dp.producto_id
  WHERE d.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())

  UNION ALL

  -- Ganancia Piezas en facturas de ventas
  SELECT SUM(
    (f.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_facturas_ventas d
  INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
  INNER JOIN (
    SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_facturas_ventas
    WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
    GROUP BY factura_venta_id
  ) ft ON ft.factura_venta_id = f.factura_venta_id
  INNER JOIN detalle_ventas_con_piezas_ dp ON dp.detalle_venta_id = d.detalle_venta_id
  INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
  WHERE d.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())

  UNION ALL

  -- Ganancia Piezas en ordenes de reparacion
  SELECT SUM(
    (frp.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_ordenRP d
  INNER JOIN facturasRP frp ON frp.orden_rp_id = d.orden_rp_id
  INNER JOIN (
    SELECT orden_rp_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_ordenRP
    WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
    GROUP BY orden_rp_id
  ) ft ON ft.orden_rp_id = frp.orden_rp_id
  INNER JOIN detalle_ordenRP_con_piezas dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
  INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
  WHERE d.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())

  UNION ALL

  -- Ganancia Servicios en facturas de ventas
  SELECT SUM(
    (f.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_facturas_ventas d
  INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
  INNER JOIN (
    SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_facturas_ventas
    WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
    GROUP BY factura_venta_id
  ) ft ON ft.factura_venta_id = f.factura_venta_id
  INNER JOIN detalle_ventas_con_servicios ds ON ds.detalle_venta_id = d.detalle_venta_id
  INNER JOIN servicios s ON s.servicio_id = ds.servicio_id
  WHERE d.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())

  UNION ALL

  -- Ganancia Servicios en ordenes de reparacion
  SELECT SUM(
    (frp.recibido / NULLIF(ft.total_facturado, 0)) *
    (
      (d.precio * d.cantidad - d.descuento) -
      COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)
    )
  ) AS ganancia
  FROM detalle_ordenRP d
  INNER JOIN facturasRP frp ON frp.orden_rp_id = d.orden_rp_id
  INNER JOIN (
    SELECT orden_rp_id, SUM(precio * cantidad - descuento) AS total_facturado
    FROM detalle_ordenRP
    WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
    GROUP BY orden_rp_id
  ) ft ON ft.orden_rp_id = frp.orden_rp_id
  INNER JOIN detalle_ordenRP_con_servicios dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
  INNER JOIN servicios s ON s.servicio_id = dp.servicio_id
  WHERE d.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())

) ganancias_mes_actual;";

      $db = Database::connect();
      return $db->query($query)->fetch_object()->total_ganancias;
   }


   public static function getTotalProducts()
   {
      $db = Database::connect();
      $query = "SELECT count(producto_id) as total FROM productos";

      return $db->query($query)->fetch_object()->total;
   }

   public static function getTotalPieces()
   {
      $db = Database::connect();
      $query = "SELECT count(pieza_id) as total FROM piezas";

      return $db->query($query)->fetch_object()->total;
   }

   public static function getTotalCustomers()
   {
      $db = Database::connect();
      $query = "SELECT count(cliente_id) as total FROM clientes";

      return $db->query($query)->fetch_object()->total;
   }

   public static function getTotalProviders()
   {
      $db = Database::connect();
      $query = "SELECT count(proveedor_id) as total FROM proveedores";

      return $db->query($query)->fetch_object()->total;
   }


   public static function getDailySalesByPaymentMethod($metodo_id)
   {
      $db = Database::connect();
      $config = Database::getConfig();
      $user_id = $_SESSION['identity']->usuario_id;

      $user_condition = "";  // Inicialización de la variable

      // Verificar el valor de modo_cierre y modificar la variable según corresponda
      if (isset($config['modo_cierre']) && $config['modo_cierre'] === "separado" && $_SESSION['identity']->nombre_rol != 'administrador') {
         $user_condition = "AND x.usuario_id = '$user_id'";
      }

      // Determinar el rango de fechas
      $condition = isset($config['auto_cierre']) && $config['auto_cierre'] === 'false'
         ? "CONCAT(x.fecha, ' ', x.hora) >= (SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1)"
         : "x.fecha = CURDATE()";

      $query = "SELECT SUM(total) AS total 
         FROM (
            -- Subconsulta 1: Facturas ventas
            SELECT (x.recibido - IFNULL(SUM(p.recibido), 0)) AS total, x.fecha
            FROM facturas_ventas x
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            LEFT JOIN pagos_a_facturas_ventas pf ON pf.factura_venta_id = x.factura_venta_id
            LEFT JOIN pagos p ON pf.pago_id = p.pago_id
            INNER JOIN detalle_facturas_ventas d ON d.factura_venta_id = x.factura_venta_id
            WHERE $condition $user_condition AND x.metodo_pago_id = '$metodo_id'
            GROUP BY x.factura_venta_id

            UNION ALL

            -- Subconsulta 2: Facturas RP
            SELECT (x.recibido - IFNULL(SUM(p.recibido), 0)) AS total, x.fecha
            FROM facturasRP x
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            LEFT JOIN pagos_a_facturasRP pf ON pf.facturaRP_id = x.facturaRP_id
            LEFT JOIN pagos p ON pf.pago_id = p.pago_id
            INNER JOIN detalle_ordenRP d ON d.orden_rp_id = x.orden_rp_id
            WHERE $condition $user_condition AND x.metodo_pago_id = '$metodo_id'
            GROUP BY x.facturaRP_id

            UNION ALL

            -- Subconsulta 3: Pagos RP
            SELECT x.recibido AS total, x.fecha
            FROM pagos_a_facturasRP pf 
            INNER JOIN pagos x ON pf.pago_id = x.pago_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            WHERE $condition $user_condition AND x.metodo_pago_id = '$metodo_id'

            UNION ALL

            -- Subconsulta 4: Pagos ventas
            SELECT x.recibido AS total, x.fecha
            FROM pagos_a_facturas_ventas pf 
            INNER JOIN pagos x ON pf.pago_id = x.pago_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            WHERE $condition $user_condition AND x.metodo_pago_id = '$metodo_id'
            ) ventas_por_metodo;";

      return $db->query($query)->fetch_object()->total;
   }


   public static function getIssuedInvoices()
   {
      $db = Database::connect();
      $config = Database::getConfig();
      $user_id = $_SESSION['identity']->usuario_id;

      $user_condition = "";  // Inicialización de la variable

      // Verificar el valor de modo_cierre y modificar la variable según corresponda
      if (isset($config['modo_cierre']) && $config['modo_cierre'] === "separado" && $_SESSION['identity']->nombre_rol != 'administrador') {
         $user_condition = "AND x.usuario_id = '$user_id'";
      }

      // Determinar el rango de fechas
      $fecha_condicion = isset($config['auto_cierre']) && $config['auto_cierre'] === 'false'
         ? "CONCAT(x.fecha, ' ', x.hora) >= (SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1)"
         : "x.fecha = CURDATE()";

      // Generar la consulta
      $query = "SELECT SUM(facturas) AS total_facturas, SUM(pagos) AS total_pagos
      FROM (
      -- Subconsulta 1: Contar facturas ventas con detalles no vacíos
      SELECT COUNT(DISTINCT x.factura_venta_id) AS facturas, 0 AS pagos
      FROM facturas_ventas x
      INNER JOIN detalle_facturas_ventas d ON d.factura_venta_id = x.factura_venta_id
      WHERE $fecha_condicion $user_condition
      GROUP BY x.factura_venta_id

      UNION ALL

      -- Subconsulta 2: Contar facturas RP con detalles no vacíos
      SELECT COUNT(DISTINCT x.facturaRP_id) AS facturas, 0 AS pagos
      FROM facturasRP x
      INNER JOIN detalle_ordenRP d ON d.orden_rp_id = x.orden_rp_id
      WHERE $fecha_condicion $user_condition
      GROUP BY x.facturaRP_id

      UNION ALL

      -- Subconsulta 3: Contar pagos RP realizados hoy
      SELECT 0 AS facturas, COUNT(x.pago_id) AS pagos
      FROM pagos_a_facturasRP pf
      INNER JOIN pagos x ON pf.pago_id = x.pago_id
      WHERE $fecha_condicion $user_condition
      GROUP BY x.pago_id

      UNION ALL

      -- Subconsulta 4: Contar pagos ventas realizados hoy
      SELECT 0 AS facturas, COUNT(x.pago_id) AS pagos
      FROM pagos_a_facturas_ventas pf
      INNER JOIN pagos x ON pf.pago_id = x.pago_id
      WHERE $fecha_condicion $user_condition
      GROUP BY x.pago_id

      ) total_emision";

      return $db->query($query)->fetch_object();
   }


   public static function getTotalReal()
   {
      $db = Database::connect();
      $config = Database::getConfig();
      $user_id = $_SESSION['identity']->usuario_id;

      $user_condition = "";  // Inicialización de la variable

      // Verificar el valor de modo_cierre y modificar la variable según corresponda
      if (isset($config['modo_cierre']) && $config['modo_cierre'] === "separado" && $_SESSION['identity']->nombre_rol != 'administrador') {
         $user_condition = "AND x.usuario_id = '$user_id'";
      }

      // Determinar el rango de fechas
      $fecha_condicion = isset($config['auto_cierre']) && $config['auto_cierre'] === 'false'
         ? "CONCAT(x.fecha, ' ', x.hora) >= (SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1)"
         : "x.fecha = CURDATE()";

      // Generar la consulta
      $query = "SELECT SUM(total) AS total
        FROM (
        -- Subconsulta 1: Facturas ventas con detalles no vacíos
        SELECT (x.recibido - IFNULL(SUM(p.recibido), 0)) AS total
        FROM facturas_ventas x
        LEFT JOIN pagos_a_facturas_ventas pf ON pf.factura_venta_id = x.factura_venta_id
        LEFT JOIN pagos p ON pf.pago_id = p.pago_id
        INNER JOIN detalle_facturas_ventas d ON d.factura_venta_id = x.factura_venta_id
        WHERE $fecha_condicion $user_condition
        GROUP BY x.factura_venta_id

        UNION ALL

        -- Subconsulta 2: Facturas RP con detalles no vacíos
        SELECT (x.recibido - IFNULL(SUM(p.recibido), 0)) AS total
        FROM facturasRP x
        LEFT JOIN pagos_a_facturasRP pf ON pf.facturaRP_id = x.facturaRP_id
        LEFT JOIN pagos p ON pf.pago_id = p.pago_id
        INNER JOIN detalle_ordenRP d ON d.orden_rp_id = x.orden_rp_id
        WHERE $fecha_condicion $user_condition
        GROUP BY x.facturaRP_id

        UNION ALL

        -- Subconsulta 3: Pagos RP
        SELECT SUM(x.recibido) AS total
        FROM pagos_a_facturasRP pf
        INNER JOIN pagos x ON pf.pago_id = x.pago_id
        WHERE $fecha_condicion $user_condition
        GROUP BY x.pago_id

        UNION ALL

        -- Subconsulta 4: Pagos ventas
        SELECT SUM(x.recibido) AS total
        FROM pagos_a_facturas_ventas pf
        INNER JOIN pagos x ON pf.pago_id = x.pago_id
        WHERE $fecha_condicion $user_condition
        GROUP BY x.pago_id
      ) ventas_reale;";

      return $db->query($query)->fetch_object()->total;
   }

   public static function getPurchaseToday()
   {
      $db = Database::connect();
      $config = Database::getConfig();
      $user_id = $_SESSION['identity']->usuario_id;

      $user_condition = "";  // Inicialización de la variable

      // Verificar el valor de modo_cierre y modificar la variable según corresponda
      if (isset($config['modo_cierre']) && $config['modo_cierre'] === "separado" && $_SESSION['identity']->nombre_rol != 'administrador') {
         $user_condition = "AND x.usuario_id = '$user_id'";
      }

      // Determinar el rango de fechas
      $fecha_condicion = isset($config['auto_cierre']) && $config['auto_cierre'] === 'false'
         ? "CONCAT(x.fecha, ' ', x.hora) >= (SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1)"
         : "x.fecha = CURDATE()";

      // Generar la consulta
      $query = "SELECT SUM(total) AS total
        FROM (
        -- Subconsulta 1: Facturas ventas con detalles no vacíos
        SELECT (x.recibido - IFNULL(SUM(p.recibido), 0)) AS total
        FROM facturas_ventas x
        LEFT JOIN pagos_a_facturas_ventas pf ON pf.factura_venta_id = x.factura_venta_id
        LEFT JOIN pagos p ON pf.pago_id = p.pago_id
        INNER JOIN detalle_facturas_ventas d ON d.factura_venta_id = x.factura_venta_id
        WHERE $fecha_condicion $user_condition
        GROUP BY x.factura_venta_id

        UNION ALL

        -- Subconsulta 2: Facturas RP con detalles no vacíos
        SELECT (x.recibido - IFNULL(SUM(p.recibido), 0)) AS total
        FROM facturasRP x
        LEFT JOIN pagos_a_facturasRP pf ON pf.facturaRP_id = x.facturaRP_id
        LEFT JOIN pagos p ON pf.pago_id = p.pago_id
        INNER JOIN detalle_ordenRP d ON d.orden_rp_id = x.orden_rp_id
        WHERE $fecha_condicion $user_condition
        GROUP BY x.facturaRP_id

        UNION ALL

        -- Subconsulta 3: Pagos RP
        SELECT SUM(x.recibido) AS total
        FROM pagos_a_facturasRP pf
        INNER JOIN pagos x ON pf.pago_id = x.pago_id
        WHERE $fecha_condicion $user_condition
        GROUP BY x.pago_id

        UNION ALL

        -- Subconsulta 4: Pagos ventas
        SELECT SUM(x.recibido) AS total
        FROM pagos_a_facturas_ventas pf
        INNER JOIN pagos x ON pf.pago_id = x.pago_id
        WHERE $fecha_condicion $user_condition
        GROUP BY x.pago_id
      ) ventas_reales;";

      return $db->query($query)->fetch_object()->total;
   }



   public static function getExpensesToday()
   {
      $db = Database::connect();
      $config = Database::getConfig();
      $user_id = $_SESSION['identity']->usuario_id;

      $user_condition = "";  // Inicialización de la variable

      // Verificar el valor de modo_cierre y modificar la variable según corresponda
      if (isset($config['modo_cierre']) && $config['modo_cierre'] === "separado" && $_SESSION['identity']->nombre_rol != 'administrador') {
         $user_condition = "AND x.usuario_id = '$user_id'";
      }

      // Determinar el rango de fechas
      $fecha_condicion = isset($config['auto_cierre']) && $config['auto_cierre'] === 'false'
         ? "CONCAT(x.fecha, ' ', x.hora) >= (SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1)"
         : "x.fecha = CURDATE()";

      $query = "SELECT SUM(total) AS total
         FROM (
            -- Subconsulta 1: Gastos
            SELECT SUM(x.pagado) AS total, x.fecha
            FROM gastos x
            WHERE $fecha_condicion $user_condition 
            GROUP BY x.fecha

            UNION ALL

            -- Subconsulta 2: Facturas de Proveedores
            SELECT SUM(x.pagado) AS total, x.fecha
            FROM ordenes_compras o
            INNER JOIN facturas_proveedores x ON o.orden_id = x.orden_id
            WHERE o.estado_id = 12
            AND $fecha_condicion $user_condition
            GROUP BY x.fecha

            UNION ALL

            -- Subconsulta 3: Pagos Proveedores
            SELECT SUM(x.recibido) AS total, x.fecha
            FROM pagos_proveedores x
            WHERE $fecha_condicion $user_condition
            GROUP BY x.fecha
            
         ) AS gastos_hoy;";

      return $db->query($query)->fetch_object()->total;
   }


   public static function getOriginExpensesToday($origin)
   {
      $db = Database::connect();
      $config = Database::getConfig();
      $user_id = $_SESSION['identity']->usuario_id;

      $user_condition = "";  // Inicialización de la variable

      // Verificar el valor de modo_cierre y modificar la variable según corresponda
      if (isset($config['modo_cierre']) && $config['modo_cierre'] === "separado" && $_SESSION['identity']->nombre_rol != 'administrador') {
         $user_condition = "AND x.usuario_id = '$user_id'";
      }

      // Determinar el rango de fechas
      $condition = isset($config['auto_cierre']) && $config['auto_cierre'] === 'false'
         ? "CONCAT(x.fecha, ' ', x.hora) >= (SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1)"
         : "x.fecha = CURDATE()";


      $query = "SELECT SUM(total) AS total
         FROM (
            -- Subconsulta 1: Gastos
            SELECT SUM(x.pagado) AS total
            FROM gastos x
            INNER JOIN ordenes_gastos o ON o.orden_id = x.orden_id
            WHERE $condition $user_condition AND o.origen = '$origin'

            UNION ALL

            -- Subconsulta 2: Facturas de Proveedores
            SELECT SUM(x.pagado) AS total
            FROM ordenes_compras o
            INNER JOIN facturas_proveedores x ON o.orden_id = x.orden_id
            WHERE $condition $user_condition AND o.estado_id = 12

            UNION ALL

            -- Subconsulta 3: Pagos Proveedores
            SELECT SUM(x.recibido) AS total
            FROM pagos_proveedores x
            WHERE $condition $user_condition 

            ) AS origen_gastos;";

      return $db->query($query)->fetch_object()->total;
   }

   public static function getTotalInventoryValue()
   {

      $db = Database::connect();

      $query = "SELECT sum(total) as total, sum(bruto) as bruto  FROM (

            SELECT sum(p.cantidad * p.precio_costo) as 'total', sum(p.cantidad * p.precio_unitario) as 'bruto' FROM productos p
              UNION ALL
            SELECT sum(pz.cantidad * pz.precio_costo) as 'total', sum(pz.cantidad * pz.precio_unitario) as 'bruto' FROM piezas pz
                                  
          ) ValorInventario;";

      return $db->query($query);
   }





   public static function getCashOpening()
   {
      $db = Database::connect();
      $config = Database::getConfig();
      $user_id = $_SESSION['identity']->usuario_id;

      $user_condition = "";  // Inicialización de la variable

      // Verificar el valor de modo_cierre y modificar la variable según corresponda
      if (isset($config['modo_cierre']) && $config['modo_cierre'] === "separado") {
         $user_condition = "AND usuario_id = '$user_id'";
      }

      // Determinar el rango de fechas
      $condition = isset($config['auto_cierre']) && $config['auto_cierre'] === 'false'
         ? "estado = 'abierto'"
         : "estado = 'abierto' AND DATE(fecha_apertura) = CURDATE()";


      $query = "SELECT fecha_apertura, saldo_inicial, cierre_id
      FROM cierres_caja WHERE $condition $user_condition
      ORDER BY cierre_id DESC
      LIMIT 1";

      return $db->query($query)->fetch_object();
   }


   public static function ConfigPDF()
   {

      $db = Database::connect();

      $query = "SELECT *FROM configuraciones";

      // Ejecutar la consulta
      $result = $db->query($query);

      // Inicializar un array para almacenar los resultados
      $configurations = [];

      if ($result->num_rows > 0) {
         // Recorrer cada fila de los resultados
         while ($row = $result->fetch_object()) {
            // Almacenar cada fila en el array usando el 'config_key' como clave
            $configurations[$row->config_key] = $row->config_value;
         }
      }

      // Retornar el array con todas las configuraciones
      return $configurations;
   }

   public static function ConfigElectronicInvoice()
   {

      $db = Database::connect();

      // Consulta para obtener las configuraciones
      $query = "SELECT * FROM configuraciones";

      // Ejecutar la consulta
      $result = $db->query($query);

      // Inicializar un array para almacenar los resultados
      $configurations = [];

      if ($result->num_rows > 0) {
         // Recorrer cada fila de los resultados
         while ($row = $result->fetch_object()) {
            // Almacenar cada fila en el array usando el 'config_key' como clave
            $configurations[$row->config_key] = $row->config_value;
         }
      }

      // Retornar el array con todas las configuraciones
      return $configurations;
   }

   // Función para mostrar los equipos

   public static function showDevices()
   {

      $db = Database::connect();

      $query = "SELECT e.equipo_id, e.modelo, e.nombre_modelo, m.nombre_marca FROM equipos e 
      INNER JOIN marcas m on e.marca_id = m.marca_id ORDER BY m.nombre_marca ASC";

      return $db->query($query);
   }


   /**
    * Usuarios
      --------------------------------------*/

   public static function loadUsers()
   {
      $query = "SELECT u.usuario_id,concat(u.nombre,' ',IFNULL(u.apellidos,'')) as nombre FROM usuarios u
                   INNER JOIN roles r ON  u.rol_id = r.rol_id";

      $db = Database::connect();

      return $db->query($query);
   }

   public static function userID($id)
   {
      $query = "SELECT * FROM usuarios u
                   INNER JOIN roles r ON  u.rol_id = r.rol_id 
                   WHERE usuario_id = '$id'";

      $db = Database::connect();

      return $db->query($query);
   }

   public static function roles()
   {
      $query = "SELECT * FROM roles";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Categorías
       -----------------------------------------------*/

   // Función para mostrar categorias

   public static function showCategories()
   {
      $query = "SELECT *FROM categorias";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar categorias por id

   public static function showCategoriesID($id)
   {
      $query = "SELECT *FROM categorias WHERE categoria_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Ofertas
     ---------------------------------------*/

   // Función para mostrar oferta por id

   public static function showOfferID($id)
   {
      $query = "SELECT *FROM ofertas WHERE oferta_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar ofertas

   public static function showOffers()
   {
      $query = "SELECT *FROM ofertas";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Impuestos
     ---------------------------------------*/

   // Función para mostrar impuestos

   public static function showTaxes()
   {
      $query = "SELECT *FROM impuestos";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar impuestos por id

   public static function showTaxID($id)
   {
      $query = "SELECT *FROM impuestos WHERE impuesto_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Posiciones
    --------------------------------------*/

   // Función para verificar los parent rows de una posición

   public static function verify_parent_position($id)
   {
      $query = "SELECT (count(pp.posicion_id) + count(pzp.posicion_id)) AS 'parent_row' FROM posiciones po 
      LEFT JOIN productos_con_posiciones pp ON po.posicion_id = pp.posicion_id
       LEFT JOIN piezas_con_posiciones pzp ON po.posicion_id = pzp.posicion_id
      WHERE po.posicion_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar posición por id

   public static function showPositionID($id)
   {
      $query = "SELECT *FROM posiciones WHERE posicion_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar posiciones

   public static function showPositions()
   {
      $query = "SELECT *FROM posiciones";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Almacenes
    --------------------------------------*/

   // Función para mostrar almacen por id

   public static function showWarehouseID($id)
   {
      $query = "SELECT *FROM almacenes WHERE almacen_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   // Función para mostrar almacenes

   public static function showWarehouses()
   {
      $query = "SELECT *FROM almacenes";

      $db = Database::connect();
      return $db->query($query);
   }


   /**
    * Clientes
     ------------------------------------*/

   public static function showCustomers()
   {
      $query = "SELECT nombre, apellidos, cliente_id,email FROM clientes";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function showCustomersID($id)
   {
      $query = "SELECT *FROM clientes WHERE cliente_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function getActiveCustomersThisMonth()
   {
      $sql = "SELECT COUNT(DISTINCT fv.cliente_id) AS total_active_customers
                FROM facturas_ventas fv
                WHERE MONTH(fv.fecha) = MONTH(CURDATE())
                  AND YEAR(fv.fecha) = YEAR(CURDATE())";

      $db = Database::connect();

      $result = $db->query($sql);

      if ($result && $row = $result->fetch_assoc()) {
         return (int)$row['total_active_customers'];
      } else {
         return 0;
      }
   }

   /**
    * Proveedor
     ------------------------------------*/

   public static function showProviders()
   {
      $query = "SELECT *FROM proveedores";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function showProvidersID($id)
   {
      $query = "SELECT *FROM proveedores WHERE proveedor_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    *  Productos
    *  -------------------------------------*/

   public static function getProductAvgCost($id)
   {

      $query = "SELECT sum(v.costo_unitario) as costo_promedio FROM variantes v
                 INNER JOIN productos p ON p.producto_id = v.producto_id
                 WHERE p.producto_id = '$id' AND v.estado_id = 13";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function showProductID($id)
   {

      $query = "SELECT *, p.producto_id as IDproducto FROM productos p 
      LEFT JOIN almacenes a ON p.almacen_id = a.almacen_id
      LEFT JOIN productos_con_categorias pc ON p.producto_id = pc.producto_id
      LEFT JOIN categorias c ON pc.categoria_id = c.categoria_id
      LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
      LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
      LEFT JOIN productos_con_ofertas po ON p.producto_id = po.producto_id
      LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
      LEFT JOIN productos_con_marcas pm ON p.producto_id = pm.producto_id
      LEFT JOIN marcas m ON pm.marca_id = m.marca_id
      LEFT JOIN productos_con_posiciones pp ON p.producto_id = pp.producto_id
      LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
      LEFT JOIN productos_con_proveedores ppr ON p.producto_id = ppr.producto_id
      LEFT JOIN proveedores pr ON ppr.proveedor_id = pr.proveedor_id
      LEFT JOIN productos_con_lista_de_precios pl ON p.producto_id = pl.producto_id
      LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
      WHERE p.producto_id = '$id' ORDER BY nombre_producto ASC";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function showProducts()
   {
      $query = "SELECT *,p.producto_id as IDproducto,o.valor FROM productos p 
               LEFT JOIN productos_con_marcas pm ON p.producto_id = pm.producto_id
               LEFT JOIN marcas m ON pm.marca_id = m.marca_id
               LEFT JOIN productos_con_ofertas po ON p.producto_id = po.producto_id
               LEFT JOIN ofertas o ON o.oferta_id = po.oferta_id
               WHERE p.estado_id = 1";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Variantes
     ---------------------------------------*/

   // Función para contar el total de variantes

   public static function countVariantsByProductId($id)
   {
      $query = "SELECT count(variante_id) AS variante_total FROM variantes 
                WHERE producto_id = '$id' AND estado_id = 13";

      $db = Database::connect();
      $datos = $db->query($query);

      return $datos->fetch_object();
   }

   public static function getTypeVariantsByProductId($id)
   {
      $query = "SELECT count(variante_id) as total, tipo FROM variantes WHERE producto_id = '$id' and tipo = 'producto'";

      $db = Database::connect();
      $datos = $db->query($query);

      return $datos->fetch_object();
   }

   // Función para mostrar las variantes de un producto

   // public static function loadVariantsByProductId($id)
   // {
   //    $query = "SELECT v.sabor,v.serial,v.caja,v.costo_unitario,c.color,pv.nombre_proveedor,
   //    v.variante_id as var_id, v.fecha as entrada FROM variantes v
   //             INNER JOIN productos p ON p.producto_id = v.producto_id
   //             LEFT JOIN variantes_con_colores vc ON vc.variante_id = v.variante_id
   //             LEFT JOIN colores c ON c.color_id = vc.color_id
   //             LEFT JOIN variantes_con_proveedores vp ON vp.variante_id = v.variante_id
   //             LEFT JOIN proveedores pv ON pv.proveedor_id = vp.proveedor_id
   //             WHERE p.producto_id = '$id' AND v.estado_id = 13";

   //    $db = Database::connect();
   //    return $db->query($query);
   // }

   // Función para mostrar las variantes vendidas de un producto

   public static function showVariant_history($id)
   {
      $query = "SELECT  v.variante_id,v.sabor,v.serial,c.color,v.caja,pv.nombre_proveedor,
       v.costo_unitario, v.fecha as entrada FROM variantes v
               LEFT JOIN variantes_con_colores vc ON vc.variante_id = v.variante_id
               LEFT JOIN colores c ON c.color_id = vc.color_id
               LEFT JOIN variantes_con_proveedores vp ON vp.variante_id = v.variante_id
               LEFT JOIN proveedores pv ON pv.proveedor_id = vp.proveedor_id
               WHERE v.producto_id = '$id' AND v.estado_id = 14";

      $db = Database::connect();
      return $db->query($query);
   }

   // Mostrar las variantes del producto en detalle_temporal

   public static function loadVariantTemp($id)
   {
      $query = "SELECT d.descripcion,c.color,v.sabor,v.serial,v.variante_id FROM detalle_temporal d
      LEFT JOIN detalle_variantes_temporal dv ON dv.detalle_temporal_id = d.detalle_temporal_id
      LEFT JOIN variantes v ON dv.variante_id = v.variante_id
      LEFT JOIN variantes_con_colores vc ON vc.variante_id = v.variante_id
      LEFT JOIN colores c ON c.color_id = vc.color_id
                     WHERE d.detalle_temporal_id = '$id'";

      $db = Database::connect();
      $datos = $db->query($query);
      $html = '';

      // Cuerpo 
      while ($element = $datos->fetch_object()) {

         $html .= '<p class="list_db">';

         if (!empty($element->descripcion)) {
            $html .= ucwords($element->descripcion) . ' ';
         }

         if (!empty($element->color)) {
            $html .= ucwords($element->color) . ' ';
         }

         if (!empty($element->sabor)) {
            $html .= '<br>' . $element->sabor . ' ';
         }

         if (!empty($element->serial)) {
            $html .= $element->serial;
         }

         $html .= '</p>';
      }

      return $html;
   }

   // Mostrar las variantes del producto facturado

   public static function getVariantId($id)
   {
      $query = "SELECT p.nombre_producto,c.color,v.sabor,v.serial,v.variante_id as var_id FROM detalle_facturas_ventas d
      LEFT JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
      LEFT JOIN productos p ON p.producto_id = dp.producto_id
      LEFT JOIN variantes_facturadas vf ON vf.detalle_venta_id = d.detalle_venta_id
      LEFT JOIN variantes v ON vf.variante_id = v.variante_id
      LEFT JOIN variantes_con_colores vc ON vc.variante_id = v.variante_id
      LEFT JOIN colores c ON c.color_id = vc.color_id
                     WHERE d.detalle_venta_id = '$id'";

      $db = Database::connect();
      $datos = $db->query($query);
      $html = '';

      // Cuerpo 
      while ($element = $datos->fetch_object()) {

         $html .= '<p class="list_db">' . ucwords($element->nombre_producto ?? '') . ' ' . ucwords($element->color ?? "") .
            ' <br> ' . $element->sabor . ' ' . ucwords($element->serial ?? '') . '</p>';
      }

      return $html;
   }

   /**
    * Cotizaciones
    --------------------------------------*/

   public static function getQuotesNoteId($id)
   {

      $db = Database::connect();

      $query = "SELECT descripcion FROM cotizaciones 
                WHERE cotizacion_id = '$id'";

      $result = $db->query($query);
      $element = $result->fetch_object();

      return $element->descripcion;
   }

   public static function loadQuotesDetail($id)
   {

      $query = "SELECT detalle_id, descripcion, cantidad, precio, impuesto, descuento 
      FROM detalle_cotizaciones WHERE cotizacion_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Marcas
     ---------------------------------------*/

   // Función para mostrar marcas

   public static function showBrands()
   {
      $query = "SELECT *FROM marcas ORDER BY nombre_marca";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar marcas

   public static function showBrandId($id)
   {
      $query = "SELECT * FROM marcas where marca_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Colores
     ---------------------------------------*/

   // Función para mostrar colores

   public static function showColours()
   {
      $query = "SELECT * FROM colores ORDER BY color";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Métodos de pagos
     ---------------------------------------*/

   // Función para mostrar los diferentes métodos de pago

   public static function showPaymentMethod()
   {
      $query = "SELECT *FROM metodos_de_pagos";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Lista de precios
     ---------------------------------------*/

   // Función para mostrar lista de precios por ID

   public static function showPrice_listID($id)
   {

      $query = "SELECT *FROM lista_de_precios WHERE lista_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   // Función para mostrar lista de precios

   public static function loadPriceLists()
   {
      $query = "SELECT lista_id,nombre_lista FROM lista_de_precios 
      ORDER BY nombre_lista";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar listas de precios de un producto

   public static function loadProductPriceListsId($id)
   {
      $query = "SELECT * FROM lista_de_precios l
                           INNER JOIN productos_con_lista_de_precios pl ON pl.lista_id = l.lista_id
                           WHERE pl.producto_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    *  Piezas
    *  -------------------------------------*/

   public static function showPieces()
   {
      $query = "SELECT p.pieza_id, p.estado_id,p.cod_pieza,p.nombre_pieza,
    p.precio_costo,p.precio_unitario,p.cantidad,p.cantidad_min,o.valor FROM piezas p
      LEFT JOIN piezas_con_ofertas po ON p.pieza_id = po.pieza_id
      LEFT JOIN ofertas o ON o.oferta_id = po.oferta_id 
      WHERE p.estado_id != 2";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function showPiecesID($id)
   {
      $query = "SELECT *,p.pieza_id as IDpieza FROM piezas p 
      LEFT JOIN almacenes a ON p.almacen_id = a.almacen_id
      LEFT JOIN piezas_con_categorias pc ON p.pieza_id = pc.pieza_id
      LEFT JOIN categorias c ON pc.categoria_id = c.categoria_id
      LEFT JOIN piezas_con_ofertas po ON p.pieza_id = po.pieza_id
      LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
      LEFT JOIN piezas_con_marcas pm ON p.pieza_id = pm.pieza_id
      LEFT JOIN marcas m ON pm.marca_id = m.marca_id
      LEFT JOIN piezas_con_posiciones pp ON p.pieza_id = pp.pieza_id
      LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
      LEFT JOIN piezas_con_proveedores ppr ON p.pieza_id = ppr.pieza_id
      LEFT JOIN proveedores pr ON ppr.proveedor_id = pr.proveedor_id
      WHERE p.pieza_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar listas de precios de un producto

   public static function loadPiecePriceListsId($id)
   {
      $query = "SELECT * FROM lista_de_precios l
                           INNER JOIN piezas_con_lista_de_precios pl ON pl.lista_id = l.lista_id
                           WHERE pl.pieza_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   /**
    *  Facturas de ventas
    *  -------------------------------------*/

   // Función estática para verificar si la factura tiene detalles
   public static function checkIfInvoiceHasDetails($id, $tipo)
   {
      $db = Database::connect();

      // Verificar el tipo de factura y construir la consulta correspondiente
      if ($tipo == 'FT') {
         // Facturas de ventas
         $sql = "
            SELECT COUNT(*) AS count
            FROM detalle_facturas_ventas
            WHERE factura_venta_id = '$id'
        ";
      } elseif ($tipo == 'RP') {
         // Facturas de reparación
         $sql = "
            SELECT COUNT(*) AS count
            FROM detalle_ordenRP
            WHERE orden_rp_id = '$id'
        ";
      } else {
         // Si el tipo no es válido, devolver false
         return false;
      }

      $result = $db->query($sql);

      // Verificar si la consulta retornó un resultado
      if ($result) {
         $row = $result->fetch_assoc();  // Obtener el resultado como un arreglo asociativo
         return $row['count'] > 0;  // Retorna true si hay detalles, false si no
      }

      // Si no hubo resultados o la consulta falló, devolver false
      return false;
   }



   public static function calculateSalesToDay()
   {
      $db = Database::connect();
      $config = Database::getConfig();

      // Verificamos si 'auto_cierre' existe en la configuración y si es 'false'
      $query = '';

      if (isset($config['auto_cierre']) && $config['auto_cierre'] === 'false') {
         // Si 'auto_cierre' es 'false', ejecutamos la primera consulta


         $query = "SELECT SUM(total) AS total, SUM(recibido) AS recibido, SUM(pendiente) AS pendiente,fecha_factura
         FROM (
         -- Subconsulta 1: Facturas de ventas con detalles no vacíos
         SELECT f.total AS total,f.recibido AS recibido,f.pendiente AS pendiente,f.fecha AS fecha_factura
         FROM facturas_ventas f
         INNER JOIN detalle_facturas_ventas d ON d.factura_venta_id = f.factura_venta_id
         WHERE CONCAT(f.fecha, ' ', f.hora) >= (
         SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1) 
         GROUP BY f.factura_venta_id

         UNION ALL

         -- Subconsulta 2: Facturas RP con detalles no vacíos
         SELECT fr.total AS total, fr.recibido AS recibido,  fr.pendiente AS pendiente,fr.fecha AS fecha_factura
         FROM facturasRP fr
         INNER JOIN detalle_ordenRP d ON d.orden_rp_id = fr.orden_rp_id
           WHERE CONCAT(fr.fecha, ' ', fr.hora) >= (
         SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1) 
         GROUP BY fr.facturaRP_id

         ) ventas_del_dia_rango_cierre GROUP BY fecha_factura;";
      } else {

         $query = "SELECT SUM(total) AS total,SUM(recibido) AS recibido,SUM(pendiente) AS pendiente,fecha_factura
         FROM (
            -- Subconsulta 1: Facturas de ventas del día
			SELECT f.total AS total,f.recibido AS recibido,f.pendiente AS pendiente,f.fecha AS fecha_factura
            FROM facturas_ventas f
            INNER JOIN detalle_facturas_ventas d ON d.factura_venta_id = f.factura_venta_id
            WHERE DATE(f.fecha) = CURDATE()
            GROUP BY f.factura_venta_id

            UNION ALL

            -- Subconsulta 2: Facturas RP del día
            SELECT fr.total AS total, fr.recibido AS recibido,  fr.pendiente AS pendiente,fr.fecha AS fecha_factura
            FROM facturasRP fr
            INNER JOIN detalle_ordenRP d ON d.orden_rp_id = fr.orden_rp_id
            WHERE DATE(fr.fecha) = CURDATE()
            GROUP BY fr.facturaRP_id

         ) AS ventas_del_dia GROUP BY fecha_factura;";
      }

      return $db->query($query);
   }

   // Función para mostrar los datos de una factura

   public static function showInvoiceID($id)
   {

      $db = Database::connect();

      $query = "SELECT p.nombre_producto, df.precio, pz.nombre_pieza, s.nombre_servicio, df.cantidad as cantidad_total, 
      df.detalle_venta_id as 'id', df.descuento, df.impuesto, i.valor FROM detalle_facturas_ventas df
               LEFT JOIN detalle_ventas_con_productos dvp ON df.detalle_venta_id = dvp.detalle_venta_id
               LEFT JOIN productos p ON p.producto_id = dvp.producto_id
               LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
               LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
               LEFT JOIN detalle_ventas_con_piezas_ dvpz ON df.detalle_venta_id = dvpz.detalle_venta_id
               LEFT JOIN piezas pz ON pz.pieza_id = dvpz.pieza_id
               LEFT JOIN detalle_ventas_con_servicios dvs ON df.detalle_venta_id = dvs.detalle_venta_id
               LEFT JOIN servicios s ON s.servicio_id = dvs.servicio_id
               WHERE df.factura_venta_id = '$id'";

      return $db->query($query);
   }

   public static function INVOICE_DESCRIPT($id)
   {

      $db = Database::connect();

      $query = "SELECT descripcion FROM facturas_ventas 
               WHERE factura_venta_id = '$id'";

      $result = $db->query($query);
      $element = $result->fetch_object();

      return $element->descripcion;
   }

   public static function INVOICERP_DESCRIPT($id)
   {

      $db = Database::connect();

      $query = "SELECT descripcion FROM facturasrp
               WHERE facturarp_id = '$id'";

      $result = $db->query($query);
      $element = $result->fetch_object();

      return $element->descripcion;
   }


   // Función para mostrar facturas por cobrar

   public static function INVOICE_PENDING()
   {

      $db = Database::connect();

      $query = "SELECT f.factura_venta_id, c.nombre, c.apellidos, f.fecha FROM facturas_ventas f 
      INNER JOIN clientes c on c.cliente_id = f.cliente_id
      INNER JOIN estados_generales e ON e.estado_id = f.estado_id
      WHERE nombre_estado = 'Por Cobrar'";

      return $db->query($query);
   }

   public static function INFO_INVOICE($id)
   {
      $query = "SELECT *, c.nombre as nombre_cliente, c.apellidos as apellidos_cliente, u.nombre as nombre_usuario, 
      u.apellidos as apellidos_usuario, f.fecha as fecha_factura FROM facturas_ventas f
               INNER JOIN clientes c ON c.cliente_id = f.cliente_id
               INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
               INNER JOIN usuarios u ON u.usuario_id = f.usuario_id
               WHERE f.factura_venta_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * Configuración
     ---------------------------------------*/

   // Función para mostrar la configuración de los bonos

   public static function showBond_config()
   {
      $query = "SELECT *FROM bono_config b
        INNER JOIN estados_generales e ON b.estado_id = e.estado_id
        WHERE b.bono_config_id = 1";

      $db = Database::connect();
      return $db->query($query);
   }


   /**
    * Condiciones
     ---------------------------------------*/

   public static function showConditions()
   {
      $query = "SELECT *FROM condiciones ORDER BY sintoma";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function getOrderNoteId(int $order_id, bool $rp = false)
   {

      if ($rp) {
         $query = "SELECT observacion FROM ordenes_rp where orden_rp_id = '$order_id'";
      } else {
         $query = "SELECT f.descripcion FROM facturas_ventas f
        INNER JOIN detalle_facturas_ventas d ON d.factura_venta_id = f.factura_venta_id
        WHERE d.comanda_id = '$order_id'";
      }

      $db = Database::connect();
      return $db->query($query);
   }

   public static function getOrderInfoId($id)
   {
      $query = "SELECT e.nombre_modelo,e.modelo,o.serie,o.imei,o.fecha_entrada,
        c.nombre,c.apellidos,c.telefono1,o.fecha_salida,o.observacion,
        concat(u.nombre,' ',u.apellidos) as usuario, m.nombre_marca,
        mp.nombre_metodo
        FROM ordenes_rp o
        INNER JOIN equipos e ON e.equipo_id = o.equipo_id
      INNER JOIN marcas m ON e.marca_id = m.marca_id
      INNER JOIN clientes c ON c.cliente_id = o.cliente_id
      INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
      LEFT JOIN facturasRP rp ON rp.orden_rp_id = o.orden_rp_id
      LEFT JOIN metodos_de_pagos mp ON mp.metodo_pago_id = rp.metodo_pago_id
        WHERE o.orden_rp_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   public static function getConditionsId($id)
   {
      $query = "SELECT c.sintoma FROM ordenes_rp o
        LEFT JOIN ordenes_rp_con_condiciones oc ON oc.orden_rp_id = o.orden_rp_id
        INNER JOIN condiciones c ON c.condicion_id = oc.condicion_id
         where o.orden_rp_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   public static function loadOrderConditionById($id)
   {
      $query = "SELECT c.sintoma as descripcion FROM ordenes_rp o 
      INNER JOIN ordenes_rp_con_condiciones oc ON oc.orden_rp_id = o.orden_rp_id
      INNER JOIN condiciones c ON c.condicion_id = oc.condicion_id
      WHERE o.orden_rp_id = '$id'";

      $db = Database::connect();

      $datos = $db->query($query);
      $html = '';

      // Cuerpo 
      while ($element = $datos->fetch_object()) {

         $html .= '<p class="list_db">' . $element->descripcion . '</p>';
      }

      return $html;
   }

   /**
    * Servicios
     ---------------------------------------*/

   public static function showServices()
   {
      $query = "SELECT *FROM servicios ORDER BY nombre_servicio";

      $db = Database::connect();
      return $db->query($query);
   }


   public static function getServicesId($id)
   {
      $query = "SELECT nombre_servicio,costo,precio,servicio_id 
      FROM servicios WHERE servicio_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   public static function hasInvoice($orderId)
   {
      $query = "SELECT f.factura_venta_id
      FROM facturas_ventas f
      INNER JOIN detalle_facturas_ventas d ON d.factura_venta_id = f.factura_venta_id
      WHERE d.comanda_id = $orderId
      LIMIT 1";

      $db = Database::connect();

      $result = $db->query($query);

      if ($result && $row = $result->fetch_object()) {
         return (int)$row->factura_venta_id;
      }

      return 0; // No hay factura
   }

   /**
    * Reparaciones
      ----------------------------------------*/

   // Función para mostrar facturas por cobrar

   public static function INVOICE_PENDING_RP()
   {

      $db = Database::connect();

      $query = "SELECT f.facturaRP_id, c.nombre, c.apellidos, f.fecha  FROM facturasRP f 
      INNER JOIN clientes c on c.cliente_id = f.cliente_id
                      INNER JOIN estados_generales e ON e.estado_id = f.estado_id
                      WHERE nombre_estado = 'Por Cobrar'";

      return $db->query($query);
   }


   /**
    * Ordenes de reparaciones
      ---------------------------------------*/

   // Función para mostrar el detalle de las ordenes

   public static function loadOrdenDetailId($id)
   {
      $query = "SELECT precio,descuento,descripcion,orden_rp_id, cantidad,detalle_ordenRP_id as detalle_id FROM detalle_ordenRP
         WHERE orden_rp_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   // Verificar si la orden ya fue facturada

   public static function checkOrderInvoiceExists(int $order_id, bool $rp = false)
   {
      if ($rp) {
         $sql = "SELECT COUNT(*) as is_exists FROM facturasRP WHERE orden_rp_id = '$order_id'";
      } else {
         $sql = "SELECT COUNT(*) as is_exists FROM comandas co
              INNER JOIN detalle_facturas_ventas d ON co.comanda_id = d.comanda_id
              INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
              WHERE co.comanda_id = '$order_id'";
      }

      $db = Database::connect();
      return $db->query($sql);
   }

   public static function INFO_INVOICE_RP($id)
   {
      $query = "SELECT *, c.nombre as nombre_cliente, c.apellidos as apellidos_cliente, u.nombre as nombre_usuario, 
      u.apellidos as apellidos_usuario, f.fecha as fecha_factura FROM facturasRP f
               INNER JOIN clientes c ON c.cliente_id = f.cliente_id
               INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
               INNER JOIN usuarios u ON u.usuario_id = f.usuario_id
               WHERE f.orden_rp_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   /**
    * Ordenes de compras
      ---------------------------------------*/

   // Función para mostrar las ordenes de compras que no esten facturadas

   public static function loadOrders()
   {
      $query = "SELECT o.orden_id,p.nombre_proveedor,p.apellidos FROM ordenes_compras o 
            INNER JOIN proveedores p on o.proveedor_id = p.proveedor_id
            INNER JOIN estados_generales e ON e.estado_id = o.estado_id 
            WHERE nombre_estado = 'Pendiente' OR nombre_estado = 'Entregado'";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar el detalle de la orden

   public static function SHOW_ORDERS_ID($id)
   {
      $query = "SELECT *, d.observacion as 'comentario', d.cantidad as cantidad_total, d.detalle_compra_id as detalle_id, o.orden_id as orden_id FROM ordenes_compras o 
      INNER JOIN detalle_compra d ON d.orden_id = o.orden_id 
      LEFT JOIN detalle_compra_con_piezas dpz ON dpz.detalle_compra_id = d.detalle_compra_id 
      LEFT JOIN piezas pz ON pz.pieza_id = dpz.pieza_id
      LEFT JOIN detalle_compra_con_productos dp ON dp.detalle_compra_id = d.detalle_compra_id
      LEFT JOIN productos p ON p.producto_id = dp.producto_id
      WHERE o.orden_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function SHOW_ORDER_INFO_ID($id)
   {
      $query = "SELECT *, o.fecha as fecha_creacion, u.apellidos as apellidos_usuario FROM ordenes_compras o 
      INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
      INNER JOIN proveedores p ON p.proveedor_id = o.proveedor_id
      INNER JOIN estados_generales e ON e.estado_id = o.estado_id
      WHERE o.orden_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar los pedidos de las ordenes de compras

   public static function loadListOrdersById($id)
   {
      $query = "SELECT p.nombre_producto, pz.nombre_pieza FROM ordenes_compras o 
               INNER JOIN detalle_compra d ON d.orden_id = o.orden_id
               LEFT JOIN detalle_compra_con_piezas dpz ON dpz.detalle_compra_id = d.detalle_compra_id
               LEFT JOIN piezas pz ON pz.pieza_id = dpz.pieza_id
               LEFT JOIN detalle_compra_con_productos dp ON dp.detalle_compra_id = d.detalle_compra_id
               LEFT JOIN productos p ON p.producto_id = dp.producto_id
               WHERE o.orden_id = '$id'";

      $db = Database::connect();

      $datos = $db->query($query);
      $html = '';

      // Cuerpo 
      while ($element = $datos->fetch_object()) {
         if ($element->nombre_producto) {
            $html .= '<p class="list_db">' . $element->nombre_producto . '</p>';
         } else if ($element->nombre_pieza) {
            $html .= '<p class="list_db">' . $element->nombre_pieza . '</p>';
         }
      }

      return $html;
   }


   /**
    * * Gastos
    *  --------------------------------------------*/

   public static function loadReasons()
   {
      $query = "SELECT motivo_id,descripcion FROM motivos ORDER BY descripcion";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * TODO: Función para mostrar los motivos de un gasto */

   public static function loadSpendingsById($id)
   {
      $query = "SELECT m.descripcion FROM ordenes_gastos o 
      INNER JOIN detalle_gasto d ON d.orden_id = o.orden_id
      INNER JOIN motivos m ON m.motivo_id = d.motivo_id
      WHERE o.orden_id = '$id'";

      $db = Database::connect();

      $datos = $db->query($query);
      $html = '';

      // Cuerpo 
      while ($element = $datos->fetch_object()) {

         $html .= '<p class="list_db">' . $element->descripcion . '</p>';
      }

      return $html;
   }

   // Función para mostrar facturas pendientes

   public static function INVOICE_PENDING_PR()
   {

      $db = Database::connect();

      $query = "SELECT * FROM facturas_proveedores f 
                  INNER JOIN estados_generales e ON e.estado_id = f.estado_id
                  WHERE nombre_estado = 'Por Cobrar'";

      return $db->query($query);
   }


   /**
    * TODO: Función para mostrar detalle de factura proveedor
    */

   public static function SHOW_DETAIL_INV_PROVE($id)
   {

      $db = Database::connect();

      $query = "SELECT * FROM facturas_proveedores fp 
   INNER JOIN ordenes_compras o ON o.orden_id = fp.orden_id
   INNER JOIN detalle_compra d ON d.orden_id = o.orden_id
   LEFT JOIN detalle_compra_con_piezas dpz ON dpz.detalle_compra_id = d.detalle_compra_id 
   LEFT JOIN piezas pz ON pz.pieza_id = dpz.pieza_id
   LEFT JOIN detalle_compra_con_productos dp ON dp.detalle_compra_id = d.detalle_compra_id
   LEFT JOIN productos p ON p.producto_id = dp.producto_id
   WHERE fp.factura_proveedor_id = '$id'";

      return $db->query($query);
   }



   /**
    * * TRIGGERS
    * TODO: Activar y desactivar Trigger
    */

   public static function CREATE_TRIGGER_restar_stock_productos()
   {

      $db = Database::connect();

      $query = "CREATE TRIGGER restar_stock_productos
      AFTER INSERT ON detalle_ventas_con_productos FOR EACH ROW
      BEGIN
      
    
      declare ID int;
      declare Cant decimal(10,2);

      SET @ID = (SELECT producto_id FROM detalle_ventas_con_productos where detalle_venta_id = NEW.detalle_venta_id);
      SET @Cant = (SELECT cantidad FROM detalle_facturas_ventas where detalle_venta_id = NEW.detalle_venta_id);

      IF (@ID != '') THEN 

      Update productos
      set cantidad = cantidad - @Cant
         where producto_id = @ID;
         
      END IF;
      
     END ;";

      if ($db->query($query) === TRUE) {
         return "create";
      } else {

         return "Error: " . $db->error;
      }
   }


   public static function CREATE_TRIGGER_restar_stock_piezas()
   {

      $db = Database::connect();

      $query = "CREATE TRIGGER restar_stock_piezas
      AFTER INSERT ON detalle_ventas_con_piezas_ FOR EACH ROW
      BEGIN
      
      declare ID int;
      declare Cant decimal(10,2);
      
      SET @ID = (SELECT pieza_id FROM detalle_ventas_con_piezas_ where detalle_venta_id = NEW.detalle_venta_id);
      SET @Cant = (SELECT cantidad FROM detalle_facturas_ventas where detalle_venta_id = NEW.detalle_venta_id);
      
      IF (@ID != '') THEN 
      
        Update piezas
        set cantidad = cantidad - @Cant
         where pieza_id = @ID;
         
      END IF;
      
     END ;";

      if ($db->query($query) === TRUE) {
         return "create";
      } else {

         return "Error: " . $db->error;
      }
   }


   public static function CREATE_TRIGGER_devolver_stocks_temporales()
   {

      $db = Database::connect();

      $query = "CREATE TRIGGER devolver_stocks_temporales
      AFTER DELETE ON detalle_temporal FOR EACH ROW
      BEGIN
      
      IF (OLD.producto_id > 0) THEN
      
        Update productos
        set cantidad = cantidad + OLD.cantidad
        where producto_id = OLD.producto_id;
        
        delete from detalle_variantes_temporal where detalle_temporal_id = OLD.detalle_temporal_id;
      
      ELSEIF (OLD.pieza_id > 0) THEN
      
        Update piezas
        set cantidad = cantidad + OLD.cantidad
        where pieza_id = OLD.pieza_id;
      
      END IF ;
      
      END ;";

      if ($db->query($query) === TRUE) {
         return "create";
      } else {

         return "Error: " . $db->error;
      }
   }

   public static function CREATE_TRIGGER_devolver_variantes_temporales()
   {

      $db = Database::connect();

      $query = "CREATE TRIGGER devolver_variantes_temporales
      AFTER DELETE ON detalle_variantes_temporal FOR EACH ROW
      BEGIN
      
        Update variantes set estado_id = 13 where variante_id = OLD.variante_id;
      
      
      END ;";

      if ($db->query($query) === TRUE) {
         return "create";
      } else {

         return "Error: " . $db->error;
      }
   }

   public static function CREATE_TRIGGER_agregar_item_venta()
   {

      $db = Database::connect();

      $query = "CREATE TRIGGER agregar_item_venta
         AFTER INSERT ON detalle_facturas_ventas FOR EACH ROW
         BEGIN

         DECLARE pendienteX DECIMAL(10,2);

         Update facturas_ventas
         set total = total + (NEW.cantidad * (NEW.impuesto + NEW.precio )- NEW.descuento), 
         pendiente = total - recibido
         where factura_venta_id = NEW.factura_venta_id;

         SET @pendienteX = (select pendiente from facturas_ventas where factura_venta_id = NEW.factura_venta_id);

         IF (@pendienteX = 0) THEN
         UPDATE facturas_ventas SET estado_id = 3 WHERE factura_venta_id = NEW.factura_venta_id;
         ELSEIF (@pendienteX > 0) THEN
         UPDATE facturas_ventas SET estado_id = 4 WHERE factura_venta_id = NEW.factura_venta_id;
         END IF;

         END ;";

      if ($db->query($query) === TRUE) {
         return "create";
      } else {

         return "Error: " . $db->error;
      }
   }



   public static function createAllTriggers()
   {
      self::CREATE_TRIGGER_restar_stock_productos();
      self::CREATE_TRIGGER_restar_stock_piezas();
      self::CREATE_TRIGGER_devolver_stocks_temporales();
      self::CREATE_TRIGGER_devolver_variantes_temporales();
      self::CREATE_TRIGGER_agregar_item_venta();
   }




   public static function CREATE_TRIGGER_agregar_item_cotizacion()
   {

      $db = Database::connect();

      $query = "CREATE TRIGGER agregar_item_cotizacion
         AFTER INSERT ON detalle_cotizaciones FOR EACH ROW
         BEGIN

         Update cotizaciones
         set total = total + (NEW.cantidad * (NEW.impuesto + NEW.precio )- NEW.descuento)
         where cotizacion_id = NEW.cotizacion_id;

         END ;";

      if ($db->query($query) === TRUE) {
         return "create";
      } else {

         return "Error: " . $db->error;
      }
   }

   // Función para mostrar la cantidad de productos y servicios fuera de stock

   public static function minStockProductAlert()
   {

      $db = Database::connect();

      $query = "SELECT count(producto_id) as stock,estado_id from productos 
      where cantidad <= cantidad_min AND estado_id = 1";

      $result = $db->query($query);
      $data = $result->fetch_object();

      return $data->stock;
   }


   public static function numOrderAlert()
   {

      $db = Database::connect();

      $query = "SELECT COUNT(DISTINCT co.comanda_id) AS total
      FROM comandas co
      INNER JOIN detalle_facturas_ventas d 
      ON co.comanda_id = d.comanda_id
      WHERE d.factura_venta_id IS NULL";

      $result = $db->query($query);
      $data = $result->fetch_object();

      return $data->total;
   }
} // Exit


class Expenses_Utils
{

   /**
    * TODO: Permitir mostrar orden de compra
    * ! Solo mostrará la orden cuando esta no este facturada
    */

   public static function verify_order_status($id)
   {
      $db = Database::connect();

      $query = "SELECT *, e.nombre_estado as nombre FROM ordenes_compras o
       INNER JOIN estados_generales e ON o.estado_id = e.estado_id WHERE o.orden_id = '$id'";

      $result = $db->query($query);
      $data = $result->fetch_object();

      if ($data->nombre == "Facturado") {
         return 'no permitir';
      } else {
         return 'permitir';
      }
   }

   /**
    * TODO: Mostrar detalle de compra de factura proveedor
    */

   public static function DETAIL_INV_PROVI($id)
   {

      $db = Database::connect();

      $query = "SELECT *, count(d.detalle_compra_id) as filas, d.cantidad as cant, o.orden_id as orden_id, d.detalle_compra_id as detalle_id FROM facturas_proveedores fp 
   INNER JOIN ordenes_compras o ON o.orden_id = fp.orden_id
   LEFT JOIN detalle_compra d ON d.orden_id = o.orden_id
   LEFT JOIN detalle_compra_con_piezas dpz ON dpz.detalle_compra_id = d.detalle_compra_id 
   LEFT JOIN piezas pz ON pz.pieza_id = dpz.pieza_id
   LEFT JOIN detalle_compra_con_productos dp ON dp.detalle_compra_id = d.detalle_compra_id
   LEFT JOIN productos p ON p.producto_id = dp.producto_id
   WHERE fp.factura_proveedor_id = '$id'";

      return $db->query($query);
   }

   /**
    * TODO: Mostrar datos de la factura a proveedor
    */

   public static function DATA_INV_PROVI($id)
   {

      $db = Database::connect();

      $query = "SELECT *, f.fecha as fecha_creacion, u.apellidos as apellidos_usuario FROM facturas_proveedores f
            INNER JOIN ordenes_compras o ON f.orden_id = o.orden_id 
            INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
            INNER JOIN proveedores p ON p.proveedor_id = o.proveedor_id
            INNER JOIN estados_generales e ON e.estado_id = o.estado_id
            WHERE f.factura_proveedor_id = '$id'";

      return $db->query($query);
   }

   /**
    * TODO: Mostrar observacion de la factura de proveedor
    * ! Utilizado en la ventana editar factura proveedor
    */

   public static function INVOICE_DESCRIPT($id)
   {

      $db = Database::connect();

      $query = "SELECT observacion FROM facturas_proveedores 
               WHERE factura_proveedor_id = '$id'";

      $result = $db->query($query);
      $element = $result->fetch_object();

      return $element->observacion;
   }
}
