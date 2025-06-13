<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();


// Ventas de todos los meses

if ($_POST['action'] == 'ventas_meses') {
    $db = Database::connect();


    $query1 = "SET @@lc_time_names = 'es_DO';";

    $query2 = "SELECT MONTH(fecha) AS mes_num, MONTHNAME(fecha) AS mes,SUM(total) AS total
    FROM (
        -- Ingresos de facturas ventas
        SELECT f.recibido AS total, f.fecha
        FROM facturas_ventas f
        WHERE YEAR(f.fecha) = YEAR(CURDATE())

        UNION ALL

        -- Ingresos de facturas RP
        SELECT fr.recibido AS total, fr.fecha
        FROM facturasRP fr
        WHERE YEAR(fr.fecha) = YEAR(CURDATE())

    ) ingresos
    GROUP BY mes_num,mes ORDER BY mes_num;";

    $db->query($query1);
    jsonQueryResult($db, $query2);
}


// Gastos de todos los meses

if ($_POST['action'] == 'gastos_meses') {
    $db = Database::connect();

    $query1 = "SET @@lc_time_names = 'es_DO';";

    $query2 = "SELECT MONTH(fecha) AS mes_num,MONTHNAME(fecha) AS mes,SUM(total) AS total FROM (
    SELECT SUM(g.pagado) AS total, g.fecha
    FROM gastos g
    WHERE YEAR(g.fecha) = YEAR(CURDATE())
    GROUP BY g.fecha

    UNION ALL

    SELECT SUM(f.pagado) AS total, f.fecha
    FROM ordenes_compras o 
    INNER JOIN facturas_proveedores f ON o.orden_id = f.orden_id
    WHERE o.estado_id = 12 AND YEAR(f.fecha) = YEAR(CURDATE())
    GROUP BY f.fecha
    ) AS gastos_por_meses GROUP BY mes_num, mes ORDER BY mes_num";

    $db->query($query1);
    
    jsonQueryResult($db,$query2);

    
}


if ($_POST['action'] == 'ventas_mes') {
    $db = Database::connect();

    $query = "SELECT DAY(fecha) AS dia, SUM(total) AS total
    FROM (
        -- Facturas ventas
        SELECT (f.recibido - IFNULL(SUM(p.recibido), 0)) AS total, f.fecha
        FROM facturas_ventas f
        INNER JOIN estados_generales e ON e.estado_id = f.estado_id
        LEFT JOIN pagos_a_facturas_ventas pf ON pf.factura_venta_id = f.factura_venta_id
        LEFT JOIN pagos p ON pf.pago_id = p.pago_id
        WHERE MONTH(f.fecha) = MONTH(CURDATE()) AND YEAR(f.fecha) = YEAR(CURDATE())
        GROUP BY f.factura_venta_id

        UNION ALL

        -- Facturas RP
        SELECT (fr.recibido - IFNULL(SUM(p.recibido), 0)) AS total, fr.fecha
        FROM facturasRP fr
        INNER JOIN estados_generales e ON e.estado_id = fr.estado_id
        LEFT JOIN pagos_a_facturasRP pf ON pf.facturaRP_id = fr.facturaRP_id
        LEFT JOIN pagos p ON pf.pago_id = p.pago_id
        WHERE MONTH(fr.fecha) = MONTH(CURDATE()) AND YEAR(fr.fecha) = YEAR(CURDATE())
        GROUP BY fr.facturaRP_id

        UNION ALL

        -- Pagos a facturas RP
        SELECT SUM(p.recibido) AS total, p.fecha
        FROM pagos_a_facturasRP pf 
        INNER JOIN pagos p ON pf.pago_id = p.pago_id
        WHERE MONTH(p.fecha) = MONTH(CURDATE()) AND YEAR(p.fecha) = YEAR(CURDATE())
        GROUP BY p.pago_id

        UNION ALL

        -- Pagos a facturas ventas
        SELECT SUM(p.recibido) AS total, p.fecha
        FROM pagos_a_facturas_ventas pf 
        INNER JOIN pagos p ON pf.pago_id = p.pago_id
        WHERE MONTH(p.fecha) = MONTH(CURDATE()) AND YEAR(p.fecha) = YEAR(CURDATE())
        GROUP BY p.pago_id

    ) ventas_del_mes GROUP BY DAY(fecha) ORDER BY dia";

    jsonQueryResult($db, $query);
}


