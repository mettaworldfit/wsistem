<?php

class ReportsController
{
    // Array de permisos por acción
    private $permissions = [
        'sales_today' => [],                   // Todos tienen permiso
        'earnings_period' => ['administrador'], // Roles con acceso a la acción 'querys'
        'cash_closing' => [],
        'sales_period' => ['administrador'],
        'equipment_sold' => ['administrador'],
        'inventory' => ['administrador'],
        'item_quantity' => ['administrador'],
        'expense_period' => ['administrador']
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

        // Si el array de roles está vacío, todos los roles tienen acceso
        if (empty($roles)) {
            return; // Permitir acceso sin restricciones
        }

        if (!in_array($_SESSION['identity']->nombre_rol, $roles)) {
            // Si no tiene permiso, redirigir a la página de acceso denegado
            require_once __DIR__ . '../home/layout/denied.php';
            exit();
        }
    }

    // Acción para mostrar las ventas del día
    public function sales_today()
    {
        // Verificar permisos para la acción 
        $this->check_permission('sales_today');

        // Obtener las ventas del día
        $invoices = Help::calculateSalesToDay();

        // Mostrar la vista correspondiente
        require_once __DIR__ . '/views/sales_today.php';
    }

    // Acción para mostrar las consultas
    public function earnings_period()
    {
        // Verificar permisos para la acción 'querys'
        $this->check_permission('earnings_period');

        // Mostrar la vista de consultas
        require_once __DIR__ . '/views/earnings-period.php';
    }

    // Acción para mostrar el cierre de caja
    public function cash_closing()
    {
        // Verificar permisos para la acción 'cash_closing'
        $this->check_permission('cash_closing');

        // Obtener los datos necesarios para el cierre de caja
        $cashOpening = Help::getCashOpening(); // Obtener datos de la caja abierta

        // Mostrar la vista de cierre de caja
        require_once __DIR__ . '/views/cash_closing.php';
    }

    // Acción para mostrar datos de ventas
    public function sales_period()
    {
        // Verificar permisos para la acción
        $this->check_permission('sales_period');

        // Mostrar la vista
        require_once __DIR__ . '/views/sales_period.php';
    }

    // Acción para consultar datos
    public function equipment_sold()
    {
        // Verificar permisos para la acción
        $this->check_permission('equipment_sold');

        // Mostrar la vista
        require_once __DIR__ . '/views/equipment_sold.php';
    }

    // Acción para mostrar el control del inventario
    public function inventory()
    {
        // Verificar permisos para la acción 'inventory'
        $this->check_permission('inventory');

        // Mostrar la vista del inventario
        require_once __DIR__ . '/views/inventory.php';
    }

    // Acción para mostrar reportes por cantidades
    public function item_quantity()
    {
        // Verificar permisos para la acción 
        $this->check_permission('item_quantity');

        require_once __DIR__ . '/views/item_quantity.php';
    }

    public function expense_period()
    {
        // Verificar permisos para la acción 
        $this->check_permission('expense_period');

        require_once __DIR__ . '/views/expense_period.php';
    }
}
