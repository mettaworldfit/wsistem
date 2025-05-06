<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Impuestos</h1>
        </div>


        <div class="float-right">
            <a class="btn-custom btn-default" href="<?= base_url ?>taxes/add">
                <i class="fas fa-plus"></i>
                <p>Agregar impuesto</p>
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
                <th>Nombre</th>
                <th>Valor</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>



        <tbody>
            <?php while ($element = $taxes->fetch_object()): ?>
                <?php $parents = Help::verify_parent_tax($element->impuesto_id);
                while ($parent = $parents->fetch_object()) { ?>

                    <tr>
                        <td><?= $element->impuesto_id ?></td>
                        <td><?= ucwords($element->nombre_impuesto) ?></td>
                        <td><?= $element->valor ?>%</td>
                        <td class="note-width"><?= $element->descripcion ?></td>
                        <td><?= $element->fecha ?></td>

                        <td>

                            <a class="action-edit" href="<?= base_url . 'taxes/edit&id=' . $element->impuesto_id; ?> title="
                                Editar"">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <span <?php if ($parent->parent_row == 0) { ?> class="action-delete"
                                    onclick="deleteTax('<?= $element->impuesto_id ?>')" <?php } else { ?>
                                    class="action-delete action-disable" <?php } ?> title="Eliminar">
                                <i class="fas fa-times"></i>
                            </span>
                        </td>
                    </tr>

                <?php }endwhile; ?>
        </tbody>

    </table>

</div>