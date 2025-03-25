<?php
require_once './models/home.php';

class HomeController 
{

    public function index()
    {
 

        // Abreviar cifras
        function number_format_short($n, $precision = 1) {
            if ($n < 900) {
                return $n; // Menos de 900
            } elseif ($n < 900000) {
                return round($n / 1000, $precision) . 'K'; // Miles
            } elseif ($n < 900000000) {
                return round($n / 1000000, $precision) . 'M'; // Millones
            } else {
                return round($n / 1000000000, $precision) . 'B'; // Billones
            }
        }

        $model = new Home();
        $total_purchase = $model->Purchase_today();
        $total_expenses = $model->Expenses_today();
        $workshop = $model->Device_in_workshop();
        $products = $model->All_products();
        $pieces = $model->All_pieces();
        $customers = $model->All_customers();
        $providers = $model->All_providers();

        require_once './views/layout/home.php';
    }

    public function error()
    {
        require_once './views/layout/404.php';
    }

    public function permise_denied()
    {
        require_once './views/layout/denied.php';
    }

  
}