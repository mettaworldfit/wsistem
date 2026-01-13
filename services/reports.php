<?php

require_once '../config/db.php';
require_once '../help.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();

if ($_POST['action'] == 'eliminar_cierre') {

    $db = Database::connect();

    echo handleDeletionAction($db, (int)$_POST['id'], 'c_eliminarCierre');
}

if ($_POST['action'] == 'abrir_caja') {

    $db = Database::connect();
    $params = [
        (int) $_SESSION['identity']->usuario_id,
        $_POST['opening_date'],
        $_POST['initial_balance'],
    ];

    echo handleProcedureAction($db, 'c_aperturaCaja', $params);
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
        $_POST['refunds'],
        $_POST['total'],
        $_POST['current_total'],
        $_POST['notes'] ?? ""
    ];

    echo handleProcedureAction($db, 'c_cierreCaja', $params);
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
        'table_with_joins' => 'cierres_caja c INNER JOIN usuarios u ON u.usuario_id = c.usuario_id',
        'select' => "SELECT c.cierre_id,concat(u.nombre,' ',IFNULL(u.apellidos,'')) as cajero, c.total_esperado,
                    c.total_real,c.diferencia,c.saldo_inicial,c.ingresos_efectivo,c.ingresos_tarjeta,
                    c.ingresos_transferencia,c.ingresos_cheque,c.egresos_caja,c.egresos_fuera,c.retiros,
                    c.fecha_apertura,c.fecha_cierre,c.observaciones,c.estado",
        'table_rows' => function ($row) {
            // Generar PDF
            $acciones = '<span ';
            if ($_SESSION['identity']->nombre_rol == 'administrador') {
                $acciones .= ' onclick="generateCashClosingPDF(\'' . $row['cierre_id'] . '\')" class="action-danger btn-action"';
            } else {
                $acciones .= ' class="action-danger btn-action action-disable"';
            }
            $acciones .= ' title="PDF">' . BUTTON_PDF . '</span>';

            // Eliminar
            $acciones .= '<span ';
            if ($_SESSION['identity']->nombre_rol == 'administrador') {
                $acciones .= ' onclick="deleteCashClosing(\'' . $row['cierre_id'] . '\')" class="action-danger btn-action"';
            } else {
                $acciones .= ' class="action-danger btn-action action-disable"';
            }
            $acciones .= ' title="Eliminar">' . BUTTON_DELETE . '</span>';

            return [
                'id' => '<span class="hide-cell">' . $row['cierre_id'] . '</span>',
                'cajero' => ucwords($row['cajero']),
                'total_real' => '<span>
                    <a href="#" class="hide-cell text-success">
                        ' . number_format($row['total_real']) . '
                    </a>
                    <span id="toggle" class="toggle-right toggle-md">' .
                    'Efectivo: ' . number_format($row['ingresos_efectivo'], 2) . '<br>' .
                    'Tarjeta: ' . number_format($row['ingresos_tarjeta'], 2) . '<br>' .
                    'Transferencia: ' . number_format($row['ingresos_transferencia'], 2) . '<br>' .
                    'Cheque: ' . number_format($row['ingresos_cheque'], 2)
                    . '</span>' .
                    '</span>',
                'gastos' => '<span>
                    <a href="#" class="hide-cell text-danger">
                        ' . number_format($row['egresos_caja'] + $row['egresos_fuera'], 2) . '
                    </a>
                    <span id="toggle" class="toggle-right toggle-md">' .
                    'Gastos de caja: ' . number_format($row['egresos_caja'], 2) . '<br>' .
                    'Gastos fuera de caja: ' . number_format($row['egresos_fuera'], 2) . '<br>' .
                    'Retiros: ' . number_format($row['retiros'], 2)
                    . '</span>' .
                    '</span>',

                'diferencia' => '<span class="hide-cell">' . number_format($row['diferencia']) . '</span>',
                'fecha_apertura' => $row['fecha_apertura'],
                'fecha_cierre' => $row['fecha_cierre'],
                'estado' => '<p class="hide-cell ' . $row['estado'] . '">' . ucwords($row['estado']) . '</p>',
                'acciones' => $acciones
            ];
        }
    ]);
}

