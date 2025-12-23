<?php

class UsersController
{
    // Array de permisos por acción
    private $permissions = [
        'index' => ['administrador'],  // Solo 'administrador' tiene acceso a 'index'
        'add' => ['administrador'],    // Solo 'administrador' tiene acceso a 'add'
        'edit' => ['administrador']
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

    // Acción para listar los usuarios
    public function index()
    {
        // Verificar permisos para la acción 'index'
        $this->check_permission('index');

        // Mostrar la vista correspondiente
        require_once './views/users/index.php';
    }

    // Acción para agregar un usuario
    public function add()
    {
        // Verificar permisos para la acción 'add'
        $this->check_permission('add');

        // Mostrar la vista de agregar usuario
        require_once './views/users/add.php';
    }

    // Acción para editar un usuario
    public function edit()
    {
        // Verificar permisos para la acción 'edit'
        $this->check_permission('edit');

        // Mostrar la vista de editar usuario
        require_once './views/users/edit.php';
    }

    // Acción para iniciar sesión
    public function login()
    {

        // Mostrar la vista de login
        require_once './views/login/login.php';
    }
}