if ($_POST['action'] == 'buscador') {

    $db = Database::connect();

    $keyword = isset($_POST['search']) ? $db->real_escape_string($_POST['search']) : '';

    // $keyword = $_POST['search'];
    $result = [];

    if ($keyword !== '') {
        $clientes = $db->query("SELECT cliente_id,nombre,apellidos FROM clientes WHERE nombre LIKE '%$keyword%' LIMIT 5");
        while ($row = $clientes->fetch_assoc()) {
            $result[] = [
                'tipo' => 'Cliente',
                'id' => $row['cliente_id'],
                'nombre' => $row['nombre'],
                'apellidos' => $row['apellidos']
            ];
        }

        $proveedores = $db->query("SELECT proveedor_id,nombre_proveedor FROM proveedores WHERE nombre_proveedor LIKE '%$keyword%' LIMIT 5");
        while ($row = $proveedores->fetch_assoc()) {
            $result[] = [
                'tipo' => 'Proveedor',
                'id' => $row['proveedor_id'],
                'nombre' => $row['nombre_proveedor']
            ];
        }

        $productos = $db->query("SELECT producto_id, nombre_producto, precio_unitario FROM productos WHERE nombre_producto LIKE '%$keyword%' LIMIT 5");
        while ($row = $productos->fetch_assoc()) {
            $result[] = [
                'tipo' => 'Producto',
                'id' => $row['producto_id'],
                'nombre' => $row['nombre_producto'],
                'precio' => $row['precio_unitario']
            ];
        }

        $piezas = $db->query("SELECT pieza_id, nombre_pieza, precio_unitario FROM piezas WHERE nombre_pieza LIKE '%$keyword%' LIMIT 5");
        while ($row = $piezas->fetch_assoc()) {
            $result[] = [
                'tipo' => 'Pieza',
                'id' => $row['pieza_id'],
                'nombre' => $row['nombre_pieza'],
                'precio' => $row['precio_unitario']
            ];
        }

        $factura_venta = $db->query("SELECT f.factura_venta_id,c.nombre,c.apellidos FROM facturas_ventas f
        INNER JOIN clientes c ON c.cliente_id = f.cliente_id 
        WHERE c.nombre LIKE '%$keyword%' or f.factura_venta_id LIKE '%$keyword%' LIMIT 10");
        while ($row = $factura_venta->fetch_assoc()) {
            $result[] = [
                'tipo' => 'Factura_venta',
                'id' => $row['factura_venta_id'],
                'nombre' => $row['nombre'],
                'apellidos' => $row['apellidos']
            ];
        }

        $ordenrp = $db->query("SELECT o.orden_rp_id,c.nombre,c.apellidos FROM ordenes_rp o
        INNER JOIN clientes c ON c.cliente_id = o.cliente_id 
        WHERE c.nombre LIKE '%$keyword%' or o.orden_rp_id LIKE '%$keyword%' LIMIT 10");
        while ($row = $ordenrp->fetch_assoc()) {
            $result[] = [
                'tipo' => 'Orden_reparacion',
                'id' => $row['orden_rp_id'],
                'nombre' => $row['nombre'],
                'apellidos' => $row['apellidos']
            ];
        }

        $factura_rp = $db->query("SELECT f.facturaRP_id,f.orden_rp_id,c.nombre,c.apellidos FROM facturasRP f
        INNER JOIN clientes c ON c.cliente_id = f.cliente_id 
        WHERE c.nombre LIKE '%$keyword%' or f.facturaRP_id LIKE '%$keyword%' LIMIT 10");
        while ($row = $factura_rp->fetch_assoc()) {
            $result[] = [
                'tipo' => 'Factura_reparacion',
                'id' => $row['facturaRP_id'],
                'orden_id' => $row['orden_rp_id'],
                'nombre' => $row['nombre'],
                'apellidos' => $row['apellidos']
            ];
        }
    }

    echo json_encode($result);
}
