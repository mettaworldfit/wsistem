<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Almacenes</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>warehouses/add" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Agregar almacen</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th>N°</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>



        <tbody>
            <?php while ($element = $warehouses->fetch_object()): ?>
                <?php $parents = Help::verify_parent_warehouse($element->almacen_id);
                while ($parent = $parents->fetch_object()) { ?>

                    <tr>
                        <td><?= $element->almacen_id ?></td>
                        <td><?= ucwords($element->nombre_almacen) ?></td>
                        <td class="note-width"><?= $element->descripcion ?></td>
                        <td><?= $element->fecha ?></td>

                        <td>

                            <a class="action-edit" href="<?= base_url . 'warehouses/edit&id=' . $element->almacen_id; ?> title="
                                Editar"">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <span <?php if ($parent->parent_row == 0) { ?> class="action-delete"
                                    onclick="deleteWarehouse('<?= $element->almacen_id ?>')" <?php } else { ?>
                                    class="action-delete action-disable" <?php } ?> title="Eliminar">
                                <i class="fas fa-times"></i>
                            </span>
                        </td>
                    </tr>

                <?php }endwhile; ?>
        </tbody>

    </table>

</div>