<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

$db = Database::connect();
$action = $_POST['action'] ?? '';


/**
 * Obtener datos de una pieza por campo específico (ID o código)
 *
 * @param string $field Campo de búsqueda: 'pieza_id' o 'cod_pieza'
 * @param string $value Valor del campo a buscar
 * @param bool $useLike Si es true, usará LIKE '%valor%', si es false usará comparación exacta
 */
function fetchPieceData($field, $value, $useLike = false)
{
  $db = Database::connect();

  // Escapar el valor para evitar inyección SQL
  $escaped_value = $db->real_escape_string($value);

  // Determinar el operador de búsqueda
  $operator = $useLike ? "LIKE '%$escaped_value%'" : "= '$escaped_value'";

  // Consulta principal para obtener los datos de la pieza
  $query = "SELECT p.nombre_pieza, p.cantidad, p.precio_unitario, p.cod_pieza, 
                   pl.valor AS valor_lista, o.valor AS oferta, 
                   p.pieza_id AS IDpieza, p.estado_id, pos.referencia, 
                   COUNT(l.lista_id) AS lista_total
            FROM piezas p 
            LEFT JOIN piezas_con_ofertas po ON p.pieza_id = po.pieza_id
            LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
            LEFT JOIN piezas_con_posiciones pp ON p.pieza_id = pp.pieza_id
            LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
            LEFT JOIN piezas_con_lista_de_precios pl ON p.pieza_id = pl.pieza_id
            LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
            WHERE p.$field $operator
            LIMIT 1";

 return jsonQueryResult($db, $query);
}

/**
 * Controlador de acciones relacionadas con piezas
 * ---------------------------------------------------------------
 */
 
  switch ($action) {

    /**
     * Listado de piezas (DataTable)
     */
    case "index_piezas":
      handleDataTableRequest($db, [
        'columns' => [
          'p.nombre_pieza',
          'p.cod_pieza',
          'p.pieza_id',
          'c.nombre_categoria',
          'p.cantidad_min',
          'p.cantidad',
          'p.precio_costo',
          'p.precio_unitario',
          'e.nombre_estado'
        ],
        'searchable' => [
          'p.cod_pieza',
          'p.nombre_pieza',
          'c.nombre_categoria',
          'e.nombre_estado'
        ],
        'base_table' => 'piezas',
        'table_with_joins' => 'piezas p
            INNER JOIN estados_generales e ON p.estado_id = e.estado_id
            INNER JOIN almacenes a ON p.almacen_id = a.almacen_id
            LEFT JOIN piezas_con_categorias pc ON p.pieza_id = pc.pieza_id
            LEFT JOIN categorias c ON pc.categoria_id = c.categoria_id',
        'select' => 'SELECT p.cod_pieza, p.nombre_pieza, p.pieza_id, c.nombre_categoria, 
                    p.cantidad_min, p.cantidad, p.precio_costo, p.precio_unitario, e.nombre_estado',
        'table_rows' => function ($row) {
          if ($row['cantidad'] > $row['cantidad_min']) {
            $cantidadFormatted = '<td class="text-success">' . $row['cantidad'] . '</td>';
          } else if ($row['cantidad'] < 1) {
            $cantidadFormatted = '<td class="text-danger">' . $row['cantidad'] . '</td>';
          } else {
            $cantidadFormatted = '<td class="text-warning">' . $row['cantidad'] . '</td>';
          }

          $editLink = ($row['nombre_estado'] === 'Activo')
            ? '<a class="action-edit" title="Editar" href="' . base_url . 'pieces/edit&id=' . $row['pieza_id'] . '"><i class="fas fa-pencil-alt"></i></a>'
            : '<a class="action-edit action-disable" title="Editar" href="#"><i class="fas fa-pencil-alt"></i></a>';

          if ($_SESSION['identity']->nombre_rol == 'administrador') {
            $toggleAction = ($row['nombre_estado'] === 'Activo')
              ? '<span class="action-active" title="Desactivar ítem" onclick="disablePiece(\'' . $row['pieza_id'] . '\')"><i class="fas fa-lightbulb"></i></span>'
              : '<span class="action-delete" title="Activar" onclick="enablePiece(\'' . $row['pieza_id'] . '\')"><i class="fas fa-lightbulb"></i></span>';
            $deleteAction = '<span class="action-delete" title="Eliminar" onclick="deletePiece(\'' . $row['pieza_id'] . '\')"><i class="fas fa-times"></i></span>';
          } else {
            $toggleAction = '<span class="action-disable"><i class="fas fa-lightbulb"></i></span>';
            $deleteAction = '<span class="action-delete action-disable" title="Eliminar"><i class="fas fa-times"></i></span>';
          }

          $acciones = $editLink . ' ' . $toggleAction . ' ' . $deleteAction;

          return [
            'id' => $row['cod_pieza'],
            'nombre' => ucwords($row['nombre_pieza']),
            'categoria' => ucwords($row['nombre_categoria'] ?? 'N/A'),
            'cantidad' => $row['cantidad'],
            'precio_costo' => number_format($row['precio_costo'], 2),
            'precio_unitario' => number_format($row['precio_unitario'], 2),
            'acciones' => $acciones
          ];
        }
      ]);
      break;

    /**
     * Buscar pieza por código (uso de LIKE)
     */
    case "buscar_codigo_pieza":
     echo fetchPieceData('cod_pieza', $_POST['piece_code'], true);
      break;

    /**
     * Buscar pieza por ID exacto
     */
    case "buscar_pieza":
      echo fetchPieceData('pieza_id', $_POST['piece_id']);
      break;

    /**
     * Agregar nueva pieza
     */
    case "agregar_pieza":
      $params = [
        $_SESSION['identity']->usuario_id,
        $_POST['warehouse'],
        $_POST['piece_code'],
        $_POST['name'],
        $_POST['price_in'] ?? 0,
        $_POST['price_out'],
        $_POST['quantity'],
        $_POST['min_quantity'] ?? 0,
        $_POST['category'],
        $_POST['position'],
        $_POST['offer'] ?? 0,
        $_POST['brand'],
        $_POST['provider'],
        "-"
      ];
      echo handleProcedureAction($db, 'pz_agregarPieza', $params);
      break;

    /**
     * Editar pieza existente
     */
    case "editar_pieza":
      $params = [
        $_POST['piece_id'],
        $_POST['warehouse'],
        $_POST['piece_code'],
        $_POST['name'],
        $_POST['price_in'] ?? 0,
        $_POST['price_out'],
        $_POST['quantity'],
        $_POST['min_quantity'] ?? 0,
        $_POST['category'],
        $_POST['position'],
        $_POST['offer'] ?? 0,
        $_POST['brand'],
        $_POST['provider'],
        "-"
      ];
      echo handleProcedureAction($db, 'pz_editarPieza', $params);
      break;

    /**
     * Desactivar pieza
     */
    case "desactivar_pieza":
      $params = [
        (int)$_POST['piece_id'],
        'desactivar'
      ];
      echo handleProcedureAction($db, 'pz_cambiarEstado', $params);
      break;

    /**
     * Activar pieza
     */
    case "activar_pieza":
      $params = [
        (int)$_POST['piece_id'],
        'activar'
      ];
      echo handleProcedureAction($db, 'pz_cambiarEstado', $params);
      break;

    /**
     * Eliminar pieza
     */
    case "eliminarPieza":
      echo handleDeletionAction($db, $_POST['pieza_id'], 'pz_eliminarPieza');
      break;

    default:
      echo json_encode(['error' => 'Acción no válida']);
      break;
  }

