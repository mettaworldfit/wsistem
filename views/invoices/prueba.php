<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Facturas de ventas</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>invoices/addpurchase" class="btn-custom btn-default">
            <i class="fas fa-plus"></i>
            <p>Nueva factura</p></a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="facturas" class="table-custom table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th class="hide-cell">Total</th>
                <th class="hide-cell">Cobrado</th>
                <th class="hide-cell">Por cobrar</th>
                <th class="hide-cell">-Bono</th>
                <th>Estado</th>

                <th>Acciones</th>
            </tr>
        </thead>

    </table>
</div>
