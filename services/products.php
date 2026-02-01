<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

$db = Database::connect();
$config = Database::getConfig(); // Cargar configuraciones
$user_id = $_SESSION['identity']->usuario_id;
$action = $_POST['action'] ?? '';

require_once __DIR__ . '/../vendor/autoload.php';

use Tinify\Tinify;

// API key por defecto
$defaultTinifyKey = "j6NYhPDxKlPVz6C0NyXrr5c6vVjNKvqj";

// Obtener desde config si existe y no está vacía
$tinify_API_KEY = !empty($config['tinify_API_KEY'])
  ? $config['tinify_API_KEY']
  : $defaultTinifyKey;

// Setear la key
Tinify::setKey($tinify_API_KEY);


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
                   p.estado_id, pos.referencia, p.imagen
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
          // $acciones .= 'class="btn-action action-info action-disable" href="#"';
          // Si no es administrador, permite editar siempre (en todos los productos)
          $acciones .= 'class="btn-action action-info" href="' . base_url . 'products/edit&id=' . $row['idproducto'] . '"';
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
          $acciones .= '<span class="btn-action action-danger btn-delete-product" 
          data-id="' . $row['idproducto'] . '" 
          data-name="' . $row['nombre_producto'] . '"
          title="Eliminar">' . BUTTON_DELETE . '</span>';
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
        // 'a.nombre_almacen',
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
          $acciones .= '<span class="btn-action action-danger btn-delete-product" 
          data-id="' . $row['idproducto'] . '" 
          data-name="' . $row['nombre_producto'] . '"
          title="Eliminar">' . BUTTON_DELETE . '</span>';
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
          // 'almacen' => '<span class="hide-cell">' . $row['nombre_almacen'] . '</span>',
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

    $db->query("SET @usuario_id = " . (int)$user_id);

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
      (!empty($_POST['offer']) && $_POST['offer'] !== "Vacío") ? $_POST['offer'] : '0',
      (!empty($_POST['brand']) && $_POST['brand'] !== "Vacío") ? $_POST['brand'] : '0',

      $_POST['provider']
    ];
    $procedure = $action === 'editar_producto' ? 'pr_editarProducto' : 'pr_agregarProducto';
    echo handleProcedureAction($db, $procedure, $params);

    break;

  // Caso: Eliminar producto
  case 'eliminar_producto':
    $db->query("SET @usuario_id = " . (int)$user_id);
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

  case 'obtener_articulos_pos':

    // Recibir parámetros de la solicitud
    $draw = isset($_POST['draw']) ? (int) $_POST['draw'] : 1;  // Número de solicitud (para seguimiento)
    $start = isset($_POST['start']) ? (int) $_POST['start'] : 0; // Índice de inicio
    $length = isset($_POST['length']) ? (int) $_POST['length'] : 15; // Cantidad de productos por página
    $search = isset($_POST['search']) ? $_POST['search'] : ''; // Término de búsqueda

    // Columnas para ordenamiento
    $columns = ['nombre', 'codigo', 'precio'];  // Ajusta las columnas según tu base de datos
    $orderColumn = isset($_POST['orderColumn']) ? (int) $_POST['orderColumn'] : 0;
    $orderDir = isset($_POST['orderDir']) ? $_POST['orderDir'] : 'asc';
    $orderBy = $columns[$orderColumn];

    // Consulta para obtener los productos con paginación y búsqueda
    // Termino de búsqueda
    $searchTerm = "%" . $search . "%";

    // Consulta para obtener los articulos con paginación y búsqueda
    $query = "SELECT *
FROM (
    SELECT 
        p.producto_id        AS item_id,
        'producto'           AS tipo,
        p.cod_producto       AS codigo,
        p.nombre_producto    AS nombre,
        p.cantidad           AS stock,
        p.precio_unitario    AS precio,
        p.precio_costo       AS costo,
        p.imagen             AS imagen,
        c.nombre_categoria   AS categoria
    FROM productos p
    LEFT JOIN productos_con_categorias pc 
        ON pc.producto_id = p.producto_id
    LEFT JOIN categorias c 
        ON pc.categoria_id = c.categoria_id

    UNION ALL

    SELECT
        s.servicio_id        AS item_id,
        'servicio'           AS tipo,
        NULL                 AS codigo,
        s.nombre_servicio    AS nombre,
        NULL                 AS stock,
        s.precio             AS precio,
        s.costo              AS costo,
        NULL                 AS imagen,
        NULL                 AS categoria
    FROM servicios s
) AS items
WHERE 
    items.nombre   LIKE '$searchTerm'
    OR items.codigo LIKE '$searchTerm'
    OR items.categoria LIKE '$searchTerm'
