<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Órdenes de ventas</h1>
        </div>

        <div class="float-right">
            <a href="#" class="btn-custom btn-orange" data-toggle="modal" data-target="#create_customer">
                <i class="fas fa-user"></i>
                <p>Agregar cliente</p>
            </a>

            <a href="#" class="btn-custom btn-default" data-toggle="modal" data-target="#modalComanda">
                <i class="fas fa-plus"></i>
                <p>Nueva orden</p>
            </a>
        </div>
    </div>
</div>

<div class="generalContainer">
    <table id="orders" class="table-custom table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Entrega</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Orden</th>
                <th>Acciones</th>
            </tr>
        </thead>

    </table>
</div>



<!-- Modal orden -->
<div class="modal fade" id="modalComanda" tabindex="-1" aria-labelledby="modalComandaLabel" aria-hidden="true" data-bs-backdrop="static" data-keyboard="false"
    aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear nueva orden</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); registerSalesOrder();">
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
                                        <option value="<?= $customer->cliente_id ?>"><?= ucwords($customer->nombre ?? '') ?>
                                            <?= ucwords($customer->apellidos ?? '') ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="form-check-label" for="">Dirección de entrega</label>
                            <textarea class="form-custom" name="" value="" id="direction" cols="30" rows="3"
                                maxlength="254" placeholder="Dirección completa"></textarea>
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Nombre del receptor</span></label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="receptor" id="fullname">
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Teléfono</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="telefono" id="tel">
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Tipo de entrega</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="entrega" id="delivery">
                                    <option value="-" selected>Nínguno</option>
                                    <option value="envio">Envío</option>
                                    <option value="retiro">Retiro</option>
                                </select>
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