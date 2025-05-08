<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
session_start();

function formatTel($numero) {
  // Eliminar todo lo que no sea dígito
   // Asegura que $numero no sea null, sino una cadena vacía si lo es
   $numero = preg_replace('/\D/', '', $numero ?? '');

  // Si tiene más de 10 dígitos, separar el código de país
  if (strlen($numero) > 10) {
      $codigoPais = substr($numero, 0, strlen($numero) - 10);
      $numeroLocal = substr($numero, -10);
      return '+' . $codigoPais . ' (' .
          substr($numeroLocal, 0, 3) . ') ' .
          substr($numeroLocal, 3, 3) . '-' .
          substr($numeroLocal, 6);
  } elseif (strlen($numero) === 10) {
      // Sin código de país
      return '(' . substr($numero, 0, 3) . ') ' .
             substr($numero, 3, 3) . '-' .
             substr($numero, 6);
  } else {
      return $numero; // No formatear si tiene menos de 10 dígitos
  }
};

// Mostrar index de todos lo clientes

if ($_POST['action'] == "index_clientes") {

  // Conexión a la base de datos
  $db = Database::connect();

  // Parámetros enviados por DataTables
  $draw = intval($_POST['draw'] ?? 0);
  $start = intval($_POST['start'] ?? 0);
  $length = intval($_POST['length'] ?? 10);
  $searchValue = $_POST['search']['value'] ?? '';

  // Columnas que se pueden ordenar desde DataTables
  $columns = [
    'nombre',
    'apellidos',
    'cedula',
    'telefono1',
    'fecha',
    'cliente_id',
    'direccion'
  ];

  // Definición de columna de ordenamiento
  $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
  $orderColumn = $columns[$orderColumnIndex] ?? 'nombre';
  $orderDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

  // Filtro de búsqueda general
  $searchQuery = '';
  if (!empty($searchValue)) {
    $searchEscaped = $db->real_escape_string($searchValue);
    $searchQuery = " WHERE (
    nombre LIKE '%$searchEscaped%' OR
    apellidos LIKE '%$searchEscaped%' OR
    cedula LIKE '%$searchEscaped%' OR
    telefono1 LIKE '%$searchEscaped%' OR
    direccion LIKE '%$searchEscaped%'
  )";
  }

  // Total de registros sin filtros
  $totalResult = $db->query("SELECT COUNT(*) AS total FROM clientes");
  $totalRecords = $totalResult->fetch_assoc()['total'] ?? 0;

  // Total de registros después de aplicar el filtro de búsqueda
  $countFiltered = $db->query("SELECT COUNT(*) AS total FROM clientes $searchQuery");
  $filteredRecords = $countFiltered->fetch_assoc()['total'] ?? 0;

  // Consulta para obtener los datos filtrados y ordenados
  $query = "SELECT nombre, apellidos, cedula, telefono1, fecha, cliente_id, direccion
          FROM clientes
          $searchQuery
          ORDER BY $orderColumn $orderDir
          LIMIT $start, $length";

  $result = $db->query($query);

  // Construcción del arreglo de respuesta con formato HTML opcional
  $data = [];
  while ($row = $result->fetch_assoc()) {
    $data[] = [
      'id' => $row['cliente_id'],
      'nombre' => ucwords($row['nombre'] . ' ' . $row['apellidos']),
      'cedula' => $row['cedula'],
      'telefono' => formatTel($row['telefono1']),
      'fecha' => $row['fecha'],
      'direccion' => $row['direccion'],
      'acciones' => '<a class="action-edit" href="' . base_url . 'contacts/edit_customer&id=' . $row['cliente_id'] . '" title="Editar">
                    <i class="fas fa-pencil-alt"></i>
                   </a>

                   <span class="action-delete" onclick="deleteCustomer(\'' . $row['cliente_id'] . '\')"  title="Eliminar"><i class="fas fa-times"></i></span>'
    ];
  }
    

  // Devolver los datos en formato JSON como lo espera DataTables
  echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
  ]);
  exit;
}

