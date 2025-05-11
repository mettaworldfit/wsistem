<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
require_once '../help.php';
session_start();

// Mostrar index de las facturas de reparaciones

if ($_POST['action'] == "index_facturas_reparacion") {

    // Conexión a la base de datos
    $db = Database::connect();

    handleDataTableRequest($db, [
        'columns' => [
                'f.facturaRP_id','f.total','f.pendiente','f.recibido','f.fecha',
                's.nombre_estado','o.orden_rp_id','c.nombre','c.apellidos'
            ],
            'searchable' => [
                'f.facturaRP_id','c.nombre','f.total','f.pendiente','f.recibido',
                'f.fecha','c.apellidos','s.nombre_estado','o.orden_rp_id'
            ],
            'base_table' => 'facturasRP',
            'table_with_joins' => "facturasRP f
                INNER JOIN clientes c ON f.cliente_id = c.cliente_id
                INNER JOIN ordenes_rp o ON o.orden_rp_id = f.orden_rp_id
                INNER JOIN estados_generales s ON f.estado_id = s.estado_id
            ",
            'select' => "
                SELECT f.facturaRP_id, f.total, f.pendiente, f.recibido, f.fecha, 
                    s.nombre_estado, o.orden_rp_id, c.nombre, c.apellidos
            ",
            'table_rows' => function ($row) {
                $facturaId = $row['facturaRP_id'];
                $ordenId = $row['orden_rp_id'];
                $estado = $row['nombre_estado'];

                $nombre = ucwords($row['nombre'] ?? '');
                $apellidos = ucwords($row['apellidos'] ?? '');

                $acciones = '';
                $esAdmin = isset($_SESSION['identity']) && $_SESSION['identity']->nombre_rol === 'administrador';
                $editable = $estado !== 'Anulada' && $esAdmin;

                if ($editable) {
                    $acciones .= '<a class="action-edit" href="' . base_url . 'invoices/repair_edit&o=' . $ordenId . '&f=' . $facturaId . '" title="Editar"><i class="fas fa-pencil-alt"></i></a>';
                } else {
                    $acciones .= '<a class="action-edit action-disable" href="#" title="Editar"><i class="fas fa-pencil-alt"></i></a>';
                }

                $acciones .= ' <span onclick="deleteInvoiceRP(\'' . $facturaId . '\')" class="action-delete"><i class="fas fa-times"></i></span>';

                return [
                    'id' => 'RP-00' . $facturaId,
                    'nombre' => $nombre . ' ' . $apellidos,
                    'fecha' => $row['fecha'],
                    'total' => '<span class="text-primary hide-cell">' . number_format($row['total'], 2) . '</span>',
                    'recibido' => '<span class="text-success hide-cell">' . number_format($row['recibido'], 2) . '</span>',
                    'pendiente' => '<span class="text-danger hide-cell">' . number_format($row['pendiente'], 2) . '</span>',
                    'estado' => '<p class="' . $estado . '">' . $estado . '</p>',
                    'acciones' => $acciones
                ];
            }
    ]);

}

// Obtener precios del detalle

if ($_POST['action'] == "precio_detalle") {

    $db = Database::connect();

    $orden_id = $_POST['orden_id'];

    $query = "SELECT sum(descuento) as descuentos, sum(cantidad * precio) as precios
FROM detalle_ordenRP WHERE orden_rp_id = '$orden_id'";

    $query2 = "SELECT * FROM facturasRP WHERE orden_rp_id = '$orden_id'";

    $datos = $db->query($query);
    $result = $datos->fetch_assoc();

    $datos2 = $db->query($query2);
    $result2 = $datos2->fetch_assoc();

    $arr = array($result, $result2);

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
}


// Agregar al detalle piezas/servicios

if ($_POST['action'] == "agregar_detalle_a_orden") {

    $user_id = $_SESSION['identity']->usuario_id;
    $piece_id = $_POST['piece_id'];
    $service_id = $_POST['service_id'];
    $description = $_POST['description'];
    $orden_id = $_POST['orden_id'];
    $quantity = $_POST['quantity'];
    $discount = (!empty($_POST['discount'])) ? $_POST['discount'] : 0;
    $price = $_POST['price'];

    $db = Database::connect();

    $query = "CALL rp_crearDetalleOrdenRP($user_id,$piece_id,$orden_id,$service_id,'$description','$quantity','$price','$discount')";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg > 0) {

        echo $data->msg;
    } else if (str_contains($data->msg, 'Duplicate')) {

        echo "duplicate";
    } else if (str_contains($data->msg, 'SQL')) {

        echo "Error : " . $data->msg;
    }
}

// Eliminar detalle

if ($_POST['action'] == 'eliminar_detalle') {

    $id = $_POST['id'];
    $db = Database::connect();

    $query = "CALL rp_eliminarDetalleOrdenRP($id)";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg == "ready") {

        echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {

        echo "Error : " . $data->msg;
    }
}

// Factura al contado

if ($_POST['action'] == "factura_contado") {

    $customer_id = $_POST['customer_id'];
    $total = $_POST['total_invoice'];
    $orden_id = $_POST['orden_id'];
    $description = $_POST['description'];
    $method = $_POST['payment_method'];
    $user_id = $_SESSION['identity']->usuario_id;
    $date = $_POST['date'];

    $db = Database::connect();

    $query = "CALL rp_facturaVenta($customer_id,$orden_id,$method,'$total',$user_id,'$description','$date')";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg > 0) {

        echo $data->msg;
    } else if (str_contains($data->msg, 'SQL')) {

        echo "Error : " . $data->msg;
    }
}


// Factura a crédito

if ($_POST['action'] == "factura_credito") {

    $customer_id = $_POST['customer_id'];
    $total = $_POST['total_invoice'];
    $description = $_POST['description'];
    $orden_id = $_POST['orden_id'];
    $method = $_POST['payment_method'];
    $user_id = $_SESSION['identity']->usuario_id;
    $pay = $_POST['pay'];
    $pending = $_POST['pending'];
    $date = $_POST['date'];

    $db = Database::connect();

    $query = "CALL rp_facturaAcredito($customer_id,$orden_id,$method,'$total','$pay','$pending',$user_id,'$description','$date')";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg > 0) {

        echo $data->msg;
    } else if (str_contains($data->msg, 'SQL')) {

        echo "Error : " . $data->msg;
    }
}


// Eliminar Factura

if ($_POST['action'] == 'eliminar_factura') {

    $id = $_POST['id'];
    $db = Database::connect();

    $query = "CALL rp_eliminarFactura($id)";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg == "ready") {

        echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {

        echo "Error : " . $data->msg;
    }
}

// actualizar datos de factura

if ($_POST['action'] == 'actualizar_factura') {

    $id = $_POST['id'];
    $customer_id = $_POST['customer_id'];
    $method = $_POST['method'];
    $db = Database::connect();

    $query = "CALL rp_actualizarFactura($id,$customer_id,$method)";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg == "ready") {

        echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {

        echo "Error : " . $data->msg;
    }
} else if ($_POST['action'] == 'actualizar_dinero_recibido') {

    $id = $_POST['id'];
    $received = $_POST['received'];
    $pending = $_POST['topay'] - $_POST['received'];
    $db = Database::connect();

    $query = "CALL rp_actualizarDineroFactura($id,$received,$pending)";
    $result = $db->query($query);
    $data = $result->fetch_object();

    if ($data->msg == "ready") {

        echo "ready";
    } else if (str_contains($data->msg, 'SQL')) {

        echo "Error : " . $data->msg;
    }
}
