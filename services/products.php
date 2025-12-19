<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

$db = Database::connect();
$user_id = $_SESSION['identity']->usuario_id;
$action = $_POST['action'] ?? '';

/**
 * Obtiene los datos de un producto y su cantidad de variantes activas.
 *
 * @param string $field      Campo por el cual se hará la búsqueda (por ejemplo, 'cod_producto' o 'producto_id').
 * @param string|int $value  Valor del campo a buscar.
 * @param bool $useLike      Si es true, usará LIKE para coincidencias parciales. Si es false, usará comparación exacta (=).
 *
 * @return array             Retorna un array con dos elementos:
 *                           [0] => datos del producto (nombre, cantidad, precio, etc.),
 *                           [1] => número total de variantes activas (estado_id = 13).
 */
function fetchProductData($field, $value, $useLike = false)
{

  $db = Database::connect();

  // Escapar el valor para evitar inyección SQL
  $escaped_value = $db->real_escape_string($value);

  // Determinar el operador de búsqueda
  $operator = $useLike ? "LIKE '%$escaped_value%'" : "= '$escaped_value'";

  // Consulta principal para obtener los datos del producto
  $query = "SELECT p.nombre_producto, p.cantidad, p.precio_unitario, p.precio_costo, p.cod_producto, 
                   pl.valor AS valor_lista, o.valor AS oferta, 
                   p.producto_id AS IDproducto, i.valor AS impuesto, 
                   p.estado_id, pos.referencia 
            FROM productos p 
            LEFT JOIN almacenes a ON p.almacen_id = a.almacen_id
            LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
            LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
            LEFT JOIN productos_con_ofertas po ON p.producto_id = po.producto_id
            LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
            LEFT JOIN productos_con_posiciones pp ON p.producto_id = pp.producto_id
            LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
            LEFT JOIN productos_con_lista_de_precios pl ON p.producto_id = pl.producto_id
            LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
            WHERE p.$field $operator
            LIMIT 1";

  // Consulta para contar variantes activas del producto
  $query2 = "SELECT COUNT(v.variante_id) AS variante_total 
             FROM variantes v 
             INNER JOIN productos p ON p.producto_id = v.producto_id 
             WHERE p.$field $operator AND v.estado_id = 13";

  // Ejecutar las consultas
  $result1 = $db->query($query)->fetch_assoc();
  $result2 = $db->query($query2)->fetch_assoc();

  // Devolver los resultados
  return [$result1, $result2];
}


