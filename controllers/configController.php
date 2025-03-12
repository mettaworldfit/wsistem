<?php

require_once './models/configs.php';

class ConfigController
{

    public function index()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/config/index.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function bond_config()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/config/bond_config.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

}
