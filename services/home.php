<?php

require_once '../config/db.php';
session_start();


// Ventas de todos los meses

if ($_POST['action'] == 'ventas_meses') {
    $db = Database::connect();


    $query1 = "SET @@lc_time_names = 'es_DO';";

    $query2 = "SELECT monthname(fecha) AS 'mes' ,sum(total) as total FROM (

        SELECT sum(f.recibido) as 'total', f.fecha FROM facturas_ventas f
        WHERE f.fecha > now() - interval 11 month
        GROUP BY f.fecha     
        
          UNION ALL
          
        SELECT sum(fr.recibido) as 'total', fr.fecha FROM facturasRP fr
        WHERE fr.fecha > now() - interval 11 month
        GROUP BY fr.fecha              
                                      
    ) ingresos_por_meses group by mes";

    $db->query($query1);
    $datos = $db->query($query2);

    if ($datos->num_rows > 0) {

        $result = $datos->fetch_all();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}


// Gastos de todos los meses

if ($_POST['action'] == 'gastos_meses') {
    $db = Database::connect();

    $query1 = "SET @@lc_time_names = 'es_DO';";

    $query2 = "SELECT monthname(fecha) AS 'mes' ,sum(total) as total FROM (

        SELECT sum(g.pagado) as 'total', g.fecha FROM gastos g
        WHERE g.fecha > now() - interval 11 month
        GROUP BY g.fecha     
        
          UNION ALL
          
        SELECT sum(f.pagado) as 'total', f.fecha FROM ordenes_compras o 
        INNER JOIN facturas_proveedores f ON o.orden_id = f.orden_id
        WHERE o.estado_id = 12 AND f.fecha > now() - interval 11 month
        GROUP BY f.fecha              
                                      
    ) gastos_por_meses group by mes";

    $db->query($query1);
    $datos = $db->query($query2);

    if ($datos->num_rows > 0) {

        $result = $datos->fetch_all();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}


if ($_POST['action'] == 'ventas_mes') {
    $db = Database::connect();

    $query = "SELECT DAY(fecha) AS dia, SUM(total) as total FROM facturas_ventas 
            WHERE MONTH(fecha) = 3 AND YEAR(fecha) = 2025
            GROUP BY dia ORDER BY dia";

    $datos = $db->query($query);

    if ($datos->num_rows > 0) {

        $result = $datos->fetch_all();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
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
    }

    echo json_encode($result);
}


