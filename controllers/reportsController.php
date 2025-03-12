<?php
require_once './models/invoices.php';

class ReportsController 
{

    public function day()
    {

        $method = new Invoices();
        $invoices = $method->showInvoicesToDay();

        require_once './views/reports/day.php';
    }

    public function querys()
    {

        require_once './views/reports/querys.php';
    }

  
}