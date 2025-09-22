<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Ficha del cliente</h1>
        </div>
    </div>
</div>

<?php $customer = Help::showCustomersID($_GET['id']);
while ($element = $customer->fetch_object()): ?>

    <div class="generalContainer-medium">

        <div class="col-legend">
            <h3><?= ucwords($element->nombre).' '.ucwords($element->apellidos ?? '') ?></h3>
        </div>
         <br>

        <div class="row col-md-12">
            <div class="form-group col-sm-4">
                <label for="Nombre" class="form-check-label label-nomb">Télefono 1</label>
               <input class="form-custom" type="number" value="<?= $element->telefono1 ?>" disabled>
            </div>

            <div class="form-group col-sm-4">
                <label for="Nombre" class="form-check-label label-nomb">Télefono 2</label>
               <input class="form-custom" type="number" value="<?= $element->telefono2 ?>" disabled>
            </div>

            <div class="form-group col-sm-4">
                <label for="Apellidos" class="form-check-label">Correo</label>
                <input class="form-custom" type="email" name="" value="<?= $element->email ?>" disabled>
            </div>

            <div class="form-group col-sm-6" id="cod_client">
                <label class="form-check-label" for="">RNC o Cédula</label>
                <input class="form-custom" type="text" name="" value="<?= $element->cedula ?>" id="" disabled>
            </div>

            <div class="form-group col-sm-6">
                <label class="form-check-label" for="">Dirección</label>
                <input class="form-custom" type="text" name="" value="<?= $element->direccion ?>" id="" disabled>
            </div>
        </div>
        <br>

        <div class="col-legend">
            <h3>Historial de compras</h3>
        </div>
         <br>

        <table id="customer_history" class="table-custom table">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Tipo Item</th>
                    <th>Item</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Descuento</th>
                    <th>Fecha</th>
                </tr>
            </thead>

        </table>
    </div>

<?php endwhile; ?>