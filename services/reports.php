<?php

require_once '../config/db.php';
require_once '../help.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

if ($_POST['action'] == 'abrir_caja') {
    
    $db = Database::connect();
    $params = [
       (int) $_SESSION['identity']->usuario_id,
        $_POST['opening_date'],
        $_POST['initial_balance'],
    ];

    echo handleProcedureAction($db,'c_aperturaCaja',$params);
}

if ($_POST['action'] == 'cierre_caja') {
    $db = Database::connect();
    $params = [
        (int)$_POST['user_id'],
        $_POST['closing_date'],
        $_POST['initial_balance'],
        $_POST['cash_income'],
        $_POST['card_income'],
        $_POST['transfer_income'],
        $_POST['check_income'],
        $_POST['cash_expenses'],
        $_POST['external_expenses'],
        $_POST['withdrawals'],
        $_POST['current_total'],
        $_POST['notes'] ?? ""
    ];

    echo handleProcedureAction($db,'c_cierreCaja',$params);
}

// index cierres de caja

if ($_POST['action'] == 'index_cierre_caja') {
    $db = Database::connect();

    handleDataTableRequest($db, [
        'columns' => [
            'cierre_id',
            'cajero',
            'total_esperado',
            'total_real',
            'diferencia',
            'fecha_apertura',
            'fecha_cierre',
            'estado'
        ],
        'searchable' => [
            'cierre_id',
            'cajero',
            'total_esperado',
            'total_real',
            'diferencia',
            'fecha_apertura',
            'fecha_cierre',
            'estado'
        ],
        'base_table' => 'cierres_caja c',
        'table_with_joins' => 'cierres_caja c
      INNER JOIN usuarios u ON u.usuario_id = c.usuario_id',
        'select' => "SELECT c.cierre_id,concat(u.nombre,' ',IFNULL(u.apellidos,'')) as cajero, c.total_esperado,
       c.total_real,c.diferencia,c.fecha_apertura,c.fecha_cierre,c.estado",
        'table_rows' => function ($row) {
            return [
                'id' => '<span class="hide-cell">' . $row['cierre_id'] . '</span>',
                'cajero' => ucwords($row['cajero']),
                'total_esperado' => '<span class="hide-cell text-success">' . number_format($row['total_esperado']) . '</span>',
                'total_real' => '<span class="hide-cell text-primary">' . number_format($row['total_real']) . '</span>',
                'diferencia' => '<span class="hide-cell text-danger">' . number_format($row['diferencia']) . '</span>',
                'fecha_apertura' => $row['fecha_apertura'],
                'fecha_cierre' => $row['fecha_cierre'],
                'estado' => '<span class="hide-cell">' . ucwords($row['estado']) . '</span>',
                'acciones' => ''
            ];
        }
    ]);
}

// Index de ventas diarias

