<?php
require_once './models/home.php';

class HomeController 
{

    public function index()
    {
 

        // Abreviar cifras
        function number_format_short($n, $precision = 1) {
            if ($n < 100000) {
                // 0 - 100000
                $n_format = number_format($n, $precision);
                $suffix = '';
            } else if ($n < 900000) {
                // 0.9k-850k
                $n_format = number_format($n / 1000, $precision);
                $suffix = 'K';
            } else if ($n < 900000000) {
                // 0.9m-850m
                $n_format = number_format($n / 1000000, $precision);
                $suffix = 'M';
            } else if ($n < 900000000000) {
                // 0.9b-850b
                $n_format = number_format($n / 1000000000, $precision);
                $suffix = 'B';
            } else {
                // 0.9t+
                $n_format = number_format($n / 1000000000000, $precision);
                $suffix = 'T';
            }
          // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
          // Intentionally does not affect partials, eg "1.50" -> "1.50"
            if ( $precision > 0 ) {
                $dotzero = '.' . str_repeat( '0', $precision );
                $n_format = str_replace( $dotzero, '', $n_format );
            }
            return $n_format . $suffix;
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