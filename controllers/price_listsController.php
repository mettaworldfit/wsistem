<?php

require_once './models/price_lists.php';

class price_listsController
{


    public function index()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            $method = new Price_lists();
            $price_lists = $method->showPrice_list();

            require_once './views/price_lists/index.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function add()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/price_lists/add.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function edit()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/price_lists/edit.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }
}
