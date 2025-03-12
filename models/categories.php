<?php

require_once 'modelo.php';

class Categories extends ModeloBase {


    public function __construct()
    {
        parent::__construct();
    }

    public function showCategories()
    {

        $query = "SELECT * from categorias";
        return $this->db->query($query);
    }
    
} 