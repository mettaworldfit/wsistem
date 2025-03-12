<?php

require_once './models/brands.php';

class BrandsController
{


    public function index()
    {

        $method = new Brands();
        $brands = $method->showBrands();

        require_once './views/brands/index.php';
    }

    public function add()
    {
   
        require_once './views/brands/add.php';
    }

    public function edit()
    {
        require_once './views/brands/edit.php';
    }
}
