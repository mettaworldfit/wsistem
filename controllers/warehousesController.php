<?php

require_once './models/warehouses.php';

class WarehousesController
{

    public function index()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            $method = new Warehouses;
            $warehouses = $method->showWarehouses();

            require_once './views/warehouses/index.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function add()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/warehouses/add.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function edit()
    {

        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/warehouses/edit.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }
}
