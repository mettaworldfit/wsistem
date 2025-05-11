<?php

require_once '../config/db.php';
require_once '../help.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

// Cargar index del taller

if ($_POST['action'] == "index_taller") {

  // Conexión a la base de datos
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => [
      'o.orden_rp_id','f.facturaRP_id','c.nombre','c.apellidos','o.fecha_entrada','o.fecha_salida',
      'e.nombre_modelo','m.nombre_marca','e.modelo','o.imei','o.serie','es.nombre_estado'
    ],
    'searchable' => [
      'c.nombre','c.apellidos','o.orden_r','o.imei','o.serie','o.fecha_entrada',
      'o.fecha_salida','e.nombre_modelo', 'm.nombre_marca','es.nombre_estado'
    ],
    "base_table" => "ordenes_rp",
    "table_with_joins" => "ordenes_rp o INNER JOIN clientes c ON c.cliente_id = o.cliente_id
    INNER JOIN estados_generales es ON es.estado_id = o.estado_id
    INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
    INNER JOIN equipos e ON e.equipo_id = o.equipo_id
    INNER JOIN marcas m ON m.marca_id = e.marca_id
    LEFT JOIN facturasRP f ON o.orden_rp_id = f.orden_rp_id",

    "select" => "SELECT o.orden_rp_id as id, f.facturaRP_id, c.cliente_id, c.nombre as nombre_cliente, 
    c.apellidos as apellidos_cliente, o.fecha_entrada, o.fecha_salida, e.nombre_modelo,
    m.nombre_marca, e.modelo, o.imei, o.serie, es.nombre_estado, es.estado_id",

    "table_rows" => function ($row) {
      return [
        'orden' =>
        '<span>
          <a href="#" class="' . ($row['facturaRP_id'] > 0 ? 'text-secondary' : 'text-danger') . '">OR-00' . $row['id'] . '</a>
          <span id="toggle" class="toggle-right toggle-md">
              No. Orden: OR-00' . $row['id'] . '<br>
              No. Factura: ' .
          ($row['facturaRP_id'] > 0
            ? '<a class="text-danger" href="' . base_url . 'invoices/repair_edit&o=' . $row['id'] . '&f=' . $row['facturaRP_id'] . '">RP-00' . $row['facturaRP_id'] . '</a>'
            : '<a class="text-danger" href="#">No facturado</a>'
          ) .
          '</span>
      </span>',
        'nombre' => ucwords($row['nombre_cliente'] . ' ' . $row['apellidos_cliente']),
        'equipo' => '<span>' .
          '<a href="#" class="text-secondary">' . ucwords($row['nombre_marca'] . ' ' . $row['nombre_modelo'] . ' ' . $row['modelo']) . '</a>' .
          '<span id="toggle" class="toggle-right toggle-xl">' .
          'Marca: ' . $row['nombre_marca'] . '<br>' .
          'Modelo: ' . $row['modelo'] . '<br>' .
          'IMEI: ' . $row['imei'] . '<br>' .
          'Serie: ' . $row['serie'] . '<br>' .
          '</span>' .
          '</span>',
        'fecha_entrada' => '<span class="text-success hide-cell">' . $row['fecha_entrada'] . '</span>',
        'fecha_salida' => '<span class="text-danger hide-cell">' . $row['fecha_salida'] . '</span>',
        'condicion' => Help::SHOW_CONDITONS_ORDER($row["id"]),
        'estado' => '
            <select class="form-custom ' . $row['nombre_estado'] . '" name="" id="status_rp" onchange="elegirEstado(this);">
                <option workshop_id="' . $row['id'] . '" value="' . $row['estado_id'] . '" selected>' . $row['nombre_estado'] . '</option>
                <option class="Pendiente" workshop_id="' . $row['id'] . '" value="6">Pendiente</option>
                <option class="En Proceso" workshop_id="' . $row['id'] . '" value="8">En Proceso</option>
                <option class="Entregado" workshop_id="' . $row['id'] . '" value="7">Entregado</option>
                <option class="No se pudo" workshop_id="' . $row['id'] . '" value="10">No se pudo</option>
                <option class="Listo" workshop_id="' . $row['id'] . '" value="9">Listo</option>
            </select>',

        'acciones' =>
        '<a href="' . base_url . 'invoices/addrepair&id=' . $row['id'] . '" title="Agregar factura" class="action-edit">
          <i class="fas fa-shopping-cart"></i>
      </a>
      <span class="action-delete" onclick="deleteOrden(\'' . $row['id'] . '\')" title="Eliminar">
          <i class="fas fa-times"></i>
      </span>'

      ];
    }
  ]);
}



// Buscar equipo

if ($_POST['action'] == "buscar_equipo") {

  $id = $_POST['device_id'];

  $db = Database::connect();

  $query = "SELECT d.modelo, m.nombre_marca FROM equipos d
  INNER JOIN marcas m on d.marca_id = m.marca_id WHERE equipo_id = '$id'";
  $datos = $db->query($query);

  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;
}

// Agregar orden de reparación

if ($_POST['action'] == "agregar_orden_reparacion") {

  $customer_id = $_POST['customer_id'];
  $device = $_POST['device'];
  $serie = $_POST['serie'];
  $imei = (!empty($_POST['imei'])) ? $_POST['imei'] : 0;
  $user_id = $_SESSION['identity']->usuario_id;
  $observation = $_POST['observation'];

  $db = Database::connect();

  $query = "CALL rp_crearOrdenRP($user_id,$customer_id,'$device','$serie','$imei','$observation')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Asignar condiciones

if ($_POST['action'] == "asignar_condiciones") {

  $condition_id = $_POST['condition_id'];
  $orden_id = $_POST['orden_id'];

  $db = Database::connect();

  $query = "CALL rp_agregarCondiciones($condition_id,$orden_id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Crear condicion

if ($_POST['action'] == "crear_condicion") {

  $condition = $_POST['condition'];
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL rp_crearCondicion($user_id,'$condition')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Crear equipo

if ($_POST['action'] == "crear_equipo") {

  $brand = $_POST['brand'];
  $device = $_POST['device'];
  $model = $_POST['model'];

  $db = Database::connect();

  $query = "CALL rp_crearEquipo('$brand','$device','$model')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// actualizar estado orden

if ($_POST['action'] == "actualizar_estado_orden") {

  $status = $_POST['status'];
  $workshop_id = $_POST['workshop_id'];

  $db = Database::connect();

  $query = "CALL rp_actualizarEstadoOrden($status,$workshop_id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Eliminar orden

if ($_POST['action'] == "eliminar_orden") {

  $id = $_POST['id'];

  $db = Database::connect();

  $query = "CALL rp_eliminarOrdenRP($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Eliminar marca

if ($_POST['action'] == "eliminar_marca") {

  $id = $_POST['id'];

  $db = Database::connect();

  $query = "CALL m_eliminarMarca($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

if ($_POST['action'] == "crear_marca") {

  $brand = $_POST['name'];

  $db = Database::connect();

  $query = "CALL m_crearMarca('$brand')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}

// Actualizar marca

if ($_POST['action'] == "actualizar_marca") {

  $name = $_POST['name'];
  $id = $_POST['id'];

  $db = Database::connect();

  $query = "CALL m_actualizarMarca('$name',$id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 54:" . $data->msg;
  }
}
