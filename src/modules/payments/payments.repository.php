<?php

require_once dirname(__DIR__,2) . '/config/connection.php';
require_once dirname(__DIR__,2) . '/functions/functions.php';
require_once dirname(__DIR__,2) . '/config/parameters.php';
session_start();

$action = $_POST['action'] ?? null;

// Conexión a la base de datos
$db = Database::connect();

// Evaluamos la acción enviada por AJAX para determinar qué proceso ejecutar
switch ($_POST['action']) {

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
            $acciones .= ' class="btn-action action-danger delete_item" data-id="'.$row['pago_id'].'" data-inv="1"  data-invrp="0" title="Eliminar">
                  '.BUTTON_DELETE.'
                </span>';
          } else {
            $acciones .= ' class="btn-action action-danger detele_item" data-id="'.$row['pago_id'].'" data-inv="0"  data-invrp="1" title="Eliminar">
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
}
