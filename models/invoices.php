<?php

require_once 'modelo.php';


class Invoices extends ModeloBase
{

    public function __construct()
    {
        parent::__construct();
    }

    public function show_detalle_temp()
    {

        $userID = $_SESSION['identity']->usuario_id;

        $query = "SELECT * FROM detalle_temporal WHERE usuario_id = '$userID' ORDER BY hora";

        $datos = $this->db->query($query);

        return $datos;
    }


    public function showInvoices()
    {

        $query = "SELECT f.factura_venta_id, c.nombre, c.apellidos , f.total, f.recibido, f.pendiente,
                f.bono, e.nombre_estado, f.fecha as fecha_factura FROM facturas_ventas f 
                INNER JOIN clientes c ON f.cliente_id = c.cliente_id
                INNER JOIN estados_generales e ON f.estado_id = e.estado_id  
                ORDER BY f.factura_venta_id ASC LIMIT 200";

        return $this->db->query($query);
    }

    public function showInvoicesToDay()
    {

        $query = "SELECT id,tipo,orden, nombre,apellidos,total,recibido,pendiente,estado,fecha_factura FROM (

            SELECT c.nombre as nombre, c.apellidos as apellidos, f.factura_venta_id as id, concat('n/d') as orden ,f.fecha as fecha_factura, f.total 
            as total, f.recibido as recibido, f.pendiente as pendiente, s.nombre_estado as estado, concat('FT') as tipo FROM facturas_ventas f 
                INNER JOIN clientes c ON f.cliente_id = c.cliente_id
                INNER JOIN estados_generales s ON f.estado_id = s.estado_id 
               
               UNION ALL
                
            SELECT c.nombre as nombre, c.apellidos as apellidos,f.facturarp_id as id, f.orden_rp_id as orden, f.fecha as fecha_factura, f.total as total, 
            f.recibido as recibido, f.pendiente as pendiente, s.nombre_estado as estado, concat('RP') as tipo FROM facturasRP f 
                INNER JOIN clientes c ON f.cliente_id = c.cliente_id
                INNER JOIN estados_generales s ON f.estado_id = s.estado_id 
                
			UNION ALL 
            
            SELECT c.nombre as nombre, c.apellidos as apellidos, pg.pago_id as id, f.factura_venta_id as orden, pg.fecha as fecha_factura, pg.recibido as total, 
            pg.recibido as recibido, '0' as pendiente, s.nombre_estado as estado, concat('PF') as tipo 
            FROM pagos_a_facturas_ventas p 
		INNER JOIN pagos pg ON pg.pago_id = p.pago_id
        INNER JOIN facturas_ventas f on f.factura_venta_id = p.factura_venta_id
		INNER JOIN clientes c ON f.cliente_id = c.cliente_id
		INNER JOIN estados_generales s ON f.estado_id = s.estado_id 
        
        
         UNION ALL
         
         SELECT c.nombre as nombre, c.apellidos as apellidos, pg.pago_id as id, f.facturarp_id as orden, pg.fecha as fecha_factura, pg.recibido as total, 
            pg.recibido as recibido, '0' as pendiente, s.nombre_estado as estado, concat('PR') as tipo 
            FROM pagos_a_facturasRP p 
		INNER JOIN pagos pg ON pg.pago_id = p.pago_id
        INNER JOIN facturasRP f on f.facturarp_id = p.facturarp_id
		INNER JOIN clientes c ON f.cliente_id = c.cliente_id
		INNER JOIN estados_generales s ON f.estado_id = s.estado_id
			
                
            ) ventas_del_dia where fecha_factura = curdate() order by id ASC";

        return $this->db->query($query);
    }

    public function showInvoicesRP()
    {

        $query = "SELECT *, f.fecha as fecha_factura FROM facturasRP f 
    INNER JOIN clientes c ON f.cliente_id = c.cliente_id
    INNER JOIN ordenes_rp o ON o.orden_rp_id = f.orden_rp_id 
    INNER JOIN estados_generales s ON f.estado_id = s.estado_id 
    INNER JOIN metodos_de_pagos m ON f.metodo_pago_id = m.metodo_pago_id";

        return $this->db->query($query);
    }

    public function showQuotes()
    {

        $query = "SELECT c.cotizacion_id, cl.nombre, cl.apellidos, c.total, c.fecha from cotizaciones c
                    INNER JOIN clientes cl ON cl.cliente_id = c.cliente_id";

        return $this->db->query($query);
    }

}
