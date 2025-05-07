<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once '../help.php';
session_start();


// Cargar index de facturas ventas

if ($_POST['action'] == "index_facturas_ventas") {

  // Conexión a la base de datos
  $db = Database::connect();

  // Parámetros de DataTables
  $draw = intval($_POST['draw'] ?? 0);
  $start = intval($_POST['start'] ?? 0);
  $length = intval($_POST['length'] ?? 10);
  $searchValue = $_POST['search']['value'] ?? '';


  // Columnas disponibles para ordenamiento
  $columns = [
    'f.factura_venta_id',
    'c.nombre',
    'c.apellidos',
    'f.fecha',
    'f.total',
    'f.recibido',
    'f.pendiente',
    'f.bono',
    'e.nombre_estado'
  ];


  // Ordenamiento
  $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
  $orderColumn = $columns[$orderColumnIndex] ?? 'f.factura_venta_id';
  $orderDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

  // Filtro de búsqueda
  $searchQuery = "";
  if (!empty($searchValue)) {
    $searchEscaped = $db->real_escape_string($searchValue);
    $searchQuery = " AND (
          c.nombre LIKE '%$searchEscaped%' OR 
          c.apellidos LIKE '%$searchEscaped%' OR 
          e.nombre_estado LIKE '%$searchEscaped%' OR 
          f.factura_venta_id LIKE '%$searchEscaped%' OR 
          f.fecha LIKE '%$searchEscaped%' 
      )";
  }

  // Total de registros sin filtrar
  $resTotal = $db->query("
  SELECT COUNT(*) AS total 
  FROM facturas_ventas f
  INNER JOIN clientes c ON f.cliente_id = c.cliente_id
  INNER JOIN estados_generales e ON f.estado_id = e.estado_id
");
  $totalRecords = $resTotal->fetch_assoc()['total'] ?? 0;


  // Total de registros filtrados

  $resFilter = $db->query("
    SELECT COUNT(*) AS total 
    FROM facturas_ventas f
    INNER JOIN clientes c ON f.cliente_id = c.cliente_id
    INNER JOIN estados_generales e ON f.estado_id = e.estado_id
    WHERE 1 $searchQuery
");
  $filteredRecords = $resFilter->fetch_assoc()['total'] ?? 0;


  // Datos paginados y filtrados
  $sql = "SELECT f.factura_venta_id, c.nombre, c.apellidos, f.total, f.recibido, 
             f.pendiente, f.bono, e.nombre_estado, f.fecha as fecha_factura 
      FROM facturas_ventas f
      INNER JOIN clientes c ON f.cliente_id = c.cliente_id
      INNER JOIN estados_generales e ON f.estado_id = e.estado_id
      WHERE 1 $searchQuery
      ORDER BY $orderColumn $orderDir
      LIMIT $start, $length";

  $result = $db->query($sql);

  // Crear arreglo de datos con formato HTML para cada celda

  $data = [];
  while ($row = $result->fetch_assoc()) {

    $acciones = '<a ';
    if ($_SESSION['identity']->nombre_rol == 'administrador') {
      $acciones .= 'class="action-edit" href="' . base_url . 'invoices/edit&id=' . $row['factura_venta_id'] . '"';
    } else {
      $acciones .= 'class="action-edit action-disable" href="#"';
    }
    $acciones .= ' title="editar"><i class="fas fa-pencil-alt"></i></a>';

    $acciones .= '<span onclick="deleteInvoice(\'' . $row['factura_venta_id'] . '\')" class="action-delete"><i class="fas fa-times"></i></span>';


    $data[] = [
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


  // Respuesta JSON
  echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
  ]);

  exit;
}


// Agregar al Detalle Temporal

if ($_POST['action'] == "agregar_detalle_temporal") {

  $user_id = $_SESSION['identity']->usuario_id;
  $product_id = (!empty($_POST['product_id'])) ? $_POST['product_id'] : 0;
  $piece_id = (!empty($_POST['piece_id'])) ? $_POST['piece_id'] : 0;
  $service_id = (!empty($_POST['service_id'])) ? $_POST['service_id'] : 0;
  $description = $_POST['description'];
  $quantity = $_POST['quantity'];
  $discount = (!empty($_POST['discount'])) ? $_POST['discount'] : 0;
  $taxes = (!empty($_POST['taxes'])) ? $_POST['taxes'] : 0;
  $price = $_POST['price'];

  $db = Database::connect();

  $query = "CALL vt_crearDetalleTemporal($product_id,$piece_id,$service_id,'$description',$user_id,$quantity,'$price','$taxes','$discount')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg; // Devuelve el id del detalle
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
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

  $db = Database::connect();

  $query = "INSERT INTO detalle_facturas_ventas values (null,$invoice_id,$user_id,$quantity,$price,$taxes,$discount,curdate())";

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

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

// Obtener precios del detalle de factura

if ($_POST['action'] == "precios_detalle_venta") {

  $db = Database::connect();

  $invoice_id = $_POST['id'];

  $query = "SELECT sum(d.cantidad * d.impuesto) as taxes, sum(d.descuento) as descuentos, sum(d.cantidad * precio) as precios,
  f.total, f.pendiente, f.recibido
  FROM detalle_facturas_ventas d INNER JOIN facturas_ventas f on f.factura_venta_id = d.factura_venta_id WHERE d.factura_venta_id = '$invoice_id'";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

// Eliminar producto del detalle temporar

if ($_POST['action'] == 'eliminar_detalle_temporal') {

  $id = $_POST['id'];
  $db = Database::connect();

  $query = "CALL vt_eliminarDetalleTemporal($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Eliminar producto del detalle venta

if ($_POST['action'] == 'eliminar_detalle_venta') {

  $id = $_POST['id'];
  $db = Database::connect();

  $query = "CALL vt_eliminarDetalleVenta($id)";
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

  $customer_id = $_POST['customer_id'];
  $total = $_POST['total_invoice'];
  $description = $_POST['description'];
  $method = $_POST['payment_method'];
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

  $query1 = "SELECT d.detalle_temporal_id, d.usuario_id, d.producto_id, d.servicio_id,
  d.pieza_id, d.descripcion, d.cantidad,d.precio,d.impuesto,d.descuento FROM detalle_temporal d 
  WHERE d.usuario_id = '$user_id';";

  $datos = $db->query($query1);


  // Desactivar TRIGGER
  $drop1 = "DROP TRIGGER IF EXISTS restar_stock_productos";
  $drop2 = "DROP TRIGGER IF EXISTS restar_stock_piezas";
  $drop3 = "DROP TRIGGER IF EXISTS devolver_variantes_temporales";
  $drop4 = "DROP TRIGGER IF EXISTS devolver_stocks_temporales";
  $db->query($drop1);
  $db->query($drop2);
  $db->query($drop3);
  $db->query($drop4);

  while ($element = $datos->fetch_object()) {

    $product_id = $element->producto_id;
    $piece_id = $element->pieza_id;
    $service_id = $element->servicio_id;
    $price = $element->precio;
    $discount = $element->descuento;
    $quantity = $element->cantidad;
    $taxes = $element->impuesto;
    $detail_temp_id = $element->detalle_temporal_id;

    $query2 = "INSERT INTO detalle_facturas_ventas values (null,$invoice_id,$user_id,$quantity,$price,$taxes,$discount,curdate())";
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

  $query = "CALL ct_eliminarCotizacion($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Ha ocurrido un error: " . $data->msg;
  }
}

// Eliminar detalle cotizacion

if ($_POST['action'] == 'eliminar_detalle_cotizacion') {

  $id = $_POST['id'];
  $db = Database::connect();

  $query = "CALL ct_eliminarDetalle($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Ha ocurrido un error: " . $data->msg;
  }
}

// Obtener total de la factura de cotizacion

if ($_POST['action'] == "total_cotizacion") {

  $db = Database::connect();

  $id = $_POST['id'];

  $query = "SELECT sum(d.cantidad * d.impuesto) as taxes, sum(d.descuento) as descuentos, sum(d.cantidad * d.precio) as precios,
  c.total FROM detalle_cotizaciones d 
  INNER JOIN cotizaciones c ON c.cotizacion_id = d.cotizacion_id
   WHERE c.cotizacion_id = '$id'";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
