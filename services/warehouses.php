<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();


if ($_POST['action'] == "index_almacen") {
   $db = Database::connect();
  handleDataTableRequest($db, [
    'columns' => ['almacen_id', 'descripcion', 'nombre_almacen', 'fecha'],
    'searchable' => ['descripcion', 'nombre_almacen'],
    'base_table' => 'almacenes',
    'table_with_joins' => 'almacenes',
    'select' => 'SELECT almacen_id, descripcion, nombre_almacen, fecha',
    'table_rows' => function ($row) {
      return [
        'id'     => $row['almacen_id'],
        'nombre_almacen' => ucwords($row['nombre_almacen']),
        'descripcion'    => $row['descripcion'],
        'fecha'          => $row['fecha'],
        'acciones'       => '
      <a class="action-edit" href="' . base_url . 'warehouses/edit&id=' . $row['almacen_id'] . '" title="Editar">
          <i class="fas fa-pencil-alt"></i>
      </a>

      <span class="action-delete" onclick="deleteWarehouse(\'' . $row['almacen_id'] . '\')" title="Eliminar">
          <i class="fas fa-times"></i>
      </span>
    '
      ];
    }
  ]);
}



if ($_POST['action'] == "agregar_almacen") {

  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL al_agregarAlmacen($user_id,'$name','$comment')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 30: " . $data->msg;
  }
}

// Actualizar Almacen

if ($_POST['action'] == "actualizar_almacen") {

  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $warehouse_id = $_POST['warehouse_id'];

  $db = Database::connect();

  $query = "CALL al_actualizarAlmacen($warehouse_id,'$name','$comment')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 31: " . $data->msg;
  }
}

//  Eliminar Posición

if ($_POST['action'] == "eliminar_almacen") {

  $id = $_POST['warehouse_id'];

  $db = Database::connect();

  $query = "CALL al_eliminarAlmacen($id)";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error 32: " . $db->error;
  }
}
