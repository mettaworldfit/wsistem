<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Ofertas</h1>
        </div>


        <div class="float-right">
            <a class="btn-custom btn-default" href="<?= base_url ?>offers/add">
                <i class="fas fa-plus"></i>
                <p>Agregar oferta</p>
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
            <?php while ($element = $offers->fetch_object()): ?>
                <?php $parents = Help::verify_parent_offer($element->oferta_id);
                while ($parent = $parents->fetch_object()) { ?>

                    <tr>
                        <td><?= $element->oferta_id ?></td>
                        <td><?= ucwords($element->nombre_oferta) ?></td>
                        <td><?= $element->valor ?>%</td>
                        <td class="note-width"><?= $element->descripcion ?></td>
                        <td><?= $element->fecha ?></td>

                        <td>

                            <a <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?> class="action-edit"
                                    href="<?= base_url . 'offers/edit&id=' . $element->oferta_id; ?>" <?php } else { ?>
                                    class="action-edit action-disable" href="#" <?php } ?> title="Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <span <?php if ($parent->parent_row == 0 && $_SESSION['identity']->nombre_rol == 'administrador') { ?>
                                    class="action-delete" onclick="deleteOffer('<?= $element->oferta_id ?>')" <?php } else { ?>
                                    class="action-delete action-disable" <?php } ?> title="Eliminar">
                                <i class="fas fa-times"></i>
                            </span>
                        </td>
                    </tr>

                <?php }endwhile; ?>
        </tbody>

    </table>

</div>