// Index de ventas diarias

if ($_POST['action'] == "index_ventas_hoy") {

    $db = Database::connect();
    $config = Database::getConfig();

    // Definir las columnas disponibles para ordenamiento
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

    $table_width_joins = "";

    if (isset($config['auto_cierre']) && $config['auto_cierre'] === 'false') {

        $table_width_joins = '
             -- Subconsulta 1: Facturas de Ventas
            (SELECT f.factura_venta_id AS id, "n/d" AS orden , c.nombre, c.apellidos, f.fecha AS fecha_factura, f.total, f.recibido, f.pendiente, s.nombre_estado AS estado, "FT" AS tipo, m.nombre_metodo FROM facturas_ventas f
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE CONCAT(f.fecha," ",f.hora) >= (
            SELECT fecha_apertura 
            FROM cierres_caja 
            WHERE estado = "abierto" 
            ORDER BY fecha_apertura DESC 
            LIMIT 1)

            UNION ALL

             -- Subconsulta 2: Facturas de RP
            SELECT f.facturarp_id AS id, f.orden_rp_id AS orden,c.nombre, c.apellidos, f.fecha AS fecha_factura, f.total, f.recibido, f.pendiente, s.nombre_estado AS estado, "RP" AS tipo, m.nombre_metodo FROM facturasRP f
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE CONCAT(f.fecha," ",f.hora) >= (
            SELECT fecha_apertura 
            FROM cierres_caja 
            WHERE estado = "abierto" 
            ORDER BY fecha_apertura DESC 
            LIMIT 1) 

            UNION ALL

            -- Subconsulta 3: Pagos de Facturas de Ventas
            SELECT pg.pago_id AS id, f.factura_venta_id AS orden, c.nombre, c.apellidos, pg.fecha AS fecha_factura,
            pg.recibido AS total, pg.recibido AS recibido, "0" AS pendiente, "Abono" AS estado,"PF" AS tipo, m.nombre_metodo
            FROM pagos_a_facturas_ventas p
            INNER JOIN pagos pg ON pg.pago_id = p.pago_id
            INNER JOIN facturas_ventas f ON f.factura_venta_id = p.factura_venta_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = pg.metodo_pago_id
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE (s.nombre_estado = "por cobrar" OR (s.nombre_estado <> "por cobrar" AND f.fecha <> pg.fecha))
            AND CONCAT(pg.fecha," ",pg.hora) >= (
            SELECT fecha_apertura 
            FROM cierres_caja 
            WHERE estado = "abierto" 
            ORDER BY fecha_apertura DESC 
            LIMIT 1)

            UNION ALL

            -- Subconsulta 4: Pagos de Facturas de RP
            SELECT pg.pago_id AS id, f.facturarp_id AS orden, c.nombre, c.apellidos, pg.fecha AS fecha_factura,
            pg.recibido AS total, pg.recibido AS recibido, "0" AS pendiente, "Abono" AS estado,"PR" AS tipo, m.nombre_metodo
            FROM pagos_a_facturasRP p
            INNER JOIN pagos pg ON pg.pago_id = p.pago_id
            INNER JOIN facturasRP f ON f.facturarp_id = p.facturarp_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = pg.metodo_pago_id
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE (s.nombre_estado = "por cobrar" OR (s.nombre_estado <> "por cobrar" AND f.fecha <> pg.fecha))
            AND CONCAT(pg.fecha," ",pg.hora) >= (
            SELECT fecha_apertura 
            FROM cierres_caja 
            WHERE estado = "abierto" 
            ORDER BY fecha_apertura DESC 
            LIMIT 1)

            ) AS ventas_del_dia_rango_cierre';
    } else {
        $table_width_joins = '
            -- Subconsulta 1: Facturas de Ventas
            (SELECT f.factura_venta_id AS id, "n/d" AS orden, c.nombre, c.apellidos, f.fecha AS fecha_factura, f.total, f.recibido, f.pendiente, s.nombre_estado AS estado, "FT" AS tipo, m.nombre_metodo 
            FROM facturas_ventas f
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE DATE(f.fecha) = CURDATE()

            UNION ALL

            -- Subconsulta 2: Facturas de RP
            SELECT f.facturarp_id AS id, f.orden_rp_id AS orden, c.nombre, c.apellidos, f.fecha AS fecha_factura, f.total, f.recibido, f.pendiente, s.nombre_estado AS estado, "RP" AS tipo, m.nombre_metodo 
            FROM facturasRP f
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE DATE(f.fecha) = CURDATE()

            UNION ALL

            -- Subconsulta 3: Pagos de Facturas de Ventas
            SELECT pg.pago_id AS id, f.factura_venta_id AS orden, c.nombre, c.apellidos, pg.fecha AS fecha_factura,
                pg.recibido AS total, pg.recibido AS recibido, "0" AS pendiente, s.nombre_estado AS estado, "PF" AS tipo, m.nombre_metodo
            FROM pagos_a_facturas_ventas p
            INNER JOIN pagos pg ON pg.pago_id = p.pago_id
            INNER JOIN facturas_ventas f ON f.factura_venta_id = p.factura_venta_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = pg.metodo_pago_id
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE (s.nombre_estado = "por cobrar" OR (s.nombre_estado <> "por cobrar" AND f.fecha <> pg.fecha))
            AND DATE(pg.fecha) = CURDATE()

            UNION ALL

            -- Subconsulta 4: Pagos de Facturas de RP
            SELECT pg.pago_id AS id, f.facturarp_id AS orden, c.nombre, c.apellidos, pg.fecha AS fecha_factura,
                pg.recibido AS total, pg.recibido AS recibido, "0" AS pendiente, s.nombre_estado AS estado, "PR" AS tipo, m.nombre_metodo
            FROM pagos_a_facturasRP p
            INNER JOIN pagos pg ON pg.pago_id = p.pago_id
            INNER JOIN facturasRP f ON f.facturarp_id = p.facturarp_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = pg.metodo_pago_id
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE (s.nombre_estado = "por cobrar" OR (s.nombre_estado <> "por cobrar" AND f.fecha <> pg.fecha))
            AND DATE(pg.fecha) = CURDATE()
        ) AS ventas_del_dia';
    }

    // Parámetros de configuración para la función handleDataTableRequest
    $params = [
        'columns' => $columns, // Las columnas que se pueden ordenar
        'searchable' => [
            'nombre',
            'apellidos',
            'tipo',
            'orden',
            'estado'
        ], // Columnas sobre las que se puede aplicar búsqueda
        'base_table' => '(SELECT f.factura_venta_id AS id FROM facturas_ventas f
            UNION ALL
            SELECT f.facturarp_id AS id FROM facturasRP f
            UNION ALL
            SELECT pg.pago_id AS id FROM pagos_a_facturas_ventas p 
            INNER JOIN pagos pg ON pg.pago_id = p.pago_id
            UNION ALL
            SELECT pg.pago_id AS id FROM pagos_a_facturasRP p 
            INNER JOIN pagos pg ON pg.pago_id = p.pago_id) AS all_data', // Base table

        'table_with_joins' => $table_width_joins,
        'select' => 'SELECT id, tipo, orden, nombre, apellidos, total, recibido, pendiente, estado, fecha_factura, nombre_metodo', // Selección de columnas
        'base_condition' => '1=1', // Condición base (aquí se puede agregar más filtros si es necesario)
        'table_rows' => function ($row) use ($db) {
            // Formatear los resultados para DataTables
            $acciones = '';

            // Generar botones de acción según el tipo de factura
            if ($row['tipo'] == 'FT') {
                if ($_SESSION['identity']->nombre_rol == 'administrador') {
                    $acciones .= '<a class="btn-action action-info" href="' . base_url . 'invoices/edit&id=' . $row['id'] . '" title="editar">' . BUTTON_EDIT . '</a>';
                } else {
                    $acciones .= '<a class="btn-action action-info action-disable" href="#" title="editar">' . BUTTON_EDIT . '</a>';
                }
            } elseif ($row['tipo'] == 'RP') {
                if ($row['estado'] != 'Anulada' && $_SESSION['identity']->nombre_rol == 'administrador') {
                    $acciones .= '<a class="btn-action action-info" href="' . base_url . 'invoices/repair_edit&id=' . $row['orden'] . '" title="Editar">' . BUTTON_EDIT . '</a>';
                } else {
                    $acciones .= '<a class="btn-action action-info action-disable" href="#" title="Editar">' . BUTTON_EDIT . '</a>';
                }
            }

            // Botón de eliminar
            $acciones .= '<span ';
            if ($_SESSION['identity']->nombre_rol == 'administrador') {
                $acciones .= 'class="action-danger btn-action"';
                if ($row['tipo'] == 'FT') {
                    $acciones .= ' onclick="deleteInvoice(\'' . $row['id'] . '\')"';
                } elseif ($row['tipo'] == 'RP') {
                    $acciones .= ' onclick="deleteInvoiceRP(\'' . $row['id'] . '\')"';
                } elseif ($row['tipo'] == 'PF') {
                    $acciones .= ' onclick="deletePayment(\'' . $row['id'] . '\', \'' . $row['orden'] . '\', 0)"';
                } elseif ($row['tipo'] == 'PR') {
                    $acciones .= ' onclick="deletePayment(\'' . $row['id'] . '\', 0, \'' . $row['orden'] . '\')"';
                }
            } else {
                $acciones .= 'class="action-danger btn-action action-disable"';
            }
            $acciones .= ' title="Eliminar">' . BUTTON_DELETE . '</span>';

            // Verificar si la factura tiene detalles
            $hasDetails = Help::checkIfInvoiceHasDetails($row['id'], $row['tipo']);

            return [
                'id' => '<span><a href="#">' . $row['tipo'] . '-00' . $row['id'] . '</a><span id="toggle" class="toggle-right toggle-md">' . 'Método: ' . $row['nombre_metodo'] . '</span></span>',
                'nombre' => ucwords($row['nombre'] . ' ' . $row['apellidos']),
                'fecha' => $row['fecha_factura'],
                'total' => '<span class="text-primary">' . number_format($row['total'], 2) . '</span>',
                'recibido' => '<span class="text-success">' . number_format($row['recibido'], 2) . '</span>',
                'pendiente' => '<span class="text-danger">' . number_format($row['pendiente'], 2) . '</span>',
                // 'estado' => $hasDetails ? '<p class="' . $row['estado'] . '">' . $row['estado'] . '</p>' : '<p class="no-details">N/D</p>',
                'estado' => $hasDetails
                    ? '<p class="' . ($row['estado'] ?? 'sin-estado') . '">' . ($row['estado'] ?? 'N/D') . '</p>'
                    : '<p class="no-details">N/D</p>',
                'acciones' => $acciones
            ];
        }
    ];

    // Llamar a la función handleDataTableRequest para procesar la solicitud
    handleDataTableRequest($db, $params);

    exit();
}





