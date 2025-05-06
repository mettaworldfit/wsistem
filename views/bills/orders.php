<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Ordenes de compras</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>bills/add_order" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Orden de compra</p>
            </a>
        </div>
    </div>
</div>

<div class="generalContainer">
<div id="loader"></div>
    <table id="example" class="table-custom table" style="display: none;">
        <thead>
            <tr>
                <th>N°</th>
                <th>Proveedor</th>
                <th class="hide-cell">Artículos ordenados</th>
                <th>Fecha</th>
                <th class="hide-cell">Expiración</th>
                <th class="hide-cell">Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($element = $orders->fetch_object()): ?>
                <tr>
                    <td>OC-00<?= $element->orden_id ?></td>
                    <td><?= ucwords($element->nombre_proveedor) ?></td>
                    <td class="hide-cell"><?= Help::LIST_ORDERS($element->orden_id) ?></td>
                    <td><?= $element->creacion ?></td>
                    <td class="hide-cell"><?= $element->expiracion ?></td>
                    <td class="hide-cell">
                        <input type="text" name="" class="form-custom <?php if ($element->nombre_estado == "Pendiente") { ?> Pendiente <?php } else { ?> Listo <?php } ?>"
                            value="<?= $element->nombre_estado ?>" id="status_rp" disabled>
                    </td>

                    <td>

                        <a class="action-edit <?php if ($element->nombre_estado == 'Facturado') { ?> action-disable <?php } ?> "
                            href="<?php if ($element->nombre_estado != 'Facturado') {
                                echo base_url . 'expenses/edit_order&id=' . $element->orden_id;
                            } else {
                                echo '#';
                            } ?> ">

                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <span style="font-size: 16px;" <?php if ($element->nombre_estado != 'Facturado') { ?>
                                onclick="deleteOrderC('<?= $element->orden_id ?>')" <?php } ?>
                            class="action-delete <?php if ($element->nombre_estado == 'Facturado') { ?>action-disable<?php } ?> "><i
                                class="fas fa-times"></i></span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>

</div>