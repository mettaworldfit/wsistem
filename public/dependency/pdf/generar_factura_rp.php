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


if (!empty($_REQUEST['f']) && !empty($_REQUEST['o'])) {

	$db = Database::connect();

	$invID = $_REQUEST['f'];
    $orID = $_REQUEST['o'];
	$subtotal = $_REQUEST['sub'];
	$discount = $_REQUEST['dis'];
	$total = $_REQUEST['total'];

	/**
	 * TODO: Datos cliente
 -------------------------------------------------------------------------------------------------------------------------------*/

	date_default_timezone_set('America/New_York');

	$query = "SELECT c.cedula ,c.nombre as nombre_cliente, c.apellidos as apellidos_cliente,c.telefono1,c.telefono2,
    c.email,c.direccion,m.nombre_metodo, u.nombre as nombre_usuario, u.apellidos as apellidos_usuario, 
    f.facturarp_id, f.fecha, f.descripcion,f.orden_rp_id FROM facturasrp f
                   INNER JOIN clientes c ON c.cliente_id = f.cliente_id
                   INNER JOIN metodos_de_pagos m ON m.metodo_pago_id = f.metodo_pago_id
                   INNER JOIN usuarios u ON u.usuario_id = f.usuario_id
                   WHERE f.facturarp_id = '$invID'";

	$data = $db->query($query)->fetch_object();


	/**
	 * TODO: Detalle de factura
 -------------------------------------------------------------------------------------------------------------------------------*/

	$query_detail = "SELECT descripcion,cantidad,precio,descuento FROM detalle_ordenrp
    WHERE orden_rp_id = '$orID'";

	$result_detail = $db->query($query_detail);


	ob_start();
	include(dirname('__FILE__') . '/facturas/factura_rp.php');
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
