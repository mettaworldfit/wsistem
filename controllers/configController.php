<?php

class ConfigController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'index' => ['administrador'],           // Solo 'administrador' tiene acceso
        'bond_config' => ['administrador'],     // Solo 'administrador' tiene acceso
        'electronic_invoice' => ['administrador'], // Solo 'administrador' tiene acceso
        'config_pdf' => ['administrador'],      // Solo 'administrador' tiene acceso
        'bonds' => ['administrador'],           // Solo 'administrador' tiene acceso
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

    // Acción para mostrar la configuración principal
    public function index()
    {
        $this->check_permission('index');
        require_once './views/config/index.php';
    }

    // Acción para la configuración de bonos
    public function bond_config()
    {
        $this->check_permission('bond_config');
        require_once './views/config/bond_config.php';
    }

    // Acción para la configuración de facturación electrónica
    public function electronic_invoice()
    {
        $this->check_permission('electronic_invoice');
        require_once './views/config/electronic_inv.php';
    }

    // Acción para la configuración de PDF
    public function config_pdf()
    {
        $this->check_permission('config_pdf');
        require_once './views/config/config_pdf.php';
    }

    // Acción para la configuración de bonos
    public function bonds()
    {
        $this->check_permission('bonds');
        require_once './views/config/bonds.php';
    }
}
?>
