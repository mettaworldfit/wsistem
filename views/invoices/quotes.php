<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Cotizaciones</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>invoices/quote" class="btn-custom btn-default">
            <i class="fas fa-plus"></i>
            <p>Nueva cotización</p></a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>


        <tbody>
            <?php while ($element = $quotes->fetch_object()) : ?>
                <tr>
                    <td>CT-00<?= $element->cotizacion_id ?></td>
                    <td><?= ucwords($element->nombre) ?> <?= ucwords($element->apellidos) ?></td>
                    <td><?= $element->fecha ?></td>
                    <td class="text-primary"><?= number_format($element->total, 2) ?></td>

                    <td>

                        <a class="action-edit" href="<?php echo base_url.'invoices/edit_quote&id='.$element->cotizacion_id; ?>"  title="editar"> 
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <span onclick="deleteQuote('<?= $element->cotizacion_id ?>')" class="action-delete"><i class="fas fa-times"></i></span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>
