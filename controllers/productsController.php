<?php

require_once './help.php';

class ProductsController
{

    public function index()
    {
        require_once './views/products/index.php';
    }

    public function add()
    {

        require_once './views/products/add.php';
    }


    public function edit()
    {
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

        $id = $_GET['id'];

        $avg = Help::getProductAvgCost($id)->fetch_object()->costo_promedio;
        $product = Help::showProductID($id);

        require_once './views/products/edit.php';

        } else {
             // Permiso denegado
             require_once './views/layout/denied.php';
        }
    } 

    public function stock()
    {
    
        require_once './views/products/stock.php';
    }    
}
