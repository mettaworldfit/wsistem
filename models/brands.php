<?php 

require_once 'modelo.php';

class Brands extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }
 


    public function showBrands(){

        $query = "SELECT nombre_marca,fecha,marca_id from marcas";

        $datos = $this->db->query($query);
        return $datos;
    }

   


    
}