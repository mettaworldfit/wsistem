<?php

require_once './models/workshop.php';

class WorkshopController
{


    public function index()
    {
       $method = new Workshop();
       $workshops = $method->showWorkshop();

       require_once './views/workshop/index.php';
    }
}
