<?php
require_once './help.php';

class InvoicesController
{
    // Definir los permisos por acción en un array
    private $permissions = [
        'pos' => ['administrador', 'cajero'],
        'addpurchase' => ['administrador', 'cajero'],
        'index' => ['administrador'],
        'edit' => ['administrador'],
        'addrepair' => ['administrador'],
        'index_repair' => ['administrador'],
        'repair_edit' => ['administrador'],
        'quote' => ['administrador', 'cajero'],
        'quotes' => ['administrador', 'cajero'],
        'edit_quote' => ['administrador', 'cajero'],
        'orders' => ['administrador', 'cajero'],
        'add_order' => ['administrador', 'cajero']
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

    public function pos()
    {
        $this->check_permission('pos');

        // Cierre de caja
        $cashOpening = Help::getCashOpening(); // Obtener datos de la caja abierta
        $tickets = Help::getIssuedInvoices(); // Total de tickets emitidos

        $totalReal = Help::getTotalReal(); // Total real
        $cash = Help::getDailySalesByPaymentMethod(1); // Efectivo
        $credit = Help::getDailySalesByPaymentMethod(2); // Tarjeta de credido
        $debit = Help::getDailySalesByPaymentMethod(3); // Tarjeta de debito
        $card = $credit + $debit;
        $transfers = Help::getDailySalesByPaymentMethod(4); // Tranferencias
        $checks = Help::getDailySalesByPaymentMethod(5); // Cheques
        $cashExpenses = Help::getOriginExpensesToday('caja'); // Total Origen de gastos
        $externalExpenses = Help::getOriginExpensesToday('fuera_caja'); // Total Origen de gastos
        
        require_once './views/invoices/pos.php';
    }

    public function addpurchase()
    {
        $this->check_permission('addpurchase');
        require_once './views/invoices/addpurchase.php';
    }

    public function index()
    {
        $this->check_permission('index');
        require_once './views/invoices/index.php';
    }

    public function edit()
    {
        $this->check_permission('edit');
        $detalle_factura = Help::showInvoiceID($_GET['id']);
        $datos_factura = Help::showInvoiceID($_GET['id']);
        $detail = json_encode($datos_factura->fetch_all(), JSON_UNESCAPED_UNICODE);
        $descripcion = Help::INVOICE_DESCRIPT($_GET['id']);
        require_once './views/invoices/edit.php';
    }

    public function addrepair()
    {
        $this->check_permission('addrepair');
        $id = $_GET['id'];
        $orden = Help::loadOrdenDetailId($id);
        $note = Help::getOrderNoteId($id, true)->fetch_object()->observacion;
        $is_exists = Help::checkOrderInvoiceExists($id, true)->fetch_object()->is_exists;
        $orderDetail = json_encode($orden->fetch_all(), JSON_UNESCAPED_UNICODE);
        $deviceInfo = json_encode(Help::getOrderInfoId($id)->fetch_all(), JSON_UNESCAPED_UNICODE);
        $conditions = json_encode(Help::getConditionsId($id)->fetch_all(), JSON_UNESCAPED_UNICODE);
        require_once './views/invoices/addrepair.php';
    }

    public function index_repair()
    {
        $this->check_permission('index_repair');
        require_once './views/invoices/index_repair.php';
    }

    public function repair_edit()
    {
        $this->check_permission('repair_edit');
        $id = $_GET['o'];
        $orden = Help::loadOrdenDetailId($id);
        $note = Help::getOrderNoteId($id, true)->fetch_object()->observacion;
        $orderDetail = json_encode($orden->fetch_all(), JSON_UNESCAPED_UNICODE);
        $deviceInfo = json_encode(Help::getOrderInfoId($id)->fetch_all(), JSON_UNESCAPED_UNICODE);
        $conditions = json_encode(Help::getConditionsId($id)->fetch_all(), JSON_UNESCAPED_UNICODE);
        require_once './views/invoices/edit_repair.php';
    }

    public function quote()
    {
        $this->check_permission('quote');
        require_once './views/invoices/quote.php';
    }

    public function quotes()
    {
        $this->check_permission('quotes');
        require_once './views/invoices/quotes.php';
    }

    public function edit_quote()
    {
        $this->check_permission('edit_quote');
        $quotes = Help::loadQuotesDetail($_GET['id']);
        $note = Help::getQuotesNoteId($_GET['id']);
        require_once './views/invoices/edit_quote.php';
    }

    public function orders()
    {
        $this->check_permission('orders');
        require_once './views/invoices/orders.php';
    }

    public function add_order()
    {
        $this->check_permission('add_order');
        $id = $_GET['id'];
        $note = Help::getOrderNoteId($id)->fetch_object()->descripcion ?? '';
        $is_exists = Help::checkOrderInvoiceExists($id)->fetch_object()->is_exists;
        require_once './views/invoices/add_order.php';
    }
}
?>