if ($_POST['action'] == "index_ventas_hoy") {

    $db = Database::connect();

    // Parámetros de DataTables
    $draw = intval($_POST['draw'] ?? 0);
    $start = intval($_POST['start'] ?? 0);
    $length = intval($_POST['length'] ?? 10);
    $searchValue = $_POST['search']['value'] ?? '';

    // Columnas disponibles para ordenamiento
    $columns = [
        'orden',
        'id',
        'nombre',
        'apellidos',
        'fecha_factura',
        'total',
        'recibido',
        'pendiente',
        'estado',
        'tipo'
    ];

    // Ordenamiento 
    $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
    $orderColumn = $columns[$orderColumnIndex] ?? 'id';
    $orderDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

    // Filtro de búsqueda
    $searchQuery = '';
    if (!empty($searchValue)) {
        $searchEscaped = $db->real_escape_string($searchValue);
        $searchQuery = " AND (
        nombre LIKE '%$searchEscaped%' OR
        apellidos LIKE '%$searchEscaped%' OR
        tipo LIKE '%$searchEscaped%' OR
        orden LIKE '%$searchEscaped%' OR
        estado LIKE '%$searchEscaped%'
      )";
    }


    $totalQuery = "SELECT COUNT(*) AS total FROM (
      SELECT f.fecha FROM facturas_ventas f 
      UNION ALL
      SELECT f.fecha FROM facturasRP f
      UNION ALL
      SELECT pg.fecha FROM pagos_a_facturas_ventas p INNER JOIN pagos pg ON pg.pago_id = p.pago_id
      UNION ALL
      SELECT pg.fecha FROM pagos_a_facturasRP p INNER JOIN pagos pg ON pg.pago_id = p.pago_id
    ) AS all_data WHERE fecha = CURDATE()";

    $totalResult = $db->query($totalQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'] ?? 0;

    // Datos paginados y filtrados

    $query = "SELECT id, tipo, orden, nombre, apellidos, total, recibido, pendiente, estado, fecha_factura, nombre_metodo FROM (
      SELECT c.nombre, c.apellidos, f.factura_venta_id AS id, 'n/d' AS orden, f.fecha AS fecha_factura,
             f.total, f.recibido, f.pendiente, s.nombre_estado AS estado, 'FT' AS tipo, m.nombre_metodo
      FROM facturas_ventas f
      INNER JOIN clientes c ON f.cliente_id = c.cliente_id
      INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
      INNER JOIN estados_generales s ON f.estado_id = s.estado_id
    
      UNION ALL
    
      SELECT c.nombre, c.apellidos, f.facturarp_id AS id, f.orden_rp_id AS orden, f.fecha AS fecha_factura,
             f.total, f.recibido, f.pendiente, s.nombre_estado AS estado, 'RP' AS tipo, m.nombre_metodo
      FROM facturasRP f
      INNER JOIN clientes c ON f.cliente_id = c.cliente_id
    INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
      INNER JOIN estados_generales s ON f.estado_id = s.estado_id
    
      UNION ALL
    
      SELECT c.nombre, c.apellidos, pg.pago_id AS id, f.factura_venta_id AS orden, pg.fecha AS fecha_factura,
             pg.recibido AS total, pg.recibido AS recibido, '0' AS pendiente, s.nombre_estado AS estado, 'PF' AS tipo, m.nombre_metodo
      FROM pagos_a_facturas_ventas p
      INNER JOIN pagos pg ON pg.pago_id = p.pago_id
      INNER JOIN facturas_ventas f ON f.factura_venta_id = p.factura_venta_id
      INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = pg.metodo_pago_id
      INNER JOIN clientes c ON f.cliente_id = c.cliente_id
      INNER JOIN estados_generales s ON f.estado_id = s.estado_id
     WHERE (
      s.nombre_estado = 'por cobrar'
      OR (s.nombre_estado <> 'por cobrar' AND f.fecha <> pg.fecha)
     )
    
      UNION ALL
    
      SELECT c.nombre, c.apellidos, pg.pago_id AS id, f.facturarp_id AS orden, pg.fecha AS fecha_factura,
             pg.recibido AS total, pg.recibido AS recibido, '0' AS pendiente, s.nombre_estado AS estado, 'PR' AS tipo, m.nombre_metodo
      FROM pagos_a_facturasRP p
      INNER JOIN pagos pg ON pg.pago_id = p.pago_id
      INNER JOIN facturasRP f ON f.facturarp_id = p.facturarp_id
      INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = pg.metodo_pago_id
      INNER JOIN clientes c ON f.cliente_id = c.cliente_id
      INNER JOIN estados_generales s ON f.estado_id = s.estado_id
      WHERE (
      s.nombre_estado = 'por cobrar'
      OR (s.nombre_estado <> 'por cobrar' AND f.fecha <> pg.fecha)
     )
    
    ) ventas_del_dia 
    WHERE fecha_factura = CURDATE() $searchQuery
    ORDER BY $orderColumn $orderDir
    LIMIT $start, $length";

    $result = $db->query($query);

    // Crear arreglo de datos con formato HTML para cada celda
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $acciones = '';

        if ($row['tipo'] == 'FT') {
            if ($_SESSION['identity']->nombre_rol == 'administrador') {
                $acciones .= '<a class="action-edit" href="' . base_url . 'invoices/edit&id=' . $row['id'] . '" title="editar"><i class="fas fa-pencil-alt"></i></a>';
            } else {
                $acciones .= '<a class="action-edit action-disable" href="#" title="editar"><i class="fas fa-pencil-alt"></i></a>';
            }
        } elseif ($row['tipo'] == 'RP') {
            if ($row['estado'] != 'Anulada' && $_SESSION['identity']->nombre_rol == 'administrador') {
                $acciones .= '<a class="action-edit" href="' . base_url . 'invoices/repair_edit&id=' . $row['orden'] . '" title="Editar"><i class="fas fa-pencil-alt"></i></a>';
            } else {
                $acciones .= '<a class="action-edit action-disable" href="#" title="Editar"><i class="fas fa-pencil-alt"></i></a>';
            }
        }

        $acciones .= '<span class="action-delete"';

        if ($row['tipo'] == 'FT') {
            $acciones .= ' onclick="deleteInvoice(\'' . $row['id'] . '\')"';
        } elseif ($row['tipo'] == 'RP') {
            $acciones .= ' onclick="deleteInvoiceRP(\'' . $row['id'] . '\')"';
        } elseif ($row['tipo'] == 'PF') {
            $acciones .= ' onclick="deletePayment(\'' . $row['id'] . '\', \'' . $row['orden'] . '\', 0)"';
        } elseif ($row['tipo'] == 'PR') {
            $acciones .= ' onclick="deletePayment(\'' . $row['id'] . '\', 0, \'' . $row['orden'] . '\')"';
        }

        $acciones .= ' title="Eliminar"><i class="fas fa-times"></i></span>';

        $data[] = [

            'id' => '
        <span>
            <a href="#">
                ' . $row['tipo'] . '-00' . $row['id'] . '
            </a>
            <span id="toggle" class="toggle-right toggle-md">
               ' . 'Método: ' . $row['nombre_metodo'] . '
            </span>
        </span>
    ',
            'nombre' => ucwords($row['nombre'] . ' ' . $row['apellidos']),
            'fecha' => $row['fecha_factura'],
            'total' => '<span class="text-primary">' . number_format($row['total'], 2) . '</span>',
            'recibido' => '<span class="text-success">' . number_format($row['recibido'], 2) . '</span>',
            'pendiente' => '<span class="text-danger">' . number_format($row['pendiente'], 2) . '</span>',
            'estado' => '<p class="' . $row['estado'] . '">' . $row['estado'] . '</p>',
            'acciones' => $acciones
        ];
    }

    // Respuesta JSON
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data
    ]);
    exit();
}


