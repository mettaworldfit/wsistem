<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Marcas</h1>
        </div>

        <div class="float-right">
            <a class="btn-custom btn-default" href="<?= base_url ?>brands/add">
                <i class="fas fa-plus"></i>
                <p>Agregar marca</p>
            </a>
        </div>

    </div>
</div>


<div class="generalContainer">
<table id="example" class="table-custom table">
        <thead>
            <tr>
                <th>Nombre marca</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>



        <tbody>
            <?php while ($element = $brands->fetch_object()): ?>
                <?php $parents = Help::verify_parent_brand($element->marca_id);
                while ($parent = $parents->fetch_object()) { ?>
                    <tr>
                        <td><?= ucwords($element->nombre_marca) ?></td>
                        <td><?= $element->fecha ?></td>
                        <td>

                            <a class="action-edit" href="<?= base_url . 'brands/edit&id=' . $element->marca_id; ?>"
                                title=" Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <span <?php if ($parent->parent_row == 0) { ?> class="action-delete"
                                    onclick="deleteBrand('<?= $element->marca_id ?>')" <?php } else { ?>
                                    class="action-delete action-disable" <?php } ?> title="Eliminar">
                                <i class="fas fa-times"></i>
                            </span>

                        </td>
                    </tr>
                <?php }
            endwhile; ?>
        </tbody>
    </table>

</div>