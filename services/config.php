<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();


if ($_POST['action'] == "index_bonos") {
  $db = Database::connect();

  handleDataTableRequest($db, [
    'columns' => ['nombre', 'apellidos', 'valor', 'fecha', 'bono_id', 'nombre_usuario', 'apellidos_usuario'],
    'searchable' => ['nombre', 'apellidos', 'valor', 'fecha', 'bono_id', 'nombre_usuario', 'apellidos_usuario'],
    'base_table' => 'bonos b',
    'table_with_joins' => 'bonos b INNER JOIN clientes c ON b.cliente_id = c.cliente_id
        INNER JOIN usuarios u ON b.usuario_id = u.usuario_id',
    'select' => 'SELECT c.nombre as nombre, c.apellidos as apellidos, b.valor as valor,
        b.fecha as fecha, b.bono_id as bono_id, u.nombre as nombre_usuario, u.apellidos as apellidos_usuario',
    'table_rows' => function ($row) {
      return [
        'id' => $row['bono_id'],
        'cliente' => ucwords($row['nombre']) . ' ' . ucwords($row['apellidos']),
        'valor' => number_format($row['valor'], 2),
        'usuario' => ucwords($row['nombre_usuario']) . ' ' . ucwords($row['apellidos_usuario']),
        'fecha' => $row['fecha'],
        'acciones' => '
        <span class="btn-action action-danger" onclick="deleteBond(\'' . $row['bono_id'] . '\')" title="Eliminar">
            '.BUTTON_DELETE.'
        </span>'
      ];
    }
  ]);
}

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
  $smtps = $_POST['smtps'];
  $fb = $_POST['facebook'];
  $ws = $_POST['whatsapp'];
  $ig = $_POST['instagram'];

  $db = Database::connect();

  $query = "CALL cf_factElectronica('$logo','$company','$email','$password','$host','$smtps',$port,'$fb','$ws','$ig')";
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
