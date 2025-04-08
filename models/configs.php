<?php 

require_once 'modelo.php';

class Configs extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showCustomer_bonds(){

        $query = "SELECT c.nombre as nombre, c.apellidos as apellidos, b.valor as valor,
        b.fecha as fecha, b.bono_id as bono_id, u.nombre as nombre_usuario, u.apellidos as apellidos_usuario FROM bonos b 
        INNER JOIN clientes c ON b.cliente_id = c.cliente_id
        INNER JOIN usuarios u ON b.usuario_id = u.usuario_id";

        $datos = $this->db->query($query);
        return $datos;
    }
    
}