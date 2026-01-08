<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
require_once '../help.php';
session_start();

if ($_POST['action'] == "cargar_detalle_orden") {
  $db = Database::connect();
  $user_id = $_SESSION['identity']->usuario_id;
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

  handleDataTableRequest($db, [
    'columns' => [
      'nombre_producto',
      'precio',
      'nombre_pieza',
      'nombre_servicio',
      'cantidad_total',
      'id',
      'descuento',
      'impuesto',
      'valor'
    ],
    'base_table' => 'detalle_facturas_ventas df',
    'table_with_joins' => 'detalle_facturas_ventas df
               LEFT JOIN detalle_ventas_con_productos dvp ON df.detalle_venta_id = dvp.detalle_venta_id
               LEFT JOIN productos p ON p.producto_id = dvp.producto_id
               LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
               LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
               LEFT JOIN detalle_ventas_con_piezas_ dvpz ON df.detalle_venta_id = dvpz.detalle_venta_id
               LEFT JOIN piezas pz ON pz.pieza_id = dvpz.pieza_id
               LEFT JOIN detalle_ventas_con_servicios dvs ON df.detalle_venta_id = dvs.detalle_venta_id
               LEFT JOIN servicios s ON s.servicio_id = dvs.servicio_id',
    'select' => 'SELECT p.nombre_producto, df.precio, pz.nombre_pieza, s.nombre_servicio, df.cantidad as cantidad_total, 
      df.detalle_venta_id as "id", df.descuento, df.impuesto, i.valor, df.costo,p.producto_id,pz.pieza_id,s.servicio_id',
    'base_condition' => 'df.comanda_id = ' . $id,
    'table_rows' => function ($row) use ($id, $db) {

      $precio = (float)($row['precio'] ?? 0);
      $costo = (float)($row['costo'] ?? 0);
      $cantidad = ($row['cantidad_total'] ?? 0);
      $impuesto = (float)($row['impuesto'] ?? 0);
      $valor = (float)($row['valor'] ?? 0);
      $descuento = (float)($row['descuento'] ?? 0);
      $importe = ($cantidad * $precio) + ($cantidad * $impuesto) - $descuento;
      $is_exists = Help::checkOrderInvoiceExists($id)->fetch_object()->is_exists;

      // Verificar si el producto tiene variantes
      $hasVariants = false;
      if ($row['producto_id']) {
        $sql_check_variants = "SELECT COUNT(*) AS variants_count FROM variantes WHERE producto_id = '" . $row['producto_id'] . "'";
        $result_variants = $db->query($sql_check_variants);
        if ($result_variants) {
          $variant_data = $result_variants->fetch_assoc();
          if ($variant_data['variants_count'] > 0) {
            $hasVariants = true;
          }
        }
      }

      // Generar el input con el atributo disabled si el producto tiene variantes
      $input_html = '<input type="number" class="input-update input-quantity" 
               value="' . (intval($cantidad) == $cantidad ? number_format($cantidad, 0) : number_format($cantidad, 2)) . '" 
               data-id="' . $row['id'] . '" 
               data-item-id="' . ($row['producto_id'] ? $row['producto_id'] : ($row['pieza_id'] ? $row['pieza_id'] : $row['servicio_id'])) . '" 
               data-item-type="' . ($row['producto_id'] ? 'producto' : ($row['pieza_id'] ? 'pieza' : 'servicio')) . '" 
               step="0.01" ' . ($hasVariants ? 'disabled' : '') . ' />';

      return [
        'descripcion' => $row['nombre_producto']
          ? Help::getVariantId($row['id'])
          : (
            !empty($row['nombre_pieza'])
            ? ucwords($row['nombre_pieza'])
            : (!empty($row['nombre_servicio']) ? ucwords($row['nombre_servicio']) : '')
          ),
        'cantidad' => $input_html,
        'precio' => '<span>
                <a href="#">' . number_format($precio, 2) . '</a>
                <span id="toggle" class="toggle-right toggle-md">
                Costo: ' . number_format($costo, 2) . '
                </span>
            </span>',
        'impuesto' => '<span class="hide-cell">' . number_format($cantidad * $impuesto, 2) . '</span>',
        'descuento' => number_format($descuento, 2),
        'importe' => number_format($importe, 2),
        'acciones' => ($is_exists == 0)
          ? '<a class="btn-action action-danger" onclick="deleteInvoiceDetail(\'' . $row['id'] . '\')">
          ' . BUTTON_ERASE . '
          </a>'
          : ''
      ];
    }
  ]);
}


// Crear orden de venta
if ($_POST['action'] === "registrar_orden") {
  $db = Database::connect();

  echo handleProcedureAction($db, 'ov_agregarOrden', [
    (int)$_POST['customer_id'],
    (int)$_SESSION['identity']->usuario_id,
    6, // Pendiente
    $_POST['observation'],
    $_POST['delivery'] ?? '-',
    $_POST['direction'],
    $_POST['name'],
    $_POST['tel']
  ]);
}

// Editar orden de venta
if ($_POST['action'] === "editar_orden") {
  $db = Database::connect();

  echo handleProcedureAction($db, 'ov_editarOrden', [
    (int)$_POST['order_id'],
    (int)$_POST['customer_id'],
    (int)$_SESSION['identity']->usuario_id,
    $_POST['observation'],
    $_POST['delivery'] ?? '-',
    $_POST['direction'],
    $_POST['name'],
    $_POST['tel']
  ]);
}

if ($_POST['action'] === "actualizar_estado_orden") {
  $db = Database::connect();
  echo handleProcedureAction($db, 'ov_actualizarEstadoOrden', [
    (int)$_POST['status'],
    (int)$_POST['order_id']
  ]);
}

if ($_POST['action'] === "eliminar_orden") {
  $db = Database::connect();
  echo handleDeletionAction($db, (int)$_POST['id'], 'ov_eliminarOrden');
}

