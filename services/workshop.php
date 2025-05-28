<?php

require_once '../config/db.php';
require_once '../help.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

// Conectar a la base de datos
$db = Database::connect();
$action = $_POST['action'] ?? '';

switch ($action) {
  
  // Cargar listado de órdenes de reparación con detalles relacionados
  case 'index_taller':
    handleDataTableRequest($db, [
      'columns' => [
        'o.orden_rp_id',
        'f.facturaRP_id',
        'c.nombre',
        'c.apellidos',
        'o.fecha_entrada',
        'o.fecha_salida',
        'e.nombre_modelo',
        'm.nombre_marca',
        'e.modelo',
        'o.imei',
        'o.serie',
        'es.nombre_estado'
      ],
      'searchable' => [
        'c.nombre',
        'c.apellidos',
        'o.orden_rp_id',
        'o.imei',
        'o.serie',
        'o.fecha_entrada',
        'o.fecha_salida',
        'e.nombre_modelo',
        'm.nombre_marca',
        'es.nombre_estado'
      ],
      'base_table' => 'ordenes_rp',
      'table_with_joins' => "ordenes_rp o
        INNER JOIN clientes c ON c.cliente_id = o.cliente_id
        INNER JOIN estados_generales es ON es.estado_id = o.estado_id
        INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
        INNER JOIN equipos e ON e.equipo_id = o.equipo_id
        INNER JOIN marcas m ON m.marca_id = e.marca_id
        LEFT JOIN facturasRP f ON o.orden_rp_id = f.orden_rp_id",
      'select' => "SELECT o.orden_rp_id as id, f.facturaRP_id, c.cliente_id,
        c.nombre as nombre_cliente, c.apellidos as apellidos_cliente,
        o.fecha_entrada, o.fecha_salida, e.nombre_modelo,
        m.nombre_marca, e.modelo, o.imei, o.serie,
        es.nombre_estado, es.estado_id",
      // Configuración del contenido de cada fila de la tabla
      'table_rows' => function ($row) {
        return [
          'orden' => '<span><a href="#" class="' .
            ($row['facturaRP_id'] > 0 ? 'text-secondary' : 'text-danger') . '">OR-00' . $row['id'] . '</a>' .
            '<span id="toggle" class="toggle-right toggle-md">No. Orden: OR-00' . $row['id'] . '<br>' .
            'No. Factura: ' . ($row['facturaRP_id'] > 0
              ? '<a class="text-danger" href="' . base_url . 'invoices/repair_edit&o=' . $row['id'] . '&f=' . $row['facturaRP_id'] . '">RP-00' . $row['facturaRP_id'] . '</a>'
              : '<a class="text-danger" href="#">No facturado</a>') . '</span></span>',

          'nombre' => ucwords($row['nombre_cliente'] . ' ' . $row['apellidos_cliente']),

          'equipo' => '<span><a href="#" class="text-secondary">' .
            ucwords($row['nombre_marca'] . ' ' . $row['nombre_modelo'] . ' ' . $row['modelo']) . '</a>' .
            '<span id="toggle" class="toggle-right toggle-xl">' .
            'Marca: ' . $row['nombre_marca'] . '<br>Modelo: ' . $row['modelo'] . '<br>' .
            'IMEI: ' . $row['imei'] . '<br>Serie: ' . $row['serie'] . '</span></span>',

          'fecha_entrada' => '<span class="text-success hide-cell">' . $row['fecha_entrada'] . '</span>',
          'fecha_salida' => '<span class="text-danger hide-cell">' . $row['fecha_salida'] . '</span>',
          'condicion' => Help::SHOW_CONDITONS_ORDER($row['id']),

          'estado' => '<select class="form-custom ' . $row['nombre_estado'] . '" id="status_rp" onchange="updateOrderStatus(this);">'
            . '<option workshop_id="' . $row['id'] . '" value="' . $row['estado_id'] . '" selected>' . $row['nombre_estado'] . '</option>' .
            '<option class="Pendiente" workshop_id="' . $row['id'] . '" value="6">Pendiente</option>' .
            '<option class="En Proceso" workshop_id="' . $row['id'] . '" value="8">En Proceso</option>' .
            '<option class="Entregado" workshop_id="' . $row['id'] . '" value="7">Entregado</option>' .
            '<option class="No se pudo" workshop_id="' . $row['id'] . '" value="10">No se pudo</option>' .
            '<option class="Listo" workshop_id="' . $row['id'] . '" value="9">Listo</option>' .
            '</select>',

          'acciones' => '<a href="' . base_url . 'invoices/addrepair&id=' . $row['id'] . '" class="action-edit" title="Agregar factura">'
            . '<i class="fas fa-shopping-cart"></i></a>' .
            '<span class="action-delete" onclick="deleteRepairOrder(\'' . $row['id'] . '\')" title="Eliminar">'
            . '<i class="fas fa-times"></i></span>'
        ];
      }
    ]);
    break;

  // Cargar todas las marcas
  case 'index_marcas':
    handleDataTableRequest($db, [
      'columns' => [
        'nombre_marca',
        'fecha',
        'marca_id'
      ],
      'searchable' => [
        'nombre_marca',
        'fecha',
      ],
      'base_table' => 'marcas',
      'table_with_joins' => 'marcas',
      'select' => 'SELECT nombre_marca,fecha,marca_id',
      'table_rows' => function ($row) {
        return [
          'nombre_marca' => ucwords($row['nombre_marca']),
          'fecha'        => $row['fecha'],
          'acciones'     => '
              <a class="action-edit" href="' . base_url . 'brands/edit&id=' . $row['marca_id'] . '" title="Editar">
                  <i class="fas fa-pencil-alt"></i>
              </a>
              <span class="action-delete" onclick="deleteBrand(\'' . $row['marca_id'] . '\')" title="Eliminar">
                  <i class="fas fa-times"></i>
              </span>
          '
        ];
      }
    ]);

    break;
  // Buscar datos del equipo por ID
  case 'buscar_equipo':
    jsonQueryResult($db, "SELECT d.modelo, m.nombre_marca FROM equipos d INNER JOIN marcas m ON d.marca_id = m.marca_id WHERE equipo_id = " . (int)$_POST['device_id']);
    break;

  // Crear una nueva orden de reparación
  case 'agregar_orden_reparacion':
    echo handleProcedureAction($db, 'rp_crearOrdenRP', [
      (int)$_SESSION['identity']->usuario_id,
      (int)$_POST['customer_id'],
      $_POST['device'],
      $_POST['serie'] ?? '',
      $_POST['imei'] ?? 0,
      $_POST['observation'] ?? ''
    ]);
    break;

  // Asignar una condición a una orden existente
  case 'asignar_condiciones':
    echo handleProcedureAction($db, 'rp_agregarCondiciones', [
      (int)$_POST['condition_id'],
      (int)$_POST['orden_id']
    ]);
    break;

  // Crear una nueva condición disponible para órdenes
  case 'crear_condicion':
    echo handleProcedureAction($db, 'rp_crearCondicion', [
      $_SESSION['identity']->usuario_id,
      $_POST['condition']
    ]);
    break;

  // Crear un nuevo equipo con marca, nombre y modelo
  case 'crear_equipo':
    echo handleProcedureAction($db, 'rp_crearEquipo', [
      (int)$_POST['brand'],
      $_POST['device'],
      $_POST['model']
    ]);
    break;

  // Actualizar el estado de una orden existente
  case 'actualizar_estado_orden':
    echo handleProcedureAction($db, 'rp_actualizarEstadoOrden', [
      (int)$_POST['status'],
      (int)$_POST['workshop_id']
    ]);
    break;

  // Eliminar una orden de reparación
  case 'eliminar_orden':
    echo handleDeletionAction($db, (int)$_POST['id'], 'rp_eliminarOrdenRP');
    break;

  // Eliminar una marca de equipos
  case 'eliminar_marca':
    echo handleDeletionAction($db, (int)$_POST['id'], 'm_eliminarMarca');
    break;

  // Crear una nueva marca
  case 'crear_marca':
    echo handleProcedureAction($db, 'm_crearMarca', [$_POST['name']]);
    break;

  // Actualizar nombre de una marca existente
  case 'actualizar_marca':
    echo handleProcedureAction($db, 'm_actualizarMarca', [
      $_POST['name'],
      (int)$_POST['id']
    ]);
    break;

  // Acción no reconocida
  default:
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
