<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Factura proveeedor</h1>
        </div>

    </div>
</div>


<div class="generalContainer padding-10 box-shadow-low">

    <table id="Detalle" class="table-custom table">
        <thead>
            <th>Descripción</th>
            <th>Cant</th>
            <th>Precio</th>
            <th>Impuestos</th>
            <th>Descuentos</th>
            <th>Observación</th>
            <th>Importe</th>
            <th></th>
        </thead>

        <tbody id="rows">

        </tbody>


    </table>
    <br>

    <!-- Información -->


    <div class="row col-sm-12">
        <div class="form-group col-sm-8">
            <textarea class="form-custom" name="" value="" id="observation" cols="30" rows="6" maxlength="150"
                placeholder="Observaciones"></textarea>
        </div>

        <!-- Precio total -->

        <div class="form-group col-sm-4">
            <div class="price-container">
                <div class="price-content bold">
                    <span>Subtotal</span>
                    <span>Descuento -</span>
                    <span>impuesto +</span>
                    <span>Total</span>
                </div>

                <div class="price-content" id="price">
                    <span><input type="text" class="invisible-input" value="" id="in-subtotal" disabled></span>
                    <span><input type="text" class="invisible-input" value="" id="in-discount" disabled></span>
                    <span><input type="text" class="invisible-input" value="" id="in-taxes" disabled></span>
                    <span><input type="text" class="invisible-input" value="" id="in-total" disabled></span>
                </div>
            </div>

        </div>

    </div> <!-- Row -->
    <br>

    <div class="button-container">
        <button class="btn-custom btn-blue" type="button" data-toggle="modal" data-target="#cash_invoice" id="">
            <i class="fas fa-dollar-sign"></i>
            <p>Facturar al contado</p>
        </button>
        <button class="btn-custom btn-green" type="button" data-toggle="modal" data-target="#credit_invoice" id="">
            <i class="fas fa-dollar-sign"></i>
            <p>Facturar a crédito</p>
        </button>
        <button class="btn-custom btn-default" type="button" data-toggle="modal" data-target="#add_order" id="">
            <i class="fas fa-search-plus"></i>
            <p>Agregar orden</p>
        </button>
        <button class="btn-custom btn-red" type="button" id="cancel_detail">
            <i class="fas fa-window-close"></i>
            <p>Cancelar</p>
        </button>
    </div>
</div> <!-- generalConntainer -->



<!--Modal agregar orden de compra-->
<div class="modal fade" id="add_order" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar orden de compra</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <!-- Content -->
                <div class="row col-sm-12">

                    <div class="form-group col-sm-6">
                        <label class="form-check-label" for="">Orden de compra</label>
                        <div class="input-div">
                            <div class="i b-right">
                                <i class="fas fa-list"></i>
                            </div>
                            <select class="form-custom-icon search" name="" id="order">
                                <option value="" disabled selected>Buscar orden</option>
                                <?php $orders = Help::SHOW_ORDERS();
                                while ($order = $orders->fetch_object()): ?>
                                    <option value="<?= $order->orden_id ?>">OC-00<?= $order->orden_id ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label class="form-check-label" for="">Fecha</label>
                        <div class="input-div">
                            <div class="i">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <input type="text" name="" class="form-custom-icon b-left" id="date_order" value=""
                                disabled>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label class="form-check-label" for="">Expiración</label>
                        <div class="input-div">
                            <div class="i">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <input type="text" name="" class="form-custom-icon b-left" id="expiration_order" value=""
                                disabled>
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Proveedor</label>
                        <div class="input-div">
                            <div class="i">
                                <i class="fas fa-portrait"></i>
                            </div>
                            <input class="form-custom-icon b-left" type="text" name="" value="" id="provider" disabled>
                            <input type="hidden" name="" value="" id="provider_id">
                        </div>
                    </div>

                </div> <!-- Row -->


                <div class="mt-4 modal-footer">
                    <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                        <i class="fas fa-window-close"></i>
                        <p>Salir</p>
                    </button>
                </div>


            </div> <!-- Body -->
        </div>
    </div>
</div>


