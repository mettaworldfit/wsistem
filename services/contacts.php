<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();
$db = Database::connect();
$action = $_POST['action'] ?? null;
$user_id = $_SESSION['identity']->usuario_id ?? null;

try {
  switch ($action) {
    // Mostrar index de todos lo proveedores
    case 'index_proveedores':

      handleDataTableRequest($db, [
        'columns' => [
          'nombre_proveedor',
          'apellidos',
          'direccion',
          'email',
          'telefono1',
          'fecha'
        ],
        'searchable' => [
          'nombre_proveedor',
          'apellidos',
          'direccion',
          'email',
          'telefono1'
        ],
        'base_table' => 'proveedores',
        'table_with_joins' => 'proveedores',
        'select' => ' SELECT nombre_proveedor, apellidos, direccion, email, telefono1, fecha, proveedor_id',
        'table_rows' => function ($row) {
          return [
            'id' => '<span class="text-success hide-cell">' . $row['proveedor_id'] . '</span>',
            'nombre'    => ucwords($row['nombre_proveedor'] . ' ' . $row['apellidos']),
            'correo'    => '<span class="text-success hide-cell">' . $row['email'] . '</span>',
            'telefono'  => formatTel($row['telefono1'] ?? ''),
            'fecha'     => $row['fecha'],

            'acciones' => '<a class="btn-action action-info" href="' . base_url . 'contacts/edit_provider&id=' . $row['proveedor_id'] . '" title="Editar">
                     ' . BUTTON_EDIT . '
                     </a>
           
                 <span class="btn-action action-danger" onclick="deleteProveedor(\'' . $row['proveedor_id'] . '\')"  title="Eliminar">' . BUTTON_DELETE . '</span>'

          ];
        }
      ]);

      break;

    // Mostrar index de todos lo clientes
    case 'index_clientes':

      handleDataTableRequest($db, [
        'columns' => [
          'c.nombre',
          'c.apellidos',
          'c.cedula',
          'c.telefono1',
          'c.fecha',
          'c.cliente_id',
          'c.direccion'
        ],
        'searchable' => [
          'c.nombre',
          'c.apellidos',
          'c.cedula',
          'c.telefono1',
          'c.direccion'
        ],
        'base_table' => 'clientes',
        'table_with_joins' => 'clientes c',
        'select' => 'SELECT c.nombre, c.apellidos, c.cedula, c.telefono1, c.fecha, c.cliente_id, c.direccion',
        'table_rows' => function ($row) {
          return [
            'id' => $row['cliente_id'],
            'nombre' => ucwords($row['nombre'] . ' ' . $row['apellidos']),
            'cedula' => '<span class="hide-cell">' . $row['cedula'] . '</span>',
            'telefono' => formatTel($row['telefono1'] ?? ''),
            'fecha' => $row['fecha'],
            'direccion' => '<span class="hide-cell">' . $row['direccion'] . '</span>',
            'acciones' => '<a class="btn-action action-warning" href="' . base_url . 'contacts/customer_history&id=' . $row['cliente_id'] . '" title="Ficha del cliente">
                        ' . BUTTON_CLIENT . '
                      </a>
                      <a class="btn-action action-info" href="' . base_url . 'contacts/edit_customer&id=' . $row['cliente_id'] . '" title="Editar">
                        ' . BUTTON_EDIT . '
                      </a>
                      <span class="btn-action action-danger" onclick="deleteCustomer(\'' . $row['cliente_id'] . '\')" title="Eliminar">
                      ' . BUTTON_DELETE . '
                      </span>'
          ];
        }
      ]);

      break;

    case 'historial_cliente':

      $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

      $sqlHistorial = "SELECT 
        concat(c.nombre,' ',IFNULL(c.apellidos,'')) AS nombre,
        concat('FT-00',fv.factura_venta_id) AS factura_id,
        'PRODUCTO' AS tipo_item,
        p.nombre_producto AS item,
        dv.cantidad,
        dv.precio,
        dv.descuento,
        (dv.cantidad * dv.precio) - dv.descuento AS total,
        fv.fecha
    FROM clientes c
    JOIN facturas_ventas fv ON c.cliente_id = fv.cliente_id AND fv.estado_id = 3
    JOIN detalle_facturas_ventas dv ON fv.factura_venta_id = dv.factura_venta_id
    LEFT JOIN detalle_ventas_con_productos dvp ON dv.detalle_venta_id = dvp.detalle_venta_id
    LEFT JOIN productos p ON dvp.producto_id = p.producto_id
    WHERE c.cliente_id = '$id' AND p.nombre_producto IS NOT NULL

    UNION ALL

    SELECT 
        concat(c.nombre,' ',IFNULL(c.apellidos,'')) AS nombre,
        concat('FT-00',fv.factura_venta_id) AS factura_id,
        'PIEZA' AS tipo_item,
        pi.nombre_pieza AS item,
        dv.cantidad,
        dv.precio,
        dv.descuento,
        (dv.cantidad * dv.precio) - dv.descuento AS total,
        fv.fecha
    FROM clientes c
    JOIN facturas_ventas fv ON c.cliente_id = fv.cliente_id AND fv.estado_id = 3
    JOIN detalle_facturas_ventas dv ON fv.factura_venta_id = dv.factura_venta_id
    LEFT JOIN detalle_ventas_con_piezas_ dvpi ON dv.detalle_venta_id = dvpi.detalle_venta_id
    LEFT JOIN piezas pi ON dvpi.pieza_id = pi.pieza_id
    WHERE c.cliente_id = '$id' AND pi.nombre_pieza IS NOT NULL

    UNION ALL

    SELECT 
        concat(c.nombre,' ',IFNULL(c.apellidos,'')) AS nombre,
        concat('FT-00',fv.factura_venta_id) AS factura_id,
        'SERVICIO' AS tipo_item,
        s.nombre_servicio AS item,
        dv.cantidad,
        dv.precio,
        dv.descuento,
        (dv.cantidad * dv.precio) - dv.descuento AS total,
        fv.fecha
    FROM clientes c
    JOIN facturas_ventas fv ON c.cliente_id = fv.cliente_id AND fv.estado_id = 3
    JOIN detalle_facturas_ventas dv ON fv.factura_venta_id = dv.factura_venta_id
    LEFT JOIN detalle_ventas_con_servicios dvs ON dv.detalle_venta_id = dvs.detalle_venta_id
    LEFT JOIN servicios s ON dvs.servicio_id = s.servicio_id
    WHERE c.cliente_id = '$id' AND s.nombre_servicio IS NOT NULL

    UNION ALL

    SELECT 
        concat(c.nombre,' ',IFNULL(c.apellidos,'')) AS nombre,
        concat('RP-00',fr.facturaRP_id) AS factura_id,
        'PIEZA' AS tipo_item,
        pi.nombre_pieza AS item,
        dor.cantidad,
        dor.precio,
        dor.descuento,
        (dor.cantidad * dor.precio) - dor.descuento AS total,
        fr.fecha
    FROM clientes c
    JOIN facturasRP fr ON c.cliente_id = fr.cliente_id AND fr.estado_id = 3
    JOIN ordenes_rp o ON fr.orden_rp_id = o.orden_rp_id
    JOIN detalle_ordenRP dor ON o.orden_rp_id = dor.orden_rp_id
    LEFT JOIN detalle_ordenRP_con_piezas dorp ON dor.detalle_ordenRP_id = dorp.detalle_ordenRP_id
    LEFT JOIN piezas pi ON dorp.pieza_id = pi.pieza_id
    WHERE c.cliente_id = '$id' AND pi.nombre_pieza IS NOT NULL

    UNION ALL

    SELECT 
        concat(c.nombre,' ',IFNULL(c.apellidos,'')) AS nombre,
        concat('RP-00',fr.facturaRP_id) AS factura_id,
        'SERVICIO' AS tipo_item,
        s.nombre_servicio AS item,
        dor.cantidad,
        dor.precio,
        dor.descuento,
        (dor.cantidad * dor.precio) - dor.descuento AS total,
        fr.fecha
    FROM clientes c
    JOIN facturasRP fr ON c.cliente_id = fr.cliente_id AND fr.estado_id = 3
    JOIN ordenes_rp o ON fr.orden_rp_id = o.orden_rp_id
    JOIN detalle_ordenRP dor ON o.orden_rp_id = dor.orden_rp_id
    LEFT JOIN detalle_ordenRP_con_servicios dors ON dor.detalle_ordenRP_id = dors.detalle_ordenRP_id
    LEFT JOIN servicios s ON dors.servicio_id = s.servicio_id
    WHERE c.cliente_id = '$id' AND s.nombre_servicio IS NOT NULL";


      handleDataTableRequest($db, [

        'columns' => ['factura_id', 'item', 'cantidad', 'precio', 'descuento', 'total', 'fecha'],
        'searchable' => ['factura_id', 'item', 'fecha'],
        'base_table' => "({$sqlHistorial}) as historial",
        'table_with_joins' => "({$sqlHistorial}) as historial",
        'select' => 'SELECT factura_id, item, cantidad, precio, descuento, total, fecha',
        'table_rows' => function ($row) {
          return [
            'factura_id' => $row['factura_id'],
            'item' => $row['item'],
            'cantidad' => $row['cantidad'],
            'precio' => number_format($row['precio'], 2),
            'descuento' => number_format($row['descuento'], 2),
            'total' => number_format($row['total'], 2),
            'fecha' => $row['fecha']
          ];
        }
      ]);


      break;

    // Crear cliente o proveedor
    case 'crear_contacto':
      $type = $_POST['type'];

      $params = [
        (int)$user_id,
        $_POST['name'],
        $_POST['lastname'] ?? "",
        $type === 'cliente' ? ($_POST['identity'] ?? "") : null,
        $_POST['tel1'] ?? 0,
        $_POST['tel2'] ?? 0,
        $_POST['address'] ?? "",
        $_POST['email'] ?? ""
      ];

      if ($type === 'proveedor') {
        unset($params[3]); // quitar identity
        $params = array_values($params);
      }

      $procedure = $type === 'cliente' ? 'cl_agregar_cliente' : 'pv_agregarProveedor';
      echo handleProcedureAction($db, $procedure, $params);
      //  echo json_encode(["data"=>$params]);
      break;

    // Actualizar cliente o proveedor
    case 'actualizar_cliente':
    case 'actualizar_proveedor':
      $isCliente = $action === 'actualizar_cliente';
      $procedure = $isCliente ? 'cl_actualizarCliente' : 'pv_actualizarProveedor';

      $params = [
        $_POST['id'],
        $_POST['name'],
        $_POST['lastname'] ?? "",
        $isCliente ? ($_POST['identity'] ?? "") : null,
        $_POST['tel1'] ?? 0,
        $_POST['tel2'] ?? 0,
        $_POST['email'] ?? "",
        $_POST['address'] ?? ""
      ];

      if (!$isCliente) {
        unset($params[3]); // quitar identity
        $params = array_values($params);
      }

      echo handleProcedureAction($db, $procedure, $params);
      break;

    case 'obtener_clientes':

      $q = isset($_POST['q']) ? $_POST['q'] : '';  // Parámetro de búsqueda

      // Preparar la consulta para prevenir inyecciones SQL
      $sql = "SELECT cliente_id, nombre, apellidos FROM clientes WHERE nombre LIKE ? OR apellidos LIKE ? LIMIT 15";
      $stmt = $db->prepare($sql);

      // Parametrizar la búsqueda para evitar inyecciones SQL
      $searchTerm = "%$q%";
      $stmt->bind_param('ss', $searchTerm, $searchTerm);

      // Ejecutar la consulta
      $stmt->execute();
      $result = $stmt->get_result();

      // Recoger los clientes
      $clientes = [];
      while ($row = $result->fetch_assoc()) {
        $clientes[] = [
          'id' => $row['cliente_id'],
          'nombre' => ucwords($row['nombre']),
          'apellidos' => ucwords($row['apellidos'] ?? '')
        ];
      }

      // Devolver los resultados en formato JSON
      echo json_encode([
        'results' => $clientes
      ]);

      break;

    case 'obtener_cliente_por_id':

      $id = intval($_POST['id']);

      $stmt = $db->prepare("SELECT cliente_id, nombre, apellidos FROM clientes WHERE cliente_id = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $res = $stmt->get_result();

      if ($row = $res->fetch_assoc()) {
        echo json_encode([
          'id' => $row['cliente_id'],
          'text' => $row['nombre'] . ($row['apellidos'] ? ' ' . $row['apellidos'] : '')
        ]);
      } else {
        echo json_encode(null);
      }
      exit;

      break;

    // Eliminar cliente, proveedor o bono
    case 'eliminar_cliente':
    case 'eliminar_proveedor':
    case 'eliminar_bono':
      $deleteMap = [
        'eliminar_cliente' => ['cl_eliminarCliente', $_POST['customer_id'] ?? null],
        'eliminar_proveedor' => ['pv_eliminarProveedor', $_POST['proveedor_id'] ?? null],
        'eliminar_bono' => ['cl_eliminarBono', $_POST['bond_id'] ?? null]
      ];

      [$procedure, $id] = $deleteMap[$action];

      if (!$id) {
        throw new Exception("ID no proporcionado para la acción: $action");
      }

      echo handleDeletionAction($db, $id, $procedure);
      break;

    default:
      echo "Acción no reconocida.";
      break;
  }
} catch (Exception $e) {
  // Devolver respuesta de error genérica
  http_response_code(500);
  echo json_encode([
    'Error' => 'Ha ocurrido un error inesperado.'
  ]);
}
