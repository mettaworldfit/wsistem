<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Proveedores</h1>
        </div>


        <div class="float-right">
            <a class="btn-custom btn-default" href="<?= base_url ?>contacts/add">
                <i class="fas fa-plus"></i>
                <p>Nuevo contacto</p>
            </a>
        </div>
    </div>
</div>


<div class="generalContainer">
<div id="loader"></div>
    <table id="example" class="table-custom table">
        <thead>
            <tr>
                <th class="hide-cell">N°</th>
                <th>Nombre/Razón social</th>
                <th class="hide-cell">Dirección</th>
                <th class="hide-cell">Correo</th>
                <th>Télefono 1</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($element = $providers->fetch_object()): ?>
              
                    <tr>
                        <td class="hide-cell"><?= $element->proveedor_id ?></td>
                        <td class="note-width"><?= $element->nombre_proveedor ?>         <?= $element->apellidos ?></td>
                        <td class="note-width hide-cell"><?= $element->direccion ?></td>
                        <td class="hide-cell"><?= $element->email ?></td>
                        <td><?= $element->telefono1 ?></td>
                        <td><?= $element->fecha ?></td>
                        <td>

                            <a class="action-edit"
                                href="<?= base_url . 'contacts/edit_provider&id=' . $element->proveedor_id; ?>" title=" Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <span class="action-delete" onclick="deleteProveedor('<?= $element->proveedor_id ?>')" title="Eliminar">
                                <i class="fas fa-times"></i>
                            </span>

                        </td>
                    </tr>
                <?php endwhile; ?>
        </tbody>
    </table>

</div>