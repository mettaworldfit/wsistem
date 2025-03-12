<?php $config = Help::showBond_config(); while($element = $config->fetch_object()): ?>

<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Configuración de bonos</h1>
        </div>

    </div>
</div>



<div class="generalContainer-medium">
    <form action="" onsubmit="event.preventDefault(); UpdateBond_config();">
        <div class="container row">

            <div class="form-group col-md-8">
                <div class="form-group d-flex">
                    <label for="" class="col-sm-3 text-right ">Mínimo de factura<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-12" type="text" name="" id="min_invoice" value="<?= $element->min_factura ?>" required>
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?"><i class="far fa-question-circle"></i></a>
                </div>

                <div class="form-group mt-3 d-flex">
                    <label for="" class="col-sm-4 text-right ">Valor del bono<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-5" type="number" name="" value="<?= $element->valor ?>" id="bonus_value" required>
                    <span class="ml-1">$</span>

                </div>

                <div class="form-group mt-3 d-flex">
                    <label for="" class="col-sm-4 text-right ">Estado</label>
                    <select class="form-custom col-sm-5 search" name="" id="status">
                    <option value="<?= $element->estado_id ?>"selected><?= $element->nombre_estado ?></option>
                    <option value="1">Activo</option>
                    <option value="2">Inactivo</option>
                </select>
                </div>
            </div>

        </div>
        <p class="info-sm mt-2">Los campos marcados con <span class="text-danger">*</span> son obligatorios</p>

        <div class="buttons clearfix">
            <div class="floatButtons">
                <button class="btn btn-sm btn-primary " type="submit" id="">Guardar</button>
            </div>
        </div>
    </form>
</div>

<?php endwhile; ?>