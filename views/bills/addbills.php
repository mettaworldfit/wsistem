<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Nuevo gasto</h1>
        </div>

         <div class="float-right">
            <a href="#" class="btn-custom btn-default" data-toggle="modal" data-target="#addReason">
                <i class="fas fa-plus"></i>
                <p>Agregar motivo</p>
            </a>
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
                    <span>impuesto +</span>
                    <span>Total</span>
                </div>

                <div class="price-content" id="price">
                    <span><input type="text" class="invisible-input" value="" id="in-subtotal" disabled></span>
                    <span><input type="text" class="invisible-input" value="" id="in-tax" disabled></span>
                    <span><input type="text" class="invisible-input" value="" id="in-total" disabled></span>
                </div>
            </div>

        </div>

    </div> <!-- Row -->
    <br>

    <div class="button-container">
        <button class="btn-green btn-custom" type="button" data-toggle="modal" data-target="#save_sp" id="">
            <i class="far fa-save"></i>
            <p>Registrar</p>
        </button>
        <button class="btn-custom btn-default" type="button" data-toggle="modal" data-target="#add_spending" id="">
            <i class="fas fa-plus"></i>
            <p>Agregar gasto</p>
        </button>
        <button class="btn-custom btn-red" type="button" id="CancelBill">
            <i class="fas fa-window-close"></i>
            <p>Cancelar</p>
        </button>
    </div>
</div> <!-- generalConntainer -->



<!--Modal agregar detalle-->
<div class="modal fade" id="add_spending" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar gastos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); AddBills();">

                    <!-- Content -->
                    <div class="row col-sm-12">

                        <div class="form-group col-sm-9 ">
                            <label class="form-check-label" for="">Descripción</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="" id="reason" required>
                                    <option value="" disabled selected>Seleccionar motivo de gasto</option>
                                    <?php $reasons = Help::loadReasons();
                                    while ($reason = $reasons->fetch_object()): ?>
                                        <option value="<?= $reason->motivo_id ?>"><?= $reason->descripcion ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>


                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Valor</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <input type="text" class="form-custom-icon b-left" name="valor" id="g_value" required>
                            </div>
                        </div>



                    </div> <!-- Row -->

                    <div class="row col-sm-12 mt-1">

                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Cantidad</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="cantidad" id="g_quantity"
                                    required>
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Impuesto</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="" id="g_taxes">
                                    <option value="0" disabled selected>Buscar impuesto</option>
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
                        <button type="submit" class="btn-custom btn-green" id="g_add_item">
                            <i class="fas fa-plus"></i>
                            <p>Agregar</p>
                        </button>
                    </div>

                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>


<!--Modal guardar gasto-->
<div class="modal fade" id="save_sp" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Guardar gastos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); saveBills();">

                    <!-- Head -->

                    <div class="row col-sm-12 invoice-head-modal">

                        <div class="col-sm-6 head-content">
                            <h6>Total Pagado</h6>
                            <input type="text" class="invisible-input text-success" value="" id="cash-received"
                                disabled>
                        </div>

                        <div class="col-sm-6 head-content">
                            <h6>Monto a Pagar</h6>
                            <input type="text" class="invisible-input text-primary" value="" id="cash-topay" disabled>
                        </div>

                    </div>
                    <br>

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
                                        <option value="<?= $provider->proveedor_id ?>"><?= ucwords($provider->nombre_proveedor)." ".ucwords($provider->apellidos ?? '') ?></option>
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
                        <button type="submit" class="btn-custom btn-green" id="save_spend">
                            <i class="far fa-save"></i>
                            <p>Registrar</p>
                        </button>
                    </div>

                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>


<!-- Agregar motivo de gasto -->

<div class="modal fade" id="addReason" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar nuevo motivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="" onsubmit="event.preventDefault(); addReason();">

                    <div class="form-group col-sm-12">
                        <p class="title-info">
                            Crea un nuevo motivo de gasto
                        </p>
                    </div>

                    <div class="row col-md-12">

                        <div class="form-group col-sm-12">
                            <label for="motivo" class="form-check-label label-nomb">Motivo<span
                                    class="text-danger">*</span></label>
                            <input class="form-custom" type="text" name="motivo" id="newReason" required>
                        </div>


                    </div> <!-- Row col-md-12 -->


                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>

                        <button type="submit" href="#" class="btn-custom btn-green">
                            <i class="fas fa-plus"></i>
                            <p>Registrar</p>
                        </button>
                    </div>

                </form>

            </div> <!-- Body -->
        </div>
    </div>
</div>