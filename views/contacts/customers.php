<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1> Clientes</h1>
        </div>


        <div class="float-right">
            <a class="btn-custom btn-default" href="<?= base_url ?>contacts/add">
                <i class="fas fa-plus"></i>
                <p>Agregar contacto</p>
            </a>
        </div>
    </div>
</div>


<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th>N°</th>
                <th>Nombre/Razón social</th>
                <th class="hide-cell">RNC o Cédula</th>
                <th>Télefono 1</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>



        <tbody>
            <?php while ($element = $customers->fetch_object()): ?>
                    <tr>
                        <td><?= $element->cliente_id ?></td>
                        <td class="note-width"><?= ucwords($element->nombre) ?>         <?= ucwords($element->apellidos) ?></td>
                        <td class="hide-cell"><?= $element->cedula ?></td>
                        <td><?= $element->telefono1 ?></td>
                        <td><?= $element->fecha ?></td>
                        <td>

                            <a class="action-edit" href="<?= base_url . 'contacts/edit_customer&id=' . $element->cliente_id; ?>"
                                title=" Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <span class="action-delete" onclick="deleteCustomer('<?= $element->cliente_id ?>')" title="Eliminar">
                                <i class="fas fa-times"></i>
                            </span>

                        </td>
                    </tr>
                <?php endwhile; ?>
        </tbody>
    </table>

</div>