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
            background-color: #f4f4f4;
        }
        
        .wrap {
            width: 100%;
            padding: 38px 0px 10px 0px;
        }
        
        p {
            color: #fff;
        }
        
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            text-align: center;
            overflow: hidden;
            height: 80px;
            color: #fff;
            padding-bottom: 40px;
        }
        
        .content {
            padding: 20px;
            border: 0.5px solid #919191;
        }
        
        .details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .details th,
        .details td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd !important;
            font-size: 13px;
            color: black;
        }
        
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
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
        <div class="wrap" style="background: rgb(56, 56, 56);">
            <div class="header" style="background: #a2a7aa;">

                <img class="logo" src="https://i.imgur.com/cdclH7v_d.png?maxwidth=520&shape=thumb&fidelity=high" alt="logo">
            </div>
            <div class="content">
                <p>Estimado/a <strong><?= $CustName; ?> <?= $CustLastName; ?></strong>,</p>
                <p>Gracias por su compra. A continuación, los detalles de su factura:</p>

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
                        <td><?= number_format($total,2) ?></td>
                    </tr>
                </table>



                <p>Puede ver y descargar su factura al final</p>
            </div>
            <div class="footer">
                <div class="social-media">
                    <a href="">
                        <img src="https://d1csarkz8obe9u.cloudfront.net/assets/social-icons/circle-black/social-icon-whatsapp.png?ts=1731689004" alt="">
                    </a>
                    <a href="">
                        <img src="https://d1csarkz8obe9u.cloudfront.net/assets/social-icons/circle-black/social-icon-facebook.png?ts=1731689004" alt="">
                    </a>
                    <a href="">
                        <img src="https://d1csarkz8obe9u.cloudfront.net/assets/social-icons/circle-black/social-icon-instagram.png?ts=1731689004" alt="">
                    </a>
                </div>
                <p><a href="" style="color: white;">Calle 27 de febrero frente al correo mao</a></p>
                <p><a href="" style="color: white;">Tel: (809) 760-0000</a></p>
                <p class="poweredby">Powered by <a href="">Codevrd</a></p>
            </div>
        </div>
    </div>
</body>

</html>