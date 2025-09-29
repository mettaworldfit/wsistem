<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

if ($_POST['action'] == "index_ofertas") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => ['oferta_id', 'nombre_oferta', 'valor', 'descripcion', 'fecha'],
    'searchable' => ['oferta_id', 'nombre_oferta', 'valor', 'fecha'],
    'base_table' => 'ofertas',
    'table_with_joins' => 'ofertas',
    'select' => 'SELECT oferta_id,nombre_oferta,valor,descripcion,fecha',
    'table_rows' => function ($row) {
      return [
        'id'    => $row['oferta_id'],
        'nombre'       => ucwords($row['nombre_oferta']),
        'valor'        => $row['valor'] . '%',
        'descripcion'  => $row['descripcion'],
        'fecha'        => $row['fecha'],
        'acciones'     => '
          <a ' . ($_SESSION['identity']->nombre_rol == 'administrador'
          ? 'class="btn-action action-info" href="' . base_url . 'offers/edit&id=' . $row['oferta_id'] . '"'
          : 'class="btn-action action-info action-disable" href="#"') . ' title="Editar">
            '.BUTTON_EDIT.'
          </a>

          <span class="btn-action action-danger" onclick="deleteOffer(\'' . $row['oferta_id'] . '\')" title="Eliminar">
            '.BUTTON_DELETE.'
          </span>'
      ];
    }
  ]);
}


if ($_POST['action'] == "agregar_oferta") {

  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $value = $_POST['value'];
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL of_agregarOferta($user_id,'$name',$value,'$comment')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 60: " . $data->msg;
  }
}

// Actualizar Oferta

if ($_POST['action'] == "actualizar_oferta") {

  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $value = $_POST['value'];
  $offer_id = $_POST['offer_id'];

  $db = Database::connect();

  $query = "CALL of_actualizarOferta($offer_id,'$name',$value,'$comment')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 61: " . $data->msg;
  }
}


//  Eliminar Oferta

if ($_POST['action'] == "eliminar_oferta") {

  $id = $_POST['offer_id'];

  $db = Database::connect();

  $query = "CALL of_eliminarOferta($id)";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error 62: " . $db->error;
  }
}