switch ($action) {
  // Caso: montrar el valor del inventario
  case 'index_valor_inventario':
    handleDataTableRequest($db, [
      'columns' => [
        'nombre_almacen',
        'codigo',
        'nombre',
        'cantidad',
        'precio_costo',
        'nombre_estado',
        'cantidad_min'
      ],
      'searchable' => [
        'nombre_almacen',
        'codigo',
        'nombre'
      ],
      'base_table' => '(SELECT 1) AS dummy', // Solo para el total sin filtro
      'table_with_joins' => "(
        SELECT nombre_almacen, cod_producto as codigo, nombre_producto as nombre, cantidad, precio_costo, nombre_estado, cantidad_min
        FROM productos p 
        INNER JOIN almacenes a ON a.almacen_id = p.almacen_id
        INNER JOIN estados_generales e ON e.estado_id = p.estado_id
        
        UNION ALL
        
        SELECT nombre_almacen, cod_pieza as codigo, nombre_pieza as nombre, cantidad, precio_costo, nombre_estado, cantidad_min
        FROM piezas pz 
        INNER JOIN almacenes a ON a.almacen_id = pz.almacen_id
        INNER JOIN estados_generales e ON e.estado_id = pz.estado_id
    ) inventario",
      'select' => "SELECT nombre_almacen, codigo, nombre, cantidad, precio_costo, nombre_estado, cantidad_min",
      'table_rows' => function ($row) {
        // Determinar clase de color según la cantidad
        $claseCantidad = 'text-warning';
        if ($row['cantidad'] > $row['cantidad_min']) {
          $claseCantidad = 'text-success';
        } elseif ($row['cantidad'] < 1) {
          $claseCantidad = 'text-danger';
        }

        return [
          'codigo'         => $row['codigo'],
          'nombre'         => ucwords($row['nombre']),
          'cantidad'       => '<span class="' . $claseCantidad . '">' . $row['cantidad'] . '</span>',
          'estado'         => '<span class="hide-cell">' . $row['nombre_estado'] . '</span>',
          'precio_costo'   => number_format($row['precio_costo'], 2),
          'total_costo'    => number_format($row['cantidad'] * $row['precio_costo'], 2),
        ];
      }
    ]);

    break;
  // Caso: Productos casi agotados
  case 'index_casi_agotados':
    handleDataTableRequest($db, [
      'columns' => [
        'p.nombre_producto',
        'p.cod_producto',
        'c.nombre_categoria',
        'a.nombre_almacen',
        'p.cantidad',
        'p.cantidad_min',
        'p.precio_costo',
        'p.precio_unitario',
        'e.nombre_estado'
      ],
      'searchable' => ['p.cod_producto', 'p.nombre_producto', 'c.nombre_categoria', 'a.nombre_almacen'],
      'base_table' => 'productos p',
      'table_with_joins' => 'productos p
        INNER JOIN estados_generales e ON p.estado_id = e.estado_id
        INNER JOIN almacenes a ON p.almacen_id = a.almacen_id
        LEFT JOIN productos_con_categorias pc ON p.producto_id = pc.producto_id
        LEFT JOIN categorias c ON pc.categoria_id = c.categoria_id',
      'base_condition' => 'p.cantidad <= p.cantidad_min AND e.estado_id = 1',
      'select' => 'SELECT p.cod_producto, p.nombre_producto, c.nombre_categoria, a.nombre_almacen,
                         p.cantidad_min, p.cantidad, p.precio_costo, p.precio_unitario,
                         e.nombre_estado, e.estado_id, p.producto_id as idproducto',
      'table_rows' => function ($row) {

        $acciones = '<a ';
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= 'class="btn-action action-info" href="' . base_url . 'products/edit&id=' . $row['idproducto'] . '"';
          } else {
            $acciones .= 'class="btn-action action-info action-disable" href="#"';
          }
        } else {
          $acciones .= 'class="btn-action action-info action-disable" href="#"';
        }
        $acciones .= ' title="Editar">' . BUTTON_EDIT . '</a>';

        // Activar o desactivar producto
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= '<span onclick="disableProduct(\'' . $row['idproducto'] . '\')" class="btn-action action-success" title="Desactivar ítem">' . BUTTON_ACTIVE . '</span>';
          } else {
            $acciones .= '<span onclick="enableProduct(\'' . $row['idproducto'] . '\')" class="btn-action action-danger" title="Activar">' . BUTTON_DISABLE . '</span>';
          }
        } else {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= '<span class="btn-action action-success action-disable" title="Desactivar">' . BUTTON_ACTIVE . '</span>';
          } else {
            $acciones .= '<span class="btn-action action-danger action-disable" title="Activar">' . BUTTON_DISABLE . '</span>';
          }
        }

        // Eliminar
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<span onclick="deleteProduct(\'' . $row['idproducto'] . '\')" class="btn-action action-danger" title="Eliminar">' . BUTTON_DELETE . '</span>';
        } else {
          $acciones .= '<span class="btn-action action-danger action-disable" title="Eliminar">' . BUTTON_DELETE . '</span>';
        }

        return [
          'cod_producto' => $row['cod_producto'],
          'nombre' => ucwords($row['nombre_producto']),
          'categoria' => ucwords($row['nombre_categoria'] ?? ''),
          'almacen' => ucwords($row['nombre_almacen'] ?? ''),

          'cantidad' => $row['cantidad'] < 1
            ? '<span class="text-danger">' . $row['cantidad'] . '</span>'
            : ($row['cantidad'] <= $row['cantidad_min']
              ? '<span class="text-warning">' . $row['cantidad'] . '</span>'
              : '<span class="text-success">' . $row['cantidad'] . '</span>'),

          'precio_costo' => number_format($row['precio_costo'], 2),
          'precio_unitario' => number_format($row['precio_unitario'], 2),

          'acciones' => $acciones
        ];
      }

    ]);
    break;

  // Caso: Listado de todos los productos
  case 'index_productos':
    handleDataTableRequest($db, [
      'columns' => [
        'p.nombre_producto',
        'p.cod_producto',
        'c.nombre_categoria',
        'a.nombre_almacen',
        'p.cantidad_min',
        'p.cantidad',
        'p.precio_costo',
        'p.precio_unitario',
        'e.nombre_estado'
      ],
      'searchable' => ['p.cod_producto', 'p.nombre_producto', 'c.nombre_categoria', 'a.nombre_almacen'],
      'base_table' => 'productos',
      'table_with_joins' => 'productos p
        INNER JOIN estados_generales e ON p.estado_id = e.estado_id
        INNER JOIN almacenes a ON p.almacen_id = a.almacen_id
        LEFT JOIN productos_con_categorias pc ON p.producto_id = pc.producto_id
        LEFT JOIN categorias c ON pc.categoria_id = c.categoria_id',
      'select' => 'SELECT p.cod_producto, p.producto_id, p.nombre_producto, c.nombre_categoria,
                         a.nombre_almacen, p.cantidad_min, p.cantidad, p.precio_costo,
                         p.precio_unitario, e.nombre_estado, p.producto_id as idproducto',
      'table_rows' => function ($row) {

        $acciones = '<a ';
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= 'class="btn-action action-info" href="' . base_url . 'products/edit&id=' . $row['idproducto'] . '"';
          } else {
            $acciones .= 'class="btn-action action-info action-disable" href="#"';
          }
        } else {
          $acciones .= 'class="btn-action action-info action-disable" href="#"';
        }
        $acciones .= ' title="Editar">' . BUTTON_EDIT . '</a>';

        // Activar o desactivar producto
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= '<span onclick="disableProduct(\'' . $row['idproducto'] . '\')" class="btn-action action-success" title="Desactivar">' . BUTTON_ACTIVE . '</span>';
          } else {
            $acciones .= '<span onclick="enableProduct(\'' . $row['idproducto'] . '\')" class="btn-action action-danger" title="Activar">' . BUTTON_DISABLE . '</span>';
          }
        } else {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= '<span class="btn-action action-success action-disable" title="Desactivar">' . BUTTON_ACTIVE . '</span>';
          } else {
            $acciones .= '<span class="btn-action action-danger action-disable" title="Activar">' . BUTTON_DISABLE . '</span>';
          }
        }

        // Eliminar
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<span onclick="deleteProduct(\'' . $row['idproducto'] . '\')" class="btn-action action-danger" title="Eliminar">' . BUTTON_DELETE . '</span>';
        } else {
          $acciones .= '<span class="btn-action action-danger action-disable" title="Eliminar">' . BUTTON_DELETE . '</span>';
        }

        $cantidad = '<span ';
        if ($row['cantidad'] > $row['cantidad_min']) {
          $cantidad .= 'class="text-success">';
        } elseif ($row['cantidad'] < 1) {
          $cantidad .= 'class="text-danger">';
        } else {
          $cantidad .= 'class="text-warning">';
        }
        $cantidad .= $row['cantidad'] . '</span>';

        return [
          'imagen' => '<img src="' . base_url . '/public/imagen/sistem/chino_com.png" alt="">',
          'codigo' => '<span class="hide-cell">' . $row['cod_producto'] . '</span>',
          'nombre' => $row['nombre_producto'],
          'categoria' => '<span class="hide-cell">' . $row['nombre_categoria'] . '</span>',
          'almacen' => '<span class="hide-cell">' . $row['nombre_almacen'] . '</span>',
          'cantidad' => $cantidad,
          'precio_costo' => '<span class="hide-cell">' . number_format($row['precio_costo'] ?? 0, 2) . '</span>',
          'precio_unitario' => number_format($row['precio_unitario'], 2),
          'estado' => '<span class="' . $row['nombre_estado'] . '">' . $row['nombre_estado'] . '</span>',
          'acciones' => $acciones

        ];
      }
    ]);
    break;

  // Cargar variantes en la seccion de editar productos
  case 'cargar_variantes':

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    handleDataTableRequest($db, [
      'columns' => [
        'nombre_proveedor',
        'sabor',
        'serial',
        'color',
        'costo_unitario',
        'caja',
        'entrada'
      ],
      'searchable' => ['serial', 'color', 'nombre_proveedor', 'sabor'],
      'base_table' => 'variantes v',
      'table_with_joins' => 'variantes v
               INNER JOIN productos p ON p.producto_id = v.producto_id
               LEFT JOIN variantes_con_colores vc ON vc.variante_id = v.variante_id
               LEFT JOIN colores c ON c.color_id = vc.color_id
               LEFT JOIN variantes_con_proveedores vp ON vp.variante_id = v.variante_id
               LEFT JOIN proveedores pv ON pv.proveedor_id = vp.proveedor_id',
      'select' => 'SELECT v.sabor,v.serial,v.caja,v.costo_unitario,c.color,pv.nombre_proveedor,
      v.variante_id as var_id, v.fecha as entrada',
      'base_condition' => 'p.producto_id = ' . $id . ' AND v.estado_id = 13',
      'table_rows' => function ($row) {
        // Si el usuario es administrador, añade la celda con la acción
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $button = '<td><span class="action-danger btn-action" onclick="deleteVariantDb(\'' . $row['var_id'] . '\', \'' . $row['costo_unitario'] . '\')">' . BUTTON_ERASE . '</span></td>';
        }

        return [
          'proveedor' => '<td>' . ucwords($row['nombre_proveedor'] ?? '') . '</td>',
          'sabor'            => '<td>' . $row['sabor'] . '</td>',
          'serial'           => '<td class="deviceField">' . $row['serial'] . '</td>',
          'color'            => '<td class="deviceField">' . ucwords($row['color'] ?? '') . '</td>',
          'costo'   => '<td>' . number_format($row['costo_unitario']) . '</td>',
          'caja'             => '<td class="deviceField">' . $row['caja'] . '</td>',
          'entrada'          => '<td>' . $row['entrada'] . '</td>',
          'acciones'         => $button
        ];
      }
    ]);

    break;

  // Caso: Buscar producto por código
  case 'buscar_codigo_producto':
    echo json_encode(fetchProductData("cod_producto", $_POST['product_code'] ?? '', true), JSON_UNESCAPED_UNICODE);
    break;

  // Caso: Buscar producto por ID
  case 'buscar_producto':
    echo json_encode(fetchProductData("producto_id", $_POST['product_id'] ?? '', false), JSON_UNESCAPED_UNICODE);
    break;

  // Caso: Buscar variantes de un producto
  case 'buscar_variantes':
    $product_id = (int)$_POST['product_id'];
    $result = $db->query("SELECT v.serial, v.sabor, v.caja, v.costo_unitario, c.color, p.nombre_producto, v.fecha, v.variante_id as id
                          FROM variantes v
                          INNER JOIN productos p ON p.producto_id = v.producto_id
                          LEFT JOIN variantes_con_colores vc ON vc.variante_id = v.variante_id
                          LEFT JOIN colores c ON c.color_id = vc.color_id
                          WHERE v.producto_id = '$product_id' AND v.estado_id != 14");

    if ($result && $result->num_rows > 0) {
      while ($element = $result->fetch_object()) {

        $text = ucwords($element->nombre_producto);

        if (!empty($element->serial)) {
          $text .= ' | SN: ' . $element->serial;
          if (!empty($element->color)) {
            $text .= ' | Color: ' . ucwords($element->color);
          }
          if (!empty($element->caja)) {
            $text .= ' | En caja: ' . $element->caja;
          }
        } elseif (!empty($element->sabor)) {
          $text .= ' | Sabor: ' . $element->sabor;
        }

        echo '<option value="' . $element->id . '">' . $text . '</option>';
      }
    }
    break;

  // Caso: Agregar o editar un producto
  case 'agregar_producto':
  case 'editar_producto':
    $params = [
      $action === 'editar_producto' ? $_POST['product_id'] : $_SESSION['identity']->usuario_id,
      $_POST['warehouse'],
      $_POST['product_code'],
      $_POST['name'],
      $_POST['price_in'] ?? 0,
      $_POST['price_out'] ?? 0,
      $_POST['quantity'] ?? 0,
      $_POST['min_quantity'] ?? 0,
      $_POST['category'],
      $_POST['position'],
      ($_POST['tax'] != "Vacío") ? $_POST['tax'] : 0,
      ($_POST['offer'] != "Vacío") ? $_POST['offer'] : 0,
      $_POST['brand'],
      $_POST['provider'],
      ''
    ];
    $procedure = $action === 'editar_producto' ? 'pr_editarProducto' : 'pr_agregarProducto';
    echo handleProcedureAction($db, $procedure, $params);
    break;

  // Caso: Eliminar producto
  case 'eliminarProducto':
    echo handleProcedureAction($db, 'pr_eliminarProducto', [$_POST['product_id']]);
    break;

  // Caso: Desactivar producto
  case 'desactivar_producto':
    echo handleProcedureAction($db, 'pr_cambiarEstado', [$_POST['product_id'], 'desactivar']);
    break;

  // Caso: Activar producto
  case 'activar_producto':
    echo handleProcedureAction($db, 'pr_cambiarEstado', [$_POST['product_id'], 'activar']);
    break;

  // Caso: Agregar variantes a un producto
  case 'agregar_variantes':
    echo handleProcedureAction($db, 'pr_asignarVariante', [
      $_POST['product_id'],
      $_POST['colour_id'] ?? 0,
      $_POST['provider_id'] ?? 0,
      $_POST['type'],
      $_POST['flavor'],
      $_POST['serial'],
      $_POST['cost'],
      $_POST['box']
    ]);
    break;

  // Caso: Eliminar una variante
  case 'eliminar_variante':
    echo handleProcedureAction($db, 'pr_eliminarVariante', [$_POST['id']]);
    break;

  case 'pos':


    // Preparar la consulta SQL
    $query = "SELECT * FROM productos"; // Asegúrate de que el nombre de la tabla y las columnas son correctas

    // Ejecutar la consulta
    $result = $db->query($query);

    // Verificar si hay resultados
    if ($result->num_rows > 0) {
      // Crear un array para almacenar los productos
      $productos = array();

      // Recorrer los resultados y almacenarlos en el array
      while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
      }

      // Devolver los productos en formato JSON
      echo json_encode($productos);
    } else {
      // Si no hay productos, devolver un mensaje vacío
      echo json_encode([]);
    }

    break;

  case 'detalle_punto_de_venta':

    $id = $_POST['order_id'];
    $query = "";

    if ($id != 0) {

      $query = "SELECT COALESCE(p.nombre_producto, pz.nombre_pieza, s.nombre_servicio) AS nombre, df.precio, df.cantidad, 
      df.detalle_venta_id, df.descuento,p.producto_id,pz.pieza_id,s.servicio_id 
     FROM detalle_facturas_ventas df
               LEFT JOIN detalle_ventas_con_productos dvp ON df.detalle_venta_id = dvp.detalle_venta_id
               LEFT JOIN productos p ON p.producto_id = dvp.producto_id
               LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
               LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
               LEFT JOIN detalle_ventas_con_piezas_ dvpz ON df.detalle_venta_id = dvpz.detalle_venta_id
               LEFT JOIN piezas pz ON pz.pieza_id = dvpz.pieza_id
               LEFT JOIN detalle_ventas_con_servicios dvs ON df.detalle_venta_id = dvs.detalle_venta_id
               LEFT JOIN servicios s ON s.servicio_id = dvs.servicio_id
      WHERE df.comanda_id = '$id'";
    } else {
      $query = "SELECT COALESCE(p.nombre_producto, pz.nombre_pieza, s.nombre_servicio) AS nombre, df.precio, df.cantidad, 
      df.detalle_venta_id, df.descuento,p.producto_id,pz.pieza_id,s.servicio_id,df.usuario_id
     FROM detalle_facturas_ventas df
               LEFT JOIN detalle_ventas_con_productos dvp ON df.detalle_venta_id = dvp.detalle_venta_id
               LEFT JOIN productos p ON p.producto_id = dvp.producto_id
               LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
               LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
               LEFT JOIN detalle_ventas_con_piezas_ dvpz ON df.detalle_venta_id = dvpz.detalle_venta_id
               LEFT JOIN piezas pz ON pz.pieza_id = dvpz.pieza_id
               LEFT JOIN detalle_ventas_con_servicios dvs ON df.detalle_venta_id = dvs.detalle_venta_id
               LEFT JOIN servicios s ON s.servicio_id = dvs.servicio_id
     WHERE df.factura_venta_id IS NULL
    AND (df.comanda_id IS NULL OR df.comanda_id = 0)
    AND df.usuario_id = '$user_id'";
    }

    $result = mysqli_query($db, $query);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Contar el total de registros (sin filtros)

    // $totalQuery = "SELECT COUNT(*) as total FROM detalle_facturas_ventas WHERE comanda_id = '$id'";
    // $totalResult = mysqli_query($db, $totalQuery);
    // $totalData = mysqli_fetch_assoc($totalResult);
    // $totalRecords = $totalData['total'];  // Total de registros encontrados

    // Verificar si las claves existen antes de acceder a ellas
    foreach ($data as &$row) {
      // Verificar si 'producto_id' y 'cantidad' están definidos
      $producto_id = isset($row['producto_id']) ? $row['producto_id'] : null;
      $cantidad = isset($row['cantidad']) ? $row['cantidad'] : 0;

      // Verificar si el producto tiene variantes
      $hasVariants = false;
      if ($producto_id) {
        $sql_check_variants = "SELECT COUNT(*) AS variants_count FROM variantes WHERE producto_id = '$producto_id'";
        $result_variants = $db->query($sql_check_variants);
        if ($result_variants) {
          $variant_data = $result_variants->fetch_assoc();
          if ($variant_data['variants_count'] > 0) {
            $hasVariants = true;
          }
        }
      }

      // Generar el input con el atributo disabled si el producto tiene variantes
      $row['cant_input'] = '<input type="number" class="input-update input-quantity" 
                   value="' . (intval($cantidad) == $cantidad ? number_format($cantidad, 0) : number_format($cantidad, 2)) . '" 
                   data-id="' . $row['detalle_venta_id'] . '" 
                   data-item-id="' . ($producto_id ? $producto_id : ($row['pieza_id'] ? $row['pieza_id'] : $row['servicio_id'])) . '" 
                   data-item-type="' . ($producto_id ? 'producto' : ($row['pieza_id'] ? 'pieza' : 'servicio')) . '" 
                   step="any" min="0"' . ($hasVariants ? 'disabled' : '') . ' />';

      // Generar las acciones para cada producto
      $row['acciones'] = '
    <div class="pos-actions">
        <a class="btn-action action-info" href="#" data-edit="' . $row['detalle_venta_id'] . '" title="editar" id="item-edit">
            ' . BUTTON_EDIT . '
        </a>
        <a class="btn-action action-danger" data-delete="' . $row['detalle_venta_id'] . '" id="item-delete">
            ' . BUTTON_DELETE . '
        </a>
    </div>';
    }

    // Responder con los datos en formato JSON
    echo json_encode([
      // "recordsTotal" => $totalRecords,          // Total de productos en la base de datos (sin filtros)
      "data" => $data,                          // Los registros de productos solicitados
      "id" => $id
    ]);

    break;
}
