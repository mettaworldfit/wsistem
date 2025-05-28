<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

if ($_POST['action'] == "index_categorias") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => ['categoria_id', 'nombre_categoria', 'descripcion', 'fecha'],
    'searchable' => ['categoria_id', 'nombre_categoria', 'fecha'],
    'base_table' => 'categorias',
    'table_with_joins' => 'categorias',
    'select' => 'SELECT categoria_id,nombre_categoria,descripcion,fecha',
    'table_rows' => function ($row) {
      $rol = $_SESSION['identity']->nombre_rol;

      // Acción de editar con validación de rol
      $editar = ($rol == 'administrador')
        ? '<a class="action-edit" href="' . base_url . 'categories/edit&id=' . $row['categoria_id'] . '" title="Editar">'
        : '<a class="action-edit action-disable" href="#" title="Editar">';

      $editar .= '<i class="fas fa-pencil-alt"></i></a>';

      return [
        'id'      => $row['categoria_id'],
        'nombre_categoria'  => ucwords($row['nombre_categoria']),
        'descripcion'       => $row['descripcion'],
        'fecha'             => $row['fecha'],
        'acciones'          => $editar . '
      <span class="action-delete" onclick="deleteCategory(\'' . $row['categoria_id'] . '\')" title="Eliminar">
        <i class="fas fa-times"></i>
      </span>'
      ];
    }
  ]);
}

if ($_POST['action'] == "agregarCategoria") {

  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL ca_agregarCategoria($user_id,'$name','$comment')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 70: " . $data->msg;
  }
}


if ($_POST['action'] == "actualizar_categoria") {

  $name = $_POST['name'];
  $comment = $_POST['comment'];
  $category_id = $_POST['category_id'];

  $db = Database::connect();

  $query = "CALL ca_actualizarCategoria($category_id,'$name','$comment')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 71: " . $data->msg;
  }
}


//  Eliminar categoría

if ($_POST['action'] == "eliminar_categoria") {

  $id = $_POST['category_id'];

  $db = Database::connect();

  $query = "CALL ca_eliminarCategoria($id)";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error 72: " . $db->error;
  }
}
