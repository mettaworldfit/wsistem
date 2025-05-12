<?php

class ReportsController 
{

    public function day()
    {

        $invoices = Help::calculateSalesToDay();

        require_once './views/reports/day.php';
    }

    public function querys()
    {

        require_once './views/reports/querys.php';
    }

  
}