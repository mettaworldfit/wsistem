<?php

class BrandsController
{


    public function index()
    {
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
