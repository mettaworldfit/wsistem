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


  if ($_POST['action'] == 'ajustes_factura_electronica') {

  
    $company = $_POST['company'];
    $logo = $_POST['logo'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $host = $_POST['host'];
    $port = $_POST['port'];
    $fb = $_POST['facebook'];
    $ws = $_POST['whatsapp'];
    $ig = $_POST['instagram'];
    
    $db = Database::connect();
  
    $query = "CALL cf_factElectronica('$logo','$company','$email','$password','$host',$port,'$fb','$ws','$ig')";
    $result = $db->query($query);
    $data = $result->fetch_object();
  
    if ($data->msg == "ready") {
  
      echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {
  
      echo "Error : " . $data->msg;
    }
    
  }

  if ($_POST['action'] == 'ajustes_factura_pdf') {

  
    $logo = $_POST['logo'];
    $slogan = $_POST['slogan'];
    $address = $_POST['address'];
    $tel = $_POST['tel'];
    $policy = $_POST['policy'];
    $title = $_POST['title'];
    
    $db = Database::connect();
  
    $query = "CALL cf_factPDF('$logo','$slogan','$address','$tel','$policy','$title')";
    $result = $db->query($query);
    $data = $result->fetch_object();
  
    if ($data->msg == "ready") {
  
      echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {
  
      echo "Error : " . $data->msg;
    }
    
  }