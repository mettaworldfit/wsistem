<?php

session_start();

if (!isset($_SESSION['identity'])) {

	header('location: ../');
}

require_once '../../vendor/autoload.php';
require_once '../../config/parameters.php';
require_once '../../config/db.php';

use Dompdf\Dompdf;
use Dompdf\Options;


if (!empty($_REQUEST['f'])) {

	$db = Database::connect();

	$ID = $_REQUEST['f'];
	$subtotal = $_REQUEST['sub'];
	$discount = $_REQUEST['dis'];
	$taxes = $_REQUEST['tax'];
	$total = $_REQUEST['total'];

	// ================================
    // Datos cliente
    // ================================

	date_default_timezone_set('America/New_York');

	$query = "SELECT c.cedula ,c.nombre as nombre_cliente, c.apellidos as apellidos_cliente,c.telefono1,c.telefono2,
				c.email,c.direccion, u.nombre as nombre_usuario, u.apellidos as apellidos_usuario, 
				ct.cotizacion_id, ct.fecha, ct.descripcion FROM cotizaciones ct
				INNER JOIN clientes c ON c.cliente_id = ct.cliente_id
				INNER JOIN usuarios u ON u.usuario_id = ct.usuario_id
				WHERE ct.cotizacion_id = '$ID'";

	$data = $db->query($query)->fetch_object();


   // ================================================
   // Detalle de factura
   //=================================================

	$query_detail = "SELECT descripcion, precio, cantidad, descuento, impuesto 
    FROM detalle_cotizaciones WHERE cotizacion_id = '$ID'";

	$result_detail = $db->query($query_detail);

	// ==============================================================
    // Obtener datos de la factura desde la base de datos
    // ==============================================================

	$query3 = "SELECT logo_pdf,tel,direccion,slogan,condiciones,titulo 
	FROM configuraciones WHERE config_id = 1";

	$conf = $db->query($query3)->fetch_object();

	$Logo_pdf = $conf->logo_pdf;
	$Tel = $conf->tel;
	$Dir = $conf->direccion;
	$Slogan = $conf->slogan;
	$Policy = $conf->condiciones;
	$Title = $conf->titulo;


	ob_start();
	include(dirname('__FILE__') . '/facturas/cotizacion.php');
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
	$dompdf->stream('cotizacion.pdf', array('Attachment' => 0));
	exit;
} else {
	echo "No es posible generar la factura.";
}
