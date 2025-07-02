<?php


class Help
{

   public static function getDailyProfit()
   {
      $query = "SELECT sum(ganancia) as total_ganancias FROM (

    SELECT sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia  
    from detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_productos dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join productos p on p.producto_id = dp.producto_id
    where d.fecha = curdate() AND f.estado_id != 4
    
    UNION ALL
    
    SELECT sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia 
    from detalle_facturas_ventas d
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_piezas_ dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where d.fecha = curdate() AND f.estado_id != 4

    UNION ALL

    SELECT sum((d.precio * d.cantidad - d.descuento)-(p.precio_costo * d.cantidad)) as ganancia  
    from detalle_ordenRP d 
    inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
    inner join detalle_ordenRP_con_piezas dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where d.fecha = curdate() AND frp.estado_id != 4

    UNION ALL

    SELECT sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
    from detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_servicios ds on ds.detalle_venta_id = d.detalle_venta_id
    inner join servicios s on s.servicio_id = ds.servicio_id 
    where d.fecha = curdate() AND f.estado_id != 4

    UNION ALL
    
    SELECT sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
    from detalle_ordenRP d 
    inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
    inner join detalle_ordenRP_con_servicios dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join servicios s on s.servicio_id = dp.servicio_id
    where d.fecha = curdate() AND frp.estado_id != 4
    
    ) ganancias_dia";