<!--Modal Factura al contado-->
<div class="modal fade" id="cash_invoice" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Forma de Pago (Factura al contado)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- Head -->

                <div class="row col-sm-12 invoice-head-modal">

                    <div class="col-sm-4 head-content">
                        <h6>Total Pagado</h6>
                        <input type="text" class="invisible-input text-success" value="" id="cash-received" disabled>
                    </div>

                    <div class="col-sm-4 head-content">
                        <h6>Monto a Pagar</h6>
                        <input type="text" class="invisible-input text-primary" value="" id="cash-topay" disabled>
                    </div>

                    <div class="col-sm-4 head-content">
                        <h6>Monto Pendiente</h6>
                        <input type="text" class="invisible-input text-danger" value="" id="cash-pending" disabled>
                    </div>

                </div>
                <br>

                <!-- Content -->
                <div class="row col-sm-12">

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Proveedor</label>
                        <div class="input-div">
                            <div class="i">
                                <i class="fas fa-portrait"></i>
                            </div>
                            <input class="form-custom-icon b-left" type="text" name="" value="" id="provider_cash"
                                disabled>

                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Método</label>
                        <div class="input-div">
                            <div class="i b-right">
                                <i class="fas fa-list"></i>
                            </div>
                            <select class="form-custom-icon search " name="" id="cash-in-method">
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

                            <input type="date" name="" class="form-custom-icon" id="cash-in-date" value="<?php date_default_timezone_set('America/New_York');
                            ;
                            echo date('Y-m-d'); ?>">
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
                            <input class="form-custom-icon b-left" type="text" name=""
                                value="<?= $_SESSION['identity']->nombre ?>" id="cash-in-seller" disabled>
                        </div>
                    </div>

                </div> <!-- Row -->


                <div class="mt-4 modal-footer">
                    <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                        <i class="fas fa-window-close"></i>
                        <p>Salir</p>
                    </button>
                    <button type="button" class="btn-custom btn-blue" data-dismiss="modal"
                        id="cash-in-finish-or-receipt">
                        <i class="fas fa-receipt"></i>
                        <p>Imprimir y facturar</p>
                    </button>
                    <button type="button" class="btn-custom btn-green" id="cash-in-finish-or">
                        <i class="fas fa-dollar-sign"></i>
                        <p>Facturar</p>
                    </button>
                </div>
            </div> <!-- Body -->
        </div>
    </div>
</div>


<!-- Factura a crédito -->

<div class="modal fade" id="credit_invoice" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Forma de Pago (Factura a crédito)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- Head -->

                <div class="row col-sm-12 invoice-head-modal">

                    <div class="col-sm-4 head-content">
                        <h6>Total Pagado</h6>
                        <input type="text" class="invisible-input text-success" value="" id="credit-received" disabled>
                    </div>

                    <div class="col-sm-4 head-content">
                        <h6>Monto a Pagar</h6>
                        <input type="text" class="invisible-input text-primary" value="" id="credit-topay" disabled>
                    </div>

                    <div class="col-sm-4 head-content">
                        <h6>Monto Pendiente</h6>
                        <input type="text" class="invisible-input text-danger" value="" id="credit-pending" disabled>
                    </div>

                </div>
                <br>

                <!-- Content -->
                <div class="row col-sm-12">

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Proveedor</label>
                        <div class="input-div">
                            <div class="i">
                                <i class="fas fa-portrait"></i>
                            </div>
                            <input class="form-custom-icon b-left" type="text" name="" value="" id="provider_credit"
                                disabled>

                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Método</label>
                        <div class="input-div">
                            <div class="i b-right">
                                <i class="fas fa-list"></i>
                            </div>
                            <select class="form-custom-icon search " name="" id="credit-in-method">
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

                            <input type="date" name="" class="form-custom-icon" id="credit-in-date" value="<?php date_default_timezone_set('America/New_York');
                            ;
                            echo date('Y-m-d'); ?>">
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
                            <input class="form-custom-icon b-left" type="text" name=""
                                value="<?= $_SESSION['identity']->nombre ?>" id="credit-in-seller" disabled>
                        </div>
                    </div>

                    <div class="form-group col-sm-4 pay">
                        <label class="form-check-label" for="">Monto</label>
                        <div class="input-div">
                            <div class="i">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <input class="form-custom-icon b-left" type="number" name="" value="" id="credit-pay-or">
                        </div>
                    </div>


                </div> <!-- Row -->


                <div class="mt-4 modal-footer">
                    <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                        <i class="fas fa-window-close"></i>
                        <p>Salir</p>
                    </button>
                    <button type="button" class="btn-custom btn-blue" data-dismiss="modal"
                        id="credit-in-finish-or-receipt">
                        <i class="fas fa-receipt"></i>
                        <p>Imprimir y facturar</p>
                    </button>
                    <button type="button" class="btn-custom btn-green" id="credit-in-finish-or">
                        <i class="fas fa-dollar-sign"></i>
                        <p>Facturar</p>
                    </button>
                </div>
            </div> <!-- Body -->
        </div>
    </div>
</div>