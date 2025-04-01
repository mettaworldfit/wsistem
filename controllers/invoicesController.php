<?php

require_once './models/invoices.php';
require_once './models/workshop.php';
require_once './help.php';


class InvoicesController
{

    public function addpurchase()
    {

        $model = new Invoices();

        $detalles = $model->show_detalle_temp(); // Datos detalle temporar
        // $result = $detalles->fetch_object();

        require_once './views/invoices/addpurchase.php';
    }

    public function index()
    {

        $method = new Invoices();
        $invoices = $method->showInvoices();

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

        $method = new Workshop();
        $info_device = $method->getOrderInfo($_GET['id']);
        $conditions = $method->getConditions($_GET['id']);
        $obv = $method->getOrderObservation($_GET['id']);

        $data = Help::IS_EXISTS_INVOICERP($_GET['id']);
        $verify = $data->fetch_object();
        $is_exists = $verify->is_exists;


        // Devolver los datos en formato JSON para imprimir

        $order_detail = Help::showOrdenDetailID($_GET['id']);
        $detail = json_encode($order_detail->fetch_all(), JSON_UNESCAPED_UNICODE);
        $device = json_encode($info_device->fetch_all(), JSON_UNESCAPED_UNICODE);
        $condition = json_encode($conditions->fetch_all(), JSON_UNESCAPED_UNICODE);

        $orden_data = $obv->fetch_object(); // datos de la orden

        require_once './views/invoices/addrepair.php';
    }

    public function index_repair()
    {

        $method = new Invoices();
        $invoices = $method->showInvoicesRP();

        require_once './views/invoices/index_repair.php';
    }

    public function repair_edit()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            $method = new Workshop();
            $info_device = $method->getOrderInfo($_GET['o']);
            $conditions = $method->getConditions($_GET['o']);
            $obv = $method->getOrderObservation($_GET['o']);


            // Devolver los datos en formato JSON para imprimir

            $order_detail = Help::showOrdenDetailID($_GET['o']);
            $detail = json_encode($order_detail->fetch_all(), JSON_UNESCAPED_UNICODE);
            $device = json_encode($info_device->fetch_all(), JSON_UNESCAPED_UNICODE);
            $condition = json_encode($conditions->fetch_all(), JSON_UNESCAPED_UNICODE);

            $orden_data = $obv->fetch_object(); // datos de la orden

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

        $model = new Invoices();
        $quotes = $model->showQuotes();
        
        require_once './views/invoices/quotes.php';
    }

    public function edit_quote()
    {

        $model = new Invoices();
        $quotes = Help::showQuotesDetail($_GET['id']);

        $descripcion = Help::INVOICE_DESCRIPT_QUOTE($_GET['id']);
        
        require_once './views/invoices/edit_quote.php';
    }
}
