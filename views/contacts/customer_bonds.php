<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Bonos de clientes</h1>
        </div>

    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th>N°</th>
                <th>Nombre</th>
                <th>Valor</th>
                <th class="hide-cell">Creado por</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php while ($element = $bonds->fetch_object()): ?>

                <tr>
                    <td><?= $element->bono_id ?></td>
                    <td class="note-width"><?= ucwords($element->nombre) ?> <?= ucwords($element->apellidos) ?></td>
                    <td><?= number_format($element->valor, 2) ?></td>
                    <td class="hide-cell"><?= ucwords($element->nombre_usuario) ?> <?= ucwords($element->apellidos_usuario) ?></td>
                    <td><?= $element->fecha ?></td>

                    <td>
                        <span class="action-delete" onclick="deleteBond('<?= $element->bono_id ?>')" title="Eliminar">
                            <i class="fas fa-times"></i>
                        </span>
                    </td>
                </tr>

            <?php endwhile; ?>
        </tbody>

    </table>

</div>