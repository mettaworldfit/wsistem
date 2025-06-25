<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

$db = Database::connect();
$action = $_POST['action'] ?? '';

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
          'id' => '<span class="hide-cell">'. $row['cod_pieza']. '</span>',
          'nombre' => ucwords($row['nombre_pieza']),
          'categoria' => '<span class="hide-cell">'. ucwords($row['nombre_categoria'] ?? 'N/A'). '</span>',
          'cantidad' => $row['cantidad'],
          'precio_costo' => '<span class="hide-cell">'. number_format($row['precio_costo'] ?? 0, 2). '</span>',
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

    $q = $_POST['piece_code'];
    $query = "SELECT p.nombre_pieza, p.cantidad, p.precio_costo,p.precio_unitario, p.cod_pieza, count(l.lista_id) AS lista_total, 
            pl.valor as valor_lista, o.valor as oferta, p.pieza_id as IDpieza, p.estado_id, pos.referencia FROM piezas p 
            LEFT JOIN piezas_con_ofertas po ON p.pieza_id = po.pieza_id
            LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
            LEFT JOIN piezas_con_posiciones pp ON p.pieza_id = pp.pieza_id
            LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
            LEFT JOIN piezas_con_lista_de_precios pl ON p.pieza_id = pl.pieza_id
            LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
            WHERE p.cod_pieza LIKE '%$q%'";

    jsonQueryResult($db, $query);

    break;

  /**
     * Buscar pieza por ID exacto
     */
  case "buscar_pieza":

    $q = $_POST['piece_id'];
    $query = " SELECT p.nombre_pieza, p.cantidad, p.precio_unitario, p.precio_costo, p.cod_pieza,pl.valor as valor_lista, 
          o.valor as oferta, p.pieza_id as IDpieza, p.estado_id, pos.referencia FROM piezas p 
          LEFT JOIN piezas_con_ofertas po ON p.pieza_id = po.pieza_id
          LEFT JOIN ofertas o ON po.oferta_id = o.oferta_id
          LEFT JOIN piezas_con_posiciones pp ON p.pieza_id = pp.pieza_id
          LEFT JOIN posiciones pos ON pp.posicion_id = pos.posicion_id
          LEFT JOIN piezas_con_lista_de_precios pl ON p.pieza_id = pl.pieza_id
          LEFT JOIN lista_de_precios l ON pl.lista_id = l.lista_id
          WHERE p.pieza_id = '$q'";

    jsonQueryResult($db, $query);
    break;

  /**
     * Agregar nueva pieza
     */
  case "agregar_pieza":
    $params = [
      (int)$_SESSION['identity']->usuario_id,
      (int) $_POST['warehouse'],
      $_POST['piece_code'],
      $_POST['name'],
      (int)  $_POST['price_in'] ?? 0,
      (int) $_POST['price_out'],
      (int) $_POST['quantity'],
      (int)$_POST['min_quantity'] ?? 0,
      (int)$_POST['category'],
      (int)$_POST['position'],
      (int) $_POST['offer'] ?? 0,
      (int)  $_POST['brand'],
      (int)  $_POST['provider'],
      "-"
    ];
    echo handleProcedureAction($db, 'pz_agregarPieza', $params);
    break;

  /**
     * Editar pieza existente
     */
  case "editar_pieza":
    $params = [
      (int) $_POST['piece_id'],
      (int) $_POST['warehouse'],
      $_POST['piece_code'],
      $_POST['name'],
      (int)  $_POST['price_in'] ?? 0,
      (int) $_POST['price_out'],
      (int) $_POST['quantity'],
      (int)$_POST['min_quantity'] ?? 0,
      (int)$_POST['category'],
      (int)$_POST['position'],
      (int) $_POST['offer'] ?? 0,
      (int)  $_POST['brand'],
      (int)  $_POST['provider'],
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
    echo handleDeletionAction($db,(int)$_POST['pieza_id'], 'pz_eliminarPieza');
    break;

  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
