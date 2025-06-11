<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Cotización CT-00<?= $_GET['id'] ?></h1>
            <input type="hidden" name="id" value="<?= $_GET['id'] ?>" id="quote_id">
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

    <table id="Detalle" class="table-custom table">
        <thead>
            <th>Descripción</th>
            <th>Cant</th>
            <th>Precio</th>
            <th class="hide-cell">Impuesto</th>
            <th>Descuento</th>
            <th>Importe</th>
            <th></th>
        </thead>

        <?php while ($element = $quotes->fetch_object()): ?>
            <tbody id="rows">
                <td><?= ucwords($element->descripcion) ?></td>
                <td><?= $element->cantidad ?></td>
                <td><?= number_format($element->precio, 2) ?></td>
                <td class="hide-cell"><?= $element->impuesto ?></td>
                <td><?= number_format($element->descuento,2) ?></td>
                <td><?= number_format((($element->cantidad * $element->precio) + $element->impuesto) - $element->descuento, 2) ?></td>
                <td> <a class="text-danger pointer" style="font-size: 16px;" onclick="DeleteItemQ('<?= $element->detalle_id ?>',true)"><i class="fas fa-times"></i></a></td>
            </tbody>
        <?php endwhile; ?>
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

    <div class="button-container">
        <button class="btn-custom btn-green" type="button" data-toggle="modal" data-target="#save_quote">
            <i class="fas fa-file-invoice"></i>
            <p>Actualizar</p>
        </button>

        <button class="btn-custom btn-default" type="button" data-toggle="modal" data-target="#add_detail" id="">
            <i class="fas fa-plus"></i>
            <p>Agregar detalle</p>
        </button>

        <button class="btn-custom btn-blue" type="button" id="SendmailQuote">
            <i class="fas fa-envelope"></i>
            <p>Enviar</p>
        </button>

        <button class="btn-custom btn-red" type="button" id="QuotePDF">
            <i class="fas fa-file-pdf"></i>
            <p>Generar PDF</p>
        </button>

    </div>
</div> <!-- generalConntainer -->


<!--Modal agregar detalle-->
<div class="modal fade" id="add_detail" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar detalle</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); AddDQuote(true);">

                    <div class="col-sm-12 row">

                        <div class="radio-list">
                            <div class="radio-item ml-3">
                                <input type="radio" name="tipo" value="producto" id="radio1" checked>
                                <label for="radio1">Productos</label>
                            </div>

                            <div class="radio-item ml-2">
                                <input type="radio" name="tipo" value="pieza" id="radio2">
                                <label for="radio2">Piezas</label>
                            </div>

                            <div class="radio-item ml-2">
                                <input type="radio" name="tipo" value="servicio" id="radio3">
                                <label for="radio3">Servicios</label>
                            </div>
                        </div>

                    </div>

                    <!-- Información -->
                    <div class="modal-legend">
                        <p>información</p>
                    </div>

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

                        <div class="form-group col-sm-4 product">
                            <label class="form-check-label" for="">Código</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="codigo" id="code">
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
                                        <option value="<?= $piece->pieza_id ?>"><?= ucwords($piece->nombre_pieza) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <input type="hidden" name="" value="" id="piece_id">
                            </div>
                        </div>

                        <div class="form-group col-sm-5 product">
                            <label class="form-check-label" for="">Productos</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="far fa-clipboard"></i>
                                </div>
                                <select class="form-custom-icon search" name="producto" id="product">
                                    <option value="" disabled selected>Buscar productos</option>
                                    <?php $products = Help::showProducts();
                                    while ($product = $products->fetch_object()): ?>
                                        <option value="<?= $product->IDproducto ?>">
                                            <?= ucwords($product->nombre_producto) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <input type="hidden" name="" value="" id="taxes">
                                <input type="hidden" name="" value="" id="product_id">
                            </div>

                        </div>


                        <div class="form-group col-sm-12 service">
                            <label class="form-check-label" for="">Servicios</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="far fa-clipboard"></i>
                                </div>
                                <select class="form-custom-icon search" name="servicio" id="service">
                                    <option value="" disabled selected>Buscar servicios</option>
                                    <?php $services = Help::showServices();
                                    while ($service = $services->fetch_object()): ?>
                                        <option value="<?= $service->servicio_id ?>">
                                            <?= ucwords($service->nombre_servicio) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                        </div>


                        <div class="form-group col-sm-3 product-piece">
                            <label class="form-check-label" for="">Cantidad inventario</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="stock" id="stock" disabled>
                            </div>
                        </div>

                    </div>


                    <div class="row">

                        <div class="col-sm-12 product">
                            <div class="modal-legend ">
                                <p>listas de precios</p>
                            </div>
                        </div>

                        <div class="col-sm-12 piece">
                            <div class="modal-legend ">
                                <p>listas de precios</p>
                            </div>
                        </div>
                    </div>


                    <div class="row mt-1">



                        <div class="form-group col-sm-4 ml-2 product">
                            <label class="form-check-label" for="">Lista de precios</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="list" id="list_id">
                                    <option value="0" selected>General</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-4 ml-3 piece">
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

                    </div>

                    <!-- Información de detalle -->
                    <div class="modal-legend">
                        <p>Información de detalle</p>
                    </div>

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

                        <div class="form-group col-sm-3 discount">
                            <label class="form-check-label" for="">Descuento</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-level-down-alt"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="descuento" id="discount">
                            </div>
                        </div>

                        <div class="form-group col-sm-3 service">
                            <label class="form-check-label" for="">Descuento</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-level-down-alt"></i>
                                </div>
                                <input type="number" class="form-custom-icon b-left" name="descuento"
                                    id="discount_service">
                            </div>
                        </div>

                        <div class="form-group col-sm-3">
                            <label class="form-check-label" for="">Precio</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <input type="text" class="form-custom-icon b-left" name="precio" id="price_out"
                                    style="font-weight: 600" required disabled>
                            </div>
                        </div>

                        <div class="form-group col-sm-3 discount">
                            <label class="form-check-label" for="">Ubicación</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="locate" id="locate" disabled>
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

                        <button type="submit" class="btn-custom btn-green" id="add_item">
                            <i class="fas fa-plus"></i>
                            <p>Agregar</p>
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


<!-- Actualizar cotizacion -->

<div class="modal fade" id="save_quote" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Actualizar (Cotización)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); updateQuote('<?= $_GET['id'] ?>');">

                    <!-- Content -->
                    <div class="row col-sm-12">

                        <div class="form-group col-sm-6">
                            <label class="form-check-label" for="">Cliente</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-portrait"></i>
                                </div>
                                <select class="form-custom-icon search" name="" id="customer" requireds>
                                    <?php $customers = Help::showCustomers();
                                    while ($customer = $customers->fetch_object()): ?>
                                        <option value="<?= $customer->cliente_id ?>"><?= ucwords($customer->nombre) . " " . ucwords($customer->apellidos) ?></option>
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
                                <input class="form-custom-icon b-left" type="date" name=""
                                    value="<?php date_default_timezone_set('America/New_York');
                                            echo date('Y-m-d'); ?>" id="date">
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
                                    value="<?= ucwords($_SESSION['identity']->nombre) ?>" id="user_id" disabled>
                            </div>
                        </div>

                    </div> <!-- Row -->


                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>
                        <button type="submit" class="btn-custom btn-green">
                            <i class="fas fa-plus"></i>
                            <p>Actualizar</p>
                        </button>
                    </div>

                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>