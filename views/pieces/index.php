<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
        <h1>Piezas</h1>
        </div>
       

        <div class="float-right">
        <a href="<?= base_url?>src/excel/reporte-piezas.php" class="btn-custom btn-green">
            <i class="fas fa-file-excel"></i> 
            <p>Excel</p></a>
        <a href="<?=base_url?>pieces/add" class="btn-custom btn-default">
        <i class="fas fa-plus"></i>
            <p>Agregar pieza</p></a>
        </div>
    </div>
</div>

<div class="generalContainer">
    <table id="pieces" class="table-custom table">
        <thead>
            <tr>
                <th class="hide-cell">Código</th>
                <th>Nombre</th>
                <th class="hide-cell">Categoría</th>
                <th>Cantidad</th>
                <th class="hide-cell">P/Compra</th>
                <th>P/Unitario</th>
                <th>Acciones</th>
            </tr>
        </thead>

    </table>

</div>