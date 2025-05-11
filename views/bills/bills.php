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
    <table id="bills" class="table-custom table">
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

    </table>
</div>