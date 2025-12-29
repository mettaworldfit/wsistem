<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Detalle orden #OR-00<?= $_GET['id'] ?></h1>
        </div>

        <div class="float-right">
            <a href="#" class="btn-custom btn-default" data-toggle="modal" data-target="#create_customer">
                <i class="fas fa-plus"></i>
                <p>Agregar cliente</p>
            </a>
        </div>
    </div>
</div>

<div class="generalContainer padding-10 box-shadow-low">

    <input type="hidden" name="" value="<?= $_GET['id'] ?>" id="orden_id">
    <table id="addrepair" class="table-custom table">
        <thead>
            <th>Descripción</th>
            <th>Cant</th>
            <th>Precio</th>
            <th>Descuento</th>
            <th>Importe</th>
            <th></th>
        </thead>

    </table>
    <br>

    <!-- Información -->


    <div class="row col-sm-12">
        <div class="form-group col-sm-8">
            <textarea class="form-custom" name="observation" id="comment" cols="30" rows="6" maxlength="150"
                placeholder="Observaciones"><?= isset($note) ? htmlspecialchars($note) : ''; ?>
            </textarea>
        </div>

        <!-- Precio total -->

        <div class="form-group col-sm-4">
            <div class="price-container">
                <div class="price-content bold">
                    <span>Subtotal</span>
                    <span>Descuento -</span>
                    <span>Total</span>
                </div>

                <div class="price-content" id="price">
                    <span><input type="text" class="invisible-input" value="" id="in-subtotal" disabled></span>
                    <span><input type="text" class="invisible-input" value="" id="in-discount" disabled></span>
                    <span><input type="text" class="invisible-input" value="" id="in-total" disabled></span>
                </div>
            </div>

        </div>

    </div> <!-- Row -->
    <br>

    <?php if ($is_exists == 0): ?>
        <div class="button-container" id="buttons">
            <button class="btn-custom btn-green" type="button" data-toggle="modal" data-target="#cash_invoice" id="">
                <i class="fas fa-dollar-sign"></i>
                <p>Facturar al contado</p>
            </button>

            <button class="btn-custom btn-blue" type="button" data-toggle="modal" data-target="#credit_invoice" id="">
                <i class="fas fa-dollar-sign"></i>
                <p>Facturar a crédito</p>
            </button>

            <button class="btn-custom btn-default" type="button" data-toggle="modal" data-target="#add_detail" id="">
                <i class="fas fa-plus"></i>
                <p>Agregar detalle</p>
            </button>


            <button class="btn-custom btn-orange" type="button" id="printer_order">
                <i class="fas fa-receipt"></i>
                <p>Imprimir ticket</p>
            </button>

            <button class="btn-custom btn-red" type="button" id="exportOrderToPDF">
                <i class="fas fa-file-pdf"></i>
                <p>Exportar PDF</p>
            </button>

            <input type="hidden" name="" value='<?= $orderDetail ?>' id="detail_order">
            <input type="hidden" name="" value='<?= $deviceInfo ?>' id="device_info">
            <input type="hidden" name="" value='<?= $conditions ?>' id="conditions">
        </div>
    <?php endif; ?>

</div> <!-- generalConntainer -->