if ($_POST['action'] === "index_ordenes") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => [
      'co.comanda_id',
      "c.nombre",
      "c.apellidos",
      'e.nombre_estado',
      'co.telefono_receptor',
      'co.direccion_entrega',
      'co.tipo_entrega',
      'co.fecha'
    ],
    'searchable' => [
      'co.comanda_id',
      'e.nombre_estado',
      'co.telefono_receptor',
      'co.fecha',
      'c.nombre',
      'c.apellidos'
    ],
    'base_table' => 'comandas co',
    'table_with_joins' => 'comandas co
              INNER JOIN clientes c ON c.cliente_id = co.cliente_id
              INNER JOIN estados_generales e ON e.estado_id = co.estado_id',
    'select' => "SELECT co.comanda_id, concat(c.nombre,' ',IFNULL(c.apellidos,'')) as nombre,
                 e.nombre_estado,e.estado_id,co.observacion,co.telefono_receptor,DATE(co.fecha) as fecha,
                 co.direccion_entrega,co.tipo_entrega,co.nombre_receptor",
    'table_rows' => function ($row) {
      $acciones  = '<a class="btn-action action-info" href="' . base_url . 'invoices/add_order&id=' . $row['comanda_id'] . '" title="Agregar factura">';
      $acciones .= BUTTON_ADD_INV . '</a>';

      // Solo permitir deleteOrder si es administrador
      if ($_SESSION['identity']->nombre_rol === 'administrador') {
        $acciones .= '<span onclick="deleteOrder(\'' . $row['comanda_id'] . '\')" class="btn-action action-danger" title="Eliminar">';
        $acciones .= BUTTON_DELETE . '</span>';
      } else {
        // Mostrar icono deshabilitado sin evento
        $acciones .= '<span class="btn-action action-danger action-disable" title="Eliminar">';
        $acciones .= BUTTON_DELETE . '</span>';
      }

      $invoice_id = Help::hasInvoice($row['comanda_id']);

      return [
        "comanda_id" => '<span><a href="#" class="' .
          ($invoice_id > 0 ? 'text-secondary' : 'text-danger') . '">OV-00' . $row['comanda_id'] . '</a>' .
          '<span id="toggle" class="toggle-right toggle-md">No. Orden: OV-00' . $row['comanda_id'] . '<br>' .
          'No. Factura: ' . ($invoice_id > 0
            ? '<a class="text-danger" href="' . base_url . 'invoices/edit&id=' . $invoice_id . '">FT-00' . $invoice_id . '</a>'
            : '<a class="text-danger" href="#">No facturado</a>') . '</span></span>',
        "nombre" => ucwords($row['nombre'], ""),
        "telefono" => formatTel($row['telefono_receptor'] ?? ''),
        "entrega" => $row['tipo_entrega'],
        "fecha" => $row['fecha'],
        'estado' => ($invoice_id > 0 ? '<a href="' . base_url . 'invoices/edit&id=' . $invoice_id . '" class="Facturado">' . 'Facturado' . '</a>' : '<span class="No">' . 'Sin facturar' . '</span>'),
        'orden' => '<select class="form-custom ' . $row['nombre_estado'] . '" id="status_order" onchange="updateOrderStatus(this);">'
          . '<option order_id="' . $row['comanda_id'] . '" value="' . $row['estado_id'] . '" selected>' . $row['nombre_estado'] . '</option>' .
          '<option class="Pendiente" order_id="' . $row['comanda_id'] . '" value="6">Pendiente</option>' .
          '<option class="En Proceso" order_id="' . $row['comanda_id'] . '" value="8">En Proceso</option>' .
          '<option class="Entregado" order_id="' . $row['comanda_id'] . '" value="7">Entregado</option>' .
          '<option class="Listo" order_id="' . $row['comanda_id'] . '" value="9">Listo</option>' .
          '</select>',
        'acciones' => $acciones

      ];
    }
  ]);
}

if ($_POST['action'] == "cargar_detalle_facturas") {
  $db = Database::connect();
  $user_id = $_SESSION['identity']->usuario_id;
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

  handleDataTableRequest($db, [
    'columns' => [
      'nombre_producto',
      'precio',
      'nombre_pieza',
      'nombre_servicio',
      'cantidad_total',
      'id',
      'descuento',
      'impuesto',
      'valor'
    ],
    'base_table' => 'detalle_facturas_ventas df',
    'table_with_joins' => 'detalle_facturas_ventas df
               LEFT JOIN detalle_ventas_con_productos dvp ON df.detalle_venta_id = dvp.detalle_venta_id
               LEFT JOIN productos p ON p.producto_id = dvp.producto_id
               LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
               LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
               LEFT JOIN detalle_ventas_con_piezas_ dvpz ON df.detalle_venta_id = dvpz.detalle_venta_id
               LEFT JOIN piezas pz ON pz.pieza_id = dvpz.pieza_id
               LEFT JOIN detalle_ventas_con_servicios dvs ON df.detalle_venta_id = dvs.detalle_venta_id
               LEFT JOIN servicios s ON s.servicio_id = dvs.servicio_id',
    'select' => 'SELECT p.nombre_producto, df.precio, pz.nombre_pieza, s.nombre_servicio, df.cantidad as cantidad_total, 
      df.detalle_venta_id as "id", df.descuento, df.impuesto, i.valor, df.costo',
    'base_condition' => 'df.factura_venta_id = ' . $id,
    'table_rows' => function ($row) {
      $precio = (float)($row['precio'] ?? 0);
      $costo = (float)($row['costo'] ?? 0);
      $cantidad = ($row['cantidad_total'] ?? 0);
      $impuesto = (float)($row['impuesto'] ?? 0);
      $valor = (float)($row['valor'] ?? 0);
      $descuento = (float)($row['descuento'] ?? 0);
      $importe = ($cantidad * $precio) + ($cantidad * $impuesto) - ($cantidad * $descuento);

      return [
        'descripcion' => $row['nombre_producto']
          ? Help::getVariantId($row['id'])
          : (
            !empty($row['nombre_pieza'])
            ? ucwords($row['nombre_pieza'])
            : (!empty($row['nombre_servicio']) ? ucwords($row['nombre_servicio']) : '')
          ),
        'cantidad' => $cantidad,
        'precio' => '<span>
                <a href="#">' . number_format($precio, 2) . '</a>
                <span id="toggle" class="toggle-right toggle-md">
                Costo: ' . number_format($costo, 2) . '
                </span>
            </span>',
        'impuesto' => '<span class="hide-cell">' . number_format($cantidad * $impuesto, 2) . ' - (' . number_format($valor, 2) . '%)</span>',
        'descuento' => number_format($cantidad * $descuento, 2),
        'total' => number_format($importe, 2),
        'acciones' => '<a class="btn-action action-danger" onclick="deleteInvoiceDetail(\'' . $row['id'] . '\')">
        ' . BUTTON_ERASE . '
        </a>'
      ];
    }
  ]);
}

