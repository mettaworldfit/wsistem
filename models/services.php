<?php

require_once 'modelo.php';

class Services extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showServices()
    {

        $query = "SELECT * FROM servicios";
        return $this->db->query($query);
    }
    
} 