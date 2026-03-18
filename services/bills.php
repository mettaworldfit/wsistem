<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
require_once '../help.php';
session_start();
$db = Database::connect();
$config = Database::getConfig();
$action = $_POST['action'] ?? null;
$user_id = $_SESSION['identity']->usuario_id;

$permissions = [

  // Dashboard / estadísticas
  'index_gastos' => [],
  'crear_orden_gasto' => [],
  'crear_orden_gasto' => [],
  'detalle_gasto' => [],
  'registrar_gasto' => [],
  'eliminar_gasto' => [],
  'agregar_motivo' => [],
  'lista_gastos' => [],
  'datos_impresion' => []
];

// Chequear permisos
if (isset($_POST['action'])) {
  check_permission_action($_POST['action'], $permissions);
}

switch ($action) {
  // Mostrar gastos
  case 'index_gastos':

    handleDataTableRequest($db, [
      'columns' => [
        'g.fecha',
        'g.gasto_id',
        'p.nombre_proveedor',
        'p.apellidos',
        'g.total',
        'g.pagado',
        'g.orden_id'
      ],
      'searchable' => [
        'p.nombre_proveedor',
        'p.apellidos',
        'g.total',
        'g.pagado',
        'g.fecha'
      ],
      'base_table' => 'gastos',
      'table_with_joins' => 'gastos g 
          INNER JOIN proveedores p ON p.proveedor_id = g.proveedor_id 
          INNER JOIN usuarios u ON u.usuario_id = g.usuario_id',
      'select' => 'SELECT g.gasto_id, p.nombre_proveedor, g.total, g.pagado, g.orden_id, g.fecha, p.apellidos',
      'table_rows' => function ($element) {
        return [
          'id'    => 'G-00' . $element['gasto_id'],
          'proveedor' => '<span class="hide-cell">' . ucwords($element['nombre_proveedor'] . ' ' . $element['apellidos']) . '</span>',
          'gastos'    => Help::loadSpendingsById($element['orden_id']),
          'fecha'     => $element['fecha'],
          'total'     => '<span class="text-primary">' . number_format($element['total'], 2) . '</span>',
          'pagado'    => '<span class="text-success hide-cell">' . number_format($element['pagado'], 2) . '</span>',
          'acciones'  => '<span class="action-danger btn-action delete_bill" data-id="' . $element['orden_id'] . '">' . BUTTON_DELETE . '</span>'
        ];
      }

    ]);

    break;
  // Crear orden de gasto
  case 'crear_orden_gasto':
    $params = [
      (int)$_POST['provider_id'],
      (int)$_SESSION['identity']->usuario_id,
      $_POST['origin'],
      $_POST['date']
    ];

    echo handleProcedureAction($db, 'or_ordenGasto', $params);
    break;
  // Agregar detalle gastos
  case 'detalle_gasto':

    $reason_id = $_POST['reason_id'] ?? 0;
    $value = $_POST['value'] ?? 0;
    $taxes = $_POST['taxes'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
    $order_id = $_POST['order_id'] ?? 0;
    $observation = $_POST['observation'] ?? '';
    $user_id = $_SESSION['identity']->usuario_id;

    if (!$order_id) {
      echo "Error: order_id vacío";
      exit;
    }

    $db = Database::connect();

    $query = "CALL or_detalleGasto($reason_id,$order_id,$quantity,$value,'$taxes',$user_id,'$observation')";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {

      echo "Error : " . $data->msg;
    }

    break;

  // Registrar gasto
  case 'registrar_gasto':

    $provider_id = $_POST['provider_id'];
    $total = $_POST['total_invoice'];
    $order_id = $_POST['order_id'];
    $observation = $_POST['observation'];
    $user_id = $_SESSION['identity']->usuario_id;
    $date = $_POST['date'];

    $db = Database::connect();

    $query = "CALL or_registrarGasto($provider_id,$order_id,'$total',$user_id,'$observation','$date')";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {

      echo "Error : " . $data->msg;
    }
    break;

  // Eliminar gasto
  case 'eliminar_gasto':

    $id = $_POST['id'];

    $db = Database::connect();

    $query = "CALL or_eliminarGasto($id)";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {

      echo "Error : " . $data->msg;
    }
    break;

  case 'agregar_motivo':

    $params = [
      (int)$_SESSION['identity']->usuario_id,
      $_POST['description']
    ];

    echo handleProcedureAction($db, 'g_agregar_motivo', $params);
    break;


  // Obtener lista de gastos
  case 'lista_gastos':

    $q = isset($_POST['q']) ? $_POST['q'] : '';

    if ($q == '') {
      $sql = "SELECT motivo_id, descripcion FROM motivos ORDER BY descripcion ASC";
      $stmt = $db->prepare($sql);
    } else {
      $sql = "SELECT motivo_id, descripcion 
                FROM motivos 
                WHERE descripcion LIKE ? ORDER BY descripcion ASC";
      $stmt = $db->prepare($sql);

      $searchTerm = "%$q%";
      $stmt->bind_param('s', $searchTerm);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $gasto = [];

    while ($row = $result->fetch_assoc()) {
      $gasto[] = [
        'id' => $row['motivo_id'],
        'nombre' => ucwords($row['descripcion'])
      ];
    }

    echo json_encode([
      'results' => $gasto
    ]);
    break;
  case 'datos_impresion':

    $id = isset($_POST['id']) ? $_POST['id'] : '';

    $sql = "SELECT concat(u.nombre,' ',IFNULL(u.apellidos,'')) as vendedor,
        concat(p.nombre_proveedor,' ',IFNULL(p.apellidos,'')) as proveedor,
        g.orden_id,g.total,g.pagado,g.observacion, concat(g.fecha,' ',g.hora) as fecha
        FROM gastos g 
        INNER JOIN proveedores p ON p.proveedor_id = g.proveedor_id
        INNER JOIN usuarios u ON u.usuario_id = g.usuario_id
        WHERE g.orden_id = $id;
        
        SELECT d.orden_id, m.descripcion, d.cantidad, d.impuestos, d.precio 
        FROM detalle_gasto d
	      INNER JOIN motivos m ON m.motivo_id = d.motivo_id
        WHERE d.orden_id = $id";


    jsonMultiQueryResult($db, $sql);
    break;
}
