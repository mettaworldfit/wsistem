<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Pagos</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>payments/add" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Nuevo pago</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
<div id="loader"></div>
    <table id="example" class="table-custom table" style="display: none;">
        <thead>
            <tr>
                <th class="hide-cell">N°</th>
                <th>Documento</th>
                <th>Cliente</th>
                <th>Recibido</th>
                <th class="hide-cell">Observación</th>
                <th class="hide-cell">Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>


        <tbody>
            <?php while ($element = $payments->fetch_object()): ?>

                <tr>
                    <td class="hide-cell">00<?= $element->id ?></td>

                    <?php if ($element->factura_venta_id > 0) { ?>
                        <td>FT-00<?= $element->factura_venta_id ?></td>
                    <?php } else if ($element->facturaRP_id > 0) { ?>
                            <td>RP-00<?= $element->facturaRP_id ?></td>
                    <?php } else { ?>
                            <td class="text-danger">Factura eliminada</td>
                    <?php } ?>

                    <td><?= ucwords($element->nombre) ?>     <?= ucwords($element->apellidos) ?></td>
                    <td class="text-success"><?= number_format($element->pagado, 2) ?></td>
                    <td class="note-width hide-cell"><?= $element->observacion ?></td>
                    <td class="hide-cell"><?= $element->creacion ?></td>
                    <td>

                        <?php if ($element->factura_venta_id > 0) { ?>
                            <span onclick="deletePayment('<?= $element->id ?>',1,0)" class="action-delete"><i
                                    class="fas fa-times"></i></span>
                        <?php } else { ?>
                            <span onclick="deletePayment('<?= $element->id ?>',0,1)" class="action-delete"><i
                                    class="fas fa-times"></i></span>
                        <?php } ?>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>

</div>