<?php

require_once './models/products.php';

class ProductsController
{

    public function index()
    {
        $symbol = "DOP";
        
        $model = new Products();

        $products = $model->showProducts();
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

  

    
}
