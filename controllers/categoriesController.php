<?php

class CategoriesController
{


    public function index()
    {

        require_once './views/categories/index.php';
    }

    public function add()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/categories/add.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function edit()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/categories/edit.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }
}
