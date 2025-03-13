<?php

require_once '../config/db.php';
session_start();

/**
 * Buscar pieza por código
 ----------------------------------------*/

if ($_POST['action'] == "buscar_codigo_pieza") {

  $q = $_POST['piece_code'];
  $db = Database::connect();

  $query = "SELECT p.nombre_pieza, p.cantidad, p.precio_unitario, p.cod_pieza, count(l.lista_id) AS lista_total, 
            pl.valor as valor_lista, o.valor as oferta, p.pieza_id as IDpieza, p.estado_id, pos.referencia FROM piezas p 
            LEFT JOIN piezas_con_ofertas po ON p.pieza_id = po.pieza_id
            LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
            LEFT JOIN piezas_con_posiciones pp ON p.pieza_id = pp.pieza_id
            LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
            LEFT JOIN piezas_con_lista_de_precios pl ON p.pieza_id = pl.pieza_id
            LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
            WHERE p.cod_pieza LIKE '%$q%'";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;
}

// Buscar pieza por nombre

if ($_POST['action'] == "buscar_pieza") {

  $q = $_POST['piece_id'];
  $db = Database::connect();

  $query = " SELECT p.nombre_pieza, p.cantidad, p.precio_unitario, p.cod_pieza,pl.valor as valor_lista, 
          o.valor as oferta, p.pieza_id as IDpieza, p.estado_id, pos.referencia FROM piezas p 
          LEFT JOIN piezas_con_ofertas po ON p.pieza_id = po.pieza_id
          LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
          LEFT JOIN piezas_con_posiciones pp ON p.pieza_id = pp.pieza_id
          LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
          LEFT JOIN piezas_con_lista_de_precios pl ON p.pieza_id = pl.pieza_id
          LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
          WHERE p.pieza_id = '$q'";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;
}

// Agregar pieza

if ($_POST['action'] == "agregar_pieza") {

  $db = Database::connect();

  $userID = $_SESSION['identity']->usuario_id;
  $piece_code = $_POST['piece_code'];
  $name = $_POST['name'];
  $price_in = (!empty($_POST['price_in'])) ? $_POST['price_in'] : 0;
  $price_out = $_POST['price_out'];
  $quantity = $_POST['quantity'];
  $min_quantity = (!empty($_POST['min_quantity'])) ? $_POST['min_quantity'] : 0;
  $provider_id = $_POST['provider'];
  $brand_id = $_POST['brand'];
  $offer_id = ($_POST['offer'] != "Vacío") ? $_POST['offer'] : 0;
  $position_id = $_POST['position'];
  $category_id = $_POST['category'];
  $warehouse_id = $_POST['warehouse'];
  $imagen = "-";


  $query = "CALL pz_agregarPieza($userID,$warehouse_id,'$piece_code','$name','$price_in','$price_out',
  '$quantity','$min_quantity','$category_id','$position_id','$offer_id','$brand_id','$provider_id','$imagen')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}


// Editar pieza

if ($_POST['action'] == "editar_pieza") {

  $db = Database::connect();

  // $userID = $_SESSION['identity']->usuario_id;
  $piece_id = $_POST['piece_id'];
  $piece_code = $_POST['piece_code'];
  $name = $_POST['name'];
  $price_in = (!empty($_POST['price_in'])) ? $_POST['price_in'] : 0;
  $price_out = $_POST['price_out'];
  $quantity = $_POST['quantity'];
  $min_quantity = (!empty($_POST['min_quantity'])) ? $_POST['min_quantity'] : 0;
  $provider_id = $_POST['provider'];
  $brand_id = $_POST['brand'];
  $offer_id = ($_POST['offer'] != "Vacío") ? $_POST['offer'] : 0;
  $position_id = $_POST['position'];
  $category_id = $_POST['category'];
  $warehouse_id = $_POST['warehouse'];
  $imagen = "-";


  $query = "CALL pz_editarPieza($piece_id,'$warehouse_id','$piece_code','$name','$price_in','$price_out',
  '$quantity','$min_quantity','$category_id','$position_id','$offer_id','$brand_id','$provider_id','$imagen')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "1") {

    echo $data->msg;
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}


/**
 * Desactivar pieza
 ----------------------------------------------*/

if ($_POST['action'] == "desactivar_pieza") {
  $db = Database::connect();

  $id = $_POST['piece_id'];

  $query = "CALL pz_cambiarEstado($id,'desactivar')";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error : " . $db->error;
  }


  /**
   * Activar pieza
 ----------------------------------------------*/
} else if ($_POST['action'] == "activar_pieza") {

  $db = Database::connect();

  $id = $_POST['piece_id'];

  $query = "CALL pz_cambiarEstado($id,'activar')";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error : " . $db->error;
  }
}

//  Eliminar pieza

if ($_POST['action'] == "eliminarPieza") {

  $id = $_POST['pieza_id'];

  $db = Database::connect();

  $query = "CALL pz_eliminarPieza('$id')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}
