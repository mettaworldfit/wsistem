<?php

class HomeController
{

    // Definir los permisos por acción en un array
    private $permissions = [
        'index' => ['administrador', 'cajero'],
        'error' => ['administrador', 'cajero'],
        'permise_denied' => ['administrador', 'cajero']
    ];

    // Verificación de permisos
    private function check_permission($action)
    {
        // Si no está autenticado, redirigir a login
        if (!isset($_SESSION['identity'])) {
            header('Location: ' . base_url . 'login');
            exit();
        }

        // Verificar si el rol del usuario tiene permiso para la acción solicitada
        $roles = isset($this->permissions[$action]) ? $this->permissions[$action] : [];

        if (!in_array($_SESSION['identity']->nombre_rol, $roles)) {
            // Si no tiene permiso, redirigir a la página de acceso denegado
            require_once './views/layout/denied.php';
            exit();
        }
    }

    public function index()
    {

        // echo var_dump($_SESSION['identity']);

        // Abreviar cifras
        function number_format_short($n, $precision = 1)
        {
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
            if ($precision > 0) {
                $dotzero = '.' . str_repeat('0', $precision);
                $n_format = str_replace($dotzero, '', $n_format);
            }
            return $n_format . $suffix;
        }

        // Cierre de caja
        $cashOpening = Help::getCashOpening(); // Obtener datos de la caja abierta

        // Dashboard
        $totalPurchase = Help::getPurchaseToday(); // Total vendido hoy
        $totalExpenses = Help::getExpensesToday(); // Total gastado hoy
        $products = Help::getTotalProducts();
        $pieces = Help::getTotalPieces();
        $customers = Help::getTotalCustomers();
        $providers = Help::getTotalProviders();

        $dailyProfit = Help::getDailyProfit(); // Ganancias de hoy
        $monthProfit = Help::getMonthProfit(); // Ganancias del mes

        $getActiveCustomersThisMonth = Help::getActiveCustomersThisMonth(); // Clientes activos de este mes

        $this->check_permission('index');
        require_once './views/layout/home.php';
    }

    public function error()
    {
        $this->check_permission('error');
        require_once './views/layout/404.php';
    }

    public function permise_denied()
    {
        $this->check_permission('permise_denied');
        require_once './views/layout/denied.php';
    }
}
