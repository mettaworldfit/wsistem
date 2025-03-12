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


if (!empty($_REQUEST['o'])) {

	$db = Database::connect();

	$orderId = $_REQUEST['o'];

	/**
	 * TODO: Datos 
 -------------------------------------------------------------------------------------------------------------------------------*/

	date_default_timezone_set('America/New_York');

	$query = "SELECT c.cedula ,c.nombre as nombre_cliente, c.apellidos as apellidos_cliente,c.telefono1,c.telefono2,
    c.email,c.direccion, u.nombre as nombre_usuario, u.apellidos as apellidos_usuario, 
    o.orden_rp_id, o.fecha_entrada,o.fecha_salida, e.nombre_modelo,e.modelo,m.nombre_marca
    ,o.serie,o.imei FROM ordenes_rp o
                   INNER JOIN clientes c ON c.cliente_id = o.cliente_id
                   INNER JOIN usuarios u ON u.usuario_id = o.usuario_id
                   INNER JOIN equipos e ON e.equipo_id = o.equipo_id
                   INNER JOIN marcas m ON m.marca_id = e.marca_id
                   WHERE o.orden_rp_id = '$orderId'";

	$data = $db->query($query)->fetch_object();


	/**
	 * TODO: Datos de la orden
 -------------------------------------------------------------------------------------------------------------------------------*/

	$query_condition = "SELECT c.sintoma FROM ordenes_rp_con_condiciones oc
    INNER JOIN ordenes_rp o ON o.orden_rp_id = oc.orden_rp_id
    INNER JOIN condiciones c ON c.condicion_id = oc.condicion_id
    WHERE o.orden_rp_id = '$orderId'";

	$result_condition = $db->query($query_condition);


	ob_start();
	include(dirname('__FILE__') . '/facturas/ordenrp.php');
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
