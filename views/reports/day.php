<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Ventas del día</h1>
        </div>

        <div class="float-right ml-2">
            <a class="btn-custom btn-green" href="<?= base_url ?>src/excel/detalle-ventas-hoy.php">
                <i class="fas fa-file-excel"></i>
                <p>Reporte</p>
            </a>
        </div>

        <div class="float-right">
            <input type="date" class="form-custom form-control-sm" name="" id="date_query">
        </div>

    </div>
</div>

<div class="generalContainer">
    <table id="today" class="table-custom table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Cobrado</th>
                <th>Por cobrar</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

    </table>
</div>

<?php
$total_vendido = "0";
$total_pendiente = "0";
$total_recibido = "0";

while ($element = $invoices->fetch_object()):

    $total_recibido += $element->recibido;
    $total_vendido += $element->total;
    $total_pendiente += $element->pendiente;
?>

<?php endwhile; ?>

<div class="buttons clearfix">
    <div class="floatButtons">
        <div class="inventoryTable">
            <div>
                <span>Total vendido :</span>
                <p><?= number_format($total_vendido, 2); ?></p>
            </div>

            <div>
                <span>Total recibido :</span>
                <p><?= number_format($total_recibido, 2); ?></p>
            </div>

            <div>
                <span>Total pendiente :</span>
                <p><?= number_format($total_pendiente, 2); ?></p>
            </div>

        </div>

    </div>
</div>