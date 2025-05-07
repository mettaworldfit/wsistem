<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Gastos</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>bills/addbills" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Nuevo gasto</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Proveedor</th>
                <th>Gastos</th>
                <th>Fecha</th>
                <th>Total</th>
                <th class="hide-cell">Pagado</th>
                <th class="hide-cell">Acciones</th>
            </tr>
        </thead>


        <tbody>
            <?php while ($element = $spendings->fetch_object()): ?>
                <tr>
                    <td>G-00<?= $element->gasto_id ?></td>
                    <td><?= ucwords($element->nombre_proveedor) ?>     <?= ucwords($element->p_apellidos) ?></td>
                    <td><?= Help::SHOW_SPENDINGS($element->orden_id) ?></td>
                    <td><?= $element->fecha_gasto ?></td>
                    <td class="text-primary"><?= number_format($element->total, 2) ?></td>
                    <td class="text-success hide-cell"><?= number_format($element->pagado, 2) ?></td>
                    <td class="hide-cell">
                        <span style="font-size: 16px;" onclick="deleteSpending('<?= $element->orden_id ?>')"
                            class="action-delete"><i class="fas fa-times"></i></span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>