<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

if ($_POST['action'] == "index_usuarios") {

  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => [
      'u.nombre',
      'u.apellidos',
      'u.usuario_id',
      'r.nombre_rol',
      's.nombre_estado',
      'u.fecha'
    ],
    'searchable' => [
      'u.nombre',
      'u.apellidos',
      'u.usuario_id',
      'r.nombre_rol',
      's.nombre_estado'
    ],
    'base_table' => 'usuarios u',
    'table_with_joins' => 'usuarios u
        INNER JOIN estados_generales s ON s.estado_id = u.estado_id
        INNER JOIN roles r ON r.rol_id = u.rol_id',
    'select' => 'SELECT u.nombre,u.apellidos,u.usuario_id,r.nombre_rol,s.nombre_estado,u.fecha',
    'table_rows' => function ($row) {

      $acciones = '<a ';
      if ($_SESSION['identity']->nombre_rol == 'administrador') {
        if ($row['nombre_estado'] == 'Activo') {
          $acciones .= 'class="btn-action action-info" href="' . base_url . 'users/edit&id=' . $row['usuario_id'] . '"';
        } else {
          $acciones .= 'class="btn-action action-info action-disable" href="#"';
        }
      } else {
        $acciones .= 'class="btn-action action-info action-disable" href="#"';
      }
      $acciones .= ' title="Editar">' . BUTTON_EDIT . '</a>';

      // Activar o desactivar usuario
      if ($_SESSION['identity']->nombre_rol == 'administrador') {
        if ($row['nombre_estado'] == 'Activo') {
          $acciones .= '<span class="btn-action action-success disabled_user" data-id="' . $row['usuario_id'] . '" title="Desactivar">' . BUTTON_ACTIVE . '</span>';
        } else {
          $acciones .= '<span class="btn-action action-danger enable_user" data-id="' . $row['usuario_id'] . '" title="Activar">' . BUTTON_DISABLE . '</span>';
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
        $acciones .= '<span class="btn-action action-danger erase_user" data-id="' . $row['usuario_id'] . '" 
         data-name="' . ucwords($row['nombre']) . ' ' . ucwords($row['apellidos']) . '" title="Eliminar">' . BUTTON_DELETE . '</span>';
      } else {
        $acciones .= '<span class="btn-action action-danger action-disable" title="Eliminar">' . BUTTON_DELETE . '</span>';
      }


      return [
        'usuario_id' => '<td class="hide-cell">' . $row['usuario_id'] . '</td>',
        'nombre' => '<td>' . ucwords($row['nombre']) . ' ' . ucwords($row['apellidos']) . '</td>',
        'rol' => '<td>' . $row['nombre_rol'] . '</td>',
        'estado' => '<td><p class="' . $row['nombre_estado'] . '">' . $row['nombre_estado'] . '</p></td>',
        'fecha' => '<td>' . $row['fecha'] . '</td>',
        'acciones' => $acciones
      ];
    }
  ]);
}


if ($_POST['action'] == "login") {

  // Verificar si existen las credenciales del usuario
  function verifyCredentials($passwordDB, $password, $data)
  {

    if ($passwordDB == $password) {

      $_SESSION['identity'] = $data;

      echo "approved";
    } else {
      echo 'denied';
    }
  }

  $db = Database::dbSelect($_POST['user']); // Verificar la base de datos del usuario

  $username = $db->real_escape_string($_POST['user']);
  $password = $_POST['password'];

  $query = "CALL us_verificarUsuario('$username')";
  $login = $db->query($query);

  if ($login && $login->num_rows == 1) {
    $userData = $login->fetch_object();

    // Verificar contraseña
    verifyCredentials($userData->password, $password, $userData);
  } else {
    printf("Error de conexión: %s\n", $db->connect_error);
    exit();
  }
}


// Cerrar sesión
if ($_POST['action'] == "logout") {

    // Limpiar las variables de sesión
    session_unset();

    // Destruir la sesión
    session_destroy();
    exit();
}


// Crear usuario
if ($_POST['action'] == "crear_usuario") {

  $db = Database::connect();

  $params = [
    (int)$_POST['role'],
    $_POST['name'],
    $_POST['lastname'],
    $_POST['username'],
    $_POST['password'],
  ];

    echo handleProcedureAction($db, "us_agregarUsuario", $params);
}

// Actualizar usuario

if ($_POST['action'] == "actualizar_usuario") {

  $params = [
    $_POST['role'],
    $_POST['name'],
    $_POST['lastname'],
    $_POST['password'],
    $_POST['user_id'],
  ];

  $db = Database::connect();

  echo handleProcedureAction($db, "us_actualizar_usuario", $params);
}

// Eliminar usuario
if ($_POST['action'] == "eliminar_usuario") {

  $db = Database::connect();

  echo handleDeletionAction($db, (int)$_POST['user_id'], 'us_eliminarUsuario');
}

// Desactivar usuario
if ($_POST['action'] == "desactivar_usuario") {
  $db = Database::connect();

  $params = [
    (int)$_POST['user_id'],
    'desactivar'
  ];

  echo handleProcedureAction($db, 'us_cambiarEstado', $params);
}

// Activar usuario
if ($_POST['action'] == "activar_usuario") {

  $db = Database::connect();

  $params = [
    (int)$_POST['user_id'],
    'activar'
  ];

  echo handleProcedureAction($db, 'us_cambiarEstado', $params);
}
