<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

// Conexión a base de datos
$db = Database::connect();

$user_id = $_SESSION['identity']->usuario_id;
$action = $_POST['action'] ?? '';

switch ($action) {

  // Listado de listas de precios (usado por DataTables)
  case 'index_lista_precios':
    handleDataTableRequest($db, [
      'columns' => ['nombre_lista', 'descripcion', 'lista_id'],
      'searchable' => ['lista_id', 'nombre_lista', 'descripcion'],
      'base_table' => 'lista_de_precios',
      'table_with_joins' => 'lista_de_precios',
      'select' => 'SELECT lista_id,nombre_lista,descripcion',
      'table_rows' => function ($row) {
        return [
          'id' => $row['lista_id'],
          'nombre_lista' => $row['nombre_lista'],
          'descripcion' => $row['descripcion'],
          'acciones' => '
            <a href="' . base_url . 'price_lists/edit&id=' . $row['lista_id'] . '">
              <span class="btn-action action-info">' . BUTTON_EDIT . '</span>
            </a>
            <span class="btn-action action-danger" onclick="deletePriceList(\'' . $row['lista_id'] . '\')" title="Eliminar">
              ' . BUTTON_DELETE . '
            </span>'
        ];
      }
    ]);
    break;

  // Buscar listas de precios asociadas a un producto o pieza
  case 'buscar_lista_de_producto':
  case 'buscar_lista_de_pieza':
    $id = $action === 'buscar_lista_de_producto' ? $_POST['product_id'] : $_POST['piece_id'];
    $table = $action === 'buscar_lista_de_producto' ? 'productos_con_lista_de_precios' : 'piezas_con_lista_de_precios';
    $field = $action === 'buscar_lista_de_producto' ? 'producto_id' : 'pieza_id';

    $query = "SELECT * FROM lista_de_precios l 
              INNER JOIN $table p ON p.lista_id = l.lista_id 
              WHERE p.$field = '$id'";
    $result = $db->query($query);
    $options = '';
    while ($row = $result->fetch_object()) {
      $options .= "<option value='{$row->lista_id}'>{$row->nombre_lista}</option>";
    }

    echo json_encode(['status' => 'ready', 'options' => $options], JSON_UNESCAPED_UNICODE);
    break;

  // Obtener detalle de valor de una lista de precio asociada a un producto o pieza
  case 'elegir_precio':
  case 'elegir_precio_pieza':
    $list_id = $_POST['list_id'];
    $id = $action === 'elegir_precio' ? $_POST['product_id'] : $_POST['piece_id'];
    $table = $action === 'elegir_precio' ? 'productos_con_lista_de_precios' : 'piezas_con_lista_de_precios';
    $field = $action === 'elegir_precio' ? 'producto_id' : 'pieza_id';

    $query = "SELECT * FROM lista_de_precios l 
              INNER JOIN $table p ON p.lista_id = l.lista_id 
              WHERE l.lista_id = '$list_id' AND p.$field = '$id'";
    jsonQueryResult($db, $query);
    break;

  // Asignar lista de precio a producto o pieza
  case 'asignar_lista_de_precios':
    echo handleProcedureAction($db, 'lp_asignarListaDePrecio', [
      (int)$_POST['id'],
      (int)$_POST['list_id'],
      $_POST['list_value'],
      $_POST['type'] // producto o pieza
    ]);
    break;

  // Quitar lista de precio de producto o pieza
  case 'desasignar_lista_de_precio':
    echo handleProcedureAction($db, 'lp_desasignarListaDePrecio', [
      (int)$_POST['id'],
      $_POST['type']
    ]);
    break;

  // Editar valor de lista de precio en un producto
  case 'editar_lista_de_precio_de_un_producto':
    echo handleProcedureAction($db, 'lp_editarListaDePrecioAproducto', [
      (int)$_POST['id'],
      (int)$_POST['list_id'],
      $_POST['list_value']
    ]);
    break;

  // Editar valor de lista de precio en una pieza
  case 'editar_lista_de_precio_de_una_pieza':
    echo handleProcedureAction($db, 'lp_editarListaDePrecioApieza', [
      (int)$_POST['id'],
      (int)$_POST['list_id'],
      $_POST['list_value']
    ]);
    break;

  // Crear una nueva lista de precios
  case 'agregar_lista':
    echo handleProcedureAction($db, 'lp_crearListaDePrecio', [
      $_SESSION['identity']->usuario_id,
      $_POST['list_name'],
      $_POST['list_comment']
    ]);
    break;

  // Actualizar información de una lista de precios existente
  case 'actualizar-lista':
    echo handleProcedureAction($db, 'lp_actualizarListaDePrecio', [
      (int)$_POST['list_id'],
      $_POST['list_name'],
      $_POST['list_comment']
    ]);
    break;

  // Eliminar lista de precios
  case 'eliminar_lista':
    echo handleDeletionAction($db, (int)$_POST['id'], 'lp_eliminarLista');
    break;

  // Actualizar precio del punto de venta
  case 'actualizar_precios_pos':

    $order_id = $_POST['order_id'] ?? 0;
    $list_id = $_POST['list_id'] ?? 0;
    $product_id = $_POST['product_id'] ?? 0;
    //$piece_id = $_POST['piece_id'] ?? 0;
    $newPrice = '';

    // Construcción de la consulta SQL según el caso
    $sql = "SELECT d.detalle_venta_id, p.producto_id, p.precio_unitario, pl.valor
        FROM detalle_facturas_ventas d
        INNER JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
        INNER JOIN productos_con_lista_de_precios pl ON pl.producto_id = dp.producto_id
        INNER JOIN productos p ON p.producto_id = pl.producto_id ";

    if ($product_id > 0) {
      $sql .= "WHERE p.producto_id = '$product_id' AND d.usuario_id = '$user_id' ORDER BY d.detalle_venta_id DESC LIMIT 1";
    } else if ($order_id == 0) {
      $sql .= "WHERE d.usuario_id = '$user_id' AND d.comanda_id IS NULL
    AND d.factura_venta_id IS NULL";
    } else {
      $sql .= "WHERE d.comanda_id = '$order_id' LIMIT 1";
    }

    // Ejecutar consulta
    $result = $db->query($sql);

    // Procesar los resultados
    while ($element = $result->fetch_object()) {
      $newPrice = ($list_id > 0) ? $element->valor : $element->precio_unitario;

      // Actualización del precio
      $sql2 = "UPDATE detalle_facturas_ventas SET precio = '$newPrice' WHERE detalle_venta_id = '$element->detalle_venta_id'";
      $db->query($sql2);
    }

    echo json_encode(["order_id" => $order_id]);

    break;
}