<!-- Modal: Agregar detalle de facturación -->
<div class="modal fade" id="add_detail" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar detalle de facturación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); addDetailOrdenRepair();">

                    <!-- Selección de tipo (pieza o servicio) y vista de total -->
                    <div class="grid-tab-detail">
                        <!-- Radios para elegir tipo -->
                        <div class="tab-detail">
                            <div class="radio-list">
                                <div class="radio-item ml-2">
                                    <input type="radio" name="tipo" value="pieza" id="radio2" checked>
                                    <label for="radio2">Piezas</label>
                                </div>
                                <div class="radio-item ml-2">
                                    <input type="radio" name="tipo" value="servicio" id="radio3">
                                    <label for="radio3">Servicios</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-product_info">
                        <!-- Información principal -->
                        <div class="modal-legend col-sm-8">
                            <p>Información</p>
                        </div>

                        <div class="row col-sm-9">

                            <!-- Código de pieza -->
                            <div class="form-group col-sm-4 piece">
                                <label for="piece_code">Código</label>
                                <div class="input-div">
                                    <div class="i"><i class="fas fa-barcode"></i></div>
                                    <input class="form-custom-icon b-left" type="text" id="piece_code" name="codigo">
                                </div>
                            </div>

                            <!-- Selección de pieza -->
                            <div class="form-group col-sm-8 piece">
                                <label for="piece">Piezas</label>
                                <div class="input-div">
                                    <div class="i b-right"><i class="fas fa-list"></i></div>
                                    <select class="form-custom-icon search" id="piece" name="pieza">
                                        <option value="" disabled selected>Buscar piezas</option>
                                        <?php $pieces = Help::showPieces();
                                        while ($piece = $pieces->fetch_object()): ?>
                                            <option value="<?= $piece->pieza_id ?>" data-price="<?= $piece->precio_unitario; ?>" data-discount="<?= $piece->valor; ?>"><?= ucwords($piece->nombre_pieza) ?> </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <!-- Campos ocultos -->
                                    <input type="hidden" id="piece_id">
                                    <input type="hidden" id="piece_cost">
                                </div>
                            </div>

                            <!-- Selección de servicio -->
                            <div class="form-group col-sm-12 service">
                                <label for="service">Servicios</label>
                                <div class="input-div">
                                    <div class="i"><i class="far fa-clipboard"></i></div>
                                    <select class="form-custom-icon search" id="service" name="servicio">
                                        <option value="" disabled selected>Buscar servicios</option>
                                        <?php $services = Help::showServices();
                                        while ($service = $services->fetch_object()): ?>
                                            <option value="<?= $service->servicio_id ?>" data-price="<?= $service->precio ?>"><?= ucwords($service->nombre_servicio) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Stock disponible (solo piezas) -->
                            <input type="hidden" name="" id="stock">
                        </div>

                        <div class="content-thumb">
                            <div class="item-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags-icon lucide-tags">
                                    <path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z" />
                                    <path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193" />
                                    <circle cx="10.5" cy="6.5" r=".5" fill="currentColor" />
                                </svg>
                                <span id="">0 inv</span>
                            </div>

                            <div class="item-price">
                                <span>DOP
                                    <span id="totalPriceProduct"></span>
                                    <span id="totalPricePiece"></span>
                                    <span id="totalPriceService"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Listas de precios -->
                    <div class="row">
                        <div class="col-sm-12 piece">
                            <div class="modal-legend">
                                <p>Listas de precios</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="form-group col-sm-3 ml-3 piece">
                            <label for="piece_list_id">Lista de precios</label>
                            <div class="input-div">
                                <div class="i b-right"><i class="fas fa-list"></i></div>
                                <select class="form-custom-icon search" id="piece_list_id" name="list">
                                    <option value="0" selected>General</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Información de detalle -->
                    <div class="modal-legend">
                        <p>Información de detalle</p>
                    </div>

                    <div class="row col-sm-12 mt-1">

                        <!-- Cantidad piezas -->
                        <div class="form-group col-sm-3 piece">
                            <label for="quantity">Cantidad</label>
                            <div class="input-div verify-quantity">
                                <div class="i"><i class="fas fa-box-open"></i></div>
                                <input type="number" step="0.01" min="0" max="999.99"
                                    class="form-custom-icon b-left"
                                    id="quantity" name="cantidad" required>
                            </div>
                        </div>

                        <!-- Cantidad servicios -->
                        <div class="form-group col-sm-3 service">
                            <label for="service_quantity">Cantidad</label>
                            <div class="input-div verify-quantity">
                                <div class="i"><i class="fas fa-box-open"></i></div>
                                <input type="number" class="form-custom-icon b-left" id="service_quantity" name="cantidad">
                            </div>
                        </div>

                        <!-- Descuento -->
                        <div class="form-group col-sm-3">
                            <label for="discount">Descuento</label>
                            <div class="input-div">
                                <div class="i"><i class="fas fa-level-down-alt"></i></div>
                                <input type="number" class="form-custom-icon b-left" id="discount" name="descuento">
                            </div>
                        </div>

                         <!-- Costo -->
                        <div class="form-group col-sm-3 service">
                            <label for="service_cost">Costo</label>
                            <div class="input-div">
                                <div class="i"><i class="fas fa-dollar-sign"></i></div>
                                <input type="text" class="form-custom-icon b-left" id="service_cost" name="costo">
                            </div>
                        </div>

                        <!-- Precio -->
                        <div class="form-group col-sm-3 service">
                            <label for="price_out">Precio</label>
                            <div class="input-div">
                                <div class="i"><i class="fas fa-dollar-sign"></i></div>
                                <input type="text" class="form-custom-icon b-left" id="price_out" name="precio" required>
                            </div>
                        </div>
                    </div> <!-- Row detalle -->

                    <!-- Footer -->
                    <div class="mt-4 modal-footer">
                        <button class="btn-custom btn-red" type="button" data-dismiss="modal">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>

                        <button class="btn-custom btn-green" type="submit" id="rp_add_item">
                            <i class="fas fa-plus"></i>
                            <p>Agregar</p>
                        </button>
                    </div>

                </form>
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
                        <label class="form-check-label" for="">Cliente</label>
                        <div class="input-div">
                            <div class="i b-right">
                                <i class="fas fa-portrait"></i>
                            </div>
                            <select class="form-custom-icon search" name="" id="cash-in-customer" requireds>
                                <?php $customers = Help::showCustomers();
                                while ($customer = $customers->fetch_object()): ?>
                                    <option value="<?= $customer->cliente_id ?>"><?= ucwords($customer->nombre) . " " . ucwords($customer->apellidos ?? '') ?></option>
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

                    <button class="btn-custom btn-red" type="button" data-dismiss="modal">
                        <i class="fas fa-window-close"></i>
                        <p>Salir</p>
                    </button>

                    <button class="btn-custom btn-blue" type="button" data-dismiss="modal"
                        id="cash-in-finish-rp-receipt">
                        <i class="fas fa-receipt"></i>
                        <p>Imprimir y facturar</p>
                    </button>

                    <button type="button" class="btn-custom btn-green" id="cash-in-finish_rp">
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
                        <label class="form-check-label" for="">Cliente</label>
                        <div class="input-div">
                            <div class="i b-right">
                                <i class="fas fa-portrait"></i>
                            </div>
                            <select class="form-custom-icon search" name="" id="credit-in-customer" requireds>
                                <?php $customers = Help::showCustomers();
                                while ($customer = $customers->fetch_object()): ?>
                                    <option value="<?= $customer->cliente_id ?>"><?= ucwords($customer->nombre) . " " . ucwords($customer->apellidos ?? '') ?></option>
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
                                while ($method = $methods->fetch_object()): ?>
                                    <option value="<?= $method->metodo_pago_id ?>"><?= $method->nombre_metodo ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Fecha</label>
                        <div class="input-div">

                            <input type="date" name="" class="form-custom-icon" id="credit-in-date" value="<?php date_default_timezone_set('America/New_York');;
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
                            <input class="form-custom-icon b-left" type="number" name="" value="" id="credit-pay_rp">
                        </div>
                    </div>


                </div> <!-- Row -->


                <div class="mt-4 modal-footer">
                    <button class="btn-custom btn-red" type="button" data-dismiss="modal">
                        <i class="fas fa-window-close"></i>
                        <p>Salir</p>
                    </button>

                    <button class="btn-custom btn-blue" type="button" data-dismiss="modal"
                        id="credit-in-finish-rp-receipt">
                        <i class="fas fa-receipt"></i>
                        <p>Imprimir y facturar</p>
                    </button>

                    <button type="button" class="btn-custom btn-green" id="credit-in-finish_rp">
                        <i class="fas fa-dollar-sign"></i>
                        <p>Facturar</p>
                    </button>
                </div>
            </div> <!-- Body -->
        </div>
    </div>
