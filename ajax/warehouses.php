<?php

require_once '../config/db.php';
session_start();


if ($_POST['action'] == "agregar_almacen") {

    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['identity']->usuario_id;
  
    $db = Database::connect();
  
    $query = "CALL al_agregarAlmacen($user_id,'$name','$comment')";
  
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error 30: ". $data->msg;
    }
  }

  // Actualizar Almacen

  if ($_POST['action'] == "actualizar_almacen") {

    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $warehouse_id = $_POST['warehouse_id'];
  
    $db = Database::connect();
  
    $query = "CALL al_actualizarAlmacen($warehouse_id,'$name','$comment')";

    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error 31: ". $data->msg;
    }
  }

  //  Eliminar Posición

  if ($_POST['action'] == "eliminar_almacen") {

    $id = $_POST['warehouse_id'];
  
    $db = Database::connect();
  
    $query = "CALL al_eliminarAlmacen($id)";
    
    if ($db->query($query) === TRUE) {

      echo "ready";

    } else {
  
      echo "Error 32: " . $db->error;
    }
  }