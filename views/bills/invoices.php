<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Facturas proveedores</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>bills/addinvoice" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Nueva factura</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="invoicesp" class="table-custom table">
        <thead>
            <tr>
                <th class="hide-cell">N°</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th class="hide-cell">Pagado</th>
                <th>Por pagar</th>
                <th class="hide-cell">Estado</th>

                <th>Acciones</th>
            </tr>
        </thead>

    </table>
</div>