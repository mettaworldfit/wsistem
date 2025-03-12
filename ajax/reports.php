<?php

require_once '../config/db.php';

/**
 * Iniciar sesión
 --------------------------------------------------------------*/

session_start();

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

if ($_POST['action'] == "imei_facturado") {

    $db = Database::connect();

    $q = $_POST['query'];

    $query = "SELECT f.factura_venta_id, c.nombre ,c.apellidos,v.imei, 
            v.costo_unitario, v.fecha as fecha_entrada, f.fecha from variantes v 
            inner join variantes_facturadas vf on v.variante_id = vf.variante_id
            inner join detalle_facturas_ventas d on d.detalle_venta_id = vf.detalle_venta_id
            inner join facturas_ventas f on f.factura_venta_id = d.factura_venta_id
            inner join clientes c on c.cliente_id = f.cliente_id
            where v.imei = '$q'";

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