<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Facturas proveedores</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>bills/addinvoice" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Nueva factura</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th class="hide-cell">N°</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th class="hide-cell">Pagado</th>
                <th>Por pagar</th>
                <th class="hide-cell">Estado</th>

                <th>Acciones</th>
            </tr>
        </thead>


        <tbody>
            <?php while ($element = $invoices->fetch_object()): ?>
                <tr>
                    <td class="hide-cell">FP-00<?= $element->factura_proveedor_id ?></td>
                    <td><?= ucwords($element->nombre_proveedor) ?>     <?= ucwords($element->p_apellidos) ?></td>
                    <td><?= $element->fecha_factura ?></td>
                    <td class="text-primary"><?= number_format($element->total, 2) ?></td>
                    <td class="text-success hide-cell"><?= number_format($element->pagado, 2) ?></td>
                    <td class="text-danger"><?= number_format($element->por_pagar, 2) ?></td>
                    <td class="hide-cell">
                        <p class="<?= $element->nombre_estado ?>"><?= $element->nombre_estado ?></p>
                    </td>

                    <td>

                        <!-- <a  class="action-edit <?php if ($element->nombre_estado == 'Anulada') { ?> action-disable <?php } ?> " 
                                 href="<?php if ($element->nombre_estado != 'Anulada') {
                                     echo base_url . 'expenses/edit_invoice&id=' . $element->factura_proveedor_id;
                                 } else {
                                     echo '#';
                                 } ?> "> 
                            
                              <i class="fas fa-pencil-alt"></i>
                        </a> -->

                        <span style="font-size: 16px;"
                            onclick="deleteInvoiceFP('<?= $element->factura_proveedor_id ?>','<?= $element->orden_id ?>')"
                            class="action-delete"><i class="fas fa-times"></i></span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>