<?php

require_once './help.php';

class ProductsController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'index' => [], // Ejemplo: ambos roles tienen acceso
        'add' => ['administrador'],             // Solo 'administrador' tiene acceso
        'edit' => [],            // Solo 'administrador' tiene acceso
        'stock' => ['administrador'], // Ambos roles tienen acceso
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

    // Acción para mostrar la lista de productos
    public function index()
    {
        // Verificar permisos para la acción 'index'
        $this->check_permission('index');

        // Mostrar la vista de productos
        require_once './views/products/index.php';
    }

    // Acción para agregar un nuevo producto
    public function add()
    {
        // Verificar permisos para la acción 'add'
        $this->check_permission('add');

        // Mostrar la vista de agregar producto
        require_once './views/products/add.php';
    }

    // Acción para editar un producto existente
    public function edit()
    {
        // Verificar permisos para la acción 'edit'
        $this->check_permission('edit');

        $id = $_GET['id'];

        $avg = Help::getProductAvgCost($id)->fetch_object()->costo_promedio;
        $product = Help::showProductID($id);

        require_once './views/products/edit.php';
    }

    // Acción para ver el stock de productos
    public function stock()
    {
        // Verificar permisos para la acción 'stock'
        $this->check_permission('stock');

        // Mostrar la vista de stock
        require_once './views/products/stock.php';
    }
}
