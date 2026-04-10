<?php

require_once '../config/db.php';
require_once '../help.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();
$db = Database::connect();
$config = Database::getConfig();
$user_id = $_SESSION['identity']->usuario_id;
$action = $_POST['action'] ?? null;

$permissions = [

    // Cierre de caja
    'abrir_caja' => [],
    'cierre_caja'  => [],
    'obtener_datos_caja'  => [],
    'index_cierre_caja'  => [],
    'eliminar_cierre' => ['administrador'],
    'imprimir_cierre' => [],

    // Ventas
    'index_ventas_hoy'  => [],

    // Consultas
    'piezas_vendidas'  => ['administrador'],
    'serial_facturado'  => [],

    // Reportes de ventas
    'reporte_ventas' => ['administrador'],
    'resumen_reporte_venta' => ['administrador'],

    // Equipos vendidos
    'equipos_vendidos' => ['administrador'],

    // Reportes por cantidades
    'item_vendidos' => ['administrador'],
    'resumen_reporte_cantidades' => ['administrador'],

    // Reportes de gastos
    'reportes_por_periodo' => ['administrador'],
    'resumen_gastos_periodo' => ['administrador'],

    // Reportes de ganancias
    'ganancias_por_periodo' => ['administrador'],
    'resumen_ganancias_periodo' => ['administrador'],
];

// Chequear permisos
if (isset($_POST['action'])) {
    check_permission_action($_POST['action'], $permissions);
}