if ($_POST['action'] == "productos_vendidos") {

    $db = Database::connect();

    $q = $_POST['query'];
    $d1 = $_POST['dateq1'];
    $d2 = $_POST['dateq2'];

    $query = "SELECT p.nombre_producto, sum(d.cantidad) as cantidad,
    sum(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) as costo, 
    sum(d.precio * d.cantidad - d.descuento) as total,
    sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia  
    from detalle_facturas_ventas d 
    inner join detalle_ventas_con_productos dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join productos p on p.producto_id = dp.producto_id
    where p.nombre_producto like '%$q%' and d.fecha between '$d1' and '$d2' group by p.nombre_producto order by total desc;";
    $result = $db->query($query);

    jsonQueryResult($db, $query);
}


if ($_POST['action'] == "servicios_vendidos") {

    $db = Database::connect();

    $q = $_POST['query'];
    $d1 = $_POST['dateq1'];
    $d2 = $_POST['dateq2'];

    $query = "SELECT nombre, sum(cantidad) as cantidad, sum(costo) as costo,
    sum(total) as total, sum(ganancia) as ganancia FROM (

    SELECT s.nombre_servicio as nombre, 'Servicio' as tipo ,sum(d.cantidad) as cantidad, 
    sum(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad,0)) as costo,
    -- Total facturado (precio - descuento)
    sum(d.precio * d.cantidad - d.descuento) as total,
    -- Ganancia = total - costo
	sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
    from detalle_facturas_ventas d 
    inner join detalle_ventas_con_servicios ds on ds.detalle_venta_id = d.detalle_venta_id
    inner join servicios s on s.servicio_id = ds.servicio_id 
    where s.nombre_servicio like '%$q%' and d.fecha between '$d1' and '$d2' group by s.nombre_servicio

    UNION ALL
    
    SELECT s.nombre_servicio as nombre,'Servicio' as tipo, sum(d.cantidad) as cantidad, 
    sum(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad,0)) as costo,
    -- Total facturado (precio - descuento)
    sum(d.precio * d.cantidad - d.descuento) as total,
    -- Ganancia = total - costo
	sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
    from detalle_ordenRP d 
    inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
    inner join detalle_ordenRP_con_servicios dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
    inner join servicios s on s.servicio_id = dp.servicio_id
    where s.nombre_servicio like '%$q%' and d.fecha between '$d1' and '$d2' group by s.nombre_servicio

    ) servicios_vendidos group by nombre order by total desc;";

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

    SELECT p.nombre_pieza as nombre,'Pieza' as tipo,sum(d.cantidad) as cantidad,
    sum(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) as costo, 
    sum(d.precio * d.cantidad - d.descuento) as total,
    sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia 
    from detalle_facturas_ventas d 
    inner join detalle_ventas_con_piezas_ dp on dp.detalle_venta_id = d.detalle_venta_id
    inner join piezas p on p.pieza_id = dp.pieza_id
    where p.nombre_pieza like '%$q%' and d.fecha between '$d1' and '$d2' group by p.nombre_pieza

    UNION ALL

    SELECT p.nombre_pieza as nombre,'Pieza' as tipo,sum(d.cantidad) as cantidad,
    sum(d.precio * d.cantidad - d.descuento) as total,
	sum(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) as costo, 
    sum((d.precio * d.cantidad - d.descuento)-(p.precio_costo * d.cantidad)) as ganancia  
    from detalle_ordenRP d 
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

// Obtener datos de JSON del cierre
if ($_POST['action'] === 'obtener_datos_caja') {
    $db = Database::connect();

    $data = [
        'caja' => [
            'apertura' => Help::getCashOpening(),
            'total_real' => Help::getTotalReal(),
        ],

        'ventas' => [
            'tickets_emitidos' => Help::getIssuedInvoices(),

            'pagos' => [
                'efectivo'      => Help::getDailySalesByPaymentMethod(1),
                'tarjeta_credito' => Help::getDailySalesByPaymentMethod(2),
                'tarjeta_debito'  => Help::getDailySalesByPaymentMethod(3),
                'tarjeta_total'   => Help::getDailySalesByPaymentMethod(2)
                                     + Help::getDailySalesByPaymentMethod(3),
                'transferencias' => Help::getDailySalesByPaymentMethod(4),
                'cheques'        => Help::getDailySalesByPaymentMethod(5),
            ]
        ],

        'gastos' => [
            'desde_caja'  => Help::getOriginExpensesToday('caja'),
            'fuera_caja'  => Help::getOriginExpensesToday('fuera_caja'),
        ]
    ];

    echo json_encode($data);
    exit;
}
