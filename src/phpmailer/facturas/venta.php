<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura Electrónica</title>
    <link rel="stylesheet" href="/fontawesome/all.min.css">
    <script src="/fontawesome/all.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }

        .wrap {
            width: 100%;
            padding: 38px 0px 10px 0px;
        }

        p {
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            text-align: center;
            padding: 20px;
            background: #ffffff;
        }

        .header img {
            max-width: 180px;
            height: auto;
        }

        .content {
            padding: 25px;
            color: #333;
            font-size: 14px;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        .details th,
        .details td {
            padding: 8px;
            text-align: left;
            font-size: 13px;
            color: black;
        }

        .details th {
            background: #f9fafc;
        }

        .footer {
            background: #f1f3f6;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #777;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 20px;
            color: #fff;
            background: #28a745;
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background: #218838;
        }

        .social-media img {
            max-width: 100%;
            height: 34px;
            margin: 0px 4px;
        }

        p.poweredby {
            font-size: 14pt;
            font-weight: 800;
            color: #ddd;
        }

        .poweredby a {
            color: black !important;
            text-decoration: none;
        }

        img.logo {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="wrap">
            <div class="header">
                <img src="<?= $Logo_url ?>" alt="Logo <?= $Company ?>">
            </div>

            <div class="content">
                <p>Estimado/a <strong><?= $CustName; ?> <?= $CustLastName; ?></strong>,</p>
                <p>Gracias por visitar <strong><?= $Company; ?></strong>. A continuación, los detalles de su factura:</p>

                <h3 style="border-bottom: 1px solid #eee; padding-bottom: 8px;">Detalles de la Factura</h3>
                <table class="details">
                    <tr>
                        <th>Factura No.</th>
                        <td>FT-00<?= $invID ?></td>
                    </tr>
                    <tr>
                        <th>Fecha</th>
                        <td><?= $date ?></td>
                    </tr>
                    <tr>
                        <th>Método de Pago</th>
                        <td><?= $method ?></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td><?= number_format($total, 2) ?></td>
                    </tr>
                </table>

                <p>Puede ver y descargar su factura al final</p>
            </div>

            <!-- Pie de página con los datos de la empresa -->
            <div class="footer">
                <p><strong><?= $Company ?></strong></p>
                <p><?= $Dir ?></p>
                <p>Tel: <?= $Tel ?></p>
                <p class="poweredby">Powered by <a href="https://wa.me/18295020900?text=Me%20intereza%20saber%20m%C3%A1s%20sobre%20el%20sistema">Codevrd</a></p>
            </div>
        </div>
    </div>
</body>

</html>