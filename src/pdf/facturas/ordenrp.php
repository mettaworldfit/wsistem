<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orden de servicio OR-00<?= $data->orden_rp_id ?></title>

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
            font-size: 10pt;
            display: block;
            background: #dc0404;
            font-weight: bold;
            color: #FFF;
            text-align: center;
            padding: 3px;
            margin-bottom: 5px;
        }

        .secondary {
            background: #484848 !important;
        }

        .eslogan {
            font-family: 'BrixSansBlack';
            font-size: 11pt;
            font-style: italic;
        }

        #page_pdf {
            width: 95%;
            margin: 15px auto 10px auto;
        }

        #factura_head,
        #factura_cliente,
        #factura_equipo,
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
    </style>
</head>

<?php
$nombreImagen = base_url."public/imagen/sistem/logo2.png";
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
                        <span class="h2"> ORDEN DE SERVICIO</span>

                    </div>
                </td>
                <td class="info_factura">
                    <div class="round">
                        <p>N° Orden: <strong>OR-00<?= $data->orden_rp_id ?></strong></p>
                        <p>Fecha impresión.: <?= date('d-m-Y', time()) ?></p>
                        <p>Hora impresión.: <?= date('h:i:s a', time()) ?></p>
                    </div>
                </td>
            </tr>
        </table>

        <br>
        <p>Fecha de ingreso: <?= $data->fecha_entrada ?></p>
        <table id="factura_cliente">
            <tr>
                <td class="info_cliente">
                    <div class="round">
                        <span class="h3">DATOS DEL CLIENTE</span>
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


        <table id="factura_equipo">
            <tr>
                <td class="info_cliente">
                    <div class="round">
                        <span class="h3 secondary">IDENTIFICACION Y CARACTERISTICAS DEL EQUIPO</span>
                        <table class="datos_cliente">
                            <tr>
                                <td><span>Nombre equipo: <?= $data->nombre_modelo ?></span>
                                </td>
                                <td><span>Número de serie: <?= $data->serie ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span>Número modelo: <?= $data->modelo ?></span>
                                </td>
                                <td><span>Imei: <?= $data->imei ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><span>Fabricante: <?= $data->nombre_marca ?></span>
                                </td>
                            </tr>
                            <br>

                            <h3> <b> Condición del equipo: </b></h3> <br>
                            <?php while ($element = $result_condition->fetch_object()) : ?>
                            <span>- <?= $element->sintoma ?></span> <br>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </td>

            </tr>
        </table>


        <!-- Footer -->
        <div>
            
            <br>
            <br>
            <br><br><br>
            <p>_______________________________________</p>
            <p>Le atendió.: </p>
            <br><br>
            <h4>Importante:</h4>
            <p class="nota">- Debe presentar este documento o referir el N° de Orden para recoger el equipo.</p>
            <p class="nota">- No nos responsabilizamos por los equipos dejados por más de 30 días sin recoger.</p>
            <br><br>
            <h4 class="label_gracias">¡Gracias por confiar en nosotros!</h4>
        </div>

    </div>

</body>

</html>