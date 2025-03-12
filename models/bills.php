<?php 

require_once 'modelo.php';

class Bills extends ModeloBase
{

    public function __construct()
    {
        parent::__construct();
    }

    public function showOrders() {

        $query = "SELECT *, o.fecha as creacion FROM ordenes_compras o 
        INNER JOIN estados_generales e ON e.estado_id = o.estado_id
        INNER JOIN proveedores p ON p.proveedor_id = o.proveedor_id
        INNER JOIN usuarios u ON u.usuario_id = o.usuario_id ORDER BY o.orden_id DESC";

        return $this->db->query($query);
        
    }

    public function showInvoices() {

        $query = "SELECT *, f.fecha as fecha_factura, p.apellidos as p_apellidos FROM facturas_proveedores f 
        INNER JOIN estados_generales e ON e.estado_id = f.estado_id
        INNER JOIN proveedores p ON p.proveedor_id = f.proveedor_id
        INNER JOIN usuarios u ON u.usuario_id = f.usuario_id";

        return $this->db->query($query);
    }

    public function showBills() {

        $query = "SELECT g.gasto_id, p.nombre_proveedor, g.total, g.pagado, g.orden_id,
        g.fecha as fecha_gasto, p.apellidos as p_apellidos FROM gastos g 
        INNER JOIN proveedores p ON p.proveedor_id = g.proveedor_id
        INNER JOIN usuarios u ON u.usuario_id = g.usuario_id";

        return $this->db->query($query);
    }
   
}
