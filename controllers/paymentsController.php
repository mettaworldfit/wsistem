<?php

require_once './models/payments.php';

class PaymentsController
{

    public function add()
    {
        require_once './views/payments/add.php';
    }

    public function index()
    {

        $model = new Payments();
        $payments = $model->showPayments();

        require_once './views/payments/index.php';
    }

}