// cargar datos del detalle temporal

if ($_POST['action'] == "cargar_detalle_temporal") {
  $db = Database::connect();
  $user_id = $_SESSION['identity']->usuario_id;
  handleDataTableRequest($db, [
    'columns' => [
      'detalle_temporal_id',
      'usuario_id',
      'producto_id',
      'pieza_id',
      'servicio_id',
      'descripcion',
      'cantidad',
      'precio',
      'impuesto',
      'descuento',
      'fecha',
      'hora'
    ],
    'base_table' => 'detalle_temporal',
    'table_with_joins' => 'detalle_temporal',
    'select' => 'SELECT detalle_temporal_id,usuario_id,producto_id,pieza_id,servicio_id,descripcion,
      cantidad,precio,costo,impuesto,descuento,fecha,hora',
    'base_condition' => 'usuario_id = ' . $user_id,
    'table_rows' => function ($row) use ($db) {

      // Verificar si el producto tiene variantes
      $hasVariants = false;
      if ($row['producto_id']) {
        $sql_check_variants = "SELECT COUNT(*) AS variants_count FROM variantes WHERE producto_id = '" . $row['producto_id'] . "'";
        $result_variants = $db->query($sql_check_variants);
        if ($result_variants) {
          $variant_data = $result_variants->fetch_assoc();
          if ($variant_data['variants_count'] > 0) {
            $hasVariants = true;
          }
        }
      }

      // Generar el input con el atributo disabled si el producto tiene variantes
      $input_html = '<input type="number" class="input-update input-quantity" 
               value="' . (intval($row['cantidad']) == $row['cantidad'] ? number_format($row['cantidad'], 0) : number_format($row['cantidad'], 2)) . '" 
               data-id="' . $row['detalle_temporal_id'] . '" 
               data-item-id="' .
        ($row['producto_id'] ? $row['producto_id'] : ($row['pieza_id'] ? $row['pieza_id'] : $row['servicio_id'])) . '" 
               data-item-type="' .
        ($row['producto_id'] ? 'producto' : ($row['pieza_id'] ? 'pieza' : 'servicio')) . '" 
               step="0.01" ' . ($hasVariants ? 'disabled' : '') . ' />';

      return [
        'descripcion' => Help::loadVariantTemp($row['detalle_temporal_id']),
        'cantidad' => $input_html,
        'precio' => '<span>
                <a href="#">
                    ' . number_format($row['precio'], 2) . '
                </a>
                <span id="toggle" class="toggle-right toggle-md">
                ' . 'Costo: ' . number_format($row['costo'], 2) . '
                </span>
            </span>',
        'impuesto' => '<span class="hide-cell">' . number_format($row['cantidad'] * $row['impuesto'], 2) . '</span>',
        'descuento' => number_format($row['descuento'] ?? 0, 2),
        'importe' => number_format(
          ($row['cantidad'] * $row['precio']) +
            ($row['cantidad'] * $row['impuesto']) -
            ($row['cantidad'] * $row['descuento']),
          2
        ),
        'acciones' => '
        <a class="btn-action action-danger" onclick="deleteInvoiceDetail(\'' . $row['detalle_temporal_id'] . '\')">
            ' . BUTTON_ERASE . '
        </a>'
      ];
    }
  ]);
}

// Mostrar index de todas las cotizaciones

if ($_POST['action'] == "index_cotizaciones") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => [
      'c.cotizacion_id',
      'cl.nombre',
      'cl.apellidos',
      'c.total',
      'c.fecha'
    ],
    'searchable' => [
      'cl.nombre',
      'cl.apellidos',
      'c.cotizacion_id',
      'c.total',
      'c.fecha'
    ],
    'base_table' => 'cotizaciones',
    'table_with_joins' => 'cotizaciones c INNER JOIN clientes cl ON cl.cliente_id = c.cliente_id',
    'select' => 'SELECT c.cotizacion_id, cl.nombre, cl.apellidos, c.total, c.fecha',
    'table_rows' => function ($row) {
      return [
        'id' => 'CT-00' . $row['cotizacion_id'],
        'nombre' => ucwords($row['nombre'] . ' ' . $row['apellidos']),
        'fecha' => $row['fecha'],
        'total' => '<span class="text-primary hide-cell">' . number_format($row['total'], 2) . '</span>',
        'acciones' => '
                  <a class="btn-action action-info" href="' . base_url . 'invoices/edit_quote&id=' . $row['cotizacion_id'] . '" title="editar">
                      ' . BUTTON_EDIT . '
                  </a>
                  <span onclick="deleteQuote(\'' . $row['cotizacion_id'] . '\')" class="btn-action action-danger">
                      ' . BUTTON_DELETE . '
                  </span>'
      ];
    }
  ]);
}

