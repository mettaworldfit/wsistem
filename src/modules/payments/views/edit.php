
 <input type="hidden" name="number" value="<?= $_GET['id']?>" id="invoice_id">

<div class="generalContainer padding-10 box-shadow-low">
    <div class="row col-sm-12 mb-2">

        <div class="form-group col-sm-3">
            <div class="input-div">
                <div class="i">
                    <i class="fas fa-barcode"></i>
                </div>
                <input class="form-custom-icon b-left" type="text" name="" id="product_code" placeholder="Código">
            </div>
        </div>

        <div class="form-group col-sm-9">
            <div class="input-div">
                <div class="i b-right">
                    <i class="fas fa-search"></i>
                </div>
                <select class="form-custom-icon search" name="" id="product">
                    <option value="" disabled selected>Buscar producto o servicio</option>
                    <?php $products = Help::showProducts();
                    while ($product = $products->fetch_object()) : ?>
                        <option value="<?= $product->IDproducto ?>"><?= $product->nombre_producto ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

    </div> <!-- Row -->


    <div class="row col-sm-12">

        <div class="form-group col-sm-2">
            <div class="input-div">
                <div class="i">
                    <i class="fas fa-cart-arrow-down"></i>
                </div>
                <input class="form-custom-icon b-left" type="number" name="" id="quantity" placeholder="Cantidad" disabled>
            </div>
        </div>

        <div class="form-group col-sm-2">
            <div class="input-div">
                <div class="i">
                    <i class="fas fa-boxes"></i>
                </div>
                <input class="form-custom-icon b-left" type="text" name="" id="stock" value="" placeholder="Stock" disabled>
            </div>
        </div>

        <div class="form-group col-sm-2">
            <div class="input-div">
                <div class="i">
                    <i class="fas fa-minus"></i>
                </div>
                <input class="form-custom-icon b-left" type="number" name="" id="discount" value="" placeholder="Descuento" disabled>
            </div>
        </div>

        <div class="form-group col-sm-2">
            <div class="input-div">
                <div class="i">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <input class="form-custom-icon b-left" type="text" name="" id="price_out" placeholder="Precio" disabled>
                <input type="hidden" name="" id="total_price">
                <input type="hidden" name="" id="taxes">
            </div>
        </div>

        <div class="form-group col-sm-2">
            <div class="input-div">
                <div class="i b-right">
                    <i class="fas fa-list"></i>
                </div>
                <select class="form-custom-icon search" name="" id="list_id">
                    <option value="0" selected>General</option>

                </select>
            </div>
        </div>

        <input class="btn btn-sm btn-primary" type="button" value="Agregar" id="add_item">

    </div> <!-- Row -->

    <br>
    <table id="Detalle" class="DetalleTemp">
        <thead>
            <th>Descripción</th>
            <th>Cant</th>
            <th>Precio</th>
            <th>Impuesto</th>
            <th>Descuento</th>
            <th>Importe</th>
            <th></th>
        </thead>

        <?php while ($element = $detalle_factura->fetch_object()) : ?>

            <tbody id="rows">
                <tr>
                    <td><?= $element->nombre_producto ?></td>
                    <td><?= $element->cantidad_total ?></td>
                    <td><?= number_format($element->precio, 2) ?></td>
                    <td><?= number_format($element->impuesto, 2) ?> - (<?= $element->valor ?>%)</td>
                    <td><?= number_format($element->descuento, 2) ?></td>
                    <td><?= number_format(($element->cantidad_total * $element->precio) + $element->impuesto - $element->descuento, 2) ?></td>
                    <td>
                        <a class="text-danger" onclick="deleteDetail('<?= $element->detalle_venta_id ?>')"><i class="far fa-trash-alt"></i></a>
                    </td>
                </tr>
            </tbody>

        <?php endwhile; ?>

    </table>
    <br>

    <!-- Información -->


    <div class="row col-sm-12">
        <div class="form-group col-sm-8">
            <textarea class="form-custom" name="" value="" id="observation" cols="30" rows="6" maxlength="150" placeholder="Observaciones"></textarea>
        </div>

        <!-- Precio total -->

        <div class="form-group col-sm-4">
            <div class="price-container">
                <div class="price-content bold">
                    <span>Subtotal</span>
                    <span>Descuento -</span>
                    <span>Impuestos +</span>
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

    <button class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#cash_invoice" id=""><i class="far fa-money-bill-alt"></i> Guardar al contado</button>
    <button class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#credit_invoice" id=""><i class="far fa-money-bill-alt"></i> Guardar a crédito</button>
    <button class="btn btn-sm btn-danger" type="button" id="cancel_detail"><i class="fas fa-window-close"></i> Cancelar</button>


