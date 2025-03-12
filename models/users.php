<?php

require_once 'modelo.php';

class Users extends ModeloBase {

    public function __construct()
    {
        parent::__construct();
    }

    public function showUsers(){

        $query = "CALL us_mostrarUsuarios_y_mas()";
        return $this->db->query($query);
    }

   
}