// Agregar cliente/proveedor

if ($_POST['action'] == 'crear_contacto') {

  $user_id = $_SESSION['identity']->usuario_id;
  $name = $_POST['name'];
  $lastname = (!empty($_POST['lastname'])) ? $_POST['lastname'] : "";
  $identity = (!empty($_POST['identity'])) ? $_POST['identity'] : "";
  $email = (!empty($_POST['email'])) ? $_POST['email'] : "";
  $tel1 = (!empty($_POST['tel1'])) ? $_POST['tel1'] : 0;
  $tel2 = (!empty($_POST['tel2'])) ? $_POST['tel2'] : 0;
  $address = (!empty($_POST['address'])) ? $_POST['address'] : "";
  $type = $_POST['type'];


  function add_contact($query)
  {
    $db = Database::connect();

    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg, 'Duplicate')) {

      echo "duplicate";
    } else if (str_contains($data->msg, 'SQL')) {

      echo "Error 101: " . $data->msg;
    }
  }

  if ($type == 'cliente') {

    $query = "CALL cl_agregarCliente($user_id,'$name','$lastname','$identity',$tel1,$tel2,'$address','$email')";
    add_contact($query);
  } else if ($type == 'proveedor') {

    $query = "CALL pv_agregarProveedor($user_id,'$name','$lastname',$tel1,$tel2,'$address','$email')";
    add_contact($query);
  }
}

// Actualizar cliente

if ($_POST['action'] == 'actualizar_cliente') {

  $id = $_POST['id'];
  $name = $_POST['name'];
  $lastname = (!empty($_POST['lastname'])) ? $_POST['lastname'] : "";
  $identity = (!empty($_POST['identity'])) ? $_POST['identity'] : "";
  $email = (!empty($_POST['email'])) ? $_POST['email'] : "";
  $tel1 = (!empty($_POST['tel1'])) ? $_POST['tel1'] : 0;
  $tel2 = (!empty($_POST['tel2'])) ? $_POST['tel2'] : 0;
  $address = (!empty($_POST['address'])) ? $_POST['address'] : "";

  $db = Database::connect();

  $query = "CALL cl_actualizarCliente($id,'$name','$lastname','$identity','$tel1','$tel2','$email','$address')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'Duplicate')) {

    echo "duplicate";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 102: " . $data->msg;
  }
}

// Eliminar cliente 

if ($_POST['action'] == 'eliminar_cliente') {

  $customer_id = $_POST['customer_id'];
  $db = Database::connect();

  $query = "CALL cl_eliminarCliente($customer_id)";
  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error: " . $db->error;
  }
}

// Actualizar proveedor

if ($_POST['action'] == 'actualizar_proveedor') {

  $id = $_POST['id'];
  $name = $_POST['name'];
  $lastname = (!empty($_POST['lastname'])) ? $_POST['lastname'] : "";
  $email = (!empty($_POST['email'])) ? $_POST['email'] : "";
  $tel1 = (!empty($_POST['tel1'])) ? $_POST['tel1'] : 0;
  $tel2 = (!empty($_POST['tel2'])) ? $_POST['tel2'] : 0;
  $address = (!empty($_POST['address'])) ? $_POST['address'] : "";

  $db = Database::connect();

  $query = "CALL pv_actualizarProveedor($id,'$name','$lastname','$tel1','$tel2','$email','$address')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error 104: " . $data->msg;
  }
}

// Eliminar proveedor 

if ($_POST['action'] == 'eliminar_proveedor') {

  $proveedor_id = $_POST['proveedor_id'];
  $db = Database::connect();

  $query = "CALL pv_eliminarProveedor($proveedor_id)";
  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error 105: " . $db->error;
  }
}

// Eliminar bono 

if ($_POST['action'] == 'eliminar_bono') {

  $bond_id = $_POST['bond_id'];
  $db = Database::connect();

  $query = "CALL cl_eliminarBono($bond_id)";
  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error 106: " . $db->error;
  }
}
