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
<table id="example" class="table-custom table">
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


        <tbody>
            <?php
            $total_vendido = "0";
            $total_pendiente = "0";
            $total_recibido = "0";

            while ($element = $invoices->fetch_object()):

                $total_recibido += $element->recibido;
                $total_vendido += $element->total;
                $total_pendiente += $element->pendiente;
                ?>

                <tr>
                    <td><?= $element->tipo ?>-00<?= $element->id ?></td>
                    <td><?= ucwords($element->nombre) ?>     <?= ucwords($element->apellidos) ?></td>
                    <td><?= $element->fecha_factura ?></td>
                    <td class="text-primary"><?= number_format($element->total, 2) ?></td>
                    <td class="text-success"><?= number_format($element->recibido, 2) ?></td>
                    <td class="text-danger"><?= number_format($element->pendiente, 2) ?></td>
                    <td>
                        <p class="<?= $element->estado ?>"><?= $element->estado ?></p>
                    </td>

                    <td>

                        <?php if ($element->tipo == 'FT') { ?>

                            <a <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?> class="action-edit"
                                    href="<?php echo base_url . 'invoices/edit&id=' . $element->id; ?>" <?php } else { ?>
                                    class="action-edit action-disable" href="#" <?php } ?> title="editar">

                                <i class="fas fa-pencil-alt"></i>
                            </a>

                        <?php } else if ($element->tipo == 'RP') { ?>

                                <a <?php if ($element->estado != 'Anulada' && $_SESSION['identity']->nombre_rol == 'administrador') { ?> class="action-edit" href="<?= base_url . 'invoices/repair_edit&id=' . $element->orden; ?>"
                                <?php } else { ?> class="action-edit action-disable" href="#" <?php } ?> title="Editar">

                                    <i class="fas fa-pencil-alt"></i>
                                </a>

                        <?php } ?>

                        <span <?php if ($element->tipo == "FT") { ?> onclick="deleteInvoice('<?= $element->id ?>')" <?php } else if ($element->tipo == 'RP') { ?> onclick="deleteInvoiceRP('<?= $element->id ?>')" <?php } else if ($element->tipo == 'PF') { ?>
                                        onclick="deletePayment('<?= $element->id ?>','<?= $element->orden ?>',0)" <?php } else if ($element->tipo == 'PR') { ?>
                                            onclick="deletePayment('<?= $element->id ?>',0,'<?= $element->orden ?>')" <?php } ?>
                            class="action-delete" title="Eliminar"><i class="fas fa-times"></i></span>
                    </td>
                </tr>

            <?php endwhile; ?>
        </tbody>

    </table>
</div>



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