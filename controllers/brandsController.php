<?php

class BrandsController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'index' => ['administrador'],
        'add' => ['administrador'],
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

    // Acción para mostrar el listado de marcas
    public function index()
    {
        $this->check_permission('index');
        require_once './views/brands/index.php';
    }

    // Acción para agregar una nueva marca
    public function add()
    {
        $this->check_permission('add');
        require_once './views/brands/add.php';
    }

    // Acción para editar una marca existente
    public function edit()
    {
        $this->check_permission('edit');
        require_once './views/brands/edit.php';
    }
}
?>

