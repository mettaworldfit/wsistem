<?php

session_start();

if (!isset($_SESSION['identity'])) {

	header('location: ../');
}

require_once '../vendor/autoload.php';
require_once '../../../config/parameters.php';
require_once '../../../config/db.php';

use Dompdf\Dompdf;
use Dompdf\Options;


if (!empty($_REQUEST['f'])) {

	$db = Database::connect();

	$invID = $_REQUEST['f'];
	$subtotal = $_REQUEST['sub'];
	$discount = $_REQUEST['dis'];
	$taxes = $_REQUEST['tax'];
	$total = $_REQUEST['total'];

	/**
	 * TODO: Datos cliente
 -------------------------------------------------------------------------------------------------------------------------------*/

	date_default_timezone_set('America/New_York');

	$query = "SELECT c.cedula ,c.nombre as nombre_cliente, c.apellidos as apellidos_cliente,c.telefono1,c.telefono2,
 c.email,c.direccion,m.nombre_metodo, u.nombre as nombre_usuario, u.apellidos as apellidos_usuario, 
 f.factura_venta_id, f.fecha, f.descripcion FROM facturas_ventas f
				INNER JOIN clientes c ON c.cliente_id = f.cliente_id
				INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
				INNER JOIN usuarios u ON u.usuario_id = f.usuario_id
				WHERE f.factura_venta_id = '$invID'";

	$data = $db->query($query)->fetch_object();


	/**
	 * TODO: Detalle de factura
 -------------------------------------------------------------------------------------------------------------------------------*/

	$query_detail = "SELECT p.nombre_producto, df.precio, pz.nombre_pieza, s.nombre_servicio, df.cantidad as cantidad_total, 
df.detalle_venta_id as 'id', df.descuento, df.impuesto, i.valor FROM detalle_facturas_ventas df
		 LEFT JOIN detalle_ventas_con_productos dvp ON df.detalle_venta_id = dvp.detalle_venta_id
		 LEFT JOIN productos p ON p.producto_id = dvp.producto_id
		 LEFT JOIN productos_con_impuestos pim ON p.producto_id = pim.producto_id
		 LEFT JOIN impuestos i ON pim.impuesto_id = i.impuesto_id
		 LEFT JOIN detalle_ventas_con_piezas_ dvpz ON df.detalle_venta_id = dvpz.detalle_venta_id
		 LEFT JOIN piezas pz ON pz.pieza_id = dvpz.pieza_id
		 LEFT JOIN detalle_ventas_con_servicios dvs ON df.detalle_venta_id = dvs.detalle_venta_id
		 LEFT JOIN servicios s ON s.servicio_id = dvs.servicio_id
		 WHERE df.factura_venta_id = '$invID'";

	$result_detail = $db->query($query_detail);


	ob_start();
	include(dirname('__FILE__') . '/facturas/factura_venta.php');
	$html = ob_get_clean();

	$options = new Options();
	$options->set('isRemoteEnabled', TRUE);

	// instantiate and use the dompdf class
	$dompdf = new Dompdf($options);

	$dompdf->loadHtml($html);
	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('letter', 'portrait');
	// Render the HTML as PDF
	$dompdf->render();
	// Output the generated PDF to Browser
	$dompdf->stream('factura.pdf', array('Attachment' => 0));
	exit;
} else {
	echo "No es posible generar la factura.";
}
