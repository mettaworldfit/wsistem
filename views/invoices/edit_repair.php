<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Factura RP-00<?= $_GET['f'] ?></h1>
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

    <input type="hidden" name="" value="<?= $_GET['o'] ?>" id="orden_id">
    <input type="hidden" name="" value="<?= $_GET['f'] ?>" id="invoice_id">
    <table id="editrepair" class="table-custom table">
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
            <textarea class="form-custom" name="" value="" id="observation" cols="30" rows="6" maxlength="150"
                placeholder="Observaciones"><?= $note ?></textarea>
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

    <div class="button-container">




        <button class="btn-custom btn-green" type="button" data-toggle="modal" data-target="#update_data_invoice" id="">
            <i class="far fa-edit"></i>
            <p>Actualizar datos</p>
        </button>

        <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?>
            <button class="btn-custom btn-blue" type="button" data-toggle="modal" data-target="#update_invoice">
                <i class="fas fa-file-invoice"></i>
                <p>Actualizar factura</p>
            </button>
        <?php } ?>

        <button class="btn-custom btn-default" type="button" data-toggle="modal" data-target="#add_detail" id="">
            <i class="fas fa-plus"></i>
            <p>Agregar detalle</p>
        </button>

        <button class="btn-custom btn-orange" type="button" id="printer_order">
            <i class="fas fa-receipt"></i>
            <p>Imprimir orden</p>
        </button>
        <button class="btn-custom btn-yellow" type="button" id="printer_inv_rp">
            <i class="fas fa-receipt"></i>
            <p>Imprimir factura</p>
        </button>

        <button class="btn-custom btn-red" type="button" id="generateInvPDF">
            <i class="fas fa-file-pdf"></i>
            <p>Imprimir PDF</p>
        </button>

        <input type="hidden" name="" value='<?= $orderDetail ?>' id="detail_order">
        <input type="hidden" name="" value='<?= $deviceInfo ?>' id="device_info">
        <input type="hidden" name="" value='<?= $conditions ?>' id="conditions">
    </div>

</div> <!-- generalConntainer -->


<!--Modal agregar detalle-->
<div class="modal fade" id="add_detail" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar detalle de facturación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); addDetailOrdenRepair();">

                    <div class="col-sm-12 row">

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

                    <!-- Content -->
                    <div class="row col-sm-12">

                        <div class="form-group col-sm-4 piece">
                            <label class="form-check-label" for="">Código</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="codigo" id="piece_code">
                            </div>
                        </div>

                        <div class="form-group col-sm-5 piece">
                            <label class="form-check-label" for="">Piezas</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="pieza" id="piece">
                                    <option value="" disabled selected>Buscar piezas</option>
                                    <?php $pieces = Help::showPieces();
                                    while ($piece = $pieces->fetch_object()): ?>
                                        <option value="<?= $piece->pieza_id ?>"><?= $piece->nombre_pieza ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <input type="hidden" name="" value="" id="piece_cost">
                            </div>
                        </div>

                        <div class="form-group col-sm-12 service">
                            <label class="form-check-label" for="">Servicios</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="far fa-clipboard"></i>
                                </div>
                                <select class="form-custom-icon search" name="servicio" id="rp_service">
                                    <option value="" disabled selected>Buscar servicios</option>
                                    <?php $services = Help::showServices();
                                    while ($service = $services->fetch_object()): ?>
                                        <option value="<?= $service->servicio_id ?>"><?= $service->nombre_servicio ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                        </div>


                        <div class="form-group col-sm-3 piece">
                            <label class="form-check-label" for="">Stock</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="stock" id="stock">
                            </div>
                        </div>



                    </div> <!-- Row -->

                    <div class="row col-sm-12 mt-1">

                        <div class="form-group col-sm-3 product-piece">
                            <label class="form-check-label" for="">Cantidad</label>
                            <div class="input-div verify-quantity">
                                <div class="i">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="cantidad" id="quantity"
                                    required>
                            </div>
                        </div>

                        <div class="form-group col-sm-3 service">
                            <label class="form-check-label" for="">Cantidad</label>
                            <div class="input-div verify-quantity">
                                <div class="i">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="cantidad" id="service_quantity">
                            </div>
                        </div>

                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Descuento</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-level-down-alt"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="descuento" id="discount">
                            </div>
                        </div>

                        <div class="form-group col-sm-3 service" id="cost-field">
                            <label class="form-check-label" for="">Costo</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-level-down-alt"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="costo" value="" id="service_cost" style="font-weight: 600" required disabled>
                            </div>
                        </div>

                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Precio</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <input type="text" class="form-custom-icon b-left" name="precio" id="price_out"
                                    required>
                            </div>
                        </div>

                        <div class="form-group col-sm-3 piece">
                            <label class="form-check-label" for="">Lista de precios</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="list" id="piece_list_id">
                                    <option value="0" selected>General</option>

                                </select>
                            </div>
                        </div>


                    </div> <!-- Row -->

                    <div class="row col-sm-12 mt-1">



                    </div> <!-- Row -->

                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>
                        <button type="submit" class="btn-custom btn-green" id="rp_add_item">
                            <i class="far fa-money-bill-alt"></i>
                            <p>Agregar</p>
                        </button>
                    </div>

                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>


