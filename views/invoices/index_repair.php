<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Facturas reparaciones</h1>
        </div>


        <div class="float-right">
            <!-- <a href="<?= base_url ?>invoices/addrepair" class="btn btn-sm btn-secondary">Nueva factura</a> -->
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th class="hide-cell">Total</th>
                <th class="hide-cell">Cobrado</th>
                <th class="hide-cell">Por cobrar</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>


        <tbody>
            <?php while ($element = $invoices->fetch_object()) : ?>
                <tr>
                    <td>RP-00<?= $element->facturaRP_id ?></td>
                    <td><?= ucwords($element->nombre) ?> <?= ucwords($element->apellidos) ?></td>
                    <td><?= $element->fecha_factura ?></td>
                    <td class="text-primary hide-cell"><?= number_format($element->total, 2) ?></td>
                    <td class="text-success hide-cell"><?= number_format($element->recibido, 2) ?></td>
                    <td class="text-danger hide-cell"><?= number_format($element->pendiente, 2) ?></td>
                    <td>
                        <p class="<?= $element->nombre_estado ?>"><?= $element->nombre_estado ?></p>
                    </td>

                    <td>

                        <a <?php if ($element->nombre_estado != 'Anulada' && $_SESSION['identity']->nombre_rol == 'administrador') { ?> class="action-edit" href="<?= base_url.'invoices/repair_edit&o='.$element->orden_rp_id.'&f='.$element->facturaRP_id ?>" 
                            <?php } else { ?> class="action-edit action-disable" href="#" <?php } ?> title="Editar" > 
    
                              <i class="fas fa-pencil-alt"></i>
                        </a>


                        <span onclick="deleteInvoiceRP('<?= $element->facturaRP_id ?>')" class="action-delete"><i class="fas fa-times"></i></span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>
