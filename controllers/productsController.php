<?php

require_once './models/products.php';

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

        $model = new Products();
        $datos = $model->averageCost($_GET['id']);
        $x = $datos->fetch_object();

        $avg = $x->costo_promedio;

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