// Mostrar index de todas las facturas
if ($_POST['action'] == "index_facturas_ventas") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => [
      'f.factura_venta_id',
      'c.nombre',
      'c.apellidos',
      'f.fecha',
      'f.total',
      'f.recibido',
      'f.pendiente',
      'f.bono',
      'e.nombre_estado'
    ],
    'searchable' => [
      'c.nombre',
      'c.apellidos',
      'e.nombre_estado',
      'f.factura_venta_id',
      'f.total',
      'f.recibido',
      'f.pendiente',
      'f.fecha'
    ],
    'base_table' => 'facturas_ventas f INNER JOIN clientes c ON f.cliente_id = c.cliente_id INNER JOIN estados_generales e ON f.estado_id = e.estado_id',
    'table_with_joins' => 'facturas_ventas f INNER JOIN clientes c ON f.cliente_id = c.cliente_id INNER JOIN estados_generales e ON f.estado_id = e.estado_id',
    'select' => 'SELECT f.factura_venta_id, c.nombre, c.apellidos, f.total, f.recibido, f.pendiente, f.bono, e.nombre_estado, f.fecha as fecha_factura',
    'table_rows' => function ($row) {

      // Comprobar si la factura tiene detalles asociados
      $hasDetails = Help::checkIfInvoiceHasDetails($row['factura_venta_id'], 'FT');
      // Acciones
      $acciones = '<a ';
      if ($_SESSION['identity']->nombre_rol == 'administrador') {
        $acciones .= 'class="btn-action action-info" href="' . base_url . 'invoices/edit&id=' . $row['factura_venta_id'] . '"';
      } else {
        $acciones .= 'class="btn-action action-info action-disable" href="#"';
      }
      $acciones .= ' title="Editar">' . BUTTON_EDIT . '</a>';

      if ($_SESSION['identity']->nombre_rol == 'administrador') {
        $acciones .= '<span onclick="deleteInvoice(\'' . $row['factura_venta_id'] . '\')" class="btn-action action-danger" title="Eliminar">
        ' . BUTTON_DELETE . '</span>';
      } else {
        $acciones .= '<span class="btn-action action-danger action-disable" title="Eliminar">' . BUTTON_DELETE . '</span>';
      }

      return [
        'factura_venta_id' => 'FT-00' . $row['factura_venta_id'],
        'nombre' => ucwords($row['nombre'] . ' ' . $row['apellidos']),
        'fecha_factura' => $row['fecha_factura'],
        'total' => '<span class="text-primary hide-cell">' . number_format($row['total'] ?? 0, 2) . '</span>',
        'recibido' => '<span class="text-success hide-cell">' . number_format($row['recibido'] ?? 0, 2) . '</span>',
        'pendiente' => '<span class="text-danger hide-cell">' . number_format($row['pendiente'] ?? 0, 2) . '</span>',
        'bono' => '<span class="text-warning hide-cell">' . number_format($row['bono'] ?? 0, 2) . '</span>',
        'nombre_estado' => $hasDetails ? '<p class="' . $row['nombre_estado'] . '">' . $row['nombre_estado'] . '</p>' : '<p class="no-details">N/D</p>',
        'acciones' => $acciones,
      ];
    }
  ]);
}

// Agregar al Detalle Temporal

if ($_POST['action'] == "agregar_detalle_temporal") {

  $db = Database::connect();

  $params = [
    (int)$_POST['product_id'] ?? 0,
    (int)$_POST['piece_id'] ?? 0,
    (int)$_POST['service_id'] ?? 0,
    $_POST['description'],
    (int)$_SESSION['identity']->usuario_id,
    $_POST['quantity'],
    $_POST['cost'] ?? 0,
    $_POST['price'],
    $_POST['taxes'] ?? 0,
    (int)$_POST['discount'] ?? 0
  ];

  echo handleProcedureAction($db, 'vt_crearDetalleTemporal', $params);
}

