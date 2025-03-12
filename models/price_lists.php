<?php

require_once 'modelo.php';

class Price_lists extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showPrice_list(){
        $query = "SELECT * from lista_de_precios";

        return $this->db->query($query);
    }
}