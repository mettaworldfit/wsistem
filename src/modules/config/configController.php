<?php

class ConfigController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'site' => ['administrador'],
        'index' => ['administrador'],
        'config_bonus' => ['administrador'],
        'bonus' => ['administrador'],
        'config_mail' => ['administrador'],
        'config_pdf' => ['administrador'],
        'add_label' => ['administrador'],
        'labels' => ['administrador'],
        'printer' => ['administrador'],
        'printers' => ['administrador'],
        'edit_printer' => ['administrador']
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
            require_once __DIR__ . '../home/layout/denied.php';
            exit();
        }
    }

    public function site()
    {
        $this->check_permission('site');
        require_once __DIR__ . '/views/site.php';
    }

    // Acción para mostrar la configuración principal
    public function index()
    {
        $this->check_permission('index');
        require_once __DIR__ . '/views/index.php';
    }

    // Acción para la configuración de bonos
    public function config_bonus()
    {
        $this->check_permission('config_bonus');
        require_once __DIR__ . '/views/config_bonus.php';
    }

    // Acción para la configuración de bonos
    public function bonus()
    {
        $this->check_permission('bonus');
        require_once __DIR__ . '/views/bonus.php';
    }

    // Acción para la configuración de facturación electrónica
    public function config_mail()
    {
        $this->check_permission('config_mail');
        require_once __DIR__ . '/views/config_mail.php';
    }

    // Acción para la configuración de PDF
    public function config_pdf()
    {
        $this->check_permission('config_pdf');
        require_once __DIR__ . '/views/config_pdf.php';
    }


    // Acción para crear una etiqueta
    public function add_label()
    {
        $this->check_permission('add_label');
        require_once __DIR__ . '/views/add_label.php';
    }

    // Acción para la mostrar todas la configuraciones de etiquetas
    public function labels()
    {
        $this->check_permission('labels');
        require_once __DIR__ . '/views/labels.php';
    }

    public function printer()
    {
        $this->check_permission('printer');
        require_once __DIR__ . '/views/printer.php';
    }

    public function printers()
    {
        $this->check_permission('printers');
        require_once __DIR__ . '/views/printers.php';
    }

    public function edit_printer()
    {
        $this->check_permission('printer');
        require_once __DIR__ . '/views/edit_printer.php';
    }
}