ORDER BY $orderBy $orderDir
LIMIT $start, $length
";
    $result = $db->query($query);

    // Verificar si hay resultados
    if ($result) {
      $data = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $data = [];
    }

    // Consulta para contar el total de productos filtrados por búsqueda
    $totalQuery = "SELECT COUNT(*) as total FROM productos WHERE nombre_producto LIKE '$searchTerm' OR cod_producto LIKE '$searchTerm'";
    $totalResult = $db->query($totalQuery);

    // Verificar si hay resultados
    if ($totalResult) {
      $totalData = $totalResult->fetch_assoc();
      $totalRecords = $totalData['total'];  // Total de registros filtrados
    } else {
      $totalRecords = 0;
    }

    // Responder con los datos en formato JSON
    echo json_encode([
      "draw" => $draw,                          // El número de solicitud de la tabla
      "recordsTotal" => $totalRecords,                    // Total de productos en la base de datos (sin filtros)
      "recordsFiltered" => $totalRecords,       // Total de productos filtrados
      "data" => $data                           // Los registros de productos solicitados
    ]);

    break;

  case 'detalle_punto_de_venta':

    $id = $_POST['order_id'];
    $query = "";

    if ($id != 0) {

      $query = "SELECT COALESCE(p.nombre_producto, pz.nombre_pieza, s.nombre_servicio) AS nombre, df.precio, df.cantidad, 
      df.detalle_venta_id, df.descuento,df.impuesto,p.producto_id,pz.pieza_id,s.servicio_id 
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
      df.detalle_venta_id, df.descuento,df.impuesto,p.producto_id,pz.pieza_id,s.servicio_id,df.usuario_id
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
      $row['cant_input'] = '
      <div class="qty-control">
        <button type="button" class="qty-btn qty-minus" ' . ($hasVariants ? 'disabled' : '') . '><i class="fas fa-minus"></i></button>

        <input
          type="number"
          class="input-update input-quantity"
          value="' . (intval($cantidad) == $cantidad ? number_format($cantidad, 0) : number_format($cantidad, 2)) . '"
          data-id="' . $row['detalle_venta_id'] . '"
          data-item-id="' . ($producto_id ? $producto_id : ($row['pieza_id'] ? $row['pieza_id'] : $row['servicio_id'])) . '"
          data-item-type="' . ($producto_id ? 'producto' : ($row['pieza_id'] ? 'pieza' : 'servicio')) . '"
          step="0.01"
          min="0"
          ' . ($hasVariants ? 'disabled' : '') . '
        />

        <button type="button" class="qty-btn qty-plus" ' . ($hasVariants ? 'disabled' : '') . '><i class="fas fa-plus"></i></button>
      </div>';


      // Generar las acciones para cada producto
      $row['acciones'] = '
    <div class="pos-actions">
        <a class="btn-action action-info" href="#" data-edit="' . $row['detalle_venta_id'] . '" title="Editar" id="item-edit">
            ' . BUTTON_EDIT . '
        </a>
        <a class="btn-action action-danger" data-delete="' . $row['detalle_venta_id'] . '" title="Eliminar" id="item-delete">
            ' . BUTTON_DELETE . '
        </a>
    </div>';
    }

    // Responder con los datos en formato JSON
    echo json_encode([
      "data" => $data,    // Los registros de productos solicitados
      "id" => $id
    ]);

    break;

  // Subir imagenes a de los productos
  case "subir_imagen":

    $response = array();  // Array para almacenar las respuestas
    $dir_name = '';
    $image_path = '';

    if (isset($config['carpeta']) && !empty($config['carpeta'])) {
      $dir_name = $config['carpeta'];
    }

    // Primero, verificamos si se está subiendo una imagen
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
      $product_id = $_POST['product_id'];  // El ID del producto que se acaba de crear

      // Definir la ruta donde se guardará la imagen 
      if ($_SERVER['SERVER_NAME'] === 'localhost') {
        // Si estamos en localhost, usar la ruta relativa
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/" . basename(dirname(__DIR__)) . "/public/uploads/" . $dir_name;
      } else {
        // Si no estamos en localhost, usar la ruta pública estándar para producción
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/public/uploads/" . $dir_name;
      }

      // Verificar si la carpeta existe, si no, crearla
      if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
          $response['error'][] = "Hubo un error al intentar crear la carpeta: " . $target_dir;
        } else {
          $response['success'][] = "La carpeta fue creada con éxito: " . $target_dir;
        }
      } else {
        // Si la carpeta ya existe, devolver la ruta
        $response['info'][] = "La carpeta ya existe: " . $target_dir;
      }

      // Obtener el nombre del archivo y la extensión
      $file_name = basename($_FILES["product_image"]["name"]);
      $target_file = $target_dir . $file_name;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      // Verificar si el archivo es una imagen
      if (getimagesize($_FILES["product_image"]["tmp_name"]) === false) {
        $response['error'][] = "El archivo no es una imagen.";
        echo json_encode($response);
        exit;
      }

      // Verificar el tamaño de la imagen (2 MB máximo)
      if ($_FILES["product_image"]["size"] > 2000000) {
        $response['error'][] = "Error archivo demasiado grande.";
        echo json_encode($response);
        exit;
      }

      // Verificar la extensión de la imagen
      $allowed_types = ["jpg", "jpeg", "png", "webp", "avif", "jfif"];
      if (!in_array($imageFileType, $allowed_types)) {
        $response['error'][] = "Error solo se permiten imágenes JPG, JPEG, PNG, AVIF, WEBP, JFIF.";
        echo json_encode($response);
        exit;
      }

      // Generar un nombre aleatorio de 11 caracteres numéricos
      function generate_random_filename($length = 11)
      {
        return substr(uniqid(rand(), true), 0, $length);  // Extraer solo los primeros 11 caracteres
      }

      // Si estamos en producción (no localhost)
      if ($_SERVER['SERVER_NAME'] !== 'localhost') {
        // Comprimir y convertir la imagen usando Tinify solo si estamos en producción
        try {
          // Comprimir la imagen utilizando Tinify
          $source = \Tinify\Source::fromFile($_FILES["product_image"]["tmp_name"]);

          // Generar el nombre aleatorio para la imagen
          $random_name = generate_random_filename(); // Nombre aleatorio de 11 caracteres

          // Convertir la imagen a WebP si no es ya WebP, JFIF o AVIF
          if ($imageFileType !== 'webp' && $imageFileType !== 'avif' && $imageFileType !== 'jfif') {
            // Establecer la ruta del archivo WebP con el nombre aleatorio
            $target_file_webp = $target_dir . $random_name . '.webp';

            // Comprimir y guardar la imagen en WebP
            $source->toFile($target_file_webp);
            $response['success'][] = "La imagen ha sido comprimida y convertida a WebP con éxito en: " . $target_file_webp;

            // Establecer la ruta de la imagen para la base de datos
            $image_path = $dir_name . $random_name . '.webp';  // Ruta relativa a la carpeta de imágenes

          } else {
            // Si la imagen ya es WebP, JFIF o AVIF, guardarla tal cual
            move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
            $response['success'][] = "La imagen ha sido subida exitosamente: " . $target_file;

            // Establecer la ruta de la imagen para la base de datos
            $image_path = $dir_name . pathinfo($file_name, PATHINFO_FILENAME) . '.' . $imageFileType;  // Ruta relativa a la carpeta de imágenes
          }
        } catch (Exception $e) {
          $response['error'][] = "Hubo un error al comprimir y convertir la imagen: " . $e->getMessage();
          echo json_encode($response);
          exit;
        }

        // En localhost
      } else {
        // No comprimir la imagen, solo moverla si es AVIF, JFIF o WEBP
        if ($imageFileType === 'webp' || $imageFileType === 'avif' || $imageFileType === 'jfif') {

          // Obtener la extensión del archivo
          $imageFileType = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
          $random_name = generate_random_filename();  // Generar un nombre aleatorio para la imagen

          // Establecer la nueva ruta de la imagen con el nombre aleatorio
          $target_file = $target_dir . $random_name . '.' . $imageFileType;

          // Mover la imagen con el nuevo nombre aleatorio
          if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $response['error'][] = "Error al mover el archivo a la carpeta de destino.";
            exit();
          }

          // Si la imagen se subió correctamente, proporcionar un mensaje
          $response['success'][] = "La imagen ha sido subida exitosamente: " . $target_file;

          // Establecer la ruta de la imagen para la base de datos
          $image_path = $dir_name . $random_name . '.' . $imageFileType;  // Ruta relativa a la carpeta de imágenes

        } else {
          // Si la imagen no es AVIF,WEBP ni JFIF, convertirla a WebP
          $image_tmp = $_FILES["product_image"]["tmp_name"];
          $image_info = getimagesize($image_tmp);
          $image_type = $image_info[2];

          if ($image_type == IMAGETYPE_JPEG || $image_type == IMAGETYPE_PNG) {
            $random_name = generate_random_filename(); // Generar un nombre aleatorio para la imagen
            $image = imagecreatefromstring(file_get_contents($image_tmp));
            $webp_file = $target_dir . $random_name . '.webp';
            imagewebp($image, $webp_file); // Guardar la imagen como WebP
            imagedestroy($image);
            $response['success'][] = "La imagen ha sido convertida a WebP y subida: " . $webp_file;

            // Establecer la ruta de la imagen para la base de datos
            $image_path = $dir_name . $random_name . '.webp';  // Ruta relativa a la carpeta de imágenes
          }
        }
      }

      // Actualizar el producto en la base de datos con la ruta de la imagen
      $sql = "UPDATE productos SET imagen = '$image_path' WHERE producto_id = '$product_id'";
      if (mysqli_query($db, $sql)) {
        $response['success'][] = "El producto ha sido actualizado con la imagen.";
      } else {
        $response['error'][] = "Error al actualizar el producto en la base de datos: " . mysqli_error($db);
      }
    } else {
      $response['error'][] = "Hubo un error al subir la imagen.";
    }

    echo json_encode($response);  // Devolver todas las respuestas en formato JSON

    break;

  case "borrar_imagen":

    $response = [
      'success' => false,
      'deleted' => false,
      'message' => '',
      'debug' => []
    ];

    $product_id = $_POST['product_id'];

    // Obtener imagen guardada
    $oldImgQuery = $db->query("SELECT imagen FROM productos WHERE producto_id = '$product_id'");

    if ($oldImgQuery && $oldRow = mysqli_fetch_assoc($oldImgQuery)) {

      if (!empty($oldRow['imagen'])) {

        // Ruta base
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
          $basePath = $_SERVER['DOCUMENT_ROOT'] . "/" . basename(dirname(__DIR__)) . "/public/uploads/";
        } else {
          $basePath = $_SERVER['DOCUMENT_ROOT'] . "/public/uploads/";
        }

        $relativePath = ltrim($oldRow['imagen'], '/');

        // Nombre sin extensión
        $imgName = pathinfo($relativePath, PATHINFO_FILENAME);
        $imgDir  = dirname($relativePath);

        $extensions = ["jpg", "jpeg", "png", "webp", "avif", "jfif"];

        foreach ($extensions as $ext) {
          $filePath = $basePath . $imgDir . '/' . $imgName . '.' . $ext;

          $response['debug'][] = $filePath;

          if (file_exists($filePath)) {
            unlink($filePath);
            $response['deleted'] = true;
          }
        }

        if ($response['deleted']) {

          $db->query("UPDATE productos SET imagen = '' WHERE producto_id = '$product_id'");

          $response['success'] = true;
          $response['message'] = 'Imagen eliminada correctamente';
        } else {
          $response['message'] = 'No se encontró la imagen en disco';
        }
      } else {
        $response['message'] = 'El producto no tiene imagen';
      }
    } else {
      $response['message'] = 'Producto no encontrado';
    }

    echo json_encode($response);
    exit;


    break;
}
