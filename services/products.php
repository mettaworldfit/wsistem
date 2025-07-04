<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

$db = Database::connect();
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
                         e.nombre_estado, e.estado_id, p.producto_id',
      'table_rows' => function ($row) {

        $acciones = '<a ';
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= 'class="action-edit" href="' . base_url . 'products/edit&id=' . $row['idproducto'] . '"';
          } else {
            $acciones .= 'class="action-edit action-disable" href="#"';
          }
        } else {
          $acciones .= 'class="action-edit action-disable" href="#"';
        }
        $acciones .= ' title="Editar"><i class="fas fa-pencil-alt"></i></a>';

        // Activar o desactivar producto
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= '<span onclick="disableProduct(\'' . $row['idproducto'] . '\')" class="action-active" title="Desactivar ítem"><i class="fas fa-lightbulb"></i></span>';
          } else {
            $acciones .= '<span onclick="enableProduct(\'' . $row['idproducto'] . '\')" class="action-delete" title="Activar"><i class="fas fa-lightbulb"></i></span>';
          }
        } else {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= '<span class="action-active action-disable" title="Desactivar ítem"><i class="fas fa-lightbulb"></i></span>';
          } else {
            $acciones .= '<span class="action-delete action-disable" title="Activar"><i class="fas fa-lightbulb"></i></span>';
          }
        }

        // Eliminar
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<span onclick="deleteProduct(\'' . $row['idproducto'] . '\')" class="action-delete" title="Eliminar"><i class="fas fa-times"></i></span>';
        } else {
          $acciones .= '<span class="action-delete action-disable" title="Eliminar"><i class="fas fa-times"></i></span>';
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
            $acciones .= 'class="action-edit" href="' . base_url . 'products/edit&id=' . $row['idproducto'] . '"';
          } else {
            $acciones .= 'class="action-edit action-disable" href="#"';
          }
        } else {
          $acciones .= 'class="action-edit action-disable" href="#"';
        }
        $acciones .= ' title="Editar"><i class="fas fa-pencil-alt"></i></a>';

        // Activar o desactivar producto
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= '<span onclick="disableProduct(\'' . $row['idproducto'] . '\')" class="action-active" title="Desactivar ítem"><i class="fas fa-lightbulb"></i></span>';
          } else {
            $acciones .= '<span onclick="enableProduct(\'' . $row['idproducto'] . '\')" class="action-delete" title="Activar"><i class="fas fa-lightbulb"></i></span>';
          }
        } else {
          if ($row['nombre_estado'] == 'Activo') {
            $acciones .= '<span class="action-active action-disable" title="Desactivar ítem"><i class="fas fa-lightbulb"></i></span>';
          } else {
            $acciones .= '<span class="action-delete action-disable" title="Activar"><i class="fas fa-lightbulb"></i></span>';
          }
        }

        // Eliminar
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<span onclick="deleteProduct(\'' . $row['idproducto'] . '\')" class="action-delete" title="Eliminar"><i class="fas fa-times"></i></span>';
        } else {
          $acciones .= '<span class="action-delete action-disable" title="Eliminar"><i class="fas fa-times"></i></span>';
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
      'searchable' => [],
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
          $button = '<td><span class="action-delete" onclick="deleteVariantDb(\'' . $row['var_id'] . '\', \'' . $row['costo_unitario'] . '\')"><i class="far fa-minus-square"></i></span></td>';
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
}
