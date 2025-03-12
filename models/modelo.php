<?php

require_once './config/db.php';

class ModeloBase
{

    public $db;

    public function __construct()
    {
        $this->db = DataBase::connect();
    }

    public function getAll($tabla)
    {
        $query = "SELECT *FROM $tabla";
          
        return $datos = $this->db->query($query);
    
    }
    
}