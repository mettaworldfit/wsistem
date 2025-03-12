<?php

require_once 'modelo.php';

class Taxes extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showTaxes()
    {

        $query = "SELECT * FROM impuestos";
        return $this->db->query($query);
    }
    
} 