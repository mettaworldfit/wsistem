<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Pagos</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>bills/add_payment" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Nuevo pago</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="payments_providers" class="table-custom table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Documento</th>
                <th class="hide-cell">Proveedor</th>
                <th>Monto</th>
                <th class="hide-cell">Observación</th>
                <th>Fecha</th>
                <th class="hide-cell">Acciones</th>
            </tr>
        </thead>

    </table>

</div>