<?php

require_once '../config/db.php';
session_start();


/**
 * Buscar producto por código
 ----------------------------------------*/

if ($_POST['action'] == "buscar_codigo_producto") {

  $q = $_POST['product_code'];
  $db = Database::connect();

  $query = "SELECT p.nombre_producto, p.cantidad, p.precio_unitario, p.cod_producto, pl.valor as 'valor_lista', o.valor as 'oferta', 
            p.producto_id as 'IDproducto', i.valor as 'impuesto', p.estado_id, pos.referencia FROM productos p 
            LEFT JOIN almacenes a ON p.almacen_id = a.almacen_id
            LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
            LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
            LEFT JOIN productos_con_ofertas po ON p.producto_id = po.producto_id
            LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
            LEFT JOIN productos_con_posiciones pp ON p.producto_id = pp.producto_id
            LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
            LEFT JOIN productos_con_lista_de_precios pl ON p.producto_id = pl.producto_id
            LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
            WHERE p.cod_producto LIKE '%$q%'";

  $query2 = "SELECT count(v.variante_id) as variante_total, p.cod_producto FROM variantes v
             INNER JOIN productos p ON p.producto_id = v.producto_id
             WHERE p.cod_producto = '$q' AND v.estado_id = 13";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  $datos2 = $db->query($query2);
  $result2 = $datos2->fetch_assoc();

  $arr = array($result, $result2);

  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

// Buscar producto por nombre

if ($_POST['action'] == "buscar_producto") {

  $q = $_POST['product_id'];
  $db = Database::connect();

  $query = "SELECT p.nombre_producto, p.cantidad, p.precio_unitario, p.cod_producto, pl.valor as 'valor_lista', o.valor as 'oferta', 
  p.producto_id as 'IDproducto', i.valor as 'impuesto', p.estado_id, pos.referencia FROM productos p 
            LEFT JOIN almacenes a ON p.almacen_id = a.almacen_id
            LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
            LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
            LEFT JOIN productos_con_ofertas po ON p.producto_id = po.producto_id
            LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
            LEFT JOIN productos_con_posiciones pp ON p.producto_id = pp.producto_id
            LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
            LEFT JOIN productos_con_lista_de_precios pl ON p.producto_id = pl.producto_id
            LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
            WHERE p.producto_id = $q";

  $query2 = "SELECT count(variante_id) as variante_total FROM variantes
             WHERE producto_id = '$q' AND estado_id = 13";

  $datos = $db->query($query);
  $result = $datos->fetch_assoc();

  $datos2 = $db->query($query2);
  $result2 = $datos2->fetch_assoc();

  $arr = array($result, $result2);

  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

// buscar variantes del producto

if ($_POST['action'] == "buscar_variantes") {

  $product_id = $_POST['product_id'];
  $db = Database::connect();

  $query = "SELECT v.imei,v.serial,v.caja,v.costo_unitario,c.color,p.nombre_producto,v.fecha,v.variante_id as id  FROM variantes v
            INNER JOIN productos p ON p.producto_id = v.producto_id
            LEFT JOIN variantes_con_colores vc ON vc.variante_id = v.variante_id
            LEFT JOIN colores c ON c.color_id = vc.color_id
            WHERE v.producto_id = '$product_id' AND v.estado_id != 14";

  $datos = $db->query($query);
  $html = '';

  while ($element = $datos->fetch_object()) {


    $html = '<option value="' . $element->id . '">' . ucwords($element->nombre_producto) . ' | IMEI: ' . $element->imei . ' | Serial: ' . $element->serial .
      ' | Color: ' .  ucwords($element->color) . ' | En caja: ' . $element->caja . '</option>';


    echo $html;
  }
}

// Crear producto

if ($_POST['action'] == "agregar_producto") {

  $db = Database::connect();

  $userID = $_SESSION['identity']->usuario_id;
  $product_code = $_POST['product_code'];
  $name = $_POST['name'];
  $price_in = (!empty($_POST['price_in'])) ? $_POST['price_in'] : 0;
  $price_out = $_POST['price_out'];
  $quantity = $_POST['quantity'];
  $min_quantity = (!empty($_POST['min_quantity'])) ? $_POST['min_quantity'] : 0;
  $tax_id = ($_POST['tax'] != "Vacío") ? $_POST['tax'] : 0;
  $provider_id = $_POST['provider'];
  $brand_id = $_POST['brand'];
  $offer_id = ($_POST['offer'] != "Vacío") ? $_POST['offer'] : 0;
  $position_id = $_POST['position'];
  $category_id = $_POST['category'];
  $warehouse_id = $_POST['warehouse'];
  $img = "";

  $query = "CALL pr_agregarProducto($userID,$warehouse_id,'$product_code','$name','$price_in','$price_out',
  '$quantity','$min_quantity','$category_id','$position_id','$tax_id','$offer_id','$brand_id','$provider_id','$img')";

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


// Editar producto

if ($_POST['action'] == "editar_producto") {

  $db = Database::connect();

  // $userID = $_SESSION['identity']->usuario_id;
  $product_id = $_POST['product_id'];
  $product_code = $_POST['product_code'];
  $name = $_POST['name'];
  $price_in = (!empty($_POST['price_in'])) ? $_POST['price_in'] : 0;
  $price_out = $_POST['price_out'];
  $quantity = $_POST['quantity'];
  $min_quantity = (!empty($_POST['min_quantity'])) ? $_POST['min_quantity'] : 0;
  $tax_id = ($_POST['tax'] != "Vacío") ? $_POST['tax'] : 0;
  $provider_id = $_POST['provider'];
  $brand_id = $_POST['brand'];
  $offer_id = ($_POST['offer'] != "Vacío") ? $_POST['offer'] : 0;
  $position_id = $_POST['position'];
  $category_id = $_POST['category'];
  $warehouse_id = $_POST['warehouse'];
  $img = "-";


  $query = "CALL pr_editarProducto($product_id,'$warehouse_id','$product_code','$name','$price_in','$price_out',
  '$quantity','$min_quantity',$category_id,$position_id,$tax_id,$offer_id,$brand_id,$provider_id,'$img')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo $data->msg;
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}


//  Eliminar producto

if ($_POST['action'] == "eliminarProducto") {

  $id = $_POST['product_id'];

  $db = Database::connect();

  $query = "CALL pr_eliminarProducto('$id')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo $data->msg;
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}

/**
 * Verificar codigo del producto
 -----------------------------------------*/

if ($_POST['action'] == "verificar-codigo") {

  $code = $_POST['product_code'];

  $db = Database::connect();

  $query = "SELECT product_code FROM products WHERE product_code = '$code'";

  if ($db->query($query) === TRUE) {

    echo 'aprovado';
  } else {

    echo 'no disponible';

    // echo "Error: " . $db->error;
  }
}


/**
 * Desactivar producto
 ----------------------------------------------*/

if ($_POST['action'] == "desactivar_producto") {
  $db = Database::connect();

  $id = $_POST['product_id'];

  $query = "CALL pr_cambiarEstado($id,'desactivar')";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error : " . $db->error;
  }


  /**
   * Activar producto
 ----------------------------------------------*/
} else if ($_POST['action'] == "activar_producto") {

  $db = Database::connect();

  $id = $_POST['product_id'];

  $query = "CALL pr_cambiarEstado($id,'activar')";

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error : " . $db->error;
  }
}

