<?php

class PiecesController
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

    // Acción para mostrar la lista de piezas
    public function index()
    {

        $this->check_permission('index');

        // Mostrar la vista de piezas
        require_once './views/pieces/index.php';
    }

    // Acción para agregar una pieza
    public function add()
    {

        $this->check_permission('add');

        // Mostrar la vista de agregar pieza
        require_once './views/pieces/add.php';
    }

    // Acción para editar una pieza
    public function edit()
    {

        $this->check_permission('edit');

        // Obtener la pieza a editar
        $id = $_GET['id'];
        $piece = Help::showPiecesID($id);

        // Mostrar la vista de editar pieza
        require_once './views/pieces/edit.php';
    }
}