switch ($action) {
    // Abrir cierre de caja
    case 'abrir_caja':
        $params = [
            (int) $_SESSION['identity']->usuario_id,
            $_POST['opening_date'],
            $_POST['initial_balance'],
        ];

        $result = handleProcedureAction($db, 'c_aperturaCaja', $params);

        // WEBSOCKET
        webSocketServer('/api/box/open');

        echo $result;
        break;
    // Cerrar caja
    case 'cierre_caja':
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

        $result = handleProcedureAction($db, 'c_cierreCaja', $params);

        // WEBSOCKET
        webSocketServer('/api/box/close');

        echo $result;
        break;
    // Obtener los datos del cierre
    case 'obtener_datos_caja':

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
        break;
    // Mostrar todos los cierres
    case 'index_cierre_caja':
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
                $acciones .= ' class="action-danger btn-action generate_pdf" data-id="' . $row['cierre_id'] . '" ';
                $acciones .= ' title="PDF">' . BUTTON_PDF . '</span>';

                $acciones .= '<span ';
                $acciones .= ' class="action-info btn-action print_closing" data-id="' . $row['cierre_id'] . '" ';
                $acciones .= ' title="Imprimir">' . BUTTON_PRINT . '</span>';

                // Eliminar
                $acciones .= '<span ';
                if ($_SESSION['identity']->nombre_rol == 'administrador') {
                    $acciones .= ' class="action-danger btn-action erase_closing" data-id="' . $row['cierre_id'] . '"';
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
        break;
    // Eliminar cierre de caja
    case 'eliminar_cierre':
        $db->query("SET @usuario_id = " . (int)$user_id);
        echo handleDeletionAction($db, (int)$_POST['id'], 'c_eliminarCierre');
        break;
    case 'imprimir_cierre':
        $id = $_POST["id"];

        $sql = "SELECT *,concat(u.nombre,' ',IFNULL(u.apellidos,'')) as nombre_completo FROM cierres_caja c 
        INNER JOIN usuarios u ON u.usuario_id = c.usuario_id 
        WHERE c.cierre_id = '$id'";

        echo jsonQueryResult($db, $sql);

        break;
    // Ventas del dia
    case 'index_ventas_hoy':

        $user_condition = "";  // Inicialización de la variable

        // Verificar el valor de modo_cierre y modificar la variable según corresponda
        if (isset($config['modo_cierre']) && $config['modo_cierre'] === "separado" && $_SESSION['identity']->nombre_rol != 'administrador') {
            $user_condition = "AND x.usuario_id = '$user_id'";
        }

        // Determinar el rango de fechas
        $date_condition = isset($config['auto_cierre']) && $config['auto_cierre'] === 'false'
            ? "CONCAT(x.fecha, ' ', x.hora) >= (SELECT fecha_apertura FROM cierres_caja WHERE estado = 'abierto' ORDER BY fecha_apertura DESC LIMIT 1)"
            : "x.fecha = CURDATE()";

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

        $table_width_joins = "-- Subconsulta 1: Facturas de Ventas
            (SELECT x.factura_venta_id AS id, 'n/d' AS orden , c.nombre, c.apellidos, x.fecha AS fecha_factura, x.total, x.recibido, x.pendiente, s.nombre_estado AS estado, 'FT' AS tipo, m.nombre_metodo FROM facturas_ventas x
            INNER JOIN clientes c ON x.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            INNER JOIN estados_generales s ON x.estado_id = s.estado_id
            WHERE $date_condition $user_condition

            UNION ALL

             -- Subconsulta 2: Facturas de RP
            SELECT x.facturarp_id AS id, x.orden_rp_id AS orden,c.nombre, c.apellidos, x.fecha AS fecha_factura, x.total, x.recibido, x.pendiente, s.nombre_estado AS estado, 'RP' AS tipo, m.nombre_metodo FROM facturasRP x
            INNER JOIN clientes c ON x.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            INNER JOIN estados_generales s ON x.estado_id = s.estado_id
            WHERE $date_condition $user_condition

            UNION ALL

            -- Subconsulta 3: Pagos de Facturas de Ventas
            SELECT x.pago_id AS id, f.factura_venta_id AS orden, c.nombre, c.apellidos, x.fecha AS fecha_factura,
            x.recibido AS total, x.recibido AS recibido, '0' AS pendiente, 'Abono' AS estado,'PF' AS tipo, m.nombre_metodo
            FROM pagos_a_facturas_ventas p
            INNER JOIN pagos x ON x.pago_id = p.pago_id
            INNER JOIN facturas_ventas f ON f.factura_venta_id = p.factura_venta_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE (s.nombre_estado = 'por cobrar' OR (s.nombre_estado <> 'por cobrar' AND f.fecha <> x.fecha))
            AND $date_condition $user_condition

            UNION ALL

            -- Subconsulta 4: Pagos de Facturas de RP
            SELECT x.pago_id AS id, f.facturarp_id AS orden, c.nombre, c.apellidos, x.fecha AS fecha_factura,
            x.recibido AS total, x.recibido AS recibido, '0' AS pendiente, 'Abono' AS estado,'PR' AS tipo, m.nombre_metodo
            FROM pagos_a_facturasRP p
            INNER JOIN pagos x ON x.pago_id = p.pago_id
            INNER JOIN facturasRP f ON f.facturarp_id = p.facturarp_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            INNER JOIN clientes c ON f.cliente_id = c.cliente_id
            INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            WHERE (s.nombre_estado = 'por cobrar' OR (s.nombre_estado <> 'por cobrar' AND f.fecha <> x.fecha))
            AND $date_condition $user_condition

            ) AS ventas_del_dia";


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
            'base_table' => '(SELECT x.factura_venta_id AS id FROM facturas_ventas x
            UNION ALL
            SELECT x.facturarp_id AS id FROM facturasRP x
            UNION ALL
            SELECT x.pago_id AS id FROM pagos_a_facturas_ventas p 
            INNER JOIN pagos x ON x.pago_id = p.pago_id
            UNION ALL
            SELECT x.pago_id AS id FROM pagos_a_facturasRP p 
            INNER JOIN pagos x ON x.pago_id = p.pago_id) AS all_data', // Base table

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
                    $acciones .= 'class="action-danger btn-action';
                    if ($row['tipo'] == 'FT') {
                        $acciones .= ' erase_invoice" data-id="' . $row['id'] . '"';
                    } elseif ($row['tipo'] == 'RP') {
                        $acciones .= '" onclick="deleteInvoiceRP(\'' . $row['id'] . '\')"';
                    } elseif ($row['tipo'] == 'PF') {
                        $acciones .= '" onclick="deletePayment(\'' . $row['id'] . '\', \'' . $row['orden'] . '\', 0)"';
                    } elseif ($row['tipo'] == 'PR') {
                        $acciones .= '" onclick="deletePayment(\'' . $row['id'] . '\', 0, \'' . $row['orden'] . '\')"';
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
        exit;
        break;

    // Piezas vendidas
    case 'piezas_vendidas':

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
        break;

    // Buscar el serial facturado
    case 'serial_facturado':
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
        break;
    case 'reporte_ventas':

        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin    = $_POST['fecha_final'] ?? date('Y-m-d');
        $usuario_id   = $_POST['usuario_id'] ?? 0;
        $customer_id  = $_POST['customer'] ?? 0;
        $method_id = $_POST['method'] ?? 0;

        $fecha_inicio = $db->real_escape_string($fecha_inicio);
        $fecha_fin    = $db->real_escape_string($fecha_fin);
        $usuario_id   = intval($usuario_id);
        $customer_id  = intval($customer_id);
        $method_id = intval($method_id);

        // Condición base
        $baseCondition = "TIMESTAMP(x.fecha, x.hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

        if ($usuario_id > 0) {
            $baseCondition .= " AND x.usuario_id = $usuario_id";
        }

        // Si hay cliente, se agrega
        if ($customer_id > 0) {
            $baseCondition .= " AND x.cliente_id = $customer_id";
        }

        if ($method_id > 0) {
            $baseCondition .= " AND x.metodo_pago_id = $method_id";
        }

        // Definir las columnas disponibles para ordenamiento
        $columns = [
            'orden',
            'id',
            'nombre',
            'apellidos',
            'fecha_factura',
            'hora',
            'total',
            'recibido',
            'pendiente',
            'estado',
            'tipo'
        ];

        $table_width_joins = "-- Subconsulta 1: Facturas de Ventas
            (SELECT x.factura_venta_id AS id, 'n/d' AS orden , c.nombre, c.apellidos, x.fecha AS fecha_factura,x.hora, x.total, x.recibido, x.pendiente, s.nombre_estado AS estado, 'FT' AS tipo, m.nombre_metodo FROM facturas_ventas x
            INNER JOIN clientes c ON x.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            INNER JOIN estados_generales s ON x.estado_id = s.estado_id
            WHERE $baseCondition

            UNION ALL

             -- Subconsulta 2: Facturas de RP
            SELECT x.facturarp_id AS id, x.orden_rp_id AS orden,c.nombre, c.apellidos, x.fecha AS fecha_factura,x.hora, x.total, x.recibido, x.pendiente, s.nombre_estado AS estado, 'RP' AS tipo, m.nombre_metodo FROM facturasRP x
            INNER JOIN clientes c ON x.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            INNER JOIN estados_generales s ON x.estado_id = s.estado_id
            WHERE $baseCondition

            ) AS reporte";


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
            'base_table' => '(SELECT x.factura_venta_id AS id FROM facturas_ventas x
            UNION ALL
            SELECT x.facturarp_id AS id FROM facturasRP x
            ) AS all_data', // Base table

            'table_with_joins' => $table_width_joins,

            'select' => 'SELECT id, tipo, orden, nombre, apellidos, total, recibido, pendiente, estado, fecha_factura,hora, nombre_metodo', // Selección de columnas

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
                    $acciones .= 'class="action-danger btn-action';
                    if ($row['tipo'] == 'FT') {
                        $acciones .= ' erase_invoice" data-id="' . $row['id'] . '"';
                    } elseif ($row['tipo'] == 'RP') {
                        $acciones .= ' onclick="deleteInvoiceRP(\'' . $row['id'] . '\')"';
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
                    'hora' => $row['hora'],
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

        handleDataTableRequest($db, $params);

        break;
    case 'resumen_reporte_venta':

        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin    = $_POST['fecha_final'] ?? date('Y-m-d');
        $usuario_id   = $_POST['usuario_id'] ?? 0;
        $customer_id   = $_POST['customer'] ?? 0;
        $method_id = $_POST['method'] ?? 0;

        $fecha_inicio = $db->real_escape_string($fecha_inicio);
        $fecha_fin    = $db->real_escape_string($fecha_fin);
        $usuario_id   = intval($usuario_id);
        $customer_id   = intval($customer_id);
        $method_id = intval($method_id);

        // Condición base
        $baseCondition = "TIMESTAMP(x.fecha, x.hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

        if ($usuario_id > 0) {
            $baseCondition .= " AND x.usuario_id = $usuario_id";
        }

        // Si hay cliente, se agrega
        if ($customer_id > 0) {
            $baseCondition .= " AND x.cliente_id = $customer_id";
        }

        if ($method_id > 0) {
            $baseCondition .= " AND x.metodo_pago_id = $method_id";
        }

        $sql = "SELECT count(*) AS total_facturas, sum(recibido) as total, sum(pendiente) AS pendiente FROM
           -- Subconsulta 1: Facturas de Ventas
            (SELECT  x.total, x.recibido, x.pendiente FROM facturas_ventas x
            INNER JOIN clientes c ON x.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            INNER JOIN estados_generales s ON x.estado_id = s.estado_id
            WHERE $baseCondition

            UNION ALL

             -- Subconsulta 2: Facturas de RP
            SELECT x.total, x.recibido, x.pendiente FROM facturasRP x
            INNER JOIN clientes c ON x.cliente_id = c.cliente_id
            INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = x.metodo_pago_id
            INNER JOIN estados_generales s ON x.estado_id = s.estado_id
            WHERE $baseCondition
         

            ) AS reporte";

        echo jsonQueryResult($db, $sql);
        break;
    case 'equipos_vendidos':

        $product_id  = intval($_POST['product_id'] ?? 0);
        $provider_id = intval($_POST['provider'] ?? 0);
        $serial      = $_POST['serial'] ?? '';

        $conditions = [];

        if ($product_id > 0) {
            $conditions[] = "p.producto_id = $product_id";
        }

        if ($provider_id > 0) {
            $conditions[] = "pr.proveedor_id = $provider_id";
        }

        if (!empty($serial)) {
            $conditions[] = "v.serial LIKE '%$serial%'";
        }

        $baseCondition = implode(' AND ', $conditions);

        handleDataTableRequest($db, [
            'columns' => [
                'factura_venta_id',
                'nombre',
                'serial',
                'costo_unitario',
                'nombre_producto',
                'entrada',
                'salida'
            ],

            'searchable' => [
                'factura_venta_id',
                'nombre',
                'serial',
                'costo_unitario',
                'nombre_producto',
                'entrada',
                'salida'
            ],

            'base_table' => 'variantes v',

            'table_with_joins' => 'variantes v 
                inner join variantes_facturadas vf on v.variante_id = vf.variante_id
                inner join detalle_facturas_ventas d on d.detalle_venta_id = vf.detalle_venta_id
                inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
                inner join variantes_con_proveedores vp on vp.variante_id = v.variante_id
                inner join proveedores pr on pr.proveedor_id = vp.proveedor_id
                inner join productos p on p.producto_id = v.producto_id',

            'select' => 'SELECT f.factura_venta_id, concat(pr.nombre_proveedor," ",IFNULL(pr.apellidos,"")) as nombre, v.serial, 
                        p.nombre_producto,v.costo_unitario, v.fecha as entrada, f.fecha as salida',

            'base_condition' => $baseCondition,

            'table_rows' => function ($row) {

                return [
                    'id' => $row['factura_venta_id'],
                    'proveedor' => ucwords($row['nombre']),
                    'producto' => ucwords($row['nombre_producto']),
                    'serial' => $row['serial'],
                    'costo' => number_format($row['costo_unitario'], 2),
                    'entrada' => $row['entrada'],
                    'salida' => '<span class="text-danger">' . $row['salida'] . '</span>'
                ];
            }
        ]);
        break;
    case 'item_vendidos':

        $query = $_POST['query'] ?? '';
        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin    = $_POST['fecha_final'] ?? date('Y-m-d');

        $query = $db->real_escape_string($query);
        $fecha_inicio = $db->real_escape_string($fecha_inicio);
        $fecha_fin    = $db->real_escape_string($fecha_fin);

        $table_width_joins = "(
        SELECT p.nombre_producto as nombre, 'Producto' as tipo,
        sum(d.cantidad) as cantidad,
        sum(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) as costo, 
        sum(d.precio * d.cantidad - d.descuento) as total,
        sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia  
        from detalle_facturas_ventas d 
        inner join detalle_ventas_con_productos dp on dp.detalle_venta_id = d.detalle_venta_id
        inner join productos p on p.producto_id = dp.producto_id
        where p.nombre_producto like '%$query%' and d.fecha 
        between '$fecha_inicio' and '$fecha_fin' group by p.nombre_producto

        UNION ALL

        SELECT s.nombre_servicio as nombre, 'Servicio' as tipo ,
        sum(d.cantidad) as cantidad, 
        sum(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad,0)) as costo,
        -- Total facturado (precio - descuento)
        sum(d.precio * d.cantidad - d.descuento) as total,
        -- Ganancia = total - costo
        sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
        from detalle_facturas_ventas d 
        inner join detalle_ventas_con_servicios ds on ds.detalle_venta_id = d.detalle_venta_id
        inner join servicios s on s.servicio_id = ds.servicio_id 
        where s.nombre_servicio like '%$query%' and d.fecha 
        between '$fecha_inicio' and '$fecha_fin' group by s.nombre_servicio

        UNION ALL

        SELECT s.nombre_servicio as nombre, 'Servicio' as tipo, 
        sum(d.cantidad) as cantidad, 
        sum(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad,0)) as costo,
        -- Total facturado (precio - descuento)
        sum(d.precio * d.cantidad - d.descuento) as total,
        -- Ganancia = total - costo
        sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
        from detalle_ordenRP d 
        inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
        inner join detalle_ordenRP_con_servicios dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
        inner join servicios s on s.servicio_id = dp.servicio_id
        where s.nombre_servicio like '%$query%' and d.fecha 
        between '$fecha_inicio' and '$fecha_fin' group by s.nombre_servicio
        ) AS items_vendidos";

        handleDataTableRequest($db, [
            'columns' => ['nombre', 'tipo', 'cantidad', 'costo', 'total', 'ganancia'],
            'searchable' => ['nombre', 'tipo'],

            'base_table' => '(SELECT d.detalle_venta_id as id FROM detalle_facturas_ventas d 
                            UNION ALL
                            SELECT d.detalle_ordenRP_id as id FROM detalle_ordenRP d 
                            ) AS all_data',

            'table_with_joins' => $table_width_joins,

            'select' => 'SELECT nombre, tipo ,sum(cantidad) as cantidad, sum(costo) as costo,
                         sum(total) as total, sum(ganancia) as ganancia',

            'group_by' => 'nombre, tipo',

            'base_condition' => '1=1',

            'table_rows' => function ($row) {
                return [
                    'descripcion' => $row['nombre'],
                    'tipo' => $row['tipo'],
                    'cantidad' => $row['cantidad'],
                    'costo' => '<span class="text-danger">' . number_format($row['costo'], 2) . '</span>',
                    'total' => number_format($row['total'], 2),
                    'ganancias' => '<span class="text-success">' . number_format($row['ganancia'], 2) . '</span>'
                ];
            }
        ]);

        break;
    case 'resumen_reporte_cantidades':

        $query = $_POST['query'] ?? '';
        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin    = $_POST['fecha_final'] ?? date('Y-m-d');

        $query = $db->real_escape_string($query);
        $fecha_inicio = $db->real_escape_string($fecha_inicio);
        $fecha_fin    = $db->real_escape_string($fecha_fin);

        $sql = "SELECT count(id) as id ,sum(cantidad) as cantidad, sum(costo) as costo,
        sum(total) as total, sum(ganancia) as ganancia FROM (

        SELECT count(d.detalle_venta_id) as id,
        sum(d.cantidad) as cantidad,
        sum(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) as costo, 
        sum(d.precio * d.cantidad - d.descuento) as total,
        sum((d.precio * d.cantidad - d.descuento)-(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad)) as ganancia  
        from detalle_facturas_ventas d 
        inner join detalle_ventas_con_productos dp on dp.detalle_venta_id = d.detalle_venta_id
        inner join productos p on p.producto_id = dp.producto_id
        where p.nombre_producto like '%$query%' and d.fecha 
        between '$fecha_inicio' and '$fecha_fin' group by p.nombre_producto

        UNION ALL

        SELECT count(d.detalle_venta_id) as id,
        sum(d.cantidad) as cantidad, 
        sum(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad,0)) as costo,
        -- Total facturado (precio - descuento)
        sum(d.precio * d.cantidad - d.descuento) as total,
        -- Ganancia = total - costo
        sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
        from detalle_facturas_ventas d 
        inner join detalle_ventas_con_servicios ds on ds.detalle_venta_id = d.detalle_venta_id
        inner join servicios s on s.servicio_id = ds.servicio_id 
        where s.nombre_servicio like '%$query%' and d.fecha 
        between '$fecha_inicio' and '$fecha_fin' group by s.nombre_servicio

        UNION ALL

        SELECT count(dp.detalle_ordenRP_id) as id,
        sum(d.cantidad) as cantidad, 
        sum(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad,0)) as costo,
        -- Total facturado (precio - descuento)
        sum(d.precio * d.cantidad - d.descuento) as total,
        -- Ganancia = total - costo
        sum((d.precio * d.cantidad - d.descuento)-COALESCE((IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo)) * d.cantidad,0)) as ganancia
        from detalle_ordenRP d 
        inner join facturasRP frp on frp.orden_rp_id = d.orden_rp_id
        inner join detalle_ordenRP_con_servicios dp on dp.detalle_ordenRP_id = d.detalle_ordenRP_id
        inner join servicios s on s.servicio_id = dp.servicio_id
        where s.nombre_servicio like '%$query%' and d.fecha 
        between '$fecha_inicio' and '$fecha_fin' group by s.nombre_servicio

        ) items_vendidos";

        jsonQueryResult($db, $sql);
        break;
    // reportes de gastos 
    case 'reportes_por_periodo':

        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin    = $_POST['fecha_final'] ?? date('Y-m-d');
        $provider_id  = intval($_POST['provider'] ?? 0);
        $reason_id    =  intval($_POST['reason'] ?? 0);

        $fecha_inicio = $db->real_escape_string($fecha_inicio);
        $fecha_fin    = $db->real_escape_string($fecha_fin);

        $conditions = [];

        // Filtro por proveedor
        if ($provider_id > 0) {
            $conditions[] = "p.proveedor_id = $provider_id";
        }

        // Filtro por motivo de gasto
        if ($reason_id > 0) {
            $conditions[] = "m.motivo_id = $reason_id";
        }

        // Filtro por fecha
        $conditions[] = "TIMESTAMP(x.fecha, x.hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

        // Unir condiciones
        $baseCondition = implode(' AND ', $conditions);

        handleDataTableRequest($db, [
            'columns' => [
                'x.fecha',
                'x.gasto_id',
                'nombre',
                'x.total',
                'x.pagado',
                'x.orden_id'
            ],
            'searchable' => [
                'nombre',
                'x.total',
                'x.pagado',
                'x.fecha'
            ],
            'base_table' => 'gastos',

            'table_with_joins' => 'gastos x 
            INNER JOIN proveedores p ON p.proveedor_id = x.proveedor_id 
            INNER JOIN usuarios u ON u.usuario_id = x.usuario_id
            INNER JOIN detalle_gasto d ON d.orden_id = x.orden_id
            INNER JOIN motivos m ON m.motivo_id = d.motivo_id',

            'select' => 'SELECT x.gasto_id, concat(p.nombre_proveedor," ",IFNULL(p.apellidos,"")) as nombre, x.total, 
            x.pagado, x.orden_id, x.fecha, x.hora, x.observacion',

            'base_condition' => $baseCondition,

            'group_by' => 'x.gasto_id',

            'table_rows' => function ($element) {
                return [
                    'id'    => 'G-00' . $element['gasto_id'],
                    'proveedor' => '<span class="hide-cell">' . ucwords($element['nombre']) . '</span>',
                    'gastos'    => Help::loadSpendingsById($element['orden_id']),
                    'fecha'     => $element['fecha'],
                    'observacion' => $element['observacion'],
                    'total'     => '<span class="text-danger">' . number_format($element['total'], 2) . '</span>',
                    'acciones'  => '<span class="action-danger btn-action delete_bill" data-id="' . $element['orden_id'] . '">' . BUTTON_DELETE . '</span>'
                ];
            }
        ]);
        break;

    case 'resumen_gastos_periodo':

        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin    = $_POST['fecha_final'] ?? date('Y-m-d');
        $provider_id  = intval($_POST['provider'] ?? 0);
        $reason_id    =  intval($_POST['reason'] ?? 0);

        $fecha_inicio = $db->real_escape_string($fecha_inicio);
        $fecha_fin    = $db->real_escape_string($fecha_fin);

        $conditions = [];

        // Filtro por proveedor
        if ($provider_id > 0) {
            $conditions[] = "p.proveedor_id = $provider_id";
        }

        // Filtro por motivo de gasto
        if ($reason_id > 0) {
            $conditions[] = "m.motivo_id = $reason_id";
        }

        // Filtro por fecha
        $conditions[] = "TIMESTAMP(x.fecha, x.hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

        // Unir condiciones
        $baseCondition = implode(' AND ', $conditions);

        $sql = "SELECT 
            COUNT(DISTINCT x.gasto_id) AS facturas,
            SUM(DISTINCT x.total) AS total
        FROM gastos x 
        INNER JOIN proveedores p ON p.proveedor_id = x.proveedor_id 
        INNER JOIN usuarios u ON u.usuario_id = x.usuario_id
        INNER JOIN detalle_gasto d ON d.orden_id = x.orden_id
        INNER JOIN motivos m ON m.motivo_id = d.motivo_id
        WHERE $baseCondition";

        jsonQueryResult($db, $sql);

        break;

    case 'ganancias_por_periodo':

        $baseCondition = "";
        $subCondition = "";

        if (!empty($_POST['fecha_inicio']) && !empty($_POST['fecha_final'])) {
            $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
            $fecha_fin    = $_POST['fecha_final'] ?? date('Y-m-d');

            $fecha_inicio = $db->real_escape_string($fecha_inicio);
            $fecha_fin    = $db->real_escape_string($fecha_fin);

            // Filtro por fecha
            $baseCondition .= "TIMESTAMP(d.fecha, d.hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            $subCondition .= "TIMESTAMP(fecha, hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } else {
            $month = $_POST['month'];
            $year  = $_POST['year'];

            $month = $db->real_escape_string($month);
            $year   = $db->real_escape_string($year);

            // Filtro por mes y año
            $baseCondition .= "MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year'";
            $subCondition .= "MONTH(fecha) = '$month' AND YEAR(fecha) = '$year'";
        }

        $table_with_joins = "
    (SELECT 
        nombre, 
        tipo, 
        SUM(cantidad) AS cantidad, 
        SUM(costo) AS costo,
        SUM(total) AS total, 
        ROUND(SUM(ganancia), 2) AS ganancia
    FROM (

        -- PRODUCTOS
        SELECT 
            p.nombre_producto AS nombre,
            'Producto' AS tipo,
            SUM(d.cantidad) AS cantidad,

            SUM(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) AS costo,

            SUM(d.precio * d.cantidad - d.descuento) AS total,

            SUM(
                ((f.recibido / NULLIF(ft.total_facturado, 0)) * (d.precio * d.cantidad - d.descuento))
                -
                (COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo), 0) * d.cantidad)
            ) AS ganancia

        FROM detalle_facturas_ventas d
        INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id

        INNER JOIN (
            SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
            FROM detalle_facturas_ventas
            WHERE $subCondition
            GROUP BY factura_venta_id
        ) ft ON ft.factura_venta_id = f.factura_venta_id

        INNER JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
        INNER JOIN productos p ON p.producto_id = dp.producto_id

        WHERE $baseCondition
        GROUP BY p.nombre_producto

        UNION ALL

        -- PIEZAS (FACTURAS)
        SELECT 
            p.nombre_pieza,
            'Pieza',
            SUM(d.cantidad),
            SUM(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad),
            SUM(d.precio * d.cantidad - d.descuento),

            SUM(
                ((f.recibido / NULLIF(ft.total_facturado, 0)) * (d.precio * d.cantidad - d.descuento))
                -
                (COALESCE(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo), 0) * d.cantidad)
            )

        FROM detalle_facturas_ventas d
        INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id

        INNER JOIN (
            SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
            FROM detalle_facturas_ventas
            WHERE $subCondition
            GROUP BY factura_venta_id
        ) ft ON ft.factura_venta_id = f.factura_venta_id

        INNER JOIN detalle_ventas_con_piezas_ dp ON dp.detalle_venta_id = d.detalle_venta_id
        INNER JOIN piezas p ON p.pieza_id = dp.pieza_id

        WHERE $baseCondition
        GROUP BY p.nombre_pieza

        UNION ALL

        -- SERVICIOS (FACTURAS)
        SELECT 
            s.nombre_servicio,
            'Servicio',
            SUM(d.cantidad),
            SUM(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)),
            SUM(d.precio * d.cantidad - d.descuento),

            SUM(
                ((f.recibido / NULLIF(ft.total_facturado, 0)) * (d.precio * d.cantidad - d.descuento))
                -
                (COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo), 0) * d.cantidad)
            )

        FROM detalle_facturas_ventas d
        INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id

        INNER JOIN (
            SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
            FROM detalle_facturas_ventas
            WHERE $subCondition
            GROUP BY factura_venta_id
        ) ft ON ft.factura_venta_id = f.factura_venta_id

        INNER JOIN detalle_ventas_con_servicios ds ON ds.detalle_venta_id = d.detalle_venta_id
        INNER JOIN servicios s ON s.servicio_id = ds.servicio_id

        WHERE $baseCondition
        GROUP BY s.nombre_servicio

    ) AS detalle_ventas_mes

    GROUP BY nombre, tipo) as t";

        handleDataTableRequest($db, [
            'columns' => ['t.nombre', 't.cantidad', 't.costo', 't.total', 't.ganancia'],
            'searchable' => ['t.nombre'],
            'base_table' => '',
            'table_with_joins' => $table_with_joins,
            'select' => 'SELECT t.nombre, t.tipo, t.cantidad, t.costo, t.total, t.ganancia',
            'base_condition' => '1=1',
            'table_rows' => function ($row) {
                return [
                    'descripcion' => $row['nombre'],
                    'cantidad_total' => $row['cantidad'],
                    'costo_total' => '<span class="text-danger">' . number_format($row['costo'], 2) . '</span>',
                    'ganancia_total' => '<span class="text-success">' . number_format($row['ganancia'], 2) . '</span>',
                    'total_vendido' => '<span>' . number_format($row['total'], 2) . '</span>'
                ];
            }
        ]);

        break;

    case 'resumen_ganancias_periodo':

        $baseCondition = "";
        $subCondition = "";

        if (!empty($_POST['fecha_inicio']) && !empty($_POST['fecha_final'])) {
            $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
            $fecha_fin    = $_POST['fecha_final'] ?? date('Y-m-d');

            $fecha_inicio = $db->real_escape_string($fecha_inicio);
            $fecha_fin    = $db->real_escape_string($fecha_fin);

            // Filtro por fecha
            $baseCondition .= "TIMESTAMP(d.fecha, d.hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            $subCondition .= "TIMESTAMP(fecha, hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } else {
            $month = $_POST['month'];
            $year  = $_POST['year'];

            $month = $db->real_escape_string($month);
            $year   = $db->real_escape_string($year);

            // Filtro por mes y año
            $baseCondition .= "MONTH(d.fecha) = '$month' AND YEAR(d.fecha) = '$year'";
            $subCondition .= "MONTH(fecha) = '$month' AND YEAR(fecha) = '$year'";
        }

        $sql = "SELECT 
            COUNT(*) AS total_registros,
            SUM(cantidad) AS total_cantidad,
            SUM(costo) AS total_costo,
            SUM(total) AS total_vendido,
            ROUND(SUM(ganancia), 2) AS total_ganancia
        FROM (
            SELECT 
            SUM(cantidad) AS cantidad, 
            SUM(costo) AS costo,
            SUM(total) AS total, 
            ROUND(SUM(ganancia), 2) AS ganancia
            FROM (
                
                -- Productos en facturas de ventas
            SELECT 
            p.nombre_producto AS nombre,
        'Producto' AS tipo,
        SUM(d.cantidad) AS cantidad,

        SUM(
            IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad
        ) AS costo,

        SUM(d.precio * d.cantidad - d.descuento) AS total,

        SUM(
            (
            (f.recibido / NULLIF(ft.total_facturado, 0)) * (d.precio * d.cantidad - d.descuento)
            )
            -
            (
            COALESCE(
                IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo), 0
            ) * d.cantidad
            )
        ) AS ganancia
            FROM detalle_facturas_ventas d
            INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
            INNER JOIN (
            SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
            FROM detalle_facturas_ventas
            WHERE $subCondition
            GROUP BY factura_venta_id
            ) ft ON ft.factura_venta_id = f.factura_venta_id
            INNER JOIN detalle_ventas_con_productos dp ON dp.detalle_venta_id = d.detalle_venta_id
            INNER JOIN productos p ON p.producto_id = dp.producto_id
            WHERE $baseCondition
            GROUP BY p.nombre_producto

            UNION ALL

            -- Piezas en facturas de ventas
            SELECT 
            p.nombre_pieza AS nombre,
            'Pieza' AS tipo,
            SUM(d.cantidad) AS cantidad,
            SUM(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) AS costo,
            SUM(d.precio * d.cantidad - d.descuento) AS total,
            SUM(
            (
            (f.recibido / NULLIF(ft.total_facturado, 0)) * (d.precio * d.cantidad - d.descuento)
            )
            -
            (
            COALESCE(
                IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo), 0
            ) * d.cantidad
            )
        ) AS ganancia
            FROM detalle_facturas_ventas d
            INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
            INNER JOIN (
            SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
            FROM detalle_facturas_ventas
            WHERE $subCondition
            GROUP BY factura_venta_id
            ) ft ON ft.factura_venta_id = f.factura_venta_id
            INNER JOIN detalle_ventas_con_piezas_ dp ON dp.detalle_venta_id = d.detalle_venta_id
            INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
            WHERE $baseCondition
            GROUP BY p.nombre_pieza

            UNION ALL

            -- Piezas en ordenes de reparacion (facturasRP)
            SELECT 
            p.nombre_pieza AS nombre,
            'Pieza' AS tipo,
            SUM(d.cantidad) AS cantidad,
            SUM(IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo) * d.cantidad) AS costo,
            SUM(d.precio * d.cantidad - d.descuento) AS total,
            SUM(
            (
                (frp.recibido / NULLIF(ft.total_facturado, 0)) * (d.precio * d.cantidad - d.descuento)
            )
            -
            (
                COALESCE(
                IF(d.costo IS NULL OR d.costo = 0, p.precio_costo, d.costo), 0
                ) * d.cantidad
            )
            ) AS ganancia
            FROM detalle_ordenRP d
            INNER JOIN facturasRP frp ON frp.orden_rp_id = d.orden_rp_id
            INNER JOIN (
            SELECT orden_rp_id, SUM(precio * cantidad - descuento) AS total_facturado
            FROM detalle_ordenRP
            WHERE $subCondition
            GROUP BY orden_rp_id
            ) ft ON ft.orden_rp_id = frp.orden_rp_id
            INNER JOIN detalle_ordenRP_con_piezas dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
            INNER JOIN piezas p ON p.pieza_id = dp.pieza_id
            WHERE $baseCondition
            GROUP BY p.nombre_pieza

            UNION ALL

            -- Servicios en facturas de ventas
            SELECT 
            s.nombre_servicio AS nombre,
            'Servicio' AS tipo,
            SUM(d.cantidad) AS cantidad,
            SUM(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)) AS costo,
            SUM(d.precio * d.cantidad - d.descuento) AS total,
            SUM(
            (
                (f.recibido / NULLIF(ft.total_facturado, 0)) * (d.precio * d.cantidad - d.descuento)
            )
            -
            (
                COALESCE(
                IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo), 0
                ) * d.cantidad
            )
            ) AS ganancia
            FROM detalle_facturas_ventas d
            INNER JOIN facturas_ventas f ON f.factura_venta_id = d.factura_venta_id
            INNER JOIN (
            SELECT factura_venta_id, SUM(precio * cantidad - descuento) AS total_facturado
            FROM detalle_facturas_ventas
            WHERE $subCondition
            GROUP BY factura_venta_id
            ) ft ON ft.factura_venta_id = f.factura_venta_id
            INNER JOIN detalle_ventas_con_servicios ds ON ds.detalle_venta_id = d.detalle_venta_id
            INNER JOIN servicios s ON s.servicio_id = ds.servicio_id
            WHERE $baseCondition
            GROUP BY s.nombre_servicio

            UNION ALL

            -- Servicios en ordenes de reparacion (facturasRP)
            SELECT 
            s.nombre_servicio AS nombre,
            'Servicio' AS tipo,
            SUM(d.cantidad) AS cantidad,
            SUM(COALESCE(IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo) * d.cantidad, 0)) AS costo,
            SUM(d.precio * d.cantidad - d.descuento) AS total,
            SUM(
            (
            (frp.recibido / NULLIF(ft.total_facturado, 0)) * (d.precio * d.cantidad - d.descuento)
            )
            -
            (
            COALESCE(
                IF(d.costo IS NULL OR d.costo = 0, s.costo, d.costo), 0
            ) * d.cantidad
            )
        ) AS ganancia
            FROM detalle_ordenRP d
            INNER JOIN facturasRP frp ON frp.orden_rp_id = d.orden_rp_id
            INNER JOIN (
            SELECT orden_rp_id, SUM(precio * cantidad - descuento) AS total_facturado
            FROM detalle_ordenRP
            WHERE $subCondition
            GROUP BY orden_rp_id
            ) ft ON ft.orden_rp_id = frp.orden_rp_id
            INNER JOIN detalle_ordenRP_con_servicios dp ON dp.detalle_ordenRP_id = d.detalle_ordenRP_id
            INNER JOIN servicios s ON s.servicio_id = dp.servicio_id
            WHERE $baseCondition
            GROUP BY s.nombre_servicio
                
            ) AS detalle_ventas_mes 
            GROUP BY nombre, tipo
        ) AS resumen;";

        jsonQueryResult($db, $sql);
    
        break;
}
