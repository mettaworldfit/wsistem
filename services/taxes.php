<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();


if ($_POST['action'] == "index_impuestos") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => ['impuesto_id', 'nombre_impuesto', 'valor', 'descripcion', 'fecha'],
    'searchable' => ['impuesto_id', 'nombre_impuesto', 'valor', 'fecha'],
    'base_table' => 'impuestos',
    'table_with_joins' => 'impuestos',
    'select' => 'SELECT impuesto_id,nombre_impuesto,valor,descripcion,fecha',
    'table_rows' => function ($row) {
      return [
        'id'   => $row['impuesto_id'],
        'nombre'        => ucwords($row['nombre_impuesto']),
        'valor'         => $row['valor'] . '%',
        'descripcion'   => $row['descripcion'],
        'fecha'         => $row['fecha'],
        'acciones'      => '
      <a class="btn-action action-info" href="' . base_url . 'taxes/edit&id=' . $row['impuesto_id'] . '" title="Editar">
        '.BUTTON_EDIT.'
      </a>

      <span class="btn-action action-danger" onclick="deleteTax(\'' . $row['impuesto_id'] . '\')" title="Eliminar">
        '.BUTTON_DELETE.'
      </span>'
      ];
    }
  ]);
}

/**
 * Buscar Impuesto
 -------------------------------*/

if ($_POST['action'] == 'buscarImpuesto') {

  $tax_name = $_POST['tax'];

  $db = Database::connect();

  $query = "SELECT *FROM impuestos WHERE nombre_impuesto = '$tax_name'";
  $data = $db->query($query);

  $element = $data->fetch_assoc();
  echo json_encode($element, JSON_UNESCAPED_UNICODE);
}



if ($_POST['action'] == "agregar_impuesto") {

  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $value = $_POST['value'];
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL im_agregarImpuesto($user_id,'$name',$value,'$comment')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 80: " . $data->msg;
  }
}


if ($_POST['action'] == "actualizar_impuesto") {

  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $value = $_POST['value'];
  $tax_id = $_POST['tax_id'];

  $db = Database::connect();

  $query = "CALL im_actualizarImpuesto($tax_id,'$name',$value,'$comment')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 81: " . $data->msg;
  }
}


//  Eliminar Impuesto

if ($_POST['action'] == "eliminar_impuesto") {

  $id = $_POST['tax_id'];

  $db = Database::connect();

  $query = "CALL im_eliminarImpuesto($id)";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error 82: " . $db->error;
  }
}