<!--Modal Factura actualizar factura-->
<div class="modal fade" id="update_data_invoice" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Actualizar (Datos de Factura)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); updateInvoiceInfo();">

                    <!-- Head -->
                    <?php $data = Help::INFO_INVOICE_RP($_GET['o']);
                    while ($info = $data->fetch_object()): ?>
                        <div class="row col-sm-12 invoice-head-modal">

                            <div class="col-sm-4 head-content">
                                <h6>Total Pagado</h6>
                                <input type="text" class="invisible-input text-success" value="" id="cash-received"
                                    disabled>
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
                                    <select class="form-custom-icon search" name="" id="customer" requireds>
                                        <option value="<?= $info->cliente_id ?>" selected><?= ucwords($info->nombre_cliente) . " " . ucwords($info->apellidos_cliente) ?></option>
                                        <?php $customers = Help::showCustomers();
                                        while ($customer = $customers->fetch_object()): ?>
                                            <option value="<?= $customer->cliente_id ?>"><?= ucwords($customer->nombre) . " " . ucwords($customer->apellidos) ?></option>
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
                                    <select class="form-custom-icon search " name="" id="method">
                                        <option value="<?= $info->metodo_pago_id ?>" selected><?= $info->nombre_metodo ?>
                                        </option>
                                        <?php $methods = Help::showPaymentMethod();
                                        while ($method = $methods->fetch_object()): ?>
                                            <option value="<?= $method->metodo_pago_id ?>"><?= $method->nombre_metodo ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-sm-4">
                                <label class="form-check-label" for="">Fecha</label>
                                <div class="input-div">
                                    <div class="i">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <input class="form-custom-icon b-left" type="text" name=""
                                        value="<?= $info->fecha_factura ?>" disabled>
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
                                        value="<?= ucwords($info->nombre_usuario) ?>" id="cash-in-seller" disabled>
                                </div>
                            </div>

                        </div> <!-- Row -->
                    <?php endwhile; ?>

                    <div class="mt-4 modal-footer">

                        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>
                        <button type="submit" class="btn-custom btn-green" id="">
                            <i class="far fa-edit"></i>
                            <p>Actualizar</p>
                        </button>
                    </div>

                </form>
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


<!-- Actualizar (Dinero recibido) -->

<div class="modal fade" id="update_invoice" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Actualizar (Dinero recibido)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); UPDATE_CASH_RECEIVED(<?= $_GET['f']; ?>);">

                    <!-- Head -->
                    <?php $data = Help::INFO_INVOICE_RP($_GET['o']);
                    while ($info = $data->fetch_object()): ?>
                        <div class="row col-sm-12 invoice-head-modal">

                            <div class="col-sm-4 head-content">
                                <h6>Total Pagado</h6>
                                <input type="text" class="invisible-input text-success" value="" id="cash-received2"
                                    disabled>
                            </div>

                            <div class="col-sm-4 head-content">
                                <h6>Monto a Pagar</h6>
                                <input type="text" class="invisible-input text-primary" value="" id="cash-topay2" disabled>
                            </div>

                            <div class="col-sm-4 head-content">
                                <h6>Monto Pendiente</h6>
                                <input type="text" class="invisible-input text-danger" value="" id="cash-pending2" disabled>
                            </div>

                        </div>
                        <br>

                        <!-- Content -->
                        <div class="row col-sm-12">

                            <div class="form-group col-sm-4">
                                <label class="form-check-label" for="">Dinero recibido</label>
                                <div class="input-div">
                                    <div class="i b-right">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <input class="form-custom-icon b-left" type="text" name=""
                                        value="<?= $info->recibido ?>" id="update-cash-received">
                                </div>
                            </div>


                        </div> <!-- Row -->

                    <?php endwhile; ?>

                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>
                        <button type="submit" class="btn-custom btn-green" id="">
                            <i class="far fa-edit"></i>
                            <p>Actualizar</p>
                        </button>
                    </div>

                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>