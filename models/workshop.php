<?php

require_once 'modelo.php';

class Workshop extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showWorkshop()
    {
      
        $query = "SELECT  o.orden_rp_id as id,f.facturaRP_id,c.cliente_id, c.nombre as nombre_cliente, 
        c.apellidos as 'apellidos_cliente', o.fecha_entrada, o.fecha_salida,e.nombre_modelo,
        m.nombre_marca,e.modelo,o.imei,o.serie,es.nombre_estado,es.estado_id FROM ordenes_rp o
                          INNER JOIN clientes c ON c.cliente_id = o.cliente_id
                          INNER JOIN estados_generales es ON es.estado_id = o.estado_id
                          INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
                          INNER JOIN equipos e ON e.equipo_id = o.equipo_id
                          INNER JOIN marcas m ON m.marca_id = e.marca_id
                          LEFT JOIN facturasRP f ON o.orden_rp_id = f.orden_rp_id";
                  
        return $this->db->query($query);
    }

    public function getOrderInfo($id)
    {
      
        $query = "SELECT e.nombre_modelo,e.modelo,o.serie,o.imei,o.fecha_entrada,
        c.nombre,c.apellidos,c.telefono1,o.fecha_salida,o.observacion,concat(u.nombre,' ',u.apellidos) as usuario, m.nombre_marca FROM ordenes_rp o
        INNER JOIN equipos e ON e.equipo_id = o.equipo_id
        INNER JOIN marcas m  ON e.marca_id = m.marca_id
        INNER JOIN clientes c ON c.cliente_id = o.cliente_id
        INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
        where o.orden_rp_id = '$id'";
                  
        return $this->db->query($query);
    }

    public function getOrderObservation($id)
    {
      
        $query = "SELECT observacion FROM ordenes_rp where orden_rp_id = '$id'";
                  
        return $this->db->query($query);
    }

    public function getConditions($id)
    {
      
        $query = "SELECT c.sintoma FROM ordenes_rp o
        LEFT JOIN ordenes_rp_con_condiciones oc ON oc.orden_rp_id = o.orden_rp_id
        INNER JOIN condiciones c ON c.condicion_id = oc.condicion_id
         where o.orden_rp_id = '$id'";
                  
        return $this->db->query($query);
    }
    
    

  
} 