<?php

require_once '../config/db.php';
session_start();



if ($_POST['action'] == "agregar_oferta") {

    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $value = $_POST['value'];
    $user_id = $_SESSION['identity']->usuario_id;
  
    $db = Database::connect();
  
    $query = "CALL of_agregarOferta($user_id,'$name',$value,'$comment')";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error 60: ". $data->msg;
    }
    
  }

  // Actualizar Oferta

  if ($_POST['action'] == "actualizar_oferta") {

    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $value = $_POST['value'];
    $offer_id = $_POST['offer_id'];
  
    $db = Database::connect();
  
    $query = "CALL of_actualizarOferta($offer_id,'$name',$value,'$comment')";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error 61: ". $data->msg;
    }
  }


  //  Eliminar Oferta

if ($_POST['action'] == "eliminar_oferta") {

    $id = $_POST['offer_id'];
  
    $db = Database::connect();
  
    $query = "CALL of_eliminarOferta($id)";
    
    if ($db->query($query) === TRUE) {

      echo "ready";

    } else {
  
      echo "Error 62: " . $db->error;
    }
  }