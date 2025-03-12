<?php

require_once '../config/db.php';
session_start();


if ($_POST['action'] == "agregarCategoria") {

    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['identity']->usuario_id;
  
    $db = Database::connect();
  
    $query = "CALL ca_agregarCategoria($user_id,'$name','$comment')";

    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";

    } else if (str_contains($data->msg,'SQL')){

      echo "Error 70: ". $data->msg;
    }
  }


  if ($_POST['action'] == "actualizar_categoria") {

    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $category_id = $_POST['category_id'];
  
    $db = Database::connect();
  
    $query = "CALL ca_actualizarCategoria($category_id,'$name','$comment')";

    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";

    } else if (str_contains($data->msg,'SQL')){

      echo "Error 71: ". $data->msg;
    }
  }


  //  Eliminar categoría

if ($_POST['action'] == "eliminar_categoria") {

    $id = $_POST['category_id'];
  
    $db = Database::connect();
  
    $query = "CALL ca_eliminarCategoria($id)";
    
    if ($db->query($query) === TRUE) {

      echo "ready";

    } else {
  
      echo "Error 72: " . $db->error;
    }
  }