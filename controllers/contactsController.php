<?php

class ContactsController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'add' => ['administrador', 'cajero'],           // 'administrador' y 'cajero' tienen acceso
        'customers' => ['administrador', 'cajero'],    // 'administrador' y 'cajero' tienen acceso
        'edit_customer' => ['administrador'],           // Solo 'administrador' tiene acceso
        'customer_history' => ['administrador', 'cajero'], // 'administrador' y 'cajero' tienen acceso
        'providers' => ['administrador', 'cajero'],     // 'administrador' y 'cajero' tienen acceso
        'edit_provider' => ['administrador'],           // Solo 'administrador' tiene acceso
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

    // Acción para agregar un nuevo contacto
    public function add()
    {
        $this->check_permission('add');
        require_once './views/contacts/add.php';
    }

    // Acción para mostrar los clientes
    public function customers()
    {
        $this->check_permission('customers');
        require_once './views/contacts/customers.php';
    }

    // Acción para editar un cliente
    public function edit_customer()
    {
        $this->check_permission('edit_customer');
        require_once './views/contacts/edit_customer.php';
    }

    // Acción para ver el historial de un cliente
    public function customer_history()
    {
        $this->check_permission('customer_history');
        require_once './views/contacts/customer_history.php';
    }

    // Acción para mostrar los proveedores
    public function providers()
    {
        $this->check_permission('providers');
        require_once './views/contacts/providers.php';
    }

    // Acción para editar un proveedor
    public function edit_provider()
    {
        $this->check_permission('edit_provider');
        require_once './views/contacts/edit_provider.php';
    }
}
