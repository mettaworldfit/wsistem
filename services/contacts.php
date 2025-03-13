<?php

require_once '../config/db.php';
session_start();

/**
 * Buscar contacto
 ------------------------------------------------------------*/

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
    
  } else if ($type == 'proveedor'){

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
