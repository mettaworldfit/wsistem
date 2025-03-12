<?php

require_once './models/offers.php';

class OffersController
{

    public function index()
    {

        $method = new Offers;
        $offers = $method->showOffers();

        require_once './views/offers/index.php';
    }

    public function add()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/offers/add.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function edit()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/offers/edit.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }
}