// Asignar variante a producto

if ($_POST['action'] == "asignar_variante") {

  $id = $_POST['id'];
  $colour = (!empty($_POST['colour_id'])) ? $_POST['colour_id'] : 0;
  $provider = (!empty($_POST['provider_id'])) ? $_POST['provider_id'] : 0;
  $imei = $_POST['imei'];
  $serial = $_POST['serial'];
  $box = $_POST['box'];
  $cost = $_POST['cost'];
  $imagen = "-";

  $db = Database::connect();

  $query = "CALL pr_asignarVariante($id,$colour,$provider,'$imei','$serial','$cost','$box','$imagen')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $db->error;
  }
}

// Agregar variantes a un producto

if ($_POST['action'] == "editar_variantes") {

  $product_id = $_POST['product_id'];
  $colour = (!empty($_POST['colour_id'])) ? $_POST['colour_id'] : 0;
  $provider = (!empty($_POST['provider_id'])) ? $_POST['provider_id'] : 0;
  $imei = $_POST['imei'];
  $serial = $_POST['serial'];
  $box = $_POST['box'];
  $cost = $_POST['cost'];
  $imagen = "-";

  $db = Database::connect();
  $query = "CALL pr_asignarVariante($product_id,$colour,$provider,'$imei','$serial','$cost','$box','$imagen')";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg > 0) {

    echo $data->msg;
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $db->error;
  }
}

// Eliminar variante

if ($_POST['action'] == "eliminar_variante") {

  $id = $_POST['id'];
  $db = Database::connect();


  $query = "CALL pr_eliminarVariante($id)";

  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 50: " . $db->error;
  }
}
