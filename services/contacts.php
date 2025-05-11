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

        handleDataTableRequest($db,[
          'columns' => [
           'nombre_proveedor','apellidos','direccion',
           'email','telefono1','fecha'
           ],
          'searchable' => [
           'nombre_proveedor','apellidos','direccion','email','telefono1'
          ],
          'base_table' => 'proveedores',
          'table_with_joins' => 'proveedores',
          'select' => ' SELECT nombre_proveedor, apellidos, direccion, email, telefono1, fecha, proveedor_id',
          'table_rows' => function ($row) {
             return [
                 'id' => $row['proveedor_id'],
                 'nombre'    => ucwords($row['nombre_proveedor'] . ' ' . $row['apellidos']),
                 'correo'     => $row['email'],
                 'telefono'  => formatTel($row['telefono1'] ?? ''),
                 'fecha'     => $row['fecha'],
           
                 'acciones' => '<a class="action-edit" href="' . base_url . 'contacts/edit_provider&id=' . $row['proveedor_id'] . '" title="Editar">
                     <i class="fas fa-pencil-alt"></i>
                     </a>
           
                 <span class="action-delete" onclick="deleteProveedor(\'' . $row['proveedor_id'] . '\')"  title="Eliminar"><i class="fas fa-times"></i></span>'
           
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
              'cedula' => $row['cedula'],
              'telefono' => formatTel($row['telefono1'] ?? ''),
              'fecha' => $row['fecha'],
              'direccion' => $row['direccion'],
              'acciones' => '<a class="action-edit" href="' . base_url . 'contacts/edit_customer&id=' . $row['cliente_id'] . '" title="Editar">
                                <i class="fas fa-pencil-alt"></i>
                              </a>
            
                              <span class="action-delete" onclick="deleteCustomer(\'' . $row['cliente_id'] . '\')"  title="Eliminar"><i class="fas fa-times"></i></span>'
            ];
          }
        ]);

        break;

      // Crear cliente o proveedor
      case 'crear_contacto':
          $type = $_POST['type'];

          $params = [
              $user_id,
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

          $procedure = $type === 'cliente' ? 'cl_agregarCliente' : 'pv_agregarProveedor';
          echo handleProcedureAction($db, $procedure, $params);
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
  // Registrar error en archivo log
  $errorLog = "[" . date('Y-m-d H:i:s') . "] Acción: $action | Error: " . $e->getMessage() . "\n";
  file_put_contents('./error_log.txt', $errorLog, FILE_APPEND);

  // Devolver respuesta de error genérica
  http_response_code(500);
  echo json_encode([
      'status' => 'error',
      'message' => 'Ha ocurrido un error inesperado. Por favor, inténtalo más tarde.'
  ]);
}
