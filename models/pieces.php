<?php

require_once 'modelo.php';

class Pieces extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showPieces()
    {
        $query = "SELECT *, p.pieza_id as idpieza FROM piezas p 
                INNER JOIN estados_generales e ON p.estado_id = e.estado_id
                INNER JOIN almacenes a on p.almacen_id = a.almacen_id
                LEFT JOIN piezas_con_marcas pm ON p.pieza_id = pm.pieza_id
                LEFT JOIN marcas m ON pm.marca_id = m.marca_id 
                LEFT JOIN piezas_con_categorias pc ON p.pieza_id = pc.pieza_id
                LEFT JOIN categorias c ON pc.categoria_id = c.categoria_id 
                ORDER BY nombre_pieza ASC";

        return $this->db->query($query);
    }
} 