if ($_POST['action'] == "productos_vendidos") {

    $db = Database::connect();

    $q = $_POST['query'];
    $d1 = $_POST['dateq1'];
    $d2 = $_POST['dateq2'];


    $query = "SELECT p.nombre_producto, sum(d.cantidad) as cantidad,sum(d.precio - d.descuento) as total,
    sum(p.precio_costo * d.cantidad) as costo, sum((d.precio - d.descuento)-(p.precio_costo * d.cantidad)) as ganancia  from detalle_facturas_ventas d 
inner join detalle_ventas_con_productos dp on dp.detalle_venta_id = d.detalle_venta_id
inner join productos p on p.producto_id = dp.producto_id
where p.nombre_producto like '%$q%' and d.fecha between '$d1' and '$d2' group by p.nombre_producto order by total desc;";
    $result = $db->query($query);

    $arr = [];

    while ($element = $result->fetch_assoc()) {
        $arr[] = $element;
    }

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}


if ($_POST['action'] == "servicios_vendidos") {

    $db = Database::connect();

    $q = $_POST['query'];
    $d1 = $_POST['dateq1'];
    $d2 = $_POST['dateq2'];


    $query = "SELECT nombre_servicio, sum(cantidad) as cantidad, sum(total) as total  FROM (

    SELECT s.nombre_servicio as nombre_servicio, sum(d.cantidad) as cantidad, sum(d.precio - d.descuento) as total from detalle_facturas_ventas d 
    inner join detalle_ventas_con_servicios ds on ds.detalle_venta_id = d.detalle_venta_id
    inner join servicios s on s.servicio_id = ds.servicio_id 
    where s.nombre_servicio like '%$q%' and d.fecha between '$d1' and '$d2' group by s.nombre_servicio

    UNION ALL
    
    SELECT s.nombre_servicio as nombre_servicio, sum(d.cantidad) as cantidad, sum(d.precio - d.descuento) as total from detalle_ordenRP d 
    inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
    inner join detalle_ordenRP_con_servicios dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join servicios s on s.servicio_id = dp.servicio_id
    where s.nombre_servicio like '%$q%' and d.fecha between '$d1' and '$d2' group by s.nombre_servicio

    ) servicios_vendidos group by nombre_servicio order by total desc;";

    $result = $db->query($query);

    $arr = [];

    while ($element = $result->fetch_assoc()) {
        $arr[] = $element;
    }

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_POST['action'] == "serial_facturado") {

    $db = Database::connect();

    $q = $_POST['query'];

    $query = "SELECT f.factura_venta_id, c.nombre ,c.apellidos,v.serial, 
            v.costo_unitario, v.fecha as fecha_entrada, f.fecha from variantes v 
            inner join variantes_facturadas vf on v.variante_id = vf.variante_id
            inner join detalle_facturas_ventas d on d.detalle_venta_id = vf.detalle_venta_id
            inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
            inner join clientes c on c.cliente_id = f.cliente_id
            where v.serial = '$q'";

    $result = $db->query($query);

    $arr = [];
    while ($element = $result->fetch_assoc()) {
        $arr[] = $element;
    }

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}


if ($_POST['action'] == "piezas_vendidas") {

    $db = Database::connect();

    $q = $_POST['query'];
    $d1 = $_POST['dateq1'];
    $d2 = $_POST['dateq2'];


    $query = "SELECT nombre_pieza, sum(cantidad) as cantidad, sum(total) as total, sum(costo) as costo, sum(ganancia) as ganancia FROM (

    SELECT p.nombre_pieza as nombre_pieza, sum(d.cantidad) as cantidad,sum(d.precio - d.descuento) as total,
    sum(p.precio_costo * d.cantidad) as costo, sum((d.precio - d.descuento)-(p.precio_costo * d.cantidad)) as ganancia from detalle_facturas_ventas d 
    inner join detalle_ventas_con_piezas_ dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where p.nombre_pieza like '%$q%' and d.fecha between '$d1' and '$d2' group by p.nombre_pieza

    UNION ALL

    SELECT p.nombre_pieza as nombre_pieza, sum(d.cantidad) as cantidad,sum(d.precio - d.descuento) as total,
        sum(p.precio_costo * d.cantidad) as costo, sum((d.precio - d.descuento)-(p.precio_costo * d.cantidad)) as ganancia  from detalle_ordenRP d 
    inner join detalle_ordenRP_con_piezas dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where p.nombre_pieza like '%$q%' and d.fecha between '$d1' and '$d2' group by p.nombre_pieza

    ) piezas_vendidas group by nombre_pieza order by total desc;";

    $result = $db->query($query);

    $arr = [];

    while ($element = $result->fetch_assoc()) {
        $arr[] = $element;
    }

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}
