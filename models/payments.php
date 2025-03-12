<?php

require_once 'modelo.php';

class Payments extends ModeloBase
{

    public function __construct()
    {
        parent::__construct();
    }

    public function showPayments()
    {
        $query = "SELECT *,p.pago_id as id, p.recibido as pagado, p.fecha as creacion FROM pagos p 
        left JOIN pagos_a_facturas_ventas pf ON pf.pago_id = p.pago_id
        left JOIN facturas_ventas f ON pf.factura_venta_id = f.factura_venta_id
        left JOIN pagos_a_facturasRP pr ON pr.pago_id = p.pago_id
        left JOIN facturasRP fr ON pr.facturaRP_id = fr.facturaRP_id
        left JOIN clientes c ON p.cliente_id = c.cliente_id";

        return $this->db->query($query);
    }

    public function showPayments_providers()
    {
        $query = "SELECT *,p.pago_factura_id as id, p.recibido as pagado, p.fecha as creacion FROM  pagos_proveedores p 
        left JOIN proveedores pr ON pr.proveedor_id = p.proveedor_id";

        return $this->db->query($query);
    }
}
