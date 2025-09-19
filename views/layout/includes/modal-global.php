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
                                <select class="form-custom-icon search" name="cliente" id="ov_customer_id" required>
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

<!--Modal ordenrp-->
<div class="modal fade" id="orden" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Crear nueva orden</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" onsubmit="event.preventDefault(); addOrdenRepair();">

                    <div class="row">
                        <div class="form-group col-sm-8">
                            <label class="form-check-label" for="">Cliente<span class="text-danger">*</span></label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="cliente" id="or_customer_id" required>
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
                                        <option value="<?= $device->equipo_id ?>"><?= ucwords($device->nombre_marca ?? '') ?>
                                            <?= ucwords($device->nombre_modelo ?? '') ?> <?= strtoupper($device->modelo ?? '') ?>
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
                            <textarea class="form-custom" name="" value="" id="observation_repair" cols="30" rows="3"
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