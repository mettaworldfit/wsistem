<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
require_once '../help.php';
session_start();

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
      return [
        'descripcion' => $row['nombre_producto']
          ? Help::getVariantId($row['id'])
          : (
            !empty($row['nombre_pieza'])
            ? ucwords($row['nombre_pieza'])
            : (!empty($row['nombre_servicio']) ? ucwords($row['nombre_servicio']) : '')
          ),
        'cantidad' => $row['cantidad_total'],
        'precio' => '<span>
                <a href="#">
                    ' . number_format($row['precio'], 2) . '
                </a>
                <span id="toggle" class="toggle-right toggle-md">
                ' . 'Costo: ' . number_format($row['costo'], 2) . '
                </span>
            </span>',
        'impuesto' => '<span class="hide-cell">' . number_format($row['cantidad_total'] * $row['impuesto'], 2) . ' - (' . $row['valor'] . '%)' . '</span>',
        'descuento' => number_format($row['descuento'], 2),
        'total' => number_format(
          ($row['cantidad_total'] * $row['precio']) +
            ($row['cantidad_total'] * $row['impuesto']) -
            $row['descuento'],
          2
        ),
        'acciones' => '<a class="text-danger pointer" style="font-size: 16px;" onclick="deleteInvoiceDetail(\'' . $row['id'] . '\')"><i class="fas fa-times"></i></a>'
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
    'table_rows' => function ($row) {

          
      return [
        'descripcion' => Help::loadVariantTemp($row['detalle_temporal_id']),
        'cantidad' => $row['cantidad'],
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
            $row['descuento'],
          2
        ),
        'acciones' => '
        <a class="text-danger pointer" style="font-size: 16px;"
           onclick="deleteInvoiceDetail(\'' . $row['detalle_temporal_id'] . '\')">
            <i class="fas fa-times"></i>
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
                  <a class="action-edit" href="' . base_url . 'invoices/edit_quote&id=' . $row['cotizacion_id'] . '" title="editar">
                      <i class="fas fa-pencil-alt"></i>
                  </a>
                  <span onclick="deleteQuote(\'' . $row['cotizacion_id'] . '\')" class="action-delete">
                      <i class="fas fa-times"></i>
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
      $acciones = '<a ';
      if ($_SESSION['identity']->nombre_rol == 'administrador') {
        $acciones .= 'class="action-edit" href="' . base_url . 'invoices/edit&id=' . $row['factura_venta_id'] . '"';
      } else {
        $acciones .= 'class="action-edit action-disable" href="#"';
      }
      $acciones .= ' title="Editar"><i class="fas fa-pencil-alt"></i></a>';

      if ($_SESSION['identity']->nombre_rol == 'administrador') {
        $acciones .= '<span onclick="deleteInvoice(\'' . $row['factura_venta_id'] . '\')" class="action-delete" title="Eliminar"><i class="fas fa-times"></i></span>';
      } else {
        $acciones .= '<span class="action-delete action-disable" title="Eliminar"><i class="fas fa-times"></i></span>';
      }

      return [
        'factura_venta_id' => 'FT-00' . $row['factura_venta_id'],
        'nombre' => ucwords($row['nombre'] . ' ' . $row['apellidos']),
        'fecha_factura' => $row['fecha_factura'],
        'total' => '<span class="text-primary hide-cell">' . number_format($row['total'], 2) . '</span>',
        'recibido' => '<span class="text-success hide-cell">' . number_format($row['recibido'], 2) . '</span>',
        'pendiente' => '<span class="text-danger hide-cell">' . number_format($row['pendiente'], 2) . '</span>',
        'bono' => '<span class="text-warning hide-cell">' . number_format($row['bono'], 2) . '</span>',
        'nombre_estado' => '<p class="' . $row['nombre_estado'] . '">' . $row['nombre_estado'] . '</p>',
        'acciones' => $acciones
      ];
    }
  ]);
}

