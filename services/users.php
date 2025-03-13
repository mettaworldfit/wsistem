<?php

require_once '../config/db.php';
require_once '../config/parameters.php';


/**
 * Iniciar sesión
 --------------------------------------------------------------*/

session_start();

if ($_POST['action'] == "login") {

  // Verificar si existen las credenciales del usuario
  function verifyCredentials($passwordDB, $password, $data)
  {

    if ($passwordDB == $password ) {

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


/**
 * Cerrar sesión
   -------------------------------------------------------------*/

   if ($_POST['action'] == "logout") {

    session_destroy();
  
    echo "ready";
  }
  

// Crear usuario

if ($_POST['action'] == "crear_usuario") {

  $name = $_POST['name'];
  $lastname = $_POST['lastname'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $rol = $_POST['rol'];

  $db = Database::connect();



  $query = "CALL us_agregarUsuario('$rol','$name','$lastname','$username','$password')";
  $result = $db->query($query);
  $data = $result->fetch_object();

    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'Duplicate')){

      echo "duplicate";

    } else if (str_contains($data->msg,'SQL')){

      echo "Error 50: " . $db->error;
    }

 
  
}

// Actualizar usuario

if ($_POST['action'] == "actualizar_usuario") {

  $name = $_POST['name'];
  $lastname = $_POST['lastname'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $rol = $_POST['rol'];
  $id = $_POST['id'];

  $db = Database::connect();

    $query = "CALL us_actualizarUsuario($rol,'$name','$lastname','$username','$password',$id)";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'Duplicate')){

      echo "duplicate";

    } else if (str_contains($data->msg,'SQL')){

      echo "Error 54:". $data->msg;

    } 

  } 

// Eliminar usuario

if ($_POST['action'] == "eliminar_usuario") {

  $id = $_POST['user_id'];

  $db = Database::connect();

  $query = "CALL us_eliminarUsuario($id)";
  $result = $db->query($query);

  if ($db->query($query) === TRUE) {

    echo "ready";
  } else {

    echo "Error 51: " . $db->error;
  }
}

/**
 * Desactivar usuario
 ----------------------------------------------*/

 if ($_POST['action'] == "desactivar_usuario") {
  $db = Database::connect();

  $id = $_POST['user_id'];

  $query = "CALL us_cambiarEstado($id,'desactivar')";
  
  if ($db->query($query) === TRUE) {

    echo "ready";
  
  } else {

    echo "Error 52: " . $db->error;
  }


  /**
 * Activar usuario
 ----------------------------------------------*/

} else if ($_POST['action'] == "activar_usuario") {

  $db = Database::connect();

  $id = $_POST['user_id'];

  $query = "CALL us_cambiarEstado($id,'activar')";
  
  if ($db->query($query) === TRUE) {

    echo "ready";
  
  } else {

    echo "Error 53: " . $db->error;
  }

}