</div>


<!-- Crear cliente -->

<div class="modal fade" id="create_customer" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar nuevo cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="" onsubmit="event.preventDefault(); AddContactModal();">

                    <div class="form-group col-sm-12">
                        <p class="title-info">
                            Incluye los datos principales de tu nuevo contacto
                        </p>
                    </div>

                    <div class="row col-md-12">

                        <div class="form-group col-sm-7">
                            <label for="Nombre" class="form-check-label label-nomb">Nombre/Razón social<span
                                    class="text-danger">*</span></label>
                            <input class="form-custom" type="text" name="" id="name" required>
                        </div>

                        <div class="form-group col-sm-5">
                            <label for="Apellidos" class="form-check-label">Apellidos</label>
                            <input class="form-custom" type="text" name="" id="lastname">
                        </div>

                        <div class="form-group col-sm-6" id="cod_client">
                            <label class="form-check-label" for="">RNC o Cédula</label>
                            <input class="form-custom" type="text" name="" maxlength="11" id="identity">
                        </div>

                        <div class="form-group col-sm-6">
                            <label class="form-check-label" for="">Dirección</label>
                            <select class="form-custom search" name="" id="address">
                                <option value="0" selected> --- </option>
                                <?php require_once "./views/contacts/includes/direcciones.php"; ?>
                            </select>
                        </div>

                    </div> <!-- Row col-md-12 -->
                    <br>

                    <div class="col-legend">
                        <h3>Información de contacto</h3>
                    </div>

                    <br>

                    <div class="form-group col-sm-12">
                        <p class="title-info">
                            Agrega estos datos para comunicarte en cualquier momento con tu contacto.
                        </p>
                    </div>

                    <div class="row col-md-12">

                        <div class="form-group col-sm-6 ">
                            <label class="form-check-label" for="">Télefono 1</label>
                            <input class="form-custom" type="number" name="" id="tel1">
                        </div>

                        <div class="form-group col-sm-6 ">
                            <label class="form-check-label" for="">Télefono 2</label>
                            <input class="form-custom" type="number" name="" id="tel2">
                        </div>

                        <div class="form-group col-sm-6 ">
                            <label class="form-check-label" for="">E-mail</label>
                            <input class="form-custom" type="email" name="" id="email">
                        </div>

                    </div>



                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>
                        <button type="submit" class="btn-custom btn-green" id="">
                            <i class="fas fa-plus"></i>
                            <p>Registrar</p>
                        </button>
                    </div>

                </form>

            </div> <!-- Body -->
        </div>
    </div>
</div>