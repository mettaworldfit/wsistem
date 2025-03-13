<?php

require_once '../config/db.php';
session_start();

if ($_POST['action'] == 'actualizar_bono_config') {

    $user_id = $_SESSION['identity']->usuario_id;
    $min = $_POST['min'];
    $value = $_POST['value'];
    $status_id = $_POST['status'];
    
    $db = Database::connect();
  
    $query = "CALL cf_bono_config($user_id,'$min','$value',$status_id)";
    $result = $db->query($query);
    $data = $result->fetch_object();
  
    if ($data->msg == "ready") {
  
      echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {
  
      echo "Error : " . $data->msg;
    }
    
  }