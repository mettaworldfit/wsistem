<?php

class PaymentsController
{

    public function add()
    {
        require_once './views/payments/add.php';
    }

    public function index()
    {
        require_once './views/payments/index.php';
    }

}