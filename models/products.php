<?php

require_once 'modelo.php';

class Products extends ModeloBase
{

    public function __construct()
    {
        parent::__construct();
    }

    public function averageCost($id)
    {
        $query = "SELECT sum(v.costo_unitario) as costo_promedio FROM variantes v
                 INNER JOIN productos p ON p.producto_id = v.producto_id
                 WHERE p.producto_id = '$id' AND v.estado_id = 13";

        return $this->db->query($query);

    }

}
