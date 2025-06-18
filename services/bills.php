<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
require_once '../help.php';
session_start();

if ($_POST['action'] == "index_gastos") {

  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => [
      'g.fecha',
      'g.gasto_id',
      'p.nombre_proveedor',
      'p.apellidos',
      'g.total',
      'g.pagado',
      'g.orden_id'
    ],
    'searchable' => [
      'p.nombre_proveedor',
      'p.apellidos',
      'g.total',
      'g.pagado',
      'g.fecha'
    ],
    'base_table' => 'gastos',
    'table_with_joins' => 'gastos g 
          INNER JOIN proveedores p ON p.proveedor_id = g.proveedor_id 
          INNER JOIN usuarios u ON u.usuario_id = g.usuario_id',
    'select' => 'SELECT g.gasto_id, p.nombre_proveedor, g.total, g.pagado, g.orden_id, g.fecha, p.apellidos',
    'table_rows' => function ($element) {
      return [
        'id'    => 'G-00' . $element['gasto_id'],
        'proveedor' => '<span class="hide-cell">' . ucwords($element['nombre_proveedor'] . ' ' . $element['apellidos']) . '</span>',
        'gastos'    => Help::loadSpendingsById($element['orden_id']),
        'fecha'     => $element['fecha'],
        'total'     => '<span class="text-primary">' . number_format($element['total'], 2) . '</span>',
        'pagado'    => '<span class="text-success hide-cell">' . number_format($element['pagado'], 2) . '</span>',
        'acciones'  => '<span class="action-delete" style="font-size:16px;" onclick="deleteSpending(\''
          . $element['orden_id'] . '\')"><i class="fas fa-times"></i></span>'
      ];
    }

  ]);
}


if ($_POST['action'] == "index_facturas_proveedores") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => [
      'f.factura_proveedor_id',
      'f.fecha',
      'f.total',
      'f.pagado',
      'f.por_pagar',
      'p.nombre_proveedor',
      'p.apellidos',
      'e.nombre_estado',
      'f.orden_id'
    ],
    'searchable' => [
      'f.factura_proveedor_id',
      'p.nombre_proveedor',
      'p.apellidos',
      'f.fecha',
      'e.nombre_estado'
    ],
    'base_table' => 'facturas_proveedores',
    'table_with_joins' => 'facturas_proveedores f
        INNER JOIN estados_generales e ON e.estado_id = f.estado_id
        INNER JOIN proveedores p ON p.proveedor_id = f.proveedor_id',
    'select' => 'SELECT f.factura_proveedor_id, f.fecha, f.total, f.pagado, f.por_pagar, p.apellidos, p.nombre_proveedor, e.nombre_estado, f.orden_id',
    'table_rows' => function ($row) {
      return [
        'id'     =>  '<span class="hide-cell">' . 'FP-00' . $row['factura_proveedor_id'] . '</span>',
        'proveedor'  => ucwords($row['nombre_proveedor'] . ' ' . $row['apellidos']),
        'fecha'      => $row['fecha'],
        'total'      => '<span class="text-primary">' . number_format($row['total'], 2) . '</span>',
        'pagado'     => '<span class="hide-cell text-success">' . number_format($row['pagado'], 2) . '</span>',
        'por_pagar'  => '<span class="text-danger">' . number_format($row['por_pagar'], 2) . '</span>',
        'estado'     => '<p class="hide-cell ' . $row['nombre_estado'] . '">' . $row['nombre_estado'] . '</p>',
        'acciones'   => '<span style="font-size: 16px;" onclick="deleteInvoiceFP(\''
          . $row['factura_proveedor_id'] . '\', \'' . $row['orden_id'] . '\')" class="action-delete">
                            <i class="fas fa-times"></i>
                        </span>'
      ];
    }

  ]);
}

