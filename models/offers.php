<?php

require_once 'modelo.php';

class Offers extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showOffers()
    {

        $query = "SELECT * FROM ofertas ORDER BY nombre_oferta";
        return $this->db->query($query);
    }
    
} 