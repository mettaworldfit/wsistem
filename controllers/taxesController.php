<?php

class TaxesController
{


    public function index()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/taxes/index.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function add()
    {

        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/taxes/add.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function edit()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/taxes/edit.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }
}
