<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Factura de reparación RP-00<?= $data->facturarp_id ?></title>

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

        .eslogan {
            font-family: 'BrixSansBlack';
            font-size: 11pt;
            font-style: italic;
        }

        .h3 {
            font-family: 'BrixSansBlack';
            font-size: 12pt;
            display: block;
            background: #bf0811;
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
            width: 35%;
        }

        .info_empresa {
            width: 35%;
            text-align: center;
        }

        .info_factura {
            width: 30%;
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

        .total {
            font-size: 14pt;
            font-weight: bold;
            font-family: 'BrixSansBlack';
        }
    </style>
</head>

<?php
$nombreImagen = base_url . $Logo_pdf;
$imagenBase64 = "data:image/png;base64," . base64_encode(file_get_contents($nombreImagen));
?>

<body>
    <!-- <img class="anulada" src="img/anulado.png" alt="Anulada"> -->
    <div id="page_pdf">
        <table id="factura_head">
            <tr>
                <td class="logo_factura">
                    <div>
                        <img src="<?= $imagenBase64 ?>"> <br>
                        <span class="eslogan"><?= $Slogan ?></span>
                        <p><?= $Dir ?></p>
                        <p><?= $Tel ?></p>
                    </div>
                </td>
                <td class="info_empresa">
                    <div>
                        <span class="h2">FACTURA DE REPARACION</span> <br>
                    </div>
                </td>
                <td class="info_factura">
                    <div class="round">
                        <p>N° Factura: <strong>RP-00<?= $data->facturarp_id ?></strong></p>
                        <p>N° Orden: <strong>OR-00<?= $data->orden_rp_id ?></strong></p>
                        <p>Fecha.: <?= $data->fecha ?></p>
                        <p>Hora.: <?= date('h:i:s a', time()) ?></p>

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
                                    <p><?= $data->cedula ?></p>
                                </td>
                                <td><label>Teléfono 1:</label>
                                    <p><?= $data->telefono1 ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Nombre:</label>
                                    <p><?= $data->nombre_cliente ?> <?= $data->apellidos_cliente ?></p>
                                </td>
                                <td><label>Télefono 2:</label>
                                    <p><?= $data->telefono2 ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Email:</label>
                                    <p><?= $data->email ?></p>
                                </td>
                                <td><label>Dirección:</label>
                                    <p><?= $data->direccion ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>

            </tr>
        </table>

        <!-- Detalle -->
        <table id="factura_detalle">
            <thead>
                <tr>
                    <th width="50px">Cant.</th>
                    <th class="textleft">Descripción</th>
                    <th class="textright" width="100px">Precio Unitario.</th>
                    <th class="textright" width="100px">Descuento</th>
                    <th class="textright" width="100px">Precio total</th>
                </tr>
            </thead>

            <tbody id="detalle_productos">
                <?php while ($element = $result_detail->fetch_object()) : ?>

                    <tr>
                        <td class="textcenter"><?= $element->cantidad ?></td>
                        <td><?= $element->descripcion ?></td>
                        <td class="textright"><?= number_format($element->precio, 2) ?></td>
                        <td class="textright"><?= number_format($element->descuento, 2) ?></td>
                        <td class="textright"><?= number_format(($element->cantidad * $element->precio) - $element->descuento, 2) ?></td>
                    </tr>

                <?php endwhile; ?>
            </tbody>

            <br>
            <tfoot id="detalle_totales">
                <tr>
                    <td colspan="4" class="textright"><span>SUBTOTAL</span></td>
                    <td class="textright"><span><?= number_format($subtotal, 2) ?></span></td>
                </tr>
                <tr>
                    <td colspan="4" class="textright"><span>DESCUENTO</span></td>
                    <td class="textright"><span><?= number_format($discount, 2) ?></span></td>
                </tr>
                <tr>
                    <td colspan="4" class="textright"><span>ITBIS</span></td>
                    <td class="textright"><span>0.00</span></td>
                </tr>
                <tr>
                    <td colspan="4" class="textright total"><span><b>TOTAL</b></span></td>
                    <td class="textright total"><span><?= number_format($total, 2) ?></span></td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div>

            <h6>COMENTARIOS O INSTRUCCIONES ESPECIALES:</h6>
             <?php if ($data->descripcion == "") { echo "-"; } ?>
            <p><?= $data->descripcion ?></p>
            <br>
            <br>
            <br><br>
            <p>_______________________________________</p>
            <p>Le atendió.: <?= $data->nombre_usuario ?> <?= $data->apellidos_usuario ?></p>
            <br><br>

            <p class="nota textcenter"><?= $Policy ?></p>
            <br>
            <p class="nota textcenter"><b><?= $Title ?></b></p>

            <h4 class="label_gracias">¡Gracias por su compra!</h4>
            <br>
            <p class="nota textcenter">De acuerdo con la Ley No. 172-13 que tiene por objeto la protección integral de los datos personales, le informamos que el tratamiento de los datos que nos proporcione solo serán utilizados para ofrecer nuestros servicios.</p>

        </div>

    </div>

</body>

</html>