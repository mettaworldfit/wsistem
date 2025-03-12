<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Factura de venta FT-00<?= $data->factura_venta_id ?></title>

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
$nombreImagen = base_url . "public/imagen/sistem/logo2.png";
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
                        <span class="eslogan">La garantía de tu comunicación</span>
                        <p>C/ 27 de Fébrero (Frente al correo) Mao, Valverde R.D.</p>
                        <p>(809) 572-3846</p>
                    </div>
                </td>
                <td class="info_empresa">
                    <div>
                        <span class="h2">FACTURA DE VENTA</span> <br>
                    </div>
                </td>
                <td class="info_factura">
                    <div class="round">
                        <p>N° Factura: <strong>FT-00<?= $data->factura_venta_id ?></strong></p>
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
                    <th class="textright" width="100px">Impuesto</th>
                    <th class="textright" width="100px">Descuento</th>
                    <th class="textright" width="100px">Precio total</th>
                </tr>
            </thead>

            <tbody id="detalle_productos">
                <?php while ($element = $result_detail->fetch_object()) : ?>

                    <tr>
                        <td class="textcenter"><?= $element->cantidad_total ?></td>
                        <td>
                            <?php
                            if ($element->nombre_producto) {
                                echo ucwords($element->nombre_producto);
                            } else if ($element->nombre_pieza) {
                                echo ucwords($element->nombre_pieza);
                            } else if ($element->nombre_servicio) {
                                echo ucwords($element->nombre_servicio);
                            }
                            ?>

                        </td>
                        <td class="textright"><?= number_format($element->precio, 2) ?></td>
                        <td class="textright"><?= number_format($element->cantidad_total * $element->impuesto, 2) ?> (<?= $element->valor ?>%)</td>
                        <td class="textright"><?= number_format($element->descuento, 2) ?></td>
                        <td class="textright"><?= number_format(($element->cantidad_total * $element->precio) + ($element->cantidad_total * $element->impuesto) - $element->descuento, 2) ?></td>
                    </tr>

                <?php endwhile; ?>
            </tbody>

            <br>
            <tfoot id="detalle_totales">
                <tr>
                    <td colspan="5" class="textright"><span>SUBTOTAL</span></td>
                    <td class="textright"><span><?= number_format($subtotal, 2) ?></span></td>
                </tr>
                <tr>
                    <td colspan="5" class="textright"><span>DESCUENTO</span></td>
                    <td class="textright"><span><?= number_format($discount, 2) ?></span></td>
                </tr>
                <tr>
                    <td colspan="5" class="textright"><span>ITBIS</span></td>
                    <td class="textright"><span><?= number_format($taxes, 2) ?></span></td>
                </tr>
                <tr>
                    <td colspan="5" class="textright total"><span><b>TOTAL</b></span></td>
                    <td class="textright total"><span><?= number_format($total, 2) ?></span></td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div>

            <h6>COMENTARIOS O INSTRUCCIONES ESPECIALES:</h6>
            <?php if ($data->descripcion == "") {
                echo "-";
            } ?>
            <p><?= $data->descripcion ?></p>
            <br>
            <br>
            <br><br>
            <p>_______________________________________</p>
            <p>Le atendió.: <?= $data->nombre_usuario ?> <?= $data->apellidos_usuario ?></p>



            <br><br>


            <p class="nota">Si usted tiene preguntas sobre esta factura, <br>pongase en contacto con nombre, teléfono y Email</p>
            <br><br>
            <p class="nota textcenter">Equipos vendidos incluyen 30 días de garantía en piezas y servicios, no garantía sin factura, no se reciben equipos: mojados, golpeados, apagados, pantallas rotas o pantallas en negro, manipulados por otros técnicos o sin sellos de garantía. </p>
            <br>
            <p class="nota textcenter">Batería y cargadores 3 días de garantía, accesorios no tienen garantía a menos que se le indique.</p>
            <br>
            <p class="nota textcenter">Pantallas y reparaciones no incluyen garantía.</p>
            <br>
            <p class="nota textcenter"><b>No hay devolución de dinero en efectivo pasadas las 24 horas.</b></p>
                       
            <h4 class="label_gracias">¡Gracias por su compra!</h4>
            <br>


        </div>

    </div>

</body>

</html>