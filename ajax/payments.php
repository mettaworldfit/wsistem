<?php

require_once '../config/db.php';
session_start();


// Cargar datos de factura

if ($_POST['action'] == 'consultar_factura_venta') {

    $id = $_POST['invoice_id'];
    $db = Database::connect();
  
    $query = "SELECT f.recibido, f.pendiente, f.total, c.cliente_id, c.nombre, c.apellidos, f.fecha, curdate() as fecha_hoy FROM facturas_ventas f 
                INNER JOIN estados_generales e ON e.estado_id = f.estado_id
                INNER JOIN clientes c ON f.cliente_id = c.cliente_id
                WHERE f.factura_venta_id = '$id'";
                
     $datos = $db->query($query);
     $result = $datos->fetch_assoc();
   
     echo json_encode($result, JSON_UNESCAPED_UNICODE);
     exit;

  } else if ($_POST['action'] == 'consultar_factura_reparacion') {

    $id = $_POST['invoice_id'];
    $db = Database::connect();
  
    $query = "SELECT f.recibido, f.pendiente, f.total, c.cliente_id, c.nombre, c.apellidos, f.fecha, curdate() as fecha_hoy FROM facturasRP f 
                INNER JOIN estados_generales e ON e.estado_id = f.estado_id
                INNER JOIN clientes c ON f.cliente_id = c.cliente_id
                WHERE f.facturaRP_id = '$id'";
                
     $datos = $db->query($query);
     $result = $datos->fetch_assoc();
   
     echo json_encode($result, JSON_UNESCAPED_UNICODE);
     exit;

  } else if ($_POST['action'] == 'consultar_factura_proveedor') { 

    $id = $_POST['invoice_id'];
    $db = Database::connect();
  
    $query = "SELECT * FROM facturas_proveedores f 
                INNER JOIN estados_generales e ON e.estado_id = f.estado_id
                INNER JOIN proveedores p ON f.proveedor_id = p.proveedor_id
                WHERE f.factura_proveedor_id = '$id'";
                
     $datos = $db->query($query);
     $result = $datos->fetch_assoc();
   
     echo json_encode($result, JSON_UNESCAPED_UNICODE);
     exit;

  }

  // Agregar pago

  if ($_POST['action'] == "agregar_pago") {

    $invoice_id = $_POST['invoice_id'];
    $invoiceRP_id = $_POST['invoiceRP_id'];
    $received = $_POST['received'];
    $comment = $_POST['comment'];
    $customer = $_POST['customer_id'];
    $method = $_POST['method'];
    $date = $_POST['date'];
    $user_id = $_SESSION['identity']->usuario_id;
  
    $db = Database::connect();

    $query = "CALL pg_crearPago($user_id,$customer,$received,$invoice_id,$invoiceRP_id,$method,'$comment','$date')";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg > 0) {

      echo $data->msg;
    } else if (str_contains($data->msg,'SQL')){

      echo "Error : ". $data->msg;
    }
    
  }

  // Pagar factura proveedor

  if ($_POST['action'] == "agregar_pago_proveedor") {

    $invoice_id = $_POST['invoice_id'];
    $received = $_POST['received'];
    $comment = $_POST['comment'];
    $provider = $_POST['provider_id'];
    $method = $_POST['method'];
    $user_id = $_SESSION['identity']->usuario_id;
  
    $db = Database::connect();

    $query = "CALL pg_pagarFactura($user_id,$provider,$received,$invoice_id,$method,'$comment')";
    $result = $db->query($query);
    $data = $result->fetch_object();
 
    if ($data->msg == "ready") {

      echo "ready";
    } else if (str_contains($data->msg,'SQL')){

      echo "Error : ". $data->msg;
    }
    
  }

  // Eliminar pago

if ($_POST['action'] == 'eliminar_pago') {

    $id = $_POST['id'];
    $invoice_id = $_POST['invoice_id'];
    $invoiceRP_id = $_POST['invoiceRP_id'];
    $db = Database::connect();
  
    $query = "CALL pg_eliminarPago($id,$invoice_id,$invoiceRP_id)";
    $result = $db->query($query);
    $data = $result->fetch_object();
  
    if ($data->msg == "ready") {
  
      echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {
  
      echo "Error : " . $data->msg;
    }
  }

  // Eliminar pago factura de proveedores

if ($_POST['action'] == 'eliminar_pago_factura_proveedor') {

  $id = $_POST['id'];
  $db = Database::connect();

  $query = "CALL pg_eliminarPagoProveedor($id)";
  $result = $db->query($query);
  $data = $result->fetch_object();

  if ($data->msg == "ready") {

    echo "ready";
  } else if (str_contains($data->msg, 'SQL')) {

    echo "Error : " . $data->msg;
  }
}