if ($_POST['action'] == "index_ordenes_compras") {

  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => [
      'o.orden_id',
      'p.nombre_proveedor',
      'o.fecha',
      'o.expiracion',
      'e.nombre_estado'
    ],
    'searchable' => [
      'p.nombre_proveedor',
      'o.fecha',
      'o.expiracion',
      'e.nombre_estado'
    ],
    'base_table' => 'ordenes_compras',
    'table_with_joins' => 'ordenes_compras o 
        INNER JOIN estados_generales e ON e.estado_id = o.estado_id
        INNER JOIN proveedores p ON p.proveedor_id = o.proveedor_id',
    'select' => 'SELECT o.orden_id,p.nombre_proveedor,o.fecha,o.expiracion,e.nombre_estado',
    'table_rows' => function ($element) {
      $orden_id = $element['orden_id'];
      $estado = $element['nombre_estado'];
      $disabled = $estado === 'Facturado' ? 'action-disable' : '';
      $edit_href = $estado !== 'Facturado' ? base_url . 'expenses/edit_order&id=' . $orden_id : '#';

      return [
        'orden_id'     => 'OC-00' . $orden_id,
        'proveedor'  => ucwords($element['nombre_proveedor']),
        'articulos'  => '<span class="hide-cell">' . Help::loadListOrdersById($orden_id) . '</span>',
        'fecha'      => '<span class="text-success">' . $element['fecha'] . '</span>',
        'expiracion' => '<span class="hide-cell text-danger">' . $element['expiracion'] . '</span>',
        'estado'     => '<input type="text" class="hide-cell form-custom ' . ($estado == 'Pendiente' ? 'Pendiente' : 'Listo') . '" value="' . $estado . '" disabled>',
        'acciones'   => '<a class="action-edit ' . $disabled . '" href="' . $edit_href . '">
                            <i class="fas fa-pencil-alt"></i>
                         </a>
                         <span style="font-size:16px;" ' .
          ($estado != 'Facturado' ? 'onclick="deleteOrderC(\'' . $orden_id . '\')"' : '') .
          ' class="action-delete ' . $disabled . '"><i class="fas fa-times"></i></span>'
      ];
    }

  ]);
}

// Calcular detalle de orden de compra

