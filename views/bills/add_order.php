<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Orden de compra</h1>
        </div>

    </div>
</div>


<div class="generalContainer padding-10 box-shadow-low">

    <table id="Detalle" class="table-custom table">
        <thead>
            <th>Descripción</th>
            <th>Cant</th>
            <th>Precio</th>
            <th class="hide-cell">Impuestos</th>
            <th>Descuentos</th>
            <th class="hide-cell">Observación</th>
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
                    <span><input type="text" class="invisible-input" value="" id="in-tax" disabled></span>
                    <span><input type="text" class="invisible-input" value="" id="in-total" disabled></span>
                </div>
            </div>

        </div>

    </div> <!-- Row -->
    <br>

    <div class="button-container">
        <button class="btn-custom btn-blue" type="button" data-toggle="modal" data-target="#save_detail"
            id="save_order">
            <i class="far fa-save"></i>
            <p>Guardar</p>
        </button>
        <button class="btn-custom btn-default" type="button" data-toggle="modal" data-target="#add_detail" id="">
            <i class="fas fa-plus"></i>
            <p>Agregar detalle</p>
        </button>
        <button class="btn-custom btn-red" type="button" id="cancel_detail">
            <i class="fas fa-window-close"></i>
            <p>Cancelar</p>
        </button>
    </div>

</div> <!-- generalConntainer -->



<!--Modal agregar detalle-->
<div class="modal fade" id="add_detail" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar detalle de orden</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); add_detail_order();">


                    <div class="row col-sm-12">
                        <div class="col-sm-12 row">

                            <div class="radio-list">
                                <div class="radio-item ">
                                    <input type="radio" name="tipo" value="producto" id="radio1" checked>
                                    <label for="radio1">Productos</label>
                                </div>

                                <div class="radio-item ml-2">
                                    <input type="radio" name="tipo" value="pieza" id="radio2">
                                    <label for="radio2">Piezas</label>
                                </div>

                            </div>

                        </div>
                    </div>


                    <!-- Content -->
                    <div class="row col-sm-12">

                        <div class="form-group col-sm-6 or_piece">
                            <label class="form-check-label" for="">Piezas</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="pieza" id="piece" required>
                                    <option value="" disabled selected>Buscar piezas</option>
                                    <?php $pieces = Help::showPieces();
                                    while ($piece = $pieces->fetch_object()): ?>
                                        <option value="<?= $piece->pieza_id ?>"><?= $piece->nombre_pieza ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-6 product">
                            <label class="form-check-label" for="">Productos</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="far fa-clipboard"></i>
                                </div>
                                <select class="form-custom-icon search" name="producto" id="product" required>
                                    <option value="" disabled selected>Buscar productos</option>
                                    <?php $products = Help::showProducts();
                                    while ($product = $products->fetch_object()): ?>
                                        <option value="<?= $product->IDproducto ?>"><?= $product->nombre_producto ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Cantidad</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="cantidad" step="0.01" min="0" max="999.99" id="or_quantity"
                                    required>
                            </div>
                        </div>


                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Precio</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <input type="text" class="form-custom-icon b-left" name="precio" id="or_price_out"
                                    required>
                            </div>
                        </div>



                    </div> <!-- Row -->

                    <div class="row col-sm-12 mt-1">

                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Descuento</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-level-down-alt"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="descuento" id="or_discount">
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Impuesto</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="producto" id="or_taxes">
                                    <option value="0" disabled selected>Buscar impuesto</option>
                                    <option value="0">Nínguno</option>
                                    <?php $taxes = Help::showTaxes();
                                    while ($tax = $taxes->fetch_object()): ?>
                                        <option value="<?= $tax->valor ?>"><?= $tax->nombre_impuesto ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-5">
                            <label class="form-check-label" for="">Observación</label>
                            <textarea class="form-custom" name="" value="" id="observation_item" cols="10" rows="2"
                                maxlength="50" placeholder=""></textarea>
                        </div>

                    </div> <!-- Row -->


                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>
                        <button type="submit" class="btn-custom btn-green" id="or_add_item">
                            <i class="fas fa-plus"></i>
                            <p>Agregar</p>
                        </button>
                    </div>

                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>


<!--Modal guardar orden-->
<div class="modal fade" id="save_detail" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Guardar orden de compra</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); saveOrder();">

                    <!-- Content -->
                    <div class="row col-sm-12">

                        <div class="form-group col-sm-6">
                            <label class="form-check-label" for="">Proveedores</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="" id="provider" required>
                                    <option value="" selected disabled>Buscar proveedores</option>
                                    <?php $providers = Help::showProviders();
                                    while ($provider = $providers->fetch_object()): ?>
                                        <option value="<?= $provider->proveedor_id ?>">
                                            <?= ucwords($provider->nombre_proveedor) ?> <?= ucwords($provider->apellidos ?? '') ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Fecha</label>
                            <div class="input-div">
                                <input type="date" name="" class="form-custom-icon" id="date" value="<?php date_default_timezone_set('America/New_York');
                                ;
                                echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Expiración</label>
                            <div class="input-div">
                                <input type="date" name="" class="form-custom-icon" id="expiration" value="">
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Registrado por</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name=""
                                    value="<?= $_SESSION['identity']->nombre ?>" id="emisor" disabled>
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Origen</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="" id="origin" required>
                                    <option value="" selected disabled>Elegir origen</option>
                                    <option value="caja">Gasto de caja</option>
                                    <option value="fuera_caja">Gasto fuera de caja</option>
                                </select>
                            </div>
                        </div>

                    </div> <!-- Row -->


                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>
                        <button type="submit" class="btn-custom btn-green" id="or_save_order">
                            <i class="far fa-save"></i>
                            <p>Guardar</p>
                        </button>
                    </div>

                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>