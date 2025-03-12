<?php

require_once 'modelo.php';

class Positions extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showPositions()
    {

        $query = "SELECT * FROM posiciones";
        return $this->db->query($query);
    }
    
} 