<?php

require_once '../config/db.php';
session_start();


// Buscar equipo
    
if ($_POST['action'] == "buscar_equipo") {

  $id = $_POST['device_id'];

  $db = Database::connect();

  $query = "SELECT d.modelo, m.nombre_marca FROM equipos d
  INNER JOIN marcas m on d.marca_id = m.marca_id WHERE equipo_id = '$id'";
  $datos = $db->query($query);

  $result = $datos->fetch_assoc();

  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;

  
}

// Agregar orden de reparación

if ($_POST['action'] == "agregar_orden_reparacion") {

    $customer_id= $_POST['customer_id'];
    $device = $_POST['device'];
    $serie = $_POST['serie'];
    $imei = (!empty($_POST['imei'])) ? $_POST['imei'] : 0;
    $user_id = $_SESSION['identity']->usuario_id;
    $observation = $_POST['observation'];
  
    $db = Database::connect();

    $query = "CALL rp_crearOrdenRP($user_id,$customer_id,'$device','$serie','$imei','$observation')";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg > 0) {

      echo $data->msg;
    } else if (str_contains($data->msg,'SQL')){

      echo "Error : ". $data->msg;
    }
    
  }

  // Asignar condiciones

  if ($_POST['action'] == "asignar_condiciones") {

    $condition_id = $_POST['condition_id'];
    $orden_id = $_POST['orden_id'];
  
    $db = Database::connect();
  
    $query = "CALL rp_agregarCondiciones($condition_id,$orden_id)";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error : ". $data->msg;
    }
    
  }

   // Crear condicion

   if ($_POST['action'] == "crear_condicion") {

    $condition = $_POST['condition'];
    $user_id = $_SESSION['identity']->usuario_id;
  
    $db = Database::connect();
  
    $query = "CALL rp_crearCondicion($user_id,'$condition')";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error : ". $data->msg;
    }
    
  }

   // Crear equipo

   if ($_POST['action'] == "crear_equipo") {

    $brand = $_POST['brand'];
    $device = $_POST['device'];
    $model = $_POST['model'];
  
    $db = Database::connect();
  
    $query = "CALL rp_crearEquipo('$brand','$device','$model')";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error : ". $data->msg;
    }
    
  }

  // actualizar estado orden
  
  if ($_POST['action'] == "actualizar_estado_orden") {

    $status = $_POST['status'];
    $workshop_id = $_POST['workshop_id'];
  
    $db = Database::connect();
  
    $query = "CALL rp_actualizarEstadoOrden($status,$workshop_id)";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error : ". $data->msg;
    }
    
  }

// Eliminar orden
    
  if ($_POST['action'] == "eliminar_orden") {

    $id = $_POST['id'];
  
    $db = Database::connect();
  
    $query = "CALL rp_eliminarOrdenRP($id)";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error : ". $data->msg;
    }
    
  }

 // Eliminar marca
    
 if ($_POST['action'] == "eliminar_marca") {

  $id = $_POST['id'];

  $db = Database::connect();

  $query = "CALL m_eliminarMarca($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg,'SQL')){

    echo "Error : ". $data->msg;
  }
  
}

if ($_POST['action'] == "crear_marca") {

  $brand = $_POST['name'];

  $db = Database::connect();

  $query = "CALL m_crearMarca('$brand')";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg,'SQL')){

    echo "Error : ". $data->msg;
  }
  
}

// Actualizar marca

if ($_POST['action'] == "actualizar_marca") {

  $name = $_POST['name'];
  $id = $_POST['id'];

  $db = Database::connect();

    $query = "CALL m_actualizarMarca('$name',$id)";
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