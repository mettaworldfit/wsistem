<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Servicios</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>services/add" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Agregar servicio</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($element = $services->fetch_object()): ?>
                <?php $verify = Help::verify_parent_services($element->servicio_id); ?>
                <tr>
                    <td><?= $element->servicio_id ?></td>
                    <td><?= $element->nombre_servicio ?></td>
                    <td>

                        <a href="<?= base_url ?>services/edit&id=<?= $element->servicio_id ?>">
                            <span class="action-edit"><i class="fas fa-pencil-alt"></i></span>
                        </a>

                        <span <?php if ($verify == 'permitir') { ?> class="action-delete"
                                onclick="deleteService('<?= $element->servicio_id ?>')" <?php } else { ?>
                                class="action-delete action-disable" <?php } ?> title="Eliminar">
                            <i class="fas fa-times"></i>
                        </span>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>