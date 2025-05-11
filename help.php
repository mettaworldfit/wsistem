<?php


class Help
{

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

   // Función para verificar los parent rows de una categoría

   public static function verify_parent_category($id)
   {
      $query = "SELECT (count(p.categoria_id) + count(pz.categoria_id)) AS 'parent_row' FROM categorias c
      LEFT JOIN productos_con_categorias p ON c.categoria_id = p.categoria_id
       LEFT JOIN piezas_con_categorias pz ON c.categoria_id = pz.categoria_id
       WHERE c.categoria_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   /**
    * Ofertas
     ---------------------------------------*/

   public static function verify_parent_offer($id)
   {
      $query = "SELECT (count(po.oferta_id) + count(pzo.oferta_id)) AS 'parent_row' FROM ofertas o
      LEFT JOIN productos_con_ofertas po ON po.oferta_id = o.oferta_id
	  LEFT JOIN piezas_con_ofertas pzo ON pzo.oferta_id = o.oferta_id
      WHERE o.oferta_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

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

   // Función para verificar los parent rows de un impuesto

   public static function verify_parent_tax($id)
   {
      $query = "SELECT count(impuesto_id) AS 'parent_row' FROM productos_con_impuestos WHERE impuesto_id = '$id'";

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

   // Función para verificar los parent rows de un almacen

   public static function verify_parent_warehouse($id)
   {
      $query = "SELECT (count(p.almacen_id) + count(pz.almacen_id)) AS 'parent_row' FROM almacenes a
      LEFT JOIN productos p ON p.almacen_id = a.almacen_id
      LEFT JOIN piezas pz ON pz.almacen_id = a.almacen_id
     WHERE a.almacen_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

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

   // Función para verificar los parent rows de un producto

   public static function verify_parent_product($id)
   {
      $query = "SELECT (count(dp.detalle_venta_id) + count(dcp.detalle_compra_id) + count(dt.detalle_temporal_id)) as parent_row FROM productos p 
      left join detalle_ventas_con_productos dp on dp.producto_id = p.producto_id 
      left join detalle_compra_con_productos dcp on dcp.producto_id = p.producto_id 
      left join detalle_temporal dt on dt.producto_id = p.producto_id
      WHERE p.producto_id = '$id'";

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

   public static function Count_Variant_pID($id)
   {
      $query = "SELECT count(variante_id) AS variante_total FROM variantes 
                WHERE producto_id = '$id' AND estado_id = 13";

      $db = Database::connect();
      $datos = $db->query($query);

      return $datos->fetch_object();
   }

   // Función para mostrar las variantes de un producto

   public static function showVariant_with_productID($id)
   {
      $query = "SELECT v.imei,v.serial,v.caja,v.costo_unitario,c.color,pv.nombre_proveedor,
      v.variante_id as var_id, v.fecha as entrada FROM variantes v
               INNER JOIN productos p ON p.producto_id = v.producto_id
               LEFT JOIN variantes_con_colores vc ON vc.variante_id = v.variante_id
               LEFT JOIN colores c ON c.color_id = vc.color_id
               LEFT JOIN variantes_con_proveedores vp ON vp.variante_id = v.variante_id
               LEFT JOIN proveedores pv ON pv.proveedor_id = vp.proveedor_id
               WHERE p.producto_id = '$id' AND v.estado_id = 13";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar las variantes vendidas de un producto

   public static function showVariant_history($id)
   {
      $query = "SELECT  v.variante_id,v.imei,v.serial,c.color,v.caja,pv.nombre_proveedor,
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
      $query = "SELECT d.descripcion,c.color,v.imei,v.serial,v.variante_id FROM detalle_temporal d
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

         $html .= '<p class="list_db">' . ucwords($element->descripcion ?? '') . ' ' . ucwords($element->color ?? '') .
            ' <br> ' . $element->imei . ' ' . $element->serial . '</p>';
      }

      return $html;
   }

   // Mostrar las variantes del producto facturado

   public static function showVariant($id)
   {
      $query = "SELECT p.nombre_producto,c.color,v.imei,v.serial,v.variante_id as var_id FROM detalle_facturas_ventas d
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

         $html .= '<p class="list_db">' . ucwords($element->nombre_producto) . ' ' . ucwords($element->color) .
            ' <br> ' . ucwords($element->imei) . ' ' . ucwords($element->serial) . '</p>';
      }

      return $html;
   }

   /**
    * Cotizaciones
    --------------------------------------*/

   public static function INVOICE_DESCRIPT_QUOTE($id)
   {

      $db = Database::connect();

      $query = "SELECT descripcion FROM cotizaciones 
                WHERE cotizacion_id = '$id'";

      $result = $db->query($query);
      $element = $result->fetch_object();

      return $element->descripcion;
   }

   public static function showQuotesDetail($id)
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

   // Función para verificar los parents rows

   public static function verify_parent_brand($id)
   {
      $query = "SELECT (count(p.marca_id) + count(pz.marca_id) + count(e.marca_id)) AS 'parent_row' FROM marcas m
      LEFT JOIN productos_con_marcas p ON m.marca_id = p.marca_id
      LEFT JOIN piezas_con_marcas pz ON pz.marca_id = m.marca_id
      LEFT JOIN equipos e ON e.marca_id = m.marca_id
      WHERE m.marca_id = '$id'";

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

   public static function showPrice_lists()
   {
      $query = "SELECT *FROM lista_de_precios ORDER BY nombre_lista";

      $db = Database::connect();
      return $db->query($query);
   }

   // Función para mostrar listas de precios de un producto

   public static function showPricelist_with_productID($id)
   {
      $query = "SELECT * FROM lista_de_precios l
                           INNER JOIN productos_con_lista_de_precios pl ON pl.lista_id = l.lista_id
                           WHERE pl.producto_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }


   // Función para verificar los parent rows de las listas de precios

   public static function verify_parent_list($id)
   {
      $db = Database::connect();

      $query = "SELECT (count(pl.lista_id) + count(pzl.lista_id)) as parent_row FROM lista_de_precios l
      LEFT JOIN productos_con_lista_de_precios pl ON l.lista_id = pl.lista_id
      LEFT JOIN piezas_con_lista_de_precios pzl ON l.lista_id = pzl.lista_id
      WHERE l.lista_id = '$id'";

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

   public static function showPricelist_with_pieceID($id)
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

    public static function loadDetailTemp()
   { 
      $db = Database::connect();
      $userID = $_SESSION['identity']->usuario_id;

      $query = "SELECT * FROM detalle_temporal WHERE usuario_id = '$userID' ORDER BY hora";
    
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


   public static function SHOW_CONDITONS_ORDER($id)
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


   public static function verify_parent_services($id)
   {
      $db = Database::connect();

      $query = "SELECT count(s.servicio_id) as servicios FROM detalle_ordenRP_con_servicios d
   INNER JOIN servicios s ON s.servicio_id = d.servicio_id
   WHERE s.servicio_id = '$id'";

      $result = $db->query($query);
      $data = $result->fetch_object();



      if ($data->servicios > 0) {
         return 'no permitir';
      } else {
         return 'permitir';
      }
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

   public static function showOrdenDetailID($id)
   {
      $query = "SELECT precio,descuento,descripcion,orden_rp_id, cantidad as cantidad_total, detalle_ordenRP_id as detalle_id FROM detalle_ordenRP
         WHERE orden_rp_id = '$id'";

      $db = Database::connect();
      return $db->query($query);
   }

   // Verificar si la orden ya fue facturada

   public static function IS_EXISTS_INVOICERP($id)
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

   public static function SHOW_ORDERS()
   {
      $query = "SELECT * FROM ordenes_compras o 
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

   public static function LIST_ORDERS($id)
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

   public static function SHOW_REASONS()
   {
      $query = "SELECT * FROM motivos ORDER BY descripcion";

      $db = Database::connect();
      return $db->query($query);
   }

   /**
    * TODO: Función para mostrar los motivos de un gasto */

   public static function SHOW_SPENDINGS($id)
   {
      $query = "SELECT m.descripcion as descripcion FROM ordenes_gastos o 
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
