<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Ordenes de servicios</h1>
        </div>


        <div class="float-right">

            <a href="#" class="btn-custom btn-blue" data-toggle="modal" data-target="#create_device">
                <i class="fas fa-mobile-alt"></i>
                <p>Agregar equipo</p>
            </a>

            <a href="#" class="btn-custom btn-default" data-toggle="modal" data-target="#create_condition">
                <i class="fas fa-tools"></i>
                <p>Agregar condición</p>
            </a>

            <a href="#" class="btn-custom btn-orange" data-toggle="modal" data-target="#create_customer">
                <i class="fas fa-user"></i>
                <p>Agregar cliente</p>
            </a>

            <a href="#" class="btn-custom btn-green" data-toggle="modal" data-target="#orden">
                <i class="fas fa-plus"></i>
                <p>Crear servicio</p>
            </a>

        </div>
    </div>
</div>


<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th class="hide-cell">Entrada</th>
                <th class="hide-cell">Salida</th>
                <th class="hide-cell">Condición</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($element = $workshops->fetch_object()):
                $parents = Help::verify_parent_invoice_rp($element->facturaRP_id);
                while ($parent = $parents->fetch_object()) {
                    $invoiced = Help::orden_rp_is_invoiced($element->id);
                    while ($inv = $invoiced->fetch_object()) {
                        ?>

                        <tr>
                            <td>
                                <span>
                                    <a href="#"
                                        class="<?php if ($element->facturaRP_id > 0) { ?> text-secondary <?php } else { ?> text-danger <?php } ?>">OR-00<?= $element->id ?></a>
                                    <!-- Toggle -->
                                    <span id="toggle" class="toggle-right toggle-md">
                                        No. Orden: OR-00<?= $element->id ?> <br>
                                        No. Factura: <?php

                                        if ($element->facturaRP_id > 0) {

                                            echo "<a class='text-danger' href='" . base_url . "invoices/repair_edit&o=" . $element->id . "&f=" . $element->facturaRP_id . "' >";
                                            echo "RP-00";
                                            echo $element->facturaRP_id;
                                            echo "</a>";
                                        } else {

                                            echo "<a class='text-danger' href='#' >";
                                            echo "No facturado";
                                            echo "</a>";
                                        } ?>
                                    </span>

                                </span>

                            </td>

                            <td><?= ucwords($element->nombre_cliente) ?>             <?= ucwords($element->apellidos_cliente) ?></td>
                            <td>
                                <span>
                                    <a href="#" class="text-secondary"><?= ucwords($element->nombre_marca) ?>
                                        <?= ucwords($element->nombre_modelo) ?>             <?= $element->modelo ?></a>

                                    <span id="toggle" class="toggle-right toggle-xl">
                                        Marca: <?= $element->nombre_marca ?> <br>
                                        Modelo: <?= $element->modelo ?> <br>
                                        IMEI: <?= $element->imei ?> <br>
                                        Serie: <?= $element->serie ?> <br>


                                    </span>
                                </span>

                            </td>
                            <td class="text-success hide-cell"><?= $element->fecha_entrada ?></td>
                            <td class="text-danger hide-cell"><?= $element->fecha_salida ?></td>
                            <td class="hide-cell"><?= Help::SHOW_CONDITONS_ORDER($element->id) ?></td>

                            <td>
                                <select class="form-custom <?= $element->nombre_estado ?>" name="" id="status_rp"
                                    onchange="elegirEstado(this);">
                                    <option workshop_id="<?= $element->id ?>" value="<?= $element->estado_id ?>" selected>
                                        <?= $element->nombre_estado ?>
                                    </option>
                                    <option class="Pendiente" workshop_id="<?= $element->id ?>" value="6">Pendiente</option>
                                    <option class="En Proceso" workshop_id="<?= $element->id ?>" value="8">En Proceso</option>
                                    <option class="Entregado" workshop_id="<?= $element->id ?>" value="7">Entregado</option>
                                    <option class="No se pudo" workshop_id="<?= $element->id ?>" value="10">No se pudo</option>
                                    <option class="Listo" workshop_id="<?= $element->id ?>" value="9">Listo</option>
                                </select>
                            </td>

                            <td>

                                <a href="<?= base_url ?>contacts/edit_customer&id=<?= $element->cliente_id ?>"
                                    title="Información del cliente" class="action-view"><i class="fas fa-user"></i></a>

                                <?php if ($element->facturaRP_id > 0) { ?>
                                    <a href="<?= base_url ?>invoices/repair_edit&o=<?= $element->id ?>&f=<?= $element->facturaRP_id ?>"
                                        title="Ver factura" class="action-paid"><i class="fas fa-eye"></i></a>
                                <?php } else { ?>
                                    <a href="<?= base_url ?>invoices/addrepair&id=<?= $element->id ?>" title="Agregar factura"
                                        class="action-edit"><i class="fas fa-shopping-cart"></i></a>
                                <?php } ?>

                                <span <?php if ($parent->parent_row == 0) { ?> class="action-delete"
                                        onclick="deleteOrden('<?= $element->id ?>')" <?php } else { ?>
                                        class="action-delete action-disable" <?php } ?> title="Eliminar">
                                    <i class="fas fa-times"></i>
                                </span>
                            </td>
                        </tr>
                    <?php }
                }
            endwhile; ?>
        </tbody>




    </table>
