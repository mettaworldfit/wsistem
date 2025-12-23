<?php

class PositionsController {

    // Definir los permisos por acción en un array
    private $permissions = [
        'index' => ['administrador'],  
        'add' => ['administrador'],      
        'edit' => ['administrador'],  
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

    // Acción para mostrar la lista de posiciones
    public function index()
    {
        // Verificar permisos para la acción 'index'
        $this->check_permission('index');

        // Mostrar la vista de posiciones
        require_once './views/positions/index.php';
    }

    // Acción para agregar una nueva posición
    public function add()
    {
        // Verificar permisos para la acción 'add'
        $this->check_permission('add');

        // Mostrar la vista de agregar posición
        require_once './views/positions/add.php';
    }

    // Acción para editar una posición existente
    public function edit()
    {
        // Verificar permisos para la acción 'edit'
        $this->check_permission('edit');

        // Obtener la posición a editar
        $id = $_GET['id'];
        $position = Help::showPositionID($id);

        // Mostrar la vista de editar posición
        require_once './views/positions/edit.php';
    }
}
