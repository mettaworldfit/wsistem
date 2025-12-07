<?php
require_once './help.php';

class InvoicesController
{

    public function pos()
    {

        require_once './views/invoices/pos.php';
    }

    public function addpurchase()
    {

        require_once './views/invoices/addpurchase.php';
    }

    public function index()
    {
        require_once './views/invoices/index.php';
    }

    public function edit()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            $detalle_factura = Help::showInvoiceID($_GET['id']);
            $datos_factura = Help::showInvoiceID($_GET['id']);

            // Devolver los datos en formato JSON para imprimir
            $detail = json_encode($datos_factura->fetch_all(), JSON_UNESCAPED_UNICODE);
            $descripcion = Help::INVOICE_DESCRIPT($_GET['id']);

            require_once './views/invoices/edit.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function addrepair()
    {
        $id = $_GET['id'];

        // Obtener datos relacionados a la orden

        $orden = Help::loadOrdenDetailId($id);
        $note = Help::getOrderNoteId($id,true)->fetch_object()->observacion;

        // Verificar si la orden esta facturada
        $is_exists = Help::checkOrderInvoiceExists($id,true)->fetch_object()->is_exists;

        // Devolver los datos en formato JSON para imprimir

        $orderDetail = json_encode($orden->fetch_all(), JSON_UNESCAPED_UNICODE);
        $deviceInfo = json_encode(Help::getOrderInfoId($id)->fetch_all(), JSON_UNESCAPED_UNICODE);
        $conditions = json_encode(Help::getConditionsId($id)->fetch_all(), JSON_UNESCAPED_UNICODE);

        require_once './views/invoices/addrepair.php';
    }

    public function index_repair()
    {
        require_once './views/invoices/index_repair.php';
    }

    public function repair_edit()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            $id = $_GET['o'];

            // Obtener datos relacionados a la orden

            $orden = Help::loadOrdenDetailId($id);
            $note = Help::getOrderNoteId($id,true)->fetch_object()->observacion;

            // Devolver los datos en formato JSON para imprimir

            $orderDetail = json_encode($orden->fetch_all(), JSON_UNESCAPED_UNICODE);
            $deviceInfo = json_encode(Help::getOrderInfoId($id)->fetch_all(), JSON_UNESCAPED_UNICODE);
            $conditions = json_encode(Help::getConditionsId($id)->fetch_all(), JSON_UNESCAPED_UNICODE);

            require_once './views/invoices/edit_repair.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }

    public function quote()
    {

        require_once './views/invoices/quote.php';
    }

    public function quotes()
    {

        require_once './views/invoices/quotes.php';
    }

    public function edit_quote()
    {
        // Obtener datos relacionados a la cotizacion

        $quotes = Help::loadQuotesDetail($_GET['id']);
        $note = Help::getQuotesNoteId($_GET['id']);

        require_once './views/invoices/edit_quote.php';
    }

     public function orders()
    {

        require_once './views/invoices/orders.php';
    }

    public function add_order()
    {

        $id = $_GET['id'];

        // Obtener observacion de la factura
        $note = Help::getOrderNoteId($id)->fetch_object()->descripcion ?? '';

        // Verificar si la orden esta facturada
        $is_exists = Help::checkOrderInvoiceExists($id)->fetch_object()->is_exists;

        require_once './views/invoices/add_order.php';
    }

}
