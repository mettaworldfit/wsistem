<?php

require_once 'modelo.php';

class Products extends ModeloBase
{

    public function __construct()
    {
        parent::__construct();
    }



    public function showProducts()
    {
        $query = "SELECT p.cod_producto, p.nombre_producto,m.nombre_marca,
                a.nombre_almacen,p.cantidad_min,p.cantidad,p.precio_costo,
                p.precio_unitario,e.nombre_estado,p.producto_id as idproducto FROM productos p 
                INNER JOIN estados_generales e ON p.estado_id = e.estado_id
                INNER JOIN almacenes a on p.almacen_id = a.almacen_id
                LEFT JOIN productos_con_marcas pm ON p.producto_id = pm.producto_id
                LEFT JOIN marcas m ON pm.marca_id = m.marca_id 
                ORDER BY nombre_producto ASC";

        return $this->db->query($query);
    }

    public function averageCost($id)
    {
        $query = "SELECT sum(v.costo_unitario) as costo_promedio FROM variantes v
                 INNER JOIN productos p ON p.producto_id = v.producto_id
                 WHERE p.producto_id = '$id' AND v.estado_id = 13";

        return $this->db->query($query);

    }

}