if ($_POST['action'] == "asignar_variantes_temporales") {

  $variant_id = $_POST['variant_id'];
  $detail_id = $_POST['detail_id'];

  $db = Database::connect();
  $query = "CALL vt_detalleVarianteTemp('$variant_id','$detail_id')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
} else if ($_POST['action'] == "asignar_variantes") {

  $variant_id = $_POST['variant_id'];
  $detail_id = $_POST['detail_id'];

  $db = Database::connect();
  $query = "CALL vt_variantesFacturadas('$variant_id','$detail_id')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Agregar producto al detalle de ventas

if ($_POST['action'] === "agregar_detalle_venta") {

  // 1) Validar invoice_id y order_id
  $invoice = (isset($_POST['invoice']) && is_numeric($_POST['invoice']))
    ? (int) $_POST['invoice']
    : null;
  $order = (isset($_POST['order_id']) && is_numeric($_POST['order_id']))
    ? (int) $_POST['order_id']
    : null;

  // 2) Resto de datos
  $user_id     = (int) $_SESSION['identity']->usuario_id;
  $product_id  = (int) ($_POST['product_id']  ?? 0);
  $piece_id    = (int) ($_POST['piece_id']    ?? 0);
  $service_id  = (int) ($_POST['service_id']  ?? 0);
  $quantity    = $_POST['quantity'] ?? 0;
  $cost        = $_POST['cost'] ?? 0;
  $price       = $_POST['price'];
  $taxes       = $_POST['taxes'] ?? 0;
  $discount    = $_POST['discount'] ?? 0;

  $db = Database::connect();

  echo handleProcedureAction($db, 'vt_agregarDetalleVenta', [
    $invoice,
    $order,
    $user_id,
    $quantity,
    $cost,
    $price,
    $taxes,
    $discount,
    $product_id,
    $piece_id,
    $service_id
  ]);
}

// Obtener precios del detalle temporal

if ($_POST['action'] == "precios_detalle_temp") {

  $db = Database::connect();

  $user_id = $_SESSION['identity']->usuario_id;

  $query = "SELECT
    SUM(cantidad * impuesto)  AS taxes,
    SUM(cantidad * descuento) AS descuentos,
    SUM(cantidad * precio)    AS precios
    FROM detalle_temporal WHERE usuario_id = '$user_id'";

  jsonQueryResult($db, $query);
}

// Obtener precios del detalle de factura

if ($_POST['action'] == "precios_detalle_venta") {

  $db = Database::connect();

  $invoice_id = (int)$_POST['invoice_id'];

  $query = "SELECT 
  SUM(d.cantidad * d.impuesto)  AS taxes,
  SUM(d.cantidad * d.descuento) AS descuentos,
  SUM(d.cantidad * d.precio)    AS precios,
  f.total, f.pendiente, f.recibido
  FROM detalle_facturas_ventas d 
  INNER JOIN facturas_ventas f on f.factura_venta_id = d.factura_venta_id 
  WHERE d.factura_venta_id = '$invoice_id'";

  jsonQueryResult($db, $query);
}

// Obtener precios de las ordenes de ventas

if ($_POST['action'] == "precios_ordenes_ventas") {

  $db = Database::connect();
  $order_id = $_POST['order_id'] ?? 0;
  $user_id = $_SESSION['identity']->usuario_id;

  $sql = "SELECT 
  SUM(cantidad * impuesto)  AS taxes,
  SUM(cantidad * descuento) AS descuentos,
  SUM(cantidad * precio)    AS precios
  FROM detalle_facturas_ventas ";

  if (!empty($order_id)) {
    $sql .= "WHERE comanda_id = '$order_id'";
  } else {
    $sql .= "WHERE usuario_id = '$user_id' AND comanda_id IS NULL
    AND factura_venta_id IS NULL";
  }

  jsonQueryResult($db, $sql);
}

// Eliminar producto del detalle temporar

if ($_POST['action'] == 'eliminar_detalle_temporal') {

  $db = Database::connect();

  echo handleDeletionAction($db, (int)$_POST['id'], 'vt_eliminarDetalleTemporal');
}

// Eliminar producto del detalle venta

if ($_POST['action'] == 'eliminar_detalle_venta') {

  $db = Database::connect();

  echo handleDeletionAction($db, (int)$_POST['id'], 'vt_eliminarDetalleVenta');
}

// Factura al contado

if ($_POST['action'] == "factura_contado") {

  $db = Database::connect();

  echo handleProcedureAction($db, 'vt_facturaVenta', [
    (int)$_POST['customer_id'],
    (int)$_POST['method_id'],
    $_POST['total_invoice'],
    $_POST['bonus'] ?? 0,
    (int)$_SESSION['identity']->usuario_id,
    $_POST['observation'],
    $_POST['date']
  ]);
}

// Pasar detalle temporal al detalle de la venta y asignarle la factura

if ($_POST['action'] == "registrar_detalle_de_venta") {


  // Registrar variantes asociadas al detalle
  function facturarVariantes($detail_temp_id, $detail_id)
  {

    $db = Database::connect();

    $exec = "SELECT variante_id FROM detalle_variantes_temporal WHERE detalle_temporal_id = '$detail_temp_id'";
    $variants = $db->query($exec);

    // Loop
    while ($variant = $variants->fetch_object()) {

      // Verificar si el producto tiene variantes
      if ($variants->num_rows > 0) {

        $exec1 = "INSERT INTO variantes_facturadas values ($variant->variante_id,$detail_id);";
        $exec2 = "DELETE FROM detalle_variantes_temporal WHERE variante_id = '$variant->variante_id';";
        $db->query($exec1);
        $db->query($exec2);
      }
    }
  }

  $invoice_id = $_POST['invoice_id'];
  $user_id = $_SESSION['identity']->usuario_id;
  $date = $_POST['date'];

  $db = Database::connect();

  // Desactivar triggers temporalmente
  $db->query("DROP TRIGGER IF EXISTS restar_stock_productos");
  $db->query("DROP TRIGGER IF EXISTS restar_stock_piezas");
  $db->query("DROP TRIGGER IF EXISTS devolver_variantes_temporales");
  $db->query("DROP TRIGGER IF EXISTS devolver_stocks_temporales");
  $db->query("DROP TRIGGER IF EXISTS agregar_item_venta");

  // Obtener datos del carrito temporal
  $query1 = "SELECT d.detalle_temporal_id, d.usuario_id, d.producto_id,d.servicio_id,
  d.pieza_id,d.descripcion,d.cantidad,d.precio,d.impuesto,d.descuento,d.costo FROM detalle_temporal d 
  WHERE d.usuario_id = '$user_id';";

  $datos = $db->query($query1);

  while ($element = $datos->fetch_object()) {

    $product_id = $element->producto_id;
    $piece_id = $element->pieza_id;
    $service_id = $element->servicio_id;
    $cost = $element->costo;
    $price = $element->precio;
    $discount = $element->descuento;
    $quantity = $element->cantidad;
    $taxes = $element->impuesto;
    $detail_temp_id = $element->detalle_temporal_id;

    $query2 = "INSERT INTO detalle_facturas_ventas 
                (factura_venta_id, usuario_id, cantidad, costo, precio, impuesto, descuento, fecha)
                VALUES ($invoice_id, $user_id, $quantity, $cost, $price, $taxes, $discount, '$date')";

    if ($db->query($query2) === TRUE) {

      // Obtener el ID del detalle insertado
      $detail_id = $db->insert_id;

      // Verificar si se debe insertar en detalle_ventas_con_piezas_
      if ($piece_id > 0) {
        $exec1 = "INSERT INTO detalle_ventas_con_piezas_ VALUES ($detail_id, $piece_id, $invoice_id, null)";
        $db->query($exec1);
      }
      // Verificar si se debe insertar en detalle_ventas_con_servicios
      else if ($service_id > 0) {
        $exec2 = "INSERT INTO detalle_ventas_con_servicios VALUES ($detail_id, $service_id, $invoice_id, null)";
        $db->query($exec2);
      }
      // Verificar si se debe insertar en detalle_ventas_con_productos
      else if ($product_id > 0) {
        $exec = "INSERT INTO detalle_ventas_con_productos VALUES ($detail_id, $product_id, $invoice_id, null)";
        $db->query($exec);

        // Llamar a la función para facturar variantes
        facturarVariantes($detail_temp_id, $detail_id);
      }
    }
  }

  // Obtener el detalle insertado (para devolverlo)
  $response = $db->query($query1);
  echo json_encode($response->fetch_all(), JSON_UNESCAPED_UNICODE); // devolver datos del detalle

  // Eliminar detalle temporal
  $query4 = "DELETE FROM detalle_temporal WHERE usuario_id = '$user_id'";
  $db->query($query4);

  // Activar TRIGGER
  Help::createAllTriggers();
}


// Factura a crédito

if ($_POST['action'] == "factura_credito") {

  $customer_id = $_POST['customer_id'];
  $total = $_POST['total_invoice'];
  $description = $_POST['description'];
  $method = $_POST['payment_method'];
  $user_id = $_SESSION['identity']->usuario_id;
  $pay = $_POST['pay'];
  //$pending = $_POST['pending'];
  $date = $_POST['date'];

  $db = Database::connect();

  $query = "CALL vt_facturaAcredito($customer_id,$method,'$total','$pay',$user_id,'$description','$date')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}


// Eliminar factura de venta

if ($_POST['action'] == 'eliminar_factura') {

  $id = $_POST['id'];
  $db = Database::connect();

  $query = "CALL vt_eliminarFacturaVenta($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Consultar bono de cliente

if ($_POST['action'] == 'consultar_bono') {

  $id = $_POST['customer_id'];
  $db = Database::connect();

  $query = "SELECT sum(b.valor) as valor FROM clientes c 
  INNER JOIN bonos b ON c.cliente_id = b.cliente_id 
  WHERE c.cliente_id = '$id'";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;
}


// actualizar datos de factura

if ($_POST['action'] == 'actualizar_factura') {

  $id = $_POST['id'];
  $customer_id = $_POST['customer_id'];
  $observation = $_POST['observation'];
  $method = $_POST['method'];
  $db = Database::connect();

  $query = "CALL vt_actualizarFactura($id,$customer_id,'$observation',$method)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Crear cotizaciones

if ($_POST['action'] == "registrar_cotizaciones") {

  $db = Database::connect();

  echo handleProcedureAction($db, 'ct_cotizacion', [
    (int) $_POST['customer_id'],
    (int) $_SESSION['identity']->usuario_id,
    $_POST['total'],
    $_POST['observation'],
    $_POST['date']
  ]);
}

// actualizar cotizaciones

if ($_POST['action'] == "actualizar_cotizaciones") {

  $customer_id = $_POST['customer_id'];
  $observation = $_POST['observation'];
  $user_id = $_SESSION['identity']->usuario_id;
  $date = $_POST['date'];
  $id = $_POST['quote_id'];

  $db = Database::connect();

  $query = "CALL ct_actualizarCotizacion($customer_id,$id,'$observation','$date')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Ha ocurrido un error al registrar: " . $data->msg;
  }
}

// Agregar detalle de cotizacion


if ($_POST['action'] == "crear_detalle_cotizacion") {

  $quote_id = $_POST['id'];
  $user_id = $_SESSION['identity']->usuario_id;
  $description = $_POST['description'];
  $discount = (!empty($_POST['discount'])) ? $_POST['discount'] : 0;
  $quantity = (!empty($_POST['quantity'])) ? $_POST['quantity'] : 0;
  $taxes = (!empty($_POST['taxes'])) ? $_POST['taxes'] : 0;
  $price = $_POST['price'];

  $db = Database::connect();

  // Desactivar trigger
  $db->query("DROP TRIGGER IF EXISTS agregar_item_cotizacion");

  $query = "INSERT INTO detalle_cotizaciones values 
  (null,$quote_id,$user_id,'$description','$quantity','$price','$taxes','$discount',curdate());";

  if ($db->query($query) === TRUE) {
    echo "ready";
  } else {
    echo "Ha ocurrido un error";
  }


  // Activar trigger
  Help::CREATE_TRIGGER_agregar_item_cotizacion();
}



if ($_POST['action'] == "agregar_detalle_cotizacion") {

  $quote_id = $_POST['id'];
  $user_id = $_SESSION['identity']->usuario_id;
  $description = $_POST['description'];
  $discount = (!empty($_POST['discount'])) ? $_POST['discount'] : 0;
  $quantity = (!empty($_POST['quantity'])) ? $_POST['quantity'] : 0;
  $taxes = (!empty($_POST['taxes'])) ? $_POST['taxes'] : 0;
  $price = $_POST['price'];

  $db = Database::connect();

  $query = "INSERT INTO detalle_cotizaciones values 
  (null,$quote_id,$user_id,'$description','$quantity','$price','$taxes','$discount',curdate());";

  if ($db->query($query) === TRUE) {
    echo "ready";
  } else {
    echo "Ha ocurrido un error";
  }
}

// Eliminar cotizacion

if ($_POST['action'] == 'eliminar_cotizacion') {

  $id = $_POST['id'];
  $db = Database::connect();

  echo handleDeletionAction($db, $id, "ct_eliminarCotizacion");
}

// Eliminar detalle cotizacion

if ($_POST['action'] == 'eliminar_detalle_cotizacion') {

  $id = $_POST['id'];
  $db = Database::connect();

  echo handleDeletionAction($db, $id, "ct_eliminarDetalle");
}

// Obtener total de la factura de cotizacion

if ($_POST['action'] == "total_cotizacion") {

  $db = Database::connect();

  $id = $_POST['invoice_id'];

  $query = "SELECT 
  SUM(d.cantidad * d.impuesto)  AS taxes,
  SUM(d.cantidad * d.descuento) AS descuentos,
  SUM(d.cantidad * d.precio)    AS precios,
  SUM((d.cantidad * d.precio) + (d.cantidad * d.impuesto) - d.descuento) AS total_factura
FROM detalle_cotizaciones d 
INNER JOIN cotizaciones c ON c.cotizacion_id = d.cotizacion_id
WHERE c.cotizacion_id = '$id'";

  jsonQueryResult($db, $query);
}

// Registra el detalle de la orden con la factura correspondiente
if ($_POST['action'] === "registrar_detalle_orden_venta") {

  $order_id = (int) $_POST['order_id'];
  $invoice_id = (int) $_POST['invoice_id'];
  $date = $_POST['date'];

  $db = Database::connect();
  $anyAffected = false;

  try {
    // 1. detalle_ventas_con_productos
    $sql1 = "UPDATE detalle_ventas_con_productos
             SET factura_venta_id = '$invoice_id'
             WHERE comanda_id = '$order_id'";
    if (!$db->query($sql1)) {
      throw new Exception("Error en productos: " . $db->error);
    }
    if ($db->affected_rows > 0) $anyAffected = true;

    // 2. detalle_ventas_con_piezas_ (verifica si ese guion bajo es correcto)
    $sql2 = "UPDATE detalle_ventas_con_piezas_
             SET factura_venta_id = '$invoice_id'
             WHERE comanda_id = '$order_id'";
    if (!$db->query($sql2)) {
      throw new Exception("Error en piezas: " . $db->error);
    }
    if ($db->affected_rows > 0) $anyAffected = true;

    // 3. detalle_ventas_con_servicios
    $sql3 = "UPDATE detalle_ventas_con_servicios
             SET factura_venta_id = '$invoice_id'
             WHERE comanda_id = '$order_id'";
    if (!$db->query($sql3)) {
      throw new Exception("Error en servicios: " . $db->error);
    }
    if ($db->affected_rows > 0) $anyAffected = true;

    // 4. detalle_facturas_ventas
    $dateEscaped = $db->real_escape_string($date);

    $sql4 = "UPDATE detalle_facturas_ventas
             SET factura_venta_id = '$invoice_id', fecha = '$dateEscaped'
             WHERE comanda_id = '$order_id'";
    if (!$db->query($sql4)) {
      throw new Exception("Error en detalle_facturas: " . $db->error);
    }
    if ($db->affected_rows > 0) $anyAffected = true;

    // Éxito
    echo json_encode([
      "success" => true,
      "anyAffected" => $anyAffected
    ]);
  } catch (Exception $e) {
    // Error capturado
    http_response_code(500);
    echo json_encode([
      "success" => false,
      "error" => $e->getMessage()
    ]);
  }
}



// Obtener datos del detalle de venta para imprimir

if ($_POST['action'] === "obtener_detalle_orden") {
  $db = Database::connect();

  $id = $_POST['orderId'];

  // Detalle
  $q1 = "SELECT df.precio,df.descuento,
  COALESCE(p.nombre_producto, s.nombre_servicio, pz.nombre_pieza) AS descripcion,
  df.comanda_id,df.cantidad
  FROM detalle_facturas_ventas df
  LEFT JOIN detalle_ventas_con_productos dvp ON df.detalle_venta_id = dvp.detalle_venta_id
  LEFT JOIN productos p ON p.producto_id = dvp.producto_id
  LEFT JOIN detalle_ventas_con_servicios dvs ON df.detalle_venta_id = dvs.detalle_venta_id
  LEFT JOIN servicios s ON s.servicio_id = dvs.servicio_id
  LEFT JOIN detalle_ventas_con_piezas_ dvpz ON df.detalle_venta_id = dvpz.detalle_venta_id
  LEFT JOIN piezas pz ON pz.pieza_id = dvpz.pieza_id
  WHERE df.comanda_id = '$id'";

  // datos de la orden
  $q2 = "SELECT concat(c.nombre,' ',IFNULL(c.apellidos,'')) as 'nombre', co.observacion,co.tipo_entrega,
  co.direccion_entrega,co.nombre_receptor,co.telefono_receptor,co.fecha,c.telefono1,u.nombre as 'cajero'
  FROM comandas co
  INNER JOIN clientes c ON c.cliente_id = co.cliente_id 
  INNER JOIN usuarios u on u.usuario_id = co.usuario_id
  WHERE comanda_id = '$id'";

  // Totales
  $q3 = "SELECT SUM(cantidad * precio) AS subtotal,
  SUM(IFNULL(descuento, 0) * cantidad) AS total_descuento,  -- Descuento total por cantidad
  SUM(IFNULL(impuesto, 0) * cantidad) AS total_impuesto,    -- Impuesto total por cantidad
  (SUM(cantidad * precio) 
    - SUM(IFNULL(descuento, 0) * cantidad) 
    + SUM(IFNULL(impuesto, 0) * cantidad)
  ) AS total 
  FROM detalle_facturas_ventas
  WHERE comanda_id = '$id'";

  // Ejecutar las consultas
  $result1 = $db->query($q1)->fetch_all();
  $result2 = $db->query($q2)->fetch_assoc();
  $result3 = $db->query($q3)->fetch_assoc();

  // Devolver los resultados

  echo json_encode([$result1, $result2, $result3], JSON_UNESCAPED_UNICODE);
}

//Obtener datos de la orden *comanda*
if ($_POST['action'] === "obtener_datos_orden") {
  $db = Database::connect();

  $id = $_POST['orden_id'];

  $sql = "SELECT c.cliente_id,concat(c.nombre,' ',IFNULL(c.apellidos,'')) as 'nombre', 
  co.comanda_id,co.usuario_id,co.observacion,co.tipo_entrega,co.telefono_receptor,
  co.direccion_entrega,co.nombre_receptor,co.fecha FROM comandas co 
  INNER JOIN clientes c ON c.cliente_id = co.cliente_id
  WHERE co.comanda_id = '$id'";


  jsonQueryResult($db, $sql);
}

// Acción para actualizar cantidad en detalle_temporal o detalle_facturas_ventas
if ($_POST['action'] === "actualizar_cantidad_detalle_temporal" || $_POST['action'] === "actualizar_cantidad_orden_venta") {
  $db = Database::connect();

  // Recoger los datos enviados desde el frontend
  $id = isset($_POST['id']) ? (int) $_POST['id'] : 0; // ID del detalle temporal
  $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0; // Nueva cantidad a actualizar
  $item_id = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0; // ID del producto, pieza o servicio
  $item_type = isset($_POST['item_type']) ? $_POST['item_type'] : ''; // Tipo de item: 'producto', 'pieza', 'servicio'

  // Validación básica para asegurar que los valores sean válidos
  if ($id <= 0 || $quantity <= 0 || $item_id <= 0 || empty($item_type)) {
    echo "Error: Datos inválidos o incompletos.";
    exit;
  }

  // Determinar la tabla de detalle y la columna del ID correspondiente
  $tabla_detalle = '';
  $tabla_id = '';

  // Determinar la tabla de detalle y la tabla del item según el tipo de item
  if ($_POST['action'] === "actualizar_cantidad_detalle_temporal") {
    $tabla_detalle = 'detalle_temporal';
    $tabla_id = "detalle_temporal_id";
  } else {
    $tabla_detalle = 'detalle_facturas_ventas';
    $tabla_id = "detalle_venta_id";
  }

  // Determinar la tabla del item según el tipo de item
  switch ($item_type) {
    case 'producto':
      $tabla_item = 'productos';
      break;
    case 'pieza':
      $tabla_item = 'piezas';
      break;
    case 'servicio':
      $tabla_item = ''; // No requiere tabla de stock
      break;
    default:
      echo "Error: tipo de item no válido.";
      exit;
  }

  // Llamar a la función de actualización
  echo updateDetailQuantity($db, $id, $quantity, $item_id, $item_type, $tabla_detalle, $tabla_id);
}


if ($_POST['action'] == "agregar_detalle_pos") {

  $db = Database::connect();

  $params = [
    (int) ($_POST['order_id'] ?? 0), // Usamos el valor de order_id o 0 si no está presente
    (int) $_SESSION['identity']->usuario_id,
    $_POST['quantity'] ?? 0,
    $_POST['cost'] ?? 0,
    $_POST['price'],
    (int) $_POST['product_id'] ?? 0,
    (int)$_POST['piece_id'] ?? 0,
    (int)$_POST['service_id'] ?? 0
  ];

  $procedure = ($_POST['order_id'] > 0) ? 'pos_agregar_producto' : 'pos_agregar_producto_sin';

  echo handleProcedureAction($db, $procedure, $params);
}

// Obtener datos de la venta editar del pos 
if ($_POST['action'] == "datos_detalle_id") {

  $db = Database::connect();

  $id = $_POST['detail_id'];

  $sql = "SELECT 
  p.producto_id,pz.pieza_id,s.servicio_id,
  COALESCE(p.nombre_producto,pz.nombre_pieza,s.nombre_servicio) AS item,
  CASE
        WHEN p.nombre_producto IS NOT NULL THEN 'Producto'
        WHEN pz.nombre_pieza IS NOT NULL THEN 'Pieza'
        WHEN s.nombre_servicio IS NOT NULL THEN 'Servicio'
        ELSE 'Desconocido' 
  END AS tipo_item,
  d.cantidad,d.descuento,d.impuesto,d.precio,p.cantidad as existencia,p.imagen
  FROM detalle_facturas_ventas d
  LEFT JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
  LEFT JOIN productos p ON p.producto_id = dp.producto_id
  LEFT JOIN detalle_ventas_con_piezas_ dpz ON dpz.detalle_venta_id = d.detalle_venta_id
  LEFT JOIN piezas pz ON pz.pieza_id = dpz.pieza_id
  LEFT JOIN detalle_ventas_con_servicios dps ON dps.detalle_venta_id = d.detalle_venta_id
  LEFT JOIN servicios s ON s.servicio_id = dps.servicio_id
  WHERE d.detalle_venta_id = '$id'";

  jsonQueryResult($db, $sql);
}

// Borrar detalle en el POS
if ($_POST['action'] == "borrar_detalle_pos") {

  $db = Database::connect();

  echo handleProcedureAction($db, 'pos_eliminar_todo', [
    (int)$_POST['order_id'],
    (int)$_SESSION['identity']->usuario_id
  ]);
}

// Actualizar el detalle en el POS
if ($_POST['action'] == "actualizar_detalle_pos") {

  $db = Database::connect();

  $params = [
    (int)$_POST['product_id'],
    (int)$_POST['piece_id'],
    (int)$_POST['service_id'],
    (int)$_POST['detail_id'],
    (int)$_SESSION['identity']->usuario_id,
    $_POST['discount'] ?? 0,
    $_POST['taxes'] ?? 0,
    $_POST['base_price'],
    $_POST['quantity']
  ];

  echo handleProcedureAction($db, 'pos_update_detalle', $params);
}


if ($_POST['action'] == "cargar_ordenes_pos") {

  $db = Database::connect();

  // Ordenes sin factura
  $sql = "SELECT COUNT(d.comanda_id) AS total_items,co.comanda_id, c.cliente_id, c.nombre
  FROM comandas co
  INNER JOIN clientes c ON c.cliente_id = co.cliente_id
  LEFT JOIN detalle_facturas_ventas d ON d.comanda_id = co.comanda_id
  WHERE d.factura_venta_id IS NULL OR d.factura_venta_id = 0  
  GROUP BY co.comanda_id
  ORDER BY co.comanda_id DESC";

  $result = mysqli_query($db, $sql);
  $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

  // Responder con los datos en formato JSON
  echo json_encode([
    "data" => $data   // Las registros de ordenes solicitadas
  ]);
}

// Factura al contado POS
if ($_POST['action'] == "factura_contado_pos") {

  $db = Database::connect();

  echo handleProcedureAction($db, 'pos_factura_venta', [
    (int)$_SESSION['identity']->usuario_id,
    (int)$_POST['customer_id'],
    (int)$_POST['method_id'],
    (int)$_POST['order_id'],
    $_POST['total_invoice']
  ]);
}

// Factura al contado POS
if ($_POST['action'] == "factura_credito_pos") {

  $db = Database::connect();

  echo handleProcedureAction($db, 'pos_factura_credito', [
    (int)$_SESSION['identity']->usuario_id,
    (int)$_POST['customer_id'],
    (int)$_POST['method_id'],
    (int)$_POST['order_id'],
    $_POST['total_invoice'],
    $_POST['pay'],
    $_POST['date']
  ]);
}

// Devolver datos del detalle para imprimir en POS

if ($_POST['action'] == "devolver_datos_impresion") {

  $id = $_POST['id'];
  $user_id = $_SESSION['identity']->usuario_id;
  $db = Database::connect();

  // Datos del detalle
  $sql = "SELECT p.nombre_producto, df.precio, pz.nombre_pieza, s.nombre_servicio, df.cantidad as cantidad_total, 
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

  // Datos de facturacion
  $sql2 = "SELECT concat(c.nombre,' ',IFNULL(c.apellidos,'')) as nombre,
  concat(u.nombre,' ',IFNULL(u.apellidos,'')) as usuario,
  m.nombre_metodo, f.factura_venta_id, f.pendiente,f.recibido, f.fecha, f.descripcion,
  SUM(df.cantidad * df.precio) AS subtotal,
  SUM(IFNULL(df.descuento, 0) * df.cantidad) AS total_descuento,  -- Descuento total por cantidad
  SUM(IFNULL(df.impuesto, 0) * df.cantidad) AS total_impuesto,    -- Impuesto total por cantidad
  (SUM(df.cantidad * df.precio) 
    - SUM(IFNULL(df.descuento, 0) * df.cantidad) 
    + SUM(IFNULL(df.impuesto, 0) * df.cantidad)
  ) AS total
  FROM facturas_ventas f
  INNER JOIN detalle_facturas_ventas df ON df.factura_venta_id = f.factura_venta_id
  INNER JOIN clientes c ON c.cliente_id = f.cliente_id
  INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
  INNER JOIN usuarios u ON u.usuario_id = f.usuario_id
  WHERE f.factura_venta_id = '$id' GROUP BY f.factura_venta_id LIMIT 1";

  // Respuesta JSON
  echo json_encode([
    "detalle" => $db->query($sql)->fetch_all(),
    JSON_UNESCAPED_UNICODE,
    "datos" => $db->query($sql2)->fetch_assoc(),
    JSON_UNESCAPED_UNICODE
  ]);
}
