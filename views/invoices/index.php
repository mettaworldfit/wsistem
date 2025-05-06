<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Facturas de ventas</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>invoices/addpurchase" class="btn-custom btn-default">
            <i class="fas fa-plus"></i>
            <p>Nueva factura</p></a>
        </div>
    </div>
</div>



<div class="generalContainer">
<div id="loader"></div>
    <table id="example" class="table-custom table" style="display: none;">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th class="hide-cell">Total</th>
                <th class="hide-cell">Cobrado</th>
                <th class="hide-cell">Por cobrar</th>
                <th class="hide-cell">-Bono</th>
                <th>Estado</th>

                <th>Acciones</th>
            </tr>
        </thead>


        <tbody>
            <?php while ($element = $invoices->fetch_object()) : ?>
                <tr>
                    <td>FT-00<?= $element->factura_venta_id ?></td>
                    <td><?= ucwords($element->nombre) ?> <?= ucwords($element->apellidos) ?></td>
                    <td><?= $element->fecha_factura ?></td>
                    <td class="text-primary hide-cell"><?= number_format($element->total, 2) ?></td>
                    <td class="text-success hide-cell"><?= number_format($element->recibido, 2) ?></td>
                    <td class="text-danger hide-cell"><?= number_format($element->pendiente, 2) ?></td>
                    <td class="text-warning hide-cell"><?= number_format($element->bono, 2) ?></td>
                    <td>
                        <p class="<?= $element->nombre_estado ?>"><?= $element->nombre_estado ?></p>
                    </td>

                    <td>
                        <!-- <span class="action-paid <?php if ($element->nombre_estado != 'Por Cobrar'): ?> action-disable  <?php endif; ?>" 
                            <?php if ($element->nombre_estado == 'Por Cobrar'): ?>  data-toggle="modal" data-target="#payment_modal" <?php endif; ?>> 
                             <i class="fas fa-hand-holding-usd"></i>
                        </span> -->

                        <a <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?> class="action-edit" href="<?php echo base_url.'invoices/edit&id='.$element->factura_venta_id; ?>"
                            <?php } else { ?> class="action-edit action-disable" href="#" <?php } ?> title="editar"> 
                            
                              <i class="fas fa-pencil-alt"></i>
                        </a>

                        <!-- <span class="action-delete <?php if ($element->nombre_estado == 'Anulada'): ?> action-disable  <?php endif; ?>" 
                        <?php if ($element->nombre_estado != 'Anulada') { ?>  onclick="disabledInvoice('<?= $element->factura_venta_id ?>')" <?php } ?> title="Desactivar" id="">
                            <i class="fas fa-minus-square"></i>
                        </span> -->



                        <span onclick="deleteInvoice('<?= $element->factura_venta_id ?>')" class="action-delete"><i class="fas fa-times"></i></span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>
