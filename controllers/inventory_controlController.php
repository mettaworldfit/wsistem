<?php

class Inventory_controlController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'inventory' => ['administrador']
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

    // Acción para mostrar el control del inventario
    public function inventory()
    {
        // Verificar permisos para la acción 'inventory'
        $this->check_permission('inventory');

        // Calcular el total del inventario
        $data = Help::getTotalInventoryValue()->fetch_object();

        $value = number_format($data->total, 2);
        $bruto = number_format($data->bruto, 2);

        // Mostrar la vista del inventario
        require_once './views/inventory_control/inventory.php';
    }
}
