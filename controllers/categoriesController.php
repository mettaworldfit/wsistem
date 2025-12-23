<?php

class CategoriesController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'index' => ['administrador'], // Roles que tienen acceso al listado
        'add' => ['administrador'],             // Solo 'administrador' puede agregar
        'edit' => ['administrador']             // Solo 'administrador' puede editar
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

    // Acción para mostrar el listado de categorías
    public function index()
    {
        $this->check_permission('index');
        require_once './views/categories/index.php';
    }

    // Acción para agregar una nueva categoría
    public function add()
    {
        $this->check_permission('add');
        require_once './views/categories/add.php';
    }

    // Acción para editar una categoría existente
    public function edit()
    {
        $this->check_permission('edit');
        require_once './views/categories/edit.php';
    }
}
?>
