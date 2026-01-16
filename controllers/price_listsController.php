<?php

class price_listsController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'index' => ['administrador'],  // Solo 'administrador' tiene acceso
        'add' => ['administrador'],    // Solo 'administrador' tiene acceso
        'edit' => ['administrador'],   // Solo 'administrador' tiene acceso
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

    // Acción para mostrar la lista de listas de precios
    public function index()
    {
        // Verificar permisos para la acción 'index'
        $this->check_permission('index');

        // Mostrar la vista de listas de precios
        require_once './views/price_lists/index.php';
    }

    // Acción para agregar una nueva lista de precios
    public function add()
    {
        // Verificar permisos para la acción 'add'
        $this->check_permission('add');

        // Mostrar la vista de agregar lista de precios
        require_once './views/price_lists/add.php';
    }

    // Acción para editar una lista de precios existente
    public function edit()
    {
        $this->check_permission('edit');

        // Mostrar la vista de editar lista de precios
        require_once './views/price_lists/edit.php';
    }
}
