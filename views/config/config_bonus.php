    <div class="section-wrapper">
        <div class="align-content clearfix">
            <div class="float-left">
                <h1><i class="fas fa-dollar-sign"></i> Configuración de bonos</h1>
            </div>
        </div>
        <p class="title-info">Configura bonos que se aplican a facturas que alcancen un valor especifico.</p>
    </div>

    <div class="generalContainer-medium">
        <form action="" method="POST" id="formBonus">
            <?php $config = Help::configBonus(); ?>

            <div class="container row">
                <div class="form-group col-md-12">
                    <div class="form-group d-flex">
                        <label class="col-sm-2 text-right">Mínimo<span class="text-danger">*</span></label>
                        <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="minimo" value="<?= $config['min_factura'] ?>" id="min_invoice" required>
                        <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Mínimo de factura"
                            data-content="Mínimo para poder obtener el bono.">
                            <i class="far fa-question-circle"></i>
                        </a>
                    </div>

                    <div class="form-group d-flex">
                        <label class="col-sm-2 text-right">Valor<span class="text-danger">*</span></label>
                        <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="valor" value="<?= $config['valor'] ?>" id="bonus_value" required>
                        <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Valor del bono"
                            data-content="Ingresa el valor del bono.">
                            <i class="far fa-question-circle"></i>
                        </a>
                    </div>

                    <div class="form-group d-flex">
                        <label class="col-sm-2 text-right">Estado<span class="text-danger">*</span></label>
                        <select class="form-custom col-sm-4 ml-3" name="estado" id="status" required>
                            <option value="<?= $config['estado_id'] ?>" selected><?= $config['nombre_estado'] ?></option>
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <p class="info-sm mt-2">Los campos marcados con <span class="text-danger">*</span> son obligatorios</p>

            <div class="buttons clearfix">
                <div class="floatButtons">
                    <button class="btn-custom btn-green" type="submit" id="">
                        <i class="fas fa-plus"></i>
                        <p>Guardar</p>
                    </button>
                </div>
            </div>
        </form>
    </div>