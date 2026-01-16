<?php

class BillsController
{

    // Definir los permisos por acción en un array
    private $permissions = [
        'add_order' => ['administrador'],
        'edit_order' => ['administrador'],
        'orders' => ['administrador'],
        'addinvoice' => ['administrador'],
        'invoices' => ['administrador'],
        'edit_invoice' => ['administrador'],
        'addbills' => [],
        'bills' => [],
        'add_payment' => ['administrador'],
        'payment' => ['administrador']
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

    public function add_order()
    {

        $this->check_permission('add_order');
        require_once './views/bills/add_order.php';
    }

    public function edit_order()
    {
        $this->check_permission('edit_order');
        require_once './views/expenses/edit_order.php';
    }

    public function orders()
    {
        $this->check_permission('orders');
        require_once './views/bills/orders.php';
    }

    public function addinvoice()
    {
        $this->check_permission('addinvoice');
        require_once './views/bills/addinvoice.php';
    }

    public function invoices()
    {
        $this->check_permission('invoices');
        require_once './views/bills/invoices.php';
    }

    public function edit_invoice()
    {

        $this->check_permission('edit_invoice');
        $detail = Expenses_utils::DETAIL_INV_PROVI($_GET['id']);
        $description = Expenses_Utils::INVOICE_DESCRIPT($_GET['id']);

        require_once './views/bills/edit_invoice.php';
    }

    public function addbills()
    {
        $this->check_permission('addbills');
        require_once './views/bills/addbills.php';
    }

    public function bills()
    {
        $this->check_permission('bills');
        require_once './views/bills/bills.php';
    }

    public function add_payment()
    {
        $this->check_permission('add_payment');
        require_once './views/bills/add_payment.php';
    }

    public function payments()
    {
        $this->check_permission('payments');
        require_once './views/bills/payments.php';
    }
}
