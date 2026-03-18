<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Valor de inventario</h1>
        </div>
        
    </div>
    <p class="title-info">Consulta el valor del inventario actual, la cantidad de productos y piezas inventariables que tienes y su costo promedio.</p>
</div>

<div class="generalContainer">
    <table id="inventory" class="table-custom table">
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