<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Pagos</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>payments/add" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Nuevo pago</p>
            </a>
        </div>
    </div>
</div>

<div class="generalContainer">
    <table id="payments" class="table-custom table">
        <thead>
            <tr>
                <th class="hide-cell">N°</th>
                <th>Documento</th>
                <th>Cliente</th>
                <th>Recibido</th>
                <th class="hide-cell">Observación</th>
                <th class="hide-cell">Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>

    </table>

</div>