      $db = Database::connect();
      return $db->query($query)->fetch_object()->total_ganancias;
   }


   public static function getMonthProfit()
   {
      $query = "SELECT sum(ganancia) as total_ganancias FROM (

    SELECT sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia  
    from detalle_facturas_ventas d 
	 inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_productos dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join productos p on p.producto_id = dp.producto_id
    where d.fecha between DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE()) AND f.estado_id != 4
    
    UNION ALL
    
    SELECT sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia 
    from detalle_facturas_ventas d 
	 inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_piezas_ dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where d.fecha between DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE()) AND f.estado_id != 4

    UNION ALL
   
    SELECT sum((d.precio * d.cantidad - d.descuento)-(p.precio_costo * d.cantidad)) as ganancia  
    from detalle_ordenRP d 
	 inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
    inner join detalle_ordenRP_con_piezas dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where d.fecha between DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE()) AND frp.estado_id != 4

    UNION ALL

    SELECT sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
    from detalle_facturas_ventas d 
    inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
    inner join detalle_ventas_con_servicios ds on ds.detalle_venta_id = d.detalle_venta_id
    inner join servicios s on s.servicio_id = ds.servicio_id 
    where d.fecha between DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE()) AND f.estado_id != 4

    UNION ALL
    
    SELECT sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
    from detalle_ordenRP d 
    inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
    inner join detalle_ordenRP_con_servicios dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join servicios s on s.servicio_id = dp.servicio_id
    where d.fecha between DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE()) AND frp.estado_id != 4
    
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
      $query = "SELECT SUM(total) AS total FROM (

    -- Subconsulta 1: Facturas ventas
    SELECT (f.recibido - IFNULL(SUM(p.recibido), 0)) AS total, f.fecha
    FROM facturas_ventas f
    INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
    LEFT JOIN pagos_a_facturas_ventas pf ON pf.factura_venta_id = f.factura_venta_id
    LEFT JOIN pagos p ON pf.pago_id = p.pago_id
    WHERE f.fecha = CURDATE() AND f.metodo_pago_id = '$metodo_id'
    GROUP BY f.factura_venta_id

    UNION ALL

    -- Subconsulta 2: Facturas RP
    SELECT (fr.recibido - IFNULL(SUM(p.recibido), 0)) AS total, fr.fecha
    FROM facturasRP fr
     INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = fr.metodo_pago_id
    LEFT JOIN pagos_a_facturasRP pf ON pf.facturaRP_id = fr.facturaRP_id
    LEFT JOIN pagos p ON pf.pago_id = p.pago_id
    WHERE fr.fecha = CURDATE() AND fr.metodo_pago_id = '$metodo_id'
    GROUP BY fr.facturaRP_id

    UNION ALL

    -- Subconsulta 3: Pagos RP
    SELECT p.recibido AS total, p.fecha
    FROM pagos_a_facturasRP pf 
    INNER JOIN pagos p ON pf.pago_id = p.pago_id
	INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = p.metodo_pago_id
    WHERE p.fecha = CURDATE() AND p.metodo_pago_id = '$metodo_id'

    UNION ALL

    -- Subconsulta 4: Pagos ventas
    SELECT p.recibido AS total, p.fecha
    FROM pagos_a_facturas_ventas pf 
    INNER JOIN pagos p ON pf.pago_id = p.pago_id
     INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = p.metodo_pago_id
    WHERE p.fecha = CURDATE() AND p.metodo_pago_id = '$metodo_id'

) ventas_por_tipo_pago;";

      return $db->query($query)->fetch_object()->total;
   }

   public static function getPurchaseToday()
   {
      $db = Database::connect();
      $query = "SELECT SUM(total) AS total FROM (

    -- Subconsulta 1: Facturas ventas
    SELECT (f.recibido - IFNULL(SUM(p.recibido), 0)) AS total, f.fecha
    FROM facturas_ventas f
    LEFT JOIN pagos_a_facturas_ventas pf ON pf.factura_venta_id = f.factura_venta_id
    LEFT JOIN pagos p ON pf.pago_id = p.pago_id
    WHERE f.fecha = CURDATE()
    GROUP BY f.factura_venta_id

    UNION ALL

    -- Subconsulta 2: Facturas RP
    SELECT (fr.recibido - IFNULL(SUM(p.recibido), 0)) AS total, fr.fecha
    FROM facturasRP fr
    LEFT JOIN pagos_a_facturasRP pf ON pf.facturaRP_id = fr.facturaRP_id
    LEFT JOIN pagos p ON pf.pago_id = p.pago_id
    WHERE fr.fecha = CURDATE()
    GROUP BY fr.facturaRP_id

    UNION ALL

    -- Subconsulta 3: Pagos RP
    SELECT p.recibido AS total, p.fecha
    FROM pagos_a_facturasRP pf 
    INNER JOIN pagos p ON pf.pago_id = p.pago_id
    WHERE p.fecha = CURDATE()

    UNION ALL

    -- Subconsulta 4: Pagos ventas
    SELECT p.recibido AS total, p.fecha
    FROM pagos_a_facturas_ventas pf 
    INNER JOIN pagos p ON pf.pago_id = p.pago_id
    WHERE p.fecha = CURDATE()

) ventas_de_hoy;";

      return $db->query($query)->fetch_object()->total;
   }

   public static function getExpensesToday()
   {
      $db = Database::connect();
      $query = "SELECT sum(total) as total FROM (

            SELECT sum(g.pagado) as 'total', g.fecha FROM gastos g
            WHERE g.fecha = curdate()
            GROUP BY g.fecha     
            
              UNION ALL
              
            SELECT sum(f.pagado) as 'total', f.fecha FROM ordenes_compras o 
            INNER JOIN facturas_proveedores f ON o.orden_id = f.orden_id
            WHERE o.estado_id = 12 AND f.fecha = curdate()
            GROUP BY f.fecha    

            UNION ALL
            
            SELECT sum(p.recibido) as 'total', p.fecha from pagos_proveedores p
            WHERE p.fecha = curdate()
            GROUP BY p.fecha          
                                          
        ) gastos_de_hoy";

      return $db->query($query)->fetch_object()->total;
   }


   public static function getOriginExpensesToday($origin)
   {
      $db = Database::connect();
      $query = "SELECT SUM(total) AS total FROM (

        SELECT SUM(g.pagado) AS total
        FROM gastos g
        INNER JOIN ordenes_gastos o ON o.orden_id = g.orden_id
        WHERE g.fecha = CURDATE() AND o.origen = '$origin'

        UNION ALL

        SELECT SUM(f.pagado) AS total
        FROM ordenes_compras o
        INNER JOIN facturas_proveedores f ON o.orden_id = f.orden_id
        WHERE o.estado_id = 12 AND f.fecha = CURDATE()

        UNION ALL

        SELECT SUM(p.recibido) AS total
        FROM pagos_proveedores p
        WHERE p.fecha = CURDATE()

    ) AS origen_gastos";

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

      $query = "SELECT fecha_apertura, saldo_inicial,cierre_id FROM cierres_caja 
      WHERE DATE(fecha_apertura) = CURDATE() AND estado = 'abierto'
      ORDER BY cierre_id DESC LIMIT 1";

      return $db->query($query)->fetch_object();
   }


   public static function ConfigPDF()
   {

      $db = Database::connect();

      $query = "SELECT logo_pdf,slogan,tel,direccion,condiciones,titulo
                FROM configuraciones WHERE config_id = 1";

      return $db->query($query);
   }

   public static function ConfigElectronicInvoice()
   {

      $db = Database::connect();

      $query = "SELECT logo_url,empresa,email,password,host,smtps,puerto,link_fb,link_ws,link_ig 
      FROM configuraciones WHERE config_id = 1";

      return $db->query($query);
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
      $query = "SELECT *,p.producto_id as IDproducto FROM productos p 
               LEFT JOIN productos_con_marcas pm ON p.producto_id = pm.producto_id
               LEFT JOIN marcas m ON pm.marca_id = m.marca_id WHERE p.estado_id = 1";

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
      $query = "SELECT *FROM piezas WHERE estado_id != 2";

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

   public static function calculateSalesToDay()
   {
      $db = Database::connect();
      $query = "SELECT total,recibido,pendiente,fecha_factura FROM (

       SELECT f.fecha as fecha_factura, f.total as total, f.recibido as recibido, f.pendiente as pendiente 
       FROM facturas_ventas f 
          
	   UNION ALL
           
       SELECT f.fecha as fecha_factura, f.total as total,f.recibido as recibido, f.pendiente as pendiente
       FROM facturasRP f 
           
	   UNION ALL 
       
       SELECT pg.fecha as fecha_factura, pg.recibido as total, pg.recibido as recibido, '0' as pendiente 
       FROM pagos_a_facturas_ventas p 
       INNER JOIN pagos pg ON pg.pago_id = p.pago_id
	   INNER JOIN facturas_ventas f on f.factura_venta_id = p.factura_venta_id
	   WHERE f.fecha <> pg.fecha  
     
        UNION ALL
    
		SELECT pg.fecha as fecha_factura, pg.recibido as total,pg.recibido as recibido, '0' as pendiente 
		FROM pagos_a_facturasRP p 
		INNER JOIN pagos pg ON pg.pago_id = p.pago_id
		INNER JOIN facturasRP f on f.facturarp_id = p.facturarp_id
		WHERE f.fecha <> pg.fecha  
           
	) ventas_del_dia where fecha_factura = curdate()";

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

   public static function getOrderNoteId($id)
   {
      $query = "SELECT observacion FROM ordenes_rp where orden_rp_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   public static function getOrderInfoId($id)
   {
      $query = "SELECT e.nombre_modelo,e.modelo,o.serie,o.imei,o.fecha_entrada,
        c.nombre,c.apellidos,c.telefono1,o.fecha_salida,o.observacion,concat(u.nombre,' ',u.apellidos) as usuario, m.nombre_marca FROM ordenes_rp o
        INNER JOIN equipos e ON e.equipo_id = o.equipo_id
        INNER JOIN marcas m  ON e.marca_id = m.marca_id
        INNER JOIN clientes c ON c.cliente_id = o.cliente_id
        INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
        where o.orden_rp_id = '$id'";

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


   public static function showServicesID($id)
   {
      $query = "SELECT *FROM servicios WHERE servicio_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
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

   public static function isInvoiceRPExists($id)
   {
      $query = "SELECT count(orden_rp_id) as is_exists FROM facturasRP WHERE orden_rp_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
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
      declare Cant int;

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
      declare Cant int;
      
      SET @ID = (SELECT producto_id FROM detalle_ventas_con_piezas_ where detalle_venta_id = NEW.detalle_venta_id);
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