</div> <!-- generalConntainer -->



<!--Modal Factura al contado-->
<div class="modal fade" id="cash_invoice" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                        <label class="form-check-label" for="">Cliente</label>
                        <div class="input-div">
                            <div class="i b-right">
                                <i class="fas fa-portrait"></i>
                            </div>
                            <select class="form-custom-icon search" name="" id="cash-in-customer" requireds>
                                <?php $customers = Help::showCustomers();
                                while ($customer = $customers->fetch_object()) : ?>
                                    <option value="<?= $customer->cliente_id ?>"><?= $customer->nombre ?> <?= $customer->apellidos ?></option>
                                <?php endwhile; ?>
                            </select>
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
                                while ($method = $methods->fetch_object()) : ?>
                                    <option value="<?= $method->metodo_pago_id ?>"><?= $method->nombre_metodo ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Fecha</label>
                        <div class="input-div">

                            <input type="date" name="" class="form-custom-icon" id="cash-in-date" value="<?php date_default_timezone_set('America/Los_Angeles');
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
                            <input class="form-custom-icon b-left" type="text" name="" value="<?= $_SESSION['identity']->nombre ?>" id="cash-in-seller" disabled>
                        </div>
                    </div>

                </div> <!-- Row -->


                <div class="mt-4 modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id=""><i class="fas fa-window-close"></i> Cancelar</button>
                    <button type="button" class="btn btn-sm btn-success" id="cash-in-update"><i class="far fa-money-bill-alt"></i> Guardar</button>
                </div>
            </div> <!-- Body -->
        </div>
    </div>
</div>


<!-- Factura a crédito -->

<div class="modal fade" id="credit_invoice" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                        <label class="form-check-label" for="">Cliente</label>
                        <div class="input-div">
                            <div class="i b-right">
                                <i class="fas fa-portrait"></i>
                            </div>
                            <select class="form-custom-icon search" name="" id="credit-in-customer" requireds>
                                <?php $customers = Help::showCustomers();
                                while ($customer = $customers->fetch_object()) : ?>
                                    <option value="<?= $customer->cliente_id ?>"><?= $customer->nombre ?> <?= $customer->apellidos ?></option>
                                <?php endwhile; ?>
                            </select>
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
                                while ($method = $methods->fetch_object()) : ?>
                                    <option value="<?= $method->metodo_pago_id ?>"><?= $method->nombre_metodo ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Fecha</label>
                        <div class="input-div">

                            <input type="date" name="" class="form-custom-icon" id="credit-in-date" value="<?php date_default_timezone_set('America/Los_Angeles');
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
                            <input class="form-custom-icon b-left" type="text" name="" value="<?= $_SESSION['identity']->nombre ?>" id="credit-in-seller" disabled>
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Monto</label>
                        <div class="input-div">
                            <div class="i">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <input class="form-custom-icon b-left" type="number" name="" value="" id="credit-pay">
                        </div>
                    </div>


                </div> <!-- Row -->


                <div class="mt-4 modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id=""><i class="fas fa-window-close"></i> Cancelar</button>
                    <button type="button" class="btn btn-sm btn-success" id="credit-in-update"><i class="far fa-money-bill-alt"></i> Guardar</button>
                </div>
            </div> <!-- Body -->
        </div>
    </div>
</div>