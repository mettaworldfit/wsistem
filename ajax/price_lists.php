<?php

require_once '../config/db.php';
session_start();


if ($_POST['action'] == "buscar_lista_de_producto") {

  $product_id = $_POST['product_id'];
  $db = Database::connect();

  $query = "SELECT *FROM lista_de_precios l 
              INNER JOIN productos_con_lista_de_precios p ON p.lista_id = l.lista_id
              WHERE p.producto_id = '$product_id'";

  $datos = $db->query($query);
  $html = '';

  while ($element = $datos->fetch_object()) {
    $html = '<option value="' . $element->lista_id . '">' . $element->nombre_lista . '</option>';
    echo $html;
  }
}

// Elegir lista de precio de producto

if ($_POST['action'] == "elegir_precio") {

  $list_id = $_POST['list_id'];
  $product_id = $_POST['product_id'];
  $db = Database::connect();

  $query = "SELECT *FROM lista_de_precios l 
              INNER JOIN productos_con_lista_de_precios p ON p.lista_id = l.lista_id
              WHERE l.lista_id = '$list_id' AND p.producto_id = '$product_id'";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;
}


// Asignar lista de precio a producto/piezas

if ($_POST['action'] == "asignar_lista_de_precios") {

  $id = $_POST['id'];
  $type = $_POST['type'];
  $list_id = $_POST['list_id'];
  $list_value = $_POST['list_value'];

  $db = Database::connect();

  $query = "CALL lp_asignarListaDePrecio($id,$list_id,'$list_value','$type')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}

// Eliminar producto/pieza con lista de precio

if ($_POST['action'] == "desasignar_lista_de_precio") {


  $id = $_POST['id'];
  $type = $_POST['type'];
  $db = Database::connect();


  $query = "CALL lp_desasignarListaDePrecio($id,'$type')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}

// Agregar lista de precio a un producto

if ($_POST['action'] == "editar_lista_de_precio_de_un_producto") {

  $product_id = $_POST['id'];
  $list_id = $_POST['list_id'];
  $list_value = $_POST['list_value'];

  $db = Database::connect();

  $query = "CALL lp_editarListaDePrecioAproducto($product_id,$list_id,'$list_value')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}

// Agregar lista de precio a una pieza

if ($_POST['action'] == "editar_lista_de_precio_de_una_pieza") {

  $piece_id = $_POST['id'];
  $list_id = $_POST['list_id'];
  $list_value = $_POST['list_value'];

  $db = Database::connect();

  $query = "CALL lp_editarListaDePrecioApieza($piece_id,$list_id,'$list_value')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}

// buscar lista de precio de producto

if ($_POST['action'] == "buscar_lista_de_pieza") {

  $piece_id = $_POST['piece_id'];
  $db = Database::connect();

  $query = "SELECT *FROM lista_de_precios l 
              INNER JOIN piezas_con_lista_de_precios p ON p.lista_id = l.lista_id
              WHERE p.pieza_id = '$piece_id'";

  $datos = $db->query($query);
  $html = '';

  while ($element = $datos->fetch_object()) {
    $html = '<option value="' . $element->lista_id . '">' . $element->nombre_lista . '</option>';
    echo $html;
  }
}

// Elegir lista de precio de pieza

if ($_POST['action'] == "elegir_precio_pieza") {

  $list_id = $_POST['list_id'];
  $piece_id = $_POST['piece_id'];
  $db = Database::connect();

  $query = "SELECT *FROM lista_de_precios l 
              INNER JOIN piezas_con_lista_de_precios p ON p.lista_id = l.lista_id
              WHERE l.lista_id = '$list_id' AND p.pieza_id = '$piece_id'";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;
}

// Agregar lista de precios

if ($_POST['action'] == 'agregar_lista') {

  $name = $_POST['list_name'];
  $comment = $_POST['list_comment'];
  $user_id = $_SESSION['identity']->usuario_id;

  $db = Database::connect();

  $query = "CALL lp_crearListaDePrecio($user_id,'$name','$comment')";
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

// Actualizar lista de precios

if ($_POST['action'] == 'actualizar-lista') {

  $name = $_POST['list_name'];
  $comment = $_POST['list_comment'];
  $list_id = $_POST['list_id'];

  $db = Database::connect();

  $query = "CALL lp_actualizarListaDePrecio($list_id,'$name','$comment')";
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


//  Eliminar lista de precio

if ($_POST['action'] == "eliminar_lista") {

  $id = $_POST['id'];

  $db = Database::connect();

  $query = "CALL lp_eliminarLista($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 55:" . $data->msg;
  }
}