// Agregar al Detalle Temporal

if ($_POST['action'] == "agregar_detalle_temporal") {

  $db = Database::connect();

  $params = [
    (int)$_POST['product_id'],
    (int)$_POST['piece_id'],
    (int)$_POST['service_id'],
    $_POST['description'],
    (int)$_SESSION['identity']->usuario_id,
    $_POST['quantity'],
    $_POST['cost'],
    $_POST['price'],
    $_POST['taxes'],
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

// Agregar producto al detalle de venta
 
if ($_POST['action'] == "agregar_detalle_venta") {

  $user_id = $_SESSION['identity']->usuario_id;
  $product_id =  $_POST['product_id'];
  $piece_id = $_POST['piece_id'];
  $service_id = $_POST['service_id'];
  $invoice_id = $_POST['invoice'];
  $discount = (!empty($_POST['discount'])) ? $_POST['discount'] : 0;
  $quantity = (!empty($_POST['quantity'])) ? $_POST['quantity'] : 0;
  $taxes = (!empty($_POST['taxes'])) ? $_POST['taxes'] : 0;
  $price = $_POST['price'];
  $cost = $_POST['cost'];

  $db = Database::connect();

  $query = "INSERT INTO detalle_facturas_ventas values (null,$invoice_id,$user_id,$quantity,$cost,$price,$taxes,$discount,curdate())";

  if ($db->query($query) === TRUE) {

    $detail_id = $db->insert_id; // ID 

    if ($product_id > 0) {

      $query1 = "INSERT INTO detalle_ventas_con_productos values ($detail_id,$product_id,$invoice_id)";
      if ($db->query($query1) === TRUE) {

        echo  $db->query("select last_insert_id() AS msg")->fetch_object()->msg;
      } else {
        echo "Ha ocurrido un error al insertar el producto";
      }
    } else if ($piece_id > 0) {

      $query2 = "INSERT INTO detalle_ventas_con_piezas_ values ($detail_id,$piece_id,$invoice_id)";
      if ($db->query($query2) === TRUE) {
        echo 1;
      } else {
        echo "Ha ocurrido un error al insertar la pieza";
      }
    } else if ($service_id > 0) {

      $query3 = "INSERT INTO detalle_ventas_con_servicios values ($detail_id,$service_id,$invoice_id)";
      if ($db->query($query3) === TRUE) {
        echo 1;
      } else {
        echo "Ha ocurrido un error al insertar el servicio";
      }
    }
  }
}

// Obtener precios del detalle temporal

if ($_POST['action'] == "precios_detalle_temp") {

  $db = Database::connect();

  $user_id = $_SESSION['identity']->usuario_id;

  $query = "SELECT sum(cantidad * impuesto) as taxes, sum(descuento) as descuentos, sum(cantidad * precio) as precios 
  FROM detalle_temporal WHERE usuario_id = '$user_id'";

  jsonQueryResult($db, $query);
}

// Obtener precios del detalle de factura

if ($_POST['action'] == "precios_detalle_venta") {

  $db = Database::connect();

  $invoice_id = $_POST['id'];

  $query = "SELECT sum(d.cantidad * d.impuesto) as taxes, sum(d.descuento) as descuentos, sum(d.cantidad * precio) as precios,
  f.total, f.pendiente, f.recibido
  FROM detalle_facturas_ventas d INNER JOIN facturas_ventas f on f.factura_venta_id = d.factura_venta_id WHERE d.factura_venta_id = '$invoice_id'";

  jsonQueryResult($db, $query);
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

  $customer_id = $_POST['customer_id'];
  $total = $_POST['total_invoice'];
  $description = $_POST['observation'];
  $method = $_POST['method_id'];
  $bonus = (!empty($_POST['bonus'])) ? $_POST['bonus'] : 0;
  $user_id = $_SESSION['identity']->usuario_id;
  $date = $_POST['date'];

  $db = Database::connect();

  $query = "CALL vt_facturaVenta($customer_id,$method,'$total','$bonus',$user_id,'$description','$date')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Pasar detalle temporal al detalle de la venta

if ($_POST['action'] == "registrar_detalle_de_venta") {

  /**
   * Registrar variantes asociadas al detalle
   */

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

  // Obtener datos del carrito temporal
  $query1 = "SELECT d.detalle_temporal_id, d.usuario_id, d.producto_id,d.servicio_id,
  d.pieza_id,d.descripcion,d.cantidad,d.costo,d.precio,d.impuesto,d.descuento FROM detalle_temporal d 
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

    $query2 = "INSERT INTO detalle_facturas_ventas values (null,$invoice_id,$user_id,$quantity,$cost,$price,$taxes,$discount,curdate())";
    if ($db->query($query2) === TRUE) {

      $detail_id = $db->insert_id; // ID 

      if ($piece_id > 0) {
        $exec1 = "INSERT INTO detalle_ventas_con_piezas_ values ($detail_id,$piece_id,$invoice_id)";
        $db->query($exec1);
      } else if ($service_id > 0) {
        $exec2 = "INSERT INTO detalle_ventas_con_servicios values ($detail_id,$service_id,$invoice_id)";
        $db->query($exec2);
      } else if ($product_id > 0) {

        $exec = "INSERT INTO detalle_ventas_con_productos values ($detail_id,$product_id,$invoice_id)";
        $db->query($exec);

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
  Help::CREATE_TRIGGER_restar_stock_productos();
  Help::CREATE_TRIGGER_restar_stock_piezas();
  Help::CREATE_TRIGGER_devolver_stocks_temporales();
  Help::CREATE_TRIGGER_devolver_variantes_temporales();
}


// Factura a crédito

if ($_POST['action'] == "factura_credito") {

  $customer_id = $_POST['customer_id'];
  $total = $_POST['total_invoice'];
  $description = $_POST['description'];
  $method = $_POST['payment_method'];
  $user_id = $_SESSION['identity']->usuario_id;
  $pay = $_POST['pay'];
  $pending = $_POST['pending'];
  $date = $_POST['date'];

  $db = Database::connect();

  $query = "CALL vt_facturaAcredito($customer_id,$method,'$total','$pay','$pending',$user_id,'$description','$date')";
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

  $customer_id = $_POST['customer_id'];
  $total = $_POST['total'];
  $observation = $_POST['observation'];
  $user_id = $_SESSION['identity']->usuario_id;
  $date = $_POST['date'];

  $db = Database::connect();

  $query = "CALL ct_cotizacion($customer_id,$user_id,'$total','$observation','$date')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Ha ocurrido un error al registrar: " . $data->msg;
  }
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


if ($_POST['action'] == "agregar_detalle_cotizacion") {

  $quote_id = $_POST['id'];
  $user_id = $_SESSION['identity']->usuario_id;
  $description = $_POST['description'];
  $discount = (!empty($_POST['discount'])) ? $_POST['discount'] : 0;
  $quantity = (!empty($_POST['quantity'])) ? $_POST['quantity'] : 0;
  $taxes = (!empty($_POST['taxes'])) ? $_POST['taxes'] : 0;
  $price = $_POST['price'];

  $db = Database::connect();

  $query = "INSERT INTO detalle_cotizaciones values (null,$quote_id,$user_id,'$description','$quantity','$price','$taxes','$discount',curdate());";
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

  $id = $_POST['id'];

  $query = "SELECT sum(d.cantidad * d.impuesto) as taxes, sum(d.descuento) as descuentos, sum(d.cantidad * d.precio) as precios,
  c.total FROM detalle_cotizaciones d 
  INNER JOIN cotizaciones c ON c.cotizacion_id = d.cotizacion_id
   WHERE c.cotizacion_id = '$id'";

  jsonQueryResult($db, $query);
}
