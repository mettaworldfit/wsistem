<?php

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

    public function electronic_invoice()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/config/electronic_inv.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function config_pdf()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/config/config_pdf.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function bonds()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/config/bonds.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

}
