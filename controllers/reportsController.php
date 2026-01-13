<?php

class ReportsController
{
    // Array de permisos por acción
    private $permissions = [
        'day' => ['administrador', 'cajero'], // Roles con acceso a la acción 'day'
        'querys' => ['administrador'], // Roles con acceso a la acción 'querys'
        'cash_closing' => ['administrador', 'cajero'], // Solo 'administrador' tiene acceso a 'cash_closing'
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

    // Acción para mostrar las ventas del día
    public function day()
    {
        // Verificar permisos para la acción 'day'
        $this->check_permission('day');

        // Obtener las ventas del día
        $invoices = Help::calculateSalesToDay();

        // Mostrar la vista correspondiente
        require_once './views/reports/day.php';
    }

    // Acción para mostrar las consultas
    public function querys()
    {
        // Verificar permisos para la acción 'querys'
        $this->check_permission('querys');

        // Mostrar la vista de consultas
        require_once './views/reports/querys.php';
    }

    // Acción para mostrar el cierre de caja
    public function cash_closing()
    {
        // Verificar permisos para la acción 'cash_closing'
        $this->check_permission('cash_closing');

        // Obtener los datos necesarios para el cierre de caja
        $cashOpening = Help::getCashOpening(); // Obtener datos de la caja abierta

        // Mostrar la vista de cierre de caja
        require_once './views/reports/cash_closing.php';
    }
}
