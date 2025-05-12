<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

if ($_POST['action'] == "index_servicios") {

  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => ['nombre_servicio', 'precio', 'servicio_id'],
    'searchable' => ['nombre_servicio', 'precio'],
    'base_table' => 'servicios',
    'table_with_joins' => 'servicios',
    'select' => 'SELECT servicio_id,nombre_servicio,precio',
    'table_rows' => function ($row) {
      return [
        'servicio_id' => '<td>' . $row['servicio_id'] . '</td>',
        'nombre_servicio' => '<td>' . htmlspecialchars($row['nombre_servicio']) . '</td>',
        'precio' => '<td>' . number_format($row['precio'] ?? 0, 2) . '</td>',
        'acciones' => '<td>' .
          '<a href="' . base_url . 'services/edit&id=' . $row['servicio_id'] . '">' .
          '<span class="action-edit"><i class="fas fa-pencil-alt"></i></span>' .
          '</a>' .
          '<span class="action-delete" onclick="deleteService(\'' . $row['servicio_id'] . '\')" title="Eliminar">' .
          '<i class="fas fa-times"></i>' .
          '</span>' .
          '</td>'
      ];
    }
  ]);
}


// Buscar servicio por nombre

if ($_POST['action'] == "buscar_servicios") {

  $q = $_POST['service_id'];
  $db = Database::connect();

  $query = "SELECT * FROM servicios WHERE servicio_id = '$q'";

  $datos = $db->query($query);
  $result = $datos->fetch_object();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;
}

if ($_POST['action'] == "agregar_servicio") {

  $name = $_POST['name'];
  $price = (!empty($_POST['price'])) ? $_POST['price'] : 0;
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL sv_agregarServicio($user_id,'$name',$price)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error :" . $data->msg;
  }
}


if ($_POST['action'] == "actualizar_servicio") {

  $name = $_POST['name'];
  $price = (!empty($_POST['price'])) ? $_POST['price'] : 0;
  $service_id = $_POST['service_id'];

  $db = Database::connect();

  $query = "CALL sv_actualizarServicio($service_id,'$name',$price)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error :" . $data->msg;
  }
}


//  Eliminar servicio

if ($_POST['action'] == "eliminar_servicio") {

  $id = $_POST['service_id'];

  $db = Database::connect();

  $query = "CALL sv_eliminarServicio($id)";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error : " . $db->error;
  }
}
