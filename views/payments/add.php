<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Registrar pagos</h1>
        </div>

    </div>
</div>


<div class="generalContainer padding-10 box-shadow-low">

    <div class="row col-sm-12 invoice-head-modal">

        <div class="col-sm-4 head-content">
            <h6>Total Pagado</h6>
            <input type="text" class="invisible-input text-success" value="" id="received" disabled>
        </div>

        <div class="col-sm-4 head-content">
            <h6>Monto a Pagar</h6>
            <input type="text" class="invisible-input text-primary" value="" id="topay" disabled>
        </div>

        <div class="col-sm-4 head-content">
            <h6>Monto Pendiente</h6>
            <input type="text" class="invisible-input text-danger" value="" id="pending" disabled>
        </div>

    </div>
    <br>

    <!-- Content -->
    <div class="row col-sm-12 ml-1">


        <div class="col-sm-12 row">

            <div class="radio-list">
                <div class="radio-item ">
                    <input type="radio" name="tipo" value="venta" id="radio1" checked>
                    <label for="radio1">Venta</label>
                </div>

                <div class="radio-item ml-2">
                    <input type="radio" name="tipo" value="reparacion" id="radio2">
                    <label for="radio2">Reparación</label>
                </div>

            </div>


        </div>

    </div>

    <div class="row col-sm-12 mb-1">

        <div class="form-group col-sm-12 repair">
            <label class="form-check-label" for="">Facturas reparaciones</label>
            <div class="input-div">
                <div class="i b-right">
                    <i class="fas fa-file-alt"></i>
                </div>
                <select class="form-custom-icon search " name="" id="invoiceRP">
                    <option value="" selected disabled>Seleccione la factura a la que desea reliazar el pago</option>
                    <?php $invoices = Help::INVOICE_PENDING_RP();
                    while ($element = $invoices->fetch_object()): ?>
                        <option value="<?= $element->facturaRP_id ?>"> RP-00<?= $element->facturaRP_id ?>
                            <?= $element->nombre ?>     <?= $element->apellidos ?>     <?= $element->fecha ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-group col-sm-12 sale">
            <label class="form-check-label" for="">Facturas ventas</label>
            <div class="input-div">
                <div class="i b-right">
                    <i class="fas fa-file-alt"></i>
                </div>
                <select class="form-custom-icon search " name="" id="invoice">
                    <option value="" selected disabled>Seleccione la factura a la que desea reliazar el pago</option>
                    <?php $invoices = Help::INVOICE_PENDING();
                    while ($element = $invoices->fetch_object()): ?>
                        <option value="<?= $element->factura_venta_id ?>"> FT-00<?= $element->factura_venta_id ?>
                            <?= $element->nombre ?>     <?= $element->apellidos ?>     <?= $element->fecha ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

    </div>



    <div class="row col-sm-12">

        <div class="form-group col-sm-4">
            <label class="form-check-label" for="">Cliente</label>
            <div class="input-div">
                <div class="i b-right">
                    <i class="fas fa-portrait"></i>
                </div>
                <input class="form-custom-icon b-left" type="text" name="" value="" id="customer" disabled>
                <input type="hidden" name="" value="" id="customer_id">
            </div>
        </div>

        <div class="form-group col-sm-4">
            <label class="form-check-label" for="">Método</label>
            <div class="input-div">
                <div class="i b-right">
                    <i class="fas fa-list"></i>
                </div>
                <select class="form-custom-icon search " name="" id="method">
                    <?php $methods = Help::showPaymentMethod();
                    while ($method = $methods->fetch_object()): ?>
                        <option value="<?= $method->metodo_pago_id ?>"><?= $method->nombre_metodo ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-group col-sm-4">
            <label class="form-check-label" for="">Fecha</label>
            <div class="input-div">

                <input type="date" name="" class="form-custom-icon" id="date" value="<?php date_default_timezone_set('America/Los_Angeles');
                echo date('Y-m-d'); ?>" <?php if ($_SESSION['identity']->nombre_rol != 'administrador') { ?> disabled
                    <?php } ?>>
            </div>
        </div>


    </div> <!-- Row -->

    <div class="row col-sm-12 mt-1">

        <div class="form-group col-sm-4">
            <label class="form-check-label" for="">Vendedor</label>
            <div class="input-div">
                <div class="i">
                    <i class="fas fa-user-tie"></i>
                </div>
                <input class="form-custom-icon b-left" type="text" name="" value="<?= $_SESSION['identity']->nombre ?>"
                    id="seller" disabled>
            </div>
        </div>

        <div class="form-group col-sm-4 pay">
            <label class="form-check-label" for="">Monto</label>
            <div class="input-div">
                <div class="i">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <input class="form-custom-icon b-left" type="number" name="" value="" id="pay">
            </div>
        </div>

    </div> <!-- Row -->

    <div class="row col-sm-12 mt-1">

        <div class="form-group col-sm-8">
            <label class="form-check-label" for="">Observación</label>
            <textarea class="form-custom" name="" value="" id="observation" cols="30" rows="6" maxlength="150"
                placeholder=""></textarea>
        </div>
    </div>


    <br>
    <div class="button-container">
        <button type="button" class="btn-custom btn-green" id="add_payment">
            <i class="fas fa-dollar-sign"></i>
            <p>Facturar</p>
        </button>
        <button type="button" class="btn-custom btn-blue" data-dismiss="modal" id="add_payment_print"><i
                class="fas fa-receipt"></i>
            <p>Imprimir y facturar</p>
        </button>
        <a href="<?= base_url ?>payments/index" class="btn-custom btn-red ml-2">
            <i class="fas fa-window-close"></i>
            <p>Cancelar</p>
        </a>
    </div>


</div> <!-- generalConntainer -->