<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

if ($_POST['action'] == "index_posiciones") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => ['posicion_id', 'referencia', 'fecha'],
    'searchable' => ['posicion_id', 'referencia', 'fecha'],
    'base_table' => 'posiciones',
    'table_with_joins' => 'posiciones',
    'select' => 'SELECT posicion_id,referencia,fecha',
    'table_rows' => function ($row) {
      return [
        'id' => $row['posicion_id'],
        'referencia'  => $row['referencia'],
        'fecha'       => $row['fecha'],
        'acciones'    => '
      <a class="btn-action action-info" href="' . base_url . 'positions/edit&id=' . $row['posicion_id'] . '" title="Editar">
        '.BUTTON_EDIT.'
      </a>

      <span class="btn-action action-danger" onclick="deletePosition(\'' . $row['posicion_id'] . '\')" title="Eliminar">
        '.BUTTON_DELETE.'
      </span>'
      ];
    }
  ]);
}


if ($_POST['action'] == "agregar_posicion") {

  $reference = $_POST['reference'];
  $comment = $_POST['comment'];
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL pos_agregarPosicion($user_id,'$reference','$comment')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 40: " . $data->msg;
  }
}

// Actualizar Posición

if ($_POST['action'] == "actualizar_posicion") {

  $reference = $_POST['reference'];
  $comment = $_POST['comment'];
  $position_id = $_POST['position_id'];

  $db = Database::connect();

  $query = "CALL pos_actualizarPosicion($position_id,'$reference','$comment')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 41: " . $data->msg;
  }
}

//  Eliminar Posición

if ($_POST['action'] == "eliminar_posicion") {

  $id = $_POST['position_id'];

  $db = Database::connect();

  $query = "CALL pos_eliminarPosicion($id)";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error 42: " . $db->error;
  }
}
