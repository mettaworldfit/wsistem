<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1><i class="fas fa-user-circle"></i> Usuarios</h1>
        </div>


        <div class="float-right">
            <a class="btn-custom btn-default" href="<?= base_url ?>users/add">
                <i class="fas fa-plus"></i>
                <p>Agregar usuario</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
<table id="example" class="table-custom table">
        <thead>
            <tr>
                <th class="hide-cell">N°</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Creación</th>
                <th></th>
            </tr>
        </thead>



        <tbody>
            <?php while ($element = $users->fetch_object()): ?>

                    <tr>
                        <td class="hide-cell"><?= $element->usuario_id ?></td>
                        <td><?= ucwords($element->nombre) ?>         <?= ucwords($element->apellidos) ?></td>
                        <td><?= $element->nombre_rol ?></td>
                        <td>
                            <p class="<?= $element->nombre_estado ?>"><?= $element->nombre_estado ?></p>
                        </td>
                        <td><?= $element->fecha ?></td>

                        <td>

                            <a class="action-edit" href="<?= base_url . 'users/edit&id=' . $element->usuario_id; ?>"
                                title="Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <span
                                class="<?php if ($element->nombre_estado == 'Activo') { ?> action-active  <?php } else { ?> action-delete <?php } ?>"
                                <?php if ($element->nombre_estado == 'Activo') { ?>
                                    onclick="disableUser('<?= $element->usuario_id ?>')" <?php } else { ?>
                                    onclick="enableUser('<?= $element->usuario_id ?>')" <?php } ?>         <?php if ($element->nombre_estado == 'Activo') { ?> title="Desactivar" <?php } else { ?> title="Activar"
                                <?php } ?> id="">
                                <i class="fas fa-lightbulb"></i>
                            </span>

                            <span class="action-delete" onclick="deleteUser('<?= $element->usuario_id ?>')" title="Eliminar">
                                <i class="fas fa-times"></i>
                            </span>
                        </td>
                    </tr>

                <?php endwhile; ?>
        </tbody>

    </table>

</div>