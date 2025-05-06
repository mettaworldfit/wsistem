<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Valor de inventario</h1>
        </div>
        
    </div>
    <p class="title-info">Consulta el valor del inventario actual, la cantidad de productos y piezas inventariables que tienes y su costo promedio.</p>
</div>

<div class="generalContainer">
<div id="loader"></div>
    <table id="example" class="table-custom table" style="display: none;">
        <thead>
            <tr>
                <th>Código</th>
                <th>ítem</th>
                <th>Cantidad</th>
                <th class="hide-cell">Estado</th>
                <th>Costo promedio</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($element = $inventory->fetch_object()) : ?>
                <tr>
                    <td><?= $element->codigo ?></td>
                    <td><?= ucwords($element->nombre) ?></td>
                    
                    <?php if($element->cantidad > $element->cantidad_min){?>
                        <td class="text-success"><?= $element->cantidad ?></td>
                    <?php } else if($element->cantidad < 1) { ?>
                    <td class="text-danger"><?= $element->cantidad ?> </td>
                    <?php } else if($element->cantidad <= $element->cantidad_min) { ?>
                        <td class="text-warning"><?= $element->cantidad ?> </td>
                    <?php }; ?>
                    <td class="hide-cell"><?= $element->nombre_estado ?></td>
                    <td><?= number_format($element->precio_costo, 2) ?></td>
                    <td><?= number_format($element->cantidad * $element->precio_costo, 2) ?> </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

<div class="buttons clearfix">
    <div class="floatButtons">
        <div class="inventoryTable">
            <div>
                <span>Valor de inventario:</span>
                <p><?= $value ?></p>
            </div>

            <div>
                <span>Valor bruto:</span>
                <p><?= $bruto ?></p>
            </div>
        </div>
       
    </div>
</div>