<?php

require_once '../config/db.php';
require_once 'functions/functions.php';
require_once '../config/parameters.php';
session_start();

$action = $_POST['action'] ?? null;

// Conexión a la base de datos
$db = Database::connect();

// Evaluamos la acción enviada por AJAX para determinar qué proceso ejecutar
switch ($_POST['action']) {

  // Mostrar pagos a proveedores en DataTable
  case "index_pagos_proveedores":
    handleDataTableRequest($db, [
      // Columnas seleccionadas para ordenar y filtrar
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
      'base_table' => 'pagos_proveedores p LEFT JOIN proveedores pr ON pr.proveedor_id = p.proveedor_id',
      'table_with_joins' => 'pagos_proveedores p LEFT JOIN proveedores pr ON pr.proveedor_id = p.proveedor_id',
      'select' => 'SELECT pr.nombre_proveedor, pr.apellidos, p.factura_proveedor_id, p.pago_factura_id as pago_id, p.recibido, p.observacion, p.fecha',

      // Formateo del resultado para el DataTable
      'table_rows' => function ($row) {
        return [
          'pago_id'     => $row['pago_id'],
          'factura'     => 'FP-00' . $row['factura_proveedor_id'],
          'proveedor'   => ucwords($row['nombre_proveedor'] . ' ' . $row['apellidos']),
          'recibido'    => '<span class="text-success">' . number_format($row['recibido'], 2) . '</span>',
          'observacion' => $row['observacion'],
          'fecha'       => $row['fecha'],
          'acciones'    => '<span style="font-size: 16px;" onclick="deletePaymentProvider(\'' . $row['pago_id'] . '\')" class="action-delete"><i class="fas fa-times"></i></span>'
        ];
      }
    ]);
    break;

  // Mostrar pagos a facturas de venta y reparación en DataTable
  case 'index_pagos_facturas_ventas':
    handleDataTableRequest($db, [
      'columns' => [
        'p.pago_id',
        'c.nombre',
        'c.apellidos',
        'p.observacion',
        'fr.facturaRP_id',
        'f.factura_venta_id',
        'p.recibido',
        'p.fecha'
      ],
      'searchable' => [
        'c.nombre',
        'c.apellidos',
        'p.observacion',
        'p.pago_id',
        'fr.facturaRP_id',
        'f.factura_venta_id'
      ],
      'base_table' => "pagos",
      'table_with_joins' => "pagos p
        LEFT JOIN pagos_a_facturas_ventas pf ON pf.pago_id = p.pago_id
        LEFT JOIN facturas_ventas f ON pf.factura_venta_id = f.factura_venta_id
        LEFT JOIN pagos_a_facturasRP pr ON pr.pago_id = p.pago_id
        LEFT JOIN facturasRP fr ON pr.facturaRP_id = fr.facturaRP_id
        LEFT JOIN clientes c ON p.cliente_id = c.cliente_id",
      'select' => "SELECT c.nombre, c.apellidos, p.observacion, fr.facturaRP_id, f.factura_venta_id, p.pago_id, p.recibido, p.fecha",

      // Formateo del resultado para el DataTable
      'table_rows' => function ($row) {
        $acciones = '<span';
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          if ($row['factura_venta_id'] > 0) {
            $acciones .= ' onclick="deletePayment(\'' . $row['pago_id'] . '\',1,0)" class="btn-action action-danger" title="Eliminar">
                  '.BUTTON_DELETE.'
                </span>';
          } else {
            $acciones .= ' onclick="deletePayment(\'' . $row['pago_id'] . '\',0,1)" class="btn-action action-danger" title="Eliminar">
                  '.BUTTON_DELETE.'
                </span>';
          }
        } else {
          $acciones .= ' class="btn-action action-danger action-disable" title="Eliminar">'.BUTTON_DELETE.'</span>';
        }

        return [
          'pago_id' => '<span class="hide-cell">' . '00' . $row['pago_id'] . '</span>',
          'factura_id' => ($row['factura_venta_id'] > 0)
            ? 'FT-00' . $row['factura_venta_id']
            : (($row['facturaRP_id'] > 0)
              ? 'RP-00' . $row['facturaRP_id']
              : '<span class="text-danger">Factura eliminada</span>'),
          'nombre' => ucwords($row['nombre'] . ' ' . $row['apellidos']),
          'recibido' => '<span class="text-success">' . number_format($row['recibido'], 2) . '</span>',
          'observacion' => '<span class="hide-cell">' . $row['observacion'] . '</span>',
          'fecha' => '<span class="hide-cell">' . $row['fecha'] . '</span>',
          'acciones' => $acciones
        ];
      }
    ]);
    break;

  // Consultar detalles de una factura de venta
  case 'consultar_factura_venta':
    $id = $_POST['invoice_id'];
    $query = "SELECT f.recibido, f.pendiente, f.total, c.cliente_id, c.nombre, c.apellidos, f.fecha, curdate() as fecha_hoy 
              FROM facturas_ventas f 
              INNER JOIN estados_generales e ON e.estado_id = f.estado_id 
              INNER JOIN clientes c ON f.cliente_id = c.cliente_id 
              WHERE f.factura_venta_id = '$id'";
    jsonQueryResult($db, $query);
    break;

  // Consultar detalles de una factura de reparación
  case 'consultar_factura_reparacion':
    $id = $_POST['invoice_id'];
    $query = "SELECT f.recibido, f.pendiente, f.total, c.cliente_id, c.nombre, c.apellidos, f.fecha, curdate() as fecha_hoy 
              FROM facturasRP f 
              INNER JOIN estados_generales e ON e.estado_id = f.estado_id 
              INNER JOIN clientes c ON f.cliente_id = c.cliente_id 
              WHERE f.facturaRP_id = '$id'";
    jsonQueryResult($db, $query);
    break;

  // Consultar detalles de una factura de proveedor
  case 'consultar_factura_proveedor':
    $id = $_POST['invoice_id'];
    $query = "SELECT * 
              FROM facturas_proveedores f 
              INNER JOIN estados_generales e ON e.estado_id = f.estado_id 
              INNER JOIN proveedores p ON f.proveedor_id = p.proveedor_id 
              WHERE f.factura_proveedor_id = '$id'";
    jsonQueryResult($db, $query);
    break;

  // Agregar un nuevo pago de cliente (venta o reparación)
  case "agregar_pago":
    $params = [
      $_SESSION['identity']->usuario_id,     // ID del usuario que registra
      $_POST['customer_id'],                 // ID del cliente
      $_POST['received'],                    // Monto recibido
      $_POST['invoice_id'],                  // ID de la factura de venta (si aplica)
      $_POST['invoiceRP_id'],                // ID de la factura de reparación (si aplica)
      $_POST['method'],                      // Método de pago
      $_POST['comment'] ?? '',               // Observación/comentario
      $_POST['date'] ?? ''                   // Fecha (puede venir vacía)
    ];
    echo handleProcedureAction($db, 'pg_crearPago', $params);
    break;

  // Agregar un nuevo pago a proveedor
  case "agregar_pago_proveedor":
    $params = [
      $_SESSION['identity']->usuario_id,     // ID del usuario
      $_POST['provider_id'],                 // ID del proveedor
      $_POST['received'],                    // Monto recibido
      $_POST['invoice_id'],                  // ID de la factura del proveedor
      $_POST['method'],                      // Método de pago
      $_POST['comment'] ?? ''                // Observación
    ];
    echo handleProcedureAction($db, 'pg_pagarFactura', $params);
    break;

  // Eliminar pago de cliente
  case 'eliminar_pago':
    $params = [
      (int)$_POST['id'],             // ID del pago
      (int)$_POST['invoice_id'],     // ID de factura de venta
      (int)$_POST['invoiceRP_id']    // ID de factura de reparación
    ];
    echo handleProcedureAction($db, 'pg_eliminarPago', $params);
    break;

  // Eliminar pago de proveedor
  case 'eliminar_pago_factura_proveedor':
    echo handleDeletionAction($db, (int)$_POST['id'], 'pg_eliminarPagoProveedor');
    break;
}
