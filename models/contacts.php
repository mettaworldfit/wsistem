<?php 

require_once 'modelo.php';

class Contacts extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }
 

    public function showCustomers(){

        $query = "SELECT nombre,apellidos,cedula,telefono1,fecha,cliente_id,direccion from clientes ORDER BY nombre";

        $datos = $this->db->query($query);
        return $datos;
    }

    public function showProviders(){

        $query = "SELECT nombre_proveedor,apellidos,direccion,email,telefono1,fecha,proveedor_id
         from proveedores ORDER BY nombre_proveedor";

        $datos = $this->db->query($query);
        return $datos;
    }
    
}