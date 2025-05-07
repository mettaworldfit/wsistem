<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Pagos</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>bills/add_payment" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Nuevo pago</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Documento</th>
                <th class="hide-cell">Proveedor</th>
                <th>Monto</th>
                <th class="hide-cell">Observación</th>
                <th>Fecha</th>
                <th class="hide-cell">Acciones</th>
            </tr>
        </thead>


        <tbody>
            <?php while ($element = $payments->fetch_object()): ?>
                <tr>
                    <td><?= $element->id ?></td>
                    <td>FP-00<?= $element->factura_proveedor_id ?></td>
                    <td class="hide-cell"><?= ucwords($element->nombre_proveedor) ?>     <?= ucwords($element->apellidos) ?></td>
                    <td class="text-success"><?= number_format($element->pagado, 2) ?></td>
                    <td class="note-width hide-cell"><?= $element->observacion ?></td>
                    <td><?= $element->creacion ?></td>
                    <td class="hide-cell">

                        <span style="font-size: 16px;" onclick="deletePaymentProvider('<?= $element->id ?>')"
                            class="action-delete"><i class="fas fa-times"></i></span>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>

</div>