<?php 

require_once 'modelo.php';

class Contacts extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }
 

    public function showCustomers(){

        $query = "SELECT nombre,apellidos,cedula,telefono1,fecha,cliente_id from clientes ORDER BY nombre";

        $datos = $this->db->query($query);
        return $datos;
    }

    public function showProviders(){

        $query = "SELECT nombre_proveedor,apellidos,direccion,email,telefono1,fecha,proveedor_id
         from proveedores ORDER BY nombre_proveedor";

        $datos = $this->db->query($query);
        return $datos;
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