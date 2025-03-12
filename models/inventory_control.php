<?php

require_once 'modelo.php';

class Inventory_control extends ModeloBase {

    public function __construct()
    {
        parent::__construct();
    }

    public function show_inventory()
    {

        $query = "SELECT nombre_almacen, codigo, nombre, cantidad, precio_costo, nombre_estado, cantidad_min FROM (

            SELECT nombre_almacen, cod_producto as codigo, nombre_producto as nombre, cantidad, precio_costo, nombre_estado, cantidad_min FROM productos p 
            INNER JOIN almacenes a ON a.almacen_id = p.almacen_id
            INNER JOIN estados_generales e ON e.estado_id = p.estado_id
                
            UNION ALL
            
            SELECT nombre_almacen, cod_pieza as codigo, nombre_pieza as nombre, cantidad, precio_costo, nombre_estado, cantidad_min FROM piezas pz 
            INNER JOIN almacenes a ON a.almacen_id = pz.almacen_id
            INNER JOIN estados_generales e ON e.estado_id = pz.estado_id
                                
        ) inventario ORDER BY nombre";

        return $this->db->query($query);
    }
    

    public function InventoryValue()
    {

        $query = "SELECT sum(total) as total, sum(bruto) as bruto  FROM (

            SELECT sum(p.cantidad * p.precio_costo) as 'total', sum(p.cantidad * p.precio_unitario) as 'bruto' FROM productos p
              UNION ALL
            SELECT sum(pz.cantidad * pz.precio_costo) as 'total', sum(pz.cantidad * pz.precio_unitario) as 'bruto' FROM piezas pz
                                  
          ) ValorInventario;";

        return $this->db->query($query);
    }

}