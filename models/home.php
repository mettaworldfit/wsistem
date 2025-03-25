<?php

require_once 'modelo.php';

class Home extends ModeloBase
{


    public function __construct()
    {
        parent::__construct();
    }

    public function Device_in_workshop()
    {

        $query = "SELECT count(orden_rp_id) as total FROM ordenes_rp 
        WHERE estado_id != 7 and estado_id != 10";

        $data = $this->db->query($query);
        $element = $data->fetch_object();

        return $element->total;
    }


    public function All_products()
    {

        $query = "SELECT count(producto_id) as total FROM productos";

        $data = $this->db->query($query);
        $element = $data->fetch_object();

        return $element->total;
    }

    public function All_pieces()
    {

        $query = "SELECT count(pieza_id) as total FROM piezas";

        $data = $this->db->query($query);
        $element = $data->fetch_object();

        return $element->total;
    }


    public function All_customers()
    {

        $query = "SELECT count(cliente_id) as total FROM clientes";

        $data = $this->db->query($query);
        $element = $data->fetch_object();

        return $element->total;
    }

    public function All_providers()
    {

        $query = "SELECT count(proveedor_id) as total FROM proveedores";

        $data = $this->db->query($query);
        $element = $data->fetch_object();

        return $element->total;
    }


    public function Purchase_today()
    {

        $query = "SELECT sum(total) as total FROM (

            SELECT sum(f.recibido) as 'total', f.fecha FROM facturas_ventas f
            WHERE f.fecha = curdate()
            GROUP BY f.fecha     
            
              UNION ALL
              
            SELECT sum(fr.recibido) as 'total', fr.fecha FROM facturasRP fr
            WHERE fr.fecha = curdate()
            GROUP BY fr.fecha            
            
            UNION ALL
            
            SELECT sum(p.recibido) as 'total', p.fecha from pagos p
            WHERE p.fecha = curdate()
            GROUP BY p.fecha 
                                          
        ) ventas_de_hoy";

        $data = $this->db->query($query);
        $element = $data->fetch_object();

        return $element->total;
    }

    public function Expenses_today()
    {

        $query = "SELECT sum(total) as total FROM (

            SELECT sum(g.pagado) as 'total', g.fecha FROM gastos g
            WHERE g.fecha = curdate()
            GROUP BY g.fecha     
            
              UNION ALL
              
            SELECT sum(f.pagado) as 'total', f.fecha FROM ordenes_compras o 
            INNER JOIN facturas_proveedores f ON o.orden_id = f.orden_id
            WHERE o.estado_id = 12 AND f.fecha = curdate()
            GROUP BY f.fecha    

            UNION ALL
            
            SELECT sum(p.recibido) as 'total', p.fecha from pagos_proveedores p
            WHERE p.fecha = curdate()
            GROUP BY p.fecha          
                                          
        ) gastos_de_hoy";

        $data = $this->db->query($query);
        $element = $data->fetch_object();

        return $element->total;
    }

}