</div>




<!--Modal agregar orden-->
<div class="modal fade" id="orden" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear orden de servicio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); add_ordenRP();">

                    <div class="row">
                        <div class="form-group col-sm-8">
                            <label class="form-check-label" for="">Cliente<span class="text-danger">*</span></label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="cliente" id="customer_id" required>
                                    <option value="" selected>Nínguno</option>
                                    <?php $customers = Help::showCustomers();
                                    while ($customer = $customers->fetch_object()): ?>
                                        <option value="<?= $customer->cliente_id ?>"><?= ucwords($customer->nombre) ?>
                                            <?= ucwords($customer->apellidos) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-sm-12">
                            <label class="form-check-label" for="">Nombre del modelo<span
                                    class="text-danger">*</span></label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="device" id="device" required>
                                    <option value="" selected>Nínguno</option>
                                    <?php $devices = Help::showDevices();
                                    while ($device = $devices->fetch_object()): ?>
                                        <option value="<?= $device->equipo_id ?>"><?= ucwords($device->nombre_marca) ?>
                                            <?= ucwords($device->nombre_modelo) ?>     <?= strtoupper($device->modelo) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="form-group col-sm-6">
                            <label class="form-check-label" for="">Número de modelo</span></label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-database"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="modelo" id="model" disabled>

                            </div>
                        </div>

                        <div class="form-group col-sm-6">
                            <label class="form-check-label" for="">Fabricante</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-copyright"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="marca" id="brand" disabled>
                            </div>
                        </div>

                        <div class="form-group col-sm-6">
                            <label class="form-check-label" for="">Número de serie</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="serie" id="serie">
                            </div>
                        </div>

                        <div class="form-group col-sm-6">
                            <label class="form-check-label" for="">IMEI</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="number" name="imei" id="imei">
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="form-check-label" for="">Comentarios o instrucciones especiales</label>
                            <textarea class="form-custom" name="" value="" id="observation" cols="30" rows="3"
                                maxlength="254" placeholder="Observaciones"></textarea>
                        </div>
                    </div>

                    <br>
                    <div class="row">

                        <div class="form-group col-sm-12">
                            <label class="form-check-label" for="">Estado o condición del equipo<span
                                    class="text-danger">*</span></label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="condition" multiple id="condition_id"
                                    required>
                                    <?php $conditions = Help::showConditions();
                                    while ($condition = $conditions->fetch_object()): ?>
                                        <option value="<?= $condition->condicion_id ?>"><?= $condition->sintoma ?> </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>


                    </div>


                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>

                        <button type="submit" href="#" class="btn-custom btn-green">
                            <i class="fas fa-plus"></i>
                            <p>Crear servicio</p>
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
                        <button type="button" data-dismiss="modal" class="btn-custom btn-red">
                            <i class="fas fa-window-close"></i>
                            <p> Salir</p>
                        </button>

                        <button type="submit" class="btn-custom btn-green">
                            <i class="fas fa-plus"></i>
                            <p>Registrar</p>
                        </button>
                    </div>

                </form>

            </div> <!-- Body -->
        </div>
    </div>
</div>


<!-- Agregar equipo -->

<div class="modal fade" id="create_device" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar nuevo equipo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="" onsubmit="event.preventDefault(); AddDevice();">

                    <div class="form-group col-sm-12">
                        <p class="title-info">
                            Crea equipos para ser utilizados en las ordenes
                        </p>
                    </div>

                    <div class="row col-md-12">

                        <div class="form-group col-md-6">
                            <label for="Condicion" class="form-check-label label-nomb">Nombre de modelo<span
                                    class="text-danger">*</span></label>
                            <input class="form-custom" type="text" name="Condicion" id="nom_device" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="Condicion" class="form-check-label label-nomb">Número de modelo</label>
                            <input class="form-custom" type="text" name="Condicion" id="num_device">
                        </div>


                        <div class="form-group col-md-6">
                            <label class="form-check-label" for="">Fabricante<span class="text-danger">*</span></label>

                            <select class="form-custom-icon search" name="fabricante" id="brand_id" required>
                                <option value="" disabled selected>Agregar fabricante</option>
                                <?php $brands = Help::showBrands();
                                while ($brand = $brands->fetch_object()): ?>
                                    <option value="<?= $brand->marca_id ?>"><?= $brand->nombre_marca ?> </option>
                                <?php endwhile; ?>
                            </select>
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


<!-- Agregar condición -->

<div class="modal fade" id="create_condition" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar nueva condición</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="" onsubmit="event.preventDefault(); AddCondition();">

                    <div class="form-group col-sm-12">
                        <p class="title-info">
                            Crea una condición de reparación
                        </p>
                    </div>

                    <div class="row col-md-12">

                        <div class="form-group col-sm-12">
                            <label for="Condicion" class="form-check-label label-nomb">Condición<span
                                    class="text-danger">*</span></label>
                            <input class="form-custom" type="text" name="Condicion" id="condition" required>
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