if ($_POST['action'] == "total_orden_compra") {

  $order_id = $_POST['id'];

  $db = Database::connect();

  $query = "SELECT sum(d.cantidad * d.precio) as precio, sum(d.impuestos) as impuestos, sum(d.descuentos) as descuentos FROM ordenes_compras o 
            INNER JOIN detalle_compra d ON o.orden_id = d.orden_id WHERE o.orden_id = '$order_id'";
  $result = $db->query($query);
  $data = $result->fetch_object();

  echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

// Crear orden de compra

if ($_POST['action'] == "crear_orden_compra") {

  $fecha_actual = date("Y-m-d");

  $user_id = $_SESSION['identity']->usuario_id;
  $status_id = 6;
  $date = $_POST['date'];
  $expiration = (!empty($_POST['expiration'])) ? $_POST['expiration'] : date("Y-m-d", strtotime($fecha_actual . "+ 1 month"));
  $provider = $_POST['provider'];
  $observation = $_POST['observation'];

  $db = Database::connect();

  $query = "CALL or_ordenCompra($user_id,$status_id,$provider,'$date','$expiration','$observation')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}


// Crear detalle orden de compra

if ($_POST['action'] == "agregar_detalle_orden_de_compra") {

  $user_id = $_SESSION['identity']->usuario_id;
  $order_id = $_POST['order_id'];
  $product_id = $_POST['product_id'];
  $piece_id = $_POST['piece_id'];
  $quantity = $_POST['quantity'];
  $taxes = $_POST['taxes'];
  $discount = (!empty($_POST['discount'])) ? $_POST['discount'] : 0;
  $price = $_POST['price'];
  $observation = $_POST['observation'];

  $db = Database::connect();

  $query = "CALL or_detalleCompra($user_id,$product_id,$piece_id,$order_id,$price,$quantity,'$discount',$taxes,'$observation')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo $data->msg;
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}


// Eliminar orden

if ($_POST['action'] == "eliminar_orden") {

  $id = $_POST['id'];

  $db = Database::connect();

  $query = "CALL or_eliminarOrdenComp($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Obtener detalle de compra

if ($_POST['action'] == "obtener_detalle") {

  $id = $_POST['id'];
  $db = Database::connect();

  $query = "SELECT pr.nombre_producto,p.nombre_pieza,d.impuestos,d.descuentos, d.cantidad as cant, 
  d.detalle_compra_id as id,d.precio,d.observacion FROM detalle_compra d
    LEFT JOIN detalle_compra_con_piezas dp ON dp.detalle_compra_id = d.detalle_compra_id
    LEFT JOIN piezas p ON p.pieza_id = dp.pieza_id
    LEFT JOIN detalle_compra_con_productos dpr ON dpr.detalle_compra_id = d.detalle_compra_id
    LEFT JOIN productos pr ON pr.producto_id = dpr.producto_id
    WHERE d.orden_id = '$id'";

  $datos = $db->query($query);
  echo json_encode($datos->fetch_all(), JSON_UNESCAPED_UNICODE);
}


// Agregar orden de compra a la factura

if ($_POST['action'] == "agregar_orden_a_factura") {

  $id = $_POST['id'];

  $db = Database::connect();

  $query = "SELECT pr.nombre_producto,p.nombre_pieza,d.impuestos,d.descuentos, d.cantidad as cant, 
  d.detalle_compra_id as id,d.precio,d.observacion FROM detalle_compra d
    LEFT JOIN detalle_compra_con_piezas dp ON dp.detalle_compra_id = d.detalle_compra_id
    LEFT JOIN piezas p ON p.pieza_id = dp.pieza_id
    LEFT JOIN detalle_compra_con_productos dpr ON dpr.detalle_compra_id = d.detalle_compra_id
    LEFT JOIN productos pr ON pr.producto_id = dpr.producto_id
    WHERE d.orden_id = '$id'";

  $datos = $db->query($query);
  $html = '';

  // Cuerpo 
  while ($element = $datos->fetch_object()) {
    $total = $element->cant * $element->precio;
    $importe = $total + $element->impuestos - $element->descuentos;

    $item = $element->nombre_producto ?: ($element->nombre_pieza ?: '');

    $html .= '
      <tr>
        <td>' . htmlspecialchars($item) . '</td>
        <td>' . $element->cant . '</td>
        <td>' . number_format($element->precio, 2) . '</td>
        <td class="hide-cell">' . number_format($element->impuestos, 2) . '</td>
        <td>' . number_format($element->descuentos, 2) . '</td>
        <td class="note-width hide-cell">' . htmlspecialchars($element->observacion) . '</td>
        <td>' . number_format($importe, 2) . '</td>
        <td><span style="font-size: 16px;" onclick="deleteDetailOrderC(' . $element->id . ')" class="action-delete"><i class="fas fa-times"></i></span></td>
      </tr>
    ';
  }

  echo $html;
}

// Calcular el total de la factura

if ($_POST['action'] == "calcular_factura") {

  $id = $_POST['id'];
  $db = Database::connect();

  $query = "SELECT sum(d.impuestos) as impuestos, sum(d.descuentos) as descuentos, 
    sum(d.cantidad * d.precio) as subtotal, p.nombre_proveedor as proveedor, p.proveedor_id as proveedor_id, o.expiracion as expiracion, o.fecha as fecha 
    FROM detalle_compra d
    INNER JOIN ordenes_compras o ON o.orden_id = d.orden_id 
    INNER JOIN proveedores p ON p.proveedor_id = o.proveedor_id
    WHERE d.orden_id = '$id'";

  jsonQueryResult($db, $query);
}


// Eliminar ítem del detalle de orden de compra desde la factura de proveedores

if ($_POST['action'] == "eliminar_detalle") {

  $id = $_POST['id'];

  $db = Database::connect();

  $query = "CALL or_eliminarItemDetalleCompra($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Eliminar ítem de la orden de compra

if ($_POST['action'] == "eliminar_item_de_la_orden") {

  $id = $_POST['detail_id'];

  $db = Database::connect();

  $query = "CALL or_eliminarItemDetalleCompra($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}


// Factura al contado

if ($_POST['action'] == "factura_contado") {

  $provider_id = $_POST['provider_id'];
  $total = $_POST['total_invoice'];
  $orden_id = $_POST['orden_id'];
  $observation = $_POST['observation'];
  $method = $_POST['payment_method'];
  $user_id = $_SESSION['identity']->usuario_id;
  $date = $_POST['date'];

  $db = Database::connect();

  $query = "CALL or_facturaCompra($provider_id,$orden_id,$method,'$total',$user_id,'$date','$observation')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}


// Factura a crédito

if ($_POST['action'] == "factura_credito") {

  $provider_id = $_POST['provider_id'];
  $total = $_POST['total_invoice'];
  $orden_id = $_POST['orden_id'];
  $method = $_POST['payment_method'];
  $user_id = $_SESSION['identity']->usuario_id;
  $observation = $_POST['observation'];
  $pay = (!empty($_POST['pay'])) ? $_POST['pay'] : 0;
  $pending = $_POST['pending'];
  $date = $_POST['date'];

  $db = Database::connect();

  $query = "CALL or_facturaAcredito($provider_id,$orden_id,$method,'$total','$pay','$pending',$user_id,'$date','$observation')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}


// Eliminar factura proveedores

if ($_POST['action'] == "eliminar_factura_proveedor") {

  $id = $_POST['id'];
  $order_id = $_POST['order_id'];

  $db = Database::connect();

  $query = "CALL or_eliminarFactura($id,$order_id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Crear orden de gasto

if ($_POST['action'] == "crear_orden_gasto") {

  $db = Database::connect();

  $params = [
    (int)$_POST['provider_id'],
    (int)$_SESSION['identity']->usuario_id,
    $_POST['origin'],
    $_POST['date']
  ];

  echo handleProcedureAction($db, 'or_ordenGasto', $params);
}

// Agregar detalle gastos

if ($_POST['action'] == "detalle_gasto") {

  $reason_id = $_POST['reason_id'];
  $value = $_POST['value'];
  $taxes = $_POST['taxes'];
  $quantity = $_POST['quantity'];
  $order_id = $_POST['order_id'];
  $observation = $_POST['observation'];
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL or_detalleGasto($reason_id,$order_id,$quantity,$value,'$taxes',$user_id,'$observation')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}


// Registrar gasto

if ($_POST['action'] == "registrar_gasto") {

  $provider_id = $_POST['provider_id'];
  $total = $_POST['total_invoice'];
  $order_id = $_POST['order_id'];
  $observation = $_POST['observation'];
  $user_id = $_SESSION['identity']->usuario_id;
  $date = $_POST['date'];

  $db = Database::connect();

  $query = "CALL or_registrarGasto($provider_id,$order_id,'$total',$user_id,'$observation','$date')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Eliminar gasto

if ($_POST['action'] == "eliminar_gasto") {

  $id = $_POST['id'];

  $db = Database::connect();

  $query = "CALL or_eliminarGasto($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Actualizar orden de compra

if ($_POST['action'] == "actualizar_orden") {

  $user_id = $_SESSION['identity']->usuario_id;
  $order_id = $_POST['order_id'];
  $provider_id = $_POST['provider_id'];
  $observation = $_POST['observation'];
  $date = $_POST['date'];
  $expiration = $_POST['expiration'];

  $db = Database::connect();

  $query = "CALL or_actualizarOrdenCompra($order_id,$provider_id,'$observation','$date','$expiration')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

/**
 * TODO: Actualizar factura de proveedor
 */

if ($_POST['action'] == "actualizar_factura") {

  $invoice_id = $_POST['invoice_id'];
  $provider_id = $_POST['provider_id'];
  $observation = $_POST['observation'];
  $date = $_POST['date'];

  $db = Database::connect();

  $query = "CALL or_actualizarFacturaCompra($invoice_id,$provider_id,'$observation','$date')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Agregar nuevo motivo

if ($_POST['action'] == "agregar_motivo") {
  $db = Database::connect();

  $params = [
    (int)$_SESSION['identity']->usuario_id,
    $_POST['description']
  ];

  echo handleProcedureAction($db, 'g_agregar_motivo', $params);
}
