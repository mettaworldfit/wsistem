<?php


class BillsController
{

    public function add_order()
    {

        require_once './views/bills/add_order.php';
    }

    public function edit_order()
    {

        require_once './views/expenses/edit_order.php';
    }

    public function orders()
    {
       
        require_once './views/bills/orders.php';
    }

    public function addinvoice()
    {
        require_once './views/bills/addinvoice.php';
    }

    public function invoices()
    {
        require_once './views/bills/invoices.php';
    }

    public function edit_invoice()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            $detail = Expenses_utils::DETAIL_INV_PROVI($_GET['id']);
            $description = Expenses_Utils::INVOICE_DESCRIPT($_GET['id']);

            require_once './views/bills/edit_invoice.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function addbills()
    {
        require_once './views/bills/addbills.php';
    }

    public function bills()
    {
        require_once './views/bills/bills.php';
    }

    public function add_payment()
    {
        require_once './views/bills/add_payment.php';
    }

    public function payments()
    {
        require_once './views/bills/payments.php';
    }
}
