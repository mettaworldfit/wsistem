<?php

class PaymentsController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'index' => [],    // Todos tienen acceso
        'add' => [],
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

    // Acción para mostrar la lista de pagos
    public function index()
    {
        // Verificar permisos para la acción 'index'
        $this->check_permission('index');

        // Mostrar la vista de pagos
        require_once './views/payments/index.php';
    }

    // Acción para agregar un pago
    public function add()
    {
        // Verificar permisos para la acción 'add'
        $this->check_permission('add');

        // Mostrar la vista de agregar pago
        require_once './views/payments/add.php';
    }
}
