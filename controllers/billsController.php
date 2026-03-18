<?php

class BillsController
{

    // Definir los permisos por acción en un array
    private $permissions = [
     
        'addbills' => [],
        'bills' => []
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
            require_once './views/layout/denied.php';
            exit();
        }
    }

    public function addbills()
    {
        $this->check_permission('addbills');
        require_once './views/bills/addbills.php';
    }

    public function bills()
    {
        $this->check_permission('bills');
        require_once './views/bills/bills.php';
    }

}
