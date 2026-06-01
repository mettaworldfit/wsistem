<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Caja</title>
    <link rel="stylesheet" href="/fontawesome/all.min.css">
    <script src="/fontawesome/all.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        .content h3 {
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin-top: 25px;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .details th,
        .details td {
            padding: 8px;
            text-align: left;
            font-size: 14px;
            color: #333;
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

        .social-media img {
            width: 34px;
            height: 34px;
            margin: 0 4px;
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

        p.poweredby {
            font-size: 14pt;
            font-weight: 800;
            color: #ddd;
        }

        .poweredby a {
            color: black !important;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header con logo -->
        <div class="header">
            <img src="<?= $logo_url ?>" alt="Logo <?= $Company ?>">
        </div>

        <!-- Contenido principal -->
        <div class="content">
            <p>Estimado/a <strong>Equipo de Administración</strong>,</p>
            <p>Se ha generado correctamente un nuevo <strong>cierre de caja</strong> dentro del sistema <strong><?= $Company; ?></strong>.</p>
            <p>A continuación se detallan los datos principales del cierre:</p>

            <h3>Detalles del Cierre de Caja</h3>
            <table class="details">
                <tr>
                    <th>Código de Cierre</th>
                    <td>0<?= $cierreNumero ?? '' ?></td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td><?= $fechaCierre ?? date('d/m/Y') ?></td>
                </tr>
                <tr>
                    <th>Responsable</th>
                    <td><?= $cajero ?? '-' ?></td>
                </tr>
                <tr>
                    <th>Total del Cierre</th>
                    <td><?= number_format($totalCierre, 2) ?></td>
                </tr>
                <tr>
                    <th>Total efectivo en caja</th>
                    <td><?= number_format($total_esperado, 2) ?></td>
                </tr>
                <tr>
                    <th>Diferencia</th>
                    <td><?= number_format($diferencia, 2) ?></td>
                </tr>
            </table>

            <p>Este mensaje ha sido generado automáticamente como confirmación del cierre de caja.</p>

        </div>

        <!-- Pie de página con datos de la empresa -->
        <div class="footer">
            <p><strong><?= $Company ?></strong></p>
            <p><?= $Dir ?></p>
            <p>Tel: <?= $Tel ?></p>
            <div class="social-media">
                <a href="<?= $Link_ws ?? '#' ?>">
                    <img src="https://d1csarkz8obe9u.cloudfront.net/assets/social-icons/circle-black/social-icon-whatsapp.png?ts=1731689004" alt="WhatsApp">
                </a>
                <a href="<?= $Link_ig ?? '#' ?>">
                    <img src="https://d1csarkz8obe9u.cloudfront.net/assets/social-icons/circle-black/social-icon-instagram.png?ts=1731689004" alt="Instagram">
                </a>
            </div>
            <p class="poweredby">Powered by <a href="https://wa.me/18295020900?text=Me%20intereza%20saber%20más%20sobre%20el%20sistema">Codevrd</a></p>
        </div>
    </div>
</body>

</html>