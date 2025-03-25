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
