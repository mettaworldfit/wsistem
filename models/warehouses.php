<?php

require_once 'modelo.php';

class Warehouses extends ModeloBase {

    public function __construct()
    {
        parent::__construct();
    }

    public function showWarehouses(){

        $query = "SELECT *FROM almacenes";
        return $this->db->query($query);
    }

   
}