<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Factura</title>

	<style>
		@import url('fonts/BrixSansRegular.css');
		@import url('fonts/BrixSansBlack.css');

		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		p,
		label,
		span,
		table {
			font-family: 'BrixSansRegular';
			font-size: 9pt;
		}

		.h2 {
			font-family: 'BrixSansBlack';
			font-size: 16pt;
		}

		.h3 {
			font-family: 'BrixSansBlack';
			font-size: 12pt;
			display: block;
			background: #484848;
			font-weight: bold;
			color: #FFF;
			text-align: center;
			padding: 3px;
			margin-bottom: 5px;
		}

		#page_pdf {
			width: 95%;
			margin: 15px auto 10px auto;
		}

		#factura_head,
		#factura_cliente,
		#factura_detalle {
			width: 100%;
			margin-bottom: 10px;
		}

		.logo_factura {
			width: 25%;
		}

		.info_empresa {
			width: 50%;
			text-align: center;
		}

		.info_factura {
			width: 25%;
		}

		.info_cliente {
			width: 100%;
		}

		.datos_cliente {
			width: 100%;
		}

		.datos_cliente tr td {
			width: 50%;
		}

		.datos_cliente {
			padding: 10px 10px 0 10px;
		}

		.datos_cliente label {
			width: 75px;
			display: inline-block;
		}

		.datos_cliente p {
			display: inline-block;
		}

		.textright {
			text-align: right;
		}

		.textleft {
			text-align: left;
		}

		.textcenter {
			text-align: center;
		}

		.round {
			border-radius: 3px;
			/* border: 1px solid #0a4661; */
			overflow: hidden;
			padding-bottom: 15px;
		}

		.round p {
			padding: 0 15px;
		}

		#factura_detalle {
			border-collapse: collapse;
		}

		#factura_detalle thead th {
			background: #484848;
			color: #FFF;
			padding: 5px;
		}

		#detalle_productos tr:nth-child(even) {
			background: #ededed;
		}

		#detalle_totales span {
			font-family: 'BrixSansBlack';
		}

		.nota {
			font-size: 8pt;
		}

		.label_gracias {
			font-family: verdana;
			font-weight: bold;
			font-style: italic;
			text-align: center;
			margin-top: 20px;
		}

		.anulada {
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translateX(-50%) translateY(-50%);
		}
	</style>
</head>

<body>
	<!-- <img class="anulada" src="img/anulado.png" alt="Anulada"> -->
	<div id="page_pdf">
		<table id="factura_head">
			<tr>
				<td class="logo_factura">
					<div>
						<!-- <img src="<?= base_url ?>public/imagen/sistem/logo2.png"> -->
					</div>
				</td>
				<td class="info_empresa">
					<div>
						<span class="h2">CHINO COMUNICACIONES</span>
						<p>Avenida las américas Zona 13, Guatemala</p>
						<p>Teléfono: +(502) 2222-3333</p>
						<p>Email: info@abelosh.com</p>
					</div>
				</td>
				<td class="info_factura">
					<div class="round">
						<span class="h3">Factura</span>
						<p>No. Factura: <strong>000001</strong></p>
						<p>Fecha: 20/01/2019</p>
						<p>Hora: 10:30am</p>
						<p>Vendedor: Jorge Pérez Hernández Cabrera</p>
					</div>
				</td>
			</tr>
		</table>
		<table id="factura_cliente">
			<tr>
				<td class="info_cliente">
					<div class="round">
						<span class="h3">Cliente</span>
						<table class="datos_cliente">
							<tr>
								<td><label>Cédula:</label>
									<p>54895468</p>
								</td>
								<td><label>Teléfono 1:</label>
									<p>7854526</p>
								</td>
							</tr>
							<tr>
								<td><label>Nombre:</label>
									<p>Angel Arana Cabrera</p>
								</td>
								<td><label>Télefono 2:</label>
									<p>00000</p>
								</td>
							</tr>
							<tr>
							<td><label>Email:</label>
									<p>Wjos260@gmail.com</p>
								</td>
								<td><label>Dirección:</label>
									<p>Calzada Buena Vista</p>
								</td>
							</tr>
						</table>
					</div>
				</td>

			</tr>
		</table>

		<table id="factura_detalle">
			<thead>
				<tr>
					<th width="50px">Cant.</th>
					<th class="textleft">Descripción</th>
					<th class="textright" width="150px">Precio Unitario.</th>
					<th class="textright" width="150px"> Precio Total</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
				<tr>
					<td class="textcenter">1</td>
					<td>Plancha</td>
					<td class="textright">516.67</td>
					<td class="textright">516.67</td>
				</tr>
			</tbody>
			<br>
			<tfoot id="detalle_totales">
				<tr>
					<td colspan="3" class="textright"><span>SUBTOTAL</span></td>
					<td class="textright"><span>516.67</span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>DESCUENTO</span></td>
					<td class="textright"><span>516.67</span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>ITBIS</span></td>
					<td class="textright"><span>516.67</span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>TOTAL</span></td>
					<td class="textright"><span>516.67</span></td>
				</tr>
			</tfoot>
		</table>
		<div>
			<p class="nota">Si usted tiene preguntas sobre esta factura, <br>pongase en contacto con nombre, teléfono y Email</p>
			<h4 class="label_gracias">¡Gracias por su compra!</h4>
		</div>

	</div>

</body>

</html>