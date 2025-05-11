<?php

require_once '../config/db.php';
require_once 'functions/functions.php';
require_once '../config/parameters.php';
session_start();

if ($_POST['action'] == "index_pagos_proveedores") {
  $db = Database::connect();
  handleDataTableRequest($db, [
      'columns' => [
          'pr.nombre_proveedor',
          'pr.apellidos',
          'p.factura_proveedor_id',
          'p.pago_factura_id',
          'p.recibido',
          'p.observacion',
          'p.fecha'
      ],
      'searchable' => [
          'pr.nombre_proveedor',
          'pr.apellidos',
          'p.recibido',
          'p.observacion',
          'p.fecha'
      ],
      'base_table' => 'pagos_proveedores p 
          LEFT JOIN proveedores pr ON pr.proveedor_id = p.proveedor_id',
      'table_with_joins' => 'pagos_proveedores p 
          LEFT JOIN proveedores pr ON pr.proveedor_id = p.proveedor_id',
      'select' => 'SELECT pr.nombre_proveedor, pr.apellidos, p.factura_proveedor_id, 
                   p.pago_factura_id as pago_id, p.recibido, p.observacion, p.fecha',
      'table_rows' => function ($row) {
          return [
            'pago_id'     => $row['pago_id'],
            'factura'     => 'FP-00' . $row['factura_proveedor_id'],
            'proveedor'   => ucwords($row['nombre_proveedor'] . ' ' . $row['apellidos']),
            'recibido'    => '<span class="text-success">' . number_format($row['recibido'], 2) . '</span>',
            'observacion' => $row['observacion'],
            'fecha'       => $row['fecha'],
            'acciones'    => '<span style="font-size: 16px;" onclick="deletePaymentProvider(\'' . $row['pago_id'] . '\')" class="action-delete">
                                <i class="fas fa-times"></i>
                              </span>'
          ]; 
      }
  ]);
}


if ($_POST['action'] == 'index_pagos_facturas_ventas') {

  $db = Database::connect();

  handleDataTableRequest($db,[
      'columns' => [
          'p.pago_id','c.nombre', 'c.apellidos', 'p.observacion',
          'fr.facturaRP_id', 'f.factura_venta_id','p.recibido', 'p.fecha'
      ],
      'searchable' => [
          'c.nombre', 'c.apellidos', 'p.observacion','p.pago_id',
          'fr.facturaRP_id', 'f.factura_venta_id'
      ],
      'base_table' => "pagos",
      'table_with_joins' => "pagos p
          LEFT JOIN pagos_a_facturas_ventas pf ON pf.pago_id = p.pago_id
          LEFT JOIN facturas_ventas f ON pf.factura_venta_id = f.factura_venta_id
          LEFT JOIN pagos_a_facturasRP pr ON pr.pago_id = p.pago_id
          LEFT JOIN facturasRP fr ON pr.facturaRP_id = fr.facturaRP_id
          LEFT JOIN clientes c ON p.cliente_id = c.cliente_id
      ",
      'select' => "SELECT c.nombre, c.apellidos, p.observacion,
                fr.facturaRP_id, f.factura_venta_id,
                p.pago_id, p.recibido, p.fecha
      ",
      'table_rows' => function ($row) {
            return [
              'pago_id' => '00' . $row['pago_id'],
              'factura_id' => ($row['factura_venta_id'] > 0)
                  ? 'FT-00' . $row['factura_venta_id']
                  : (($row['facturaRP_id'] > 0)
                      ? 'RP-00' . $row['facturaRP_id']
                      : '<span class="text-danger">Factura eliminada</span>'),
              'nombre' => ucwords($row['nombre']. ' ' .$row['apellidos']),
              'recibido' => '<span class="text-success">' . number_format($row['recibido'], 2) . '</span>',
              'observacion' => $row['observacion'],
              'fecha' => $row['fecha'],
              'acciones' => ($row['factura_venta_id'] > 0)
                  ? '<span onclick="deletePayment(\'' . $row['pago_id'] . '\',1,0)" class="action-delete"><i class="fas fa-times"></i></span>'
                  : '<span onclick="deletePayment(\'' . $row['pago_id'] . '\',0,1)" class="action-delete"><i class="fas fa-times"></i></span>'
          ];
      }
  ]);

}



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