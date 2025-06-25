<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

$db = Database::connect();
$action = $_POST['action'] ?? '';

switch ($action) {

  // Cargar tabla de servicios
  case 'index_servicios':
    handleDataTableRequest($db, [
      'columns' => ['nombre_servicio', 'precio', 'servicio_id'],
      'searchable' => ['nombre_servicio', 'precio'],
      'base_table' => 'servicios',
      'table_with_joins' => 'servicios',
      'select' => 'SELECT servicio_id, nombre_servicio,costo ,precio',
      'table_rows' => function ($row) {

        $acciones = '<td>';
        // Editar
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<a href="' . base_url . 'services/edit&id=' . $row['servicio_id'] . '">
                  <span class="action-edit"><i class="fas fa-pencil-alt"></i></span>
                </a>';
        } else {
          $acciones .= '<a href="#">
                  <span class="action-edit action-disable"><i class="fas fa-pencil-alt"></i></span>
                </a>';
        }

        // Eliminar
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<span class="action-delete" onclick="deleteService(\'' . $row['servicio_id'] . '\')" title="Eliminar">
                  <i class="fas fa-times"></i>
                </span>';
        } else {
          $acciones .= '<span class="action-delete action-disable" title="Eliminar">
                  <i class="fas fa-times"></i>
                </span>';
        }
 
        $acciones .= '</td>';

        return [
          'servicio_id' => "<td>{$row['servicio_id']}</td>",
          'nombre_servicio' => "<td>" . htmlspecialchars($row['nombre_servicio']) . "</td>",
          'costo' => "<td>" . number_format($row['costo'] ?? 0, 2) . "</td>",
          'precio' => "<td>" . number_format($row['precio'] ?? 0, 2) . "</td>",
          'acciones' => $acciones
        ];
      }
    ]);
    break;

  // Buscar servicio por ID
  case 'buscar_servicios':
    $id = $_POST['service_id'];
    jsonQueryResult($db, "SELECT * FROM servicios WHERE servicio_id = '$id'");
    break;

  // Agregar servicio
  case 'agregar_servicio':
    $params = [
      $_SESSION['identity']->usuario_id,
      $_POST['name'] ?? '',
      $_POST['cost'] ?? 0,
      $_POST['price'] ?? 0
    ];
    echo handleProcedureAction($db, 'sv_agregarServicio', $params);
    break;

  // Actualizar servicio
  case 'actualizar_servicio':
    $params = [
      $_POST['service_id'],
      $_POST['name'] ?? '',
      $_POST['cost'] ?? 0,
      $_POST['price'] ?? 0
    ];
    echo handleProcedureAction($db, 'sv_actualizarServicio', $params);
    break;

  // Eliminar servicio
  case 'eliminar_servicio':
    echo handleProcedureAction($db, 'sv_eliminarServicio', [(int)$_POST['service_id']]);
    break;
}
