<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1><i class="fas fa-file-pdf"></i> Configuración factura PDF</h1>
        </div>
    </div>

    <p class="title-info">Configura los datos de las facturas PDF.</p>
</div>

<div class="generalContainer-medium">

    <form action="" onsubmit="event.preventDefault(); ConfigPDF();">

        <?php $config = Help::ConfigPDF(); while ($element = $config->fetch_object()): ?>

            <div class="container row">

                <div class="form-group col-md-8">
                    <div class="form-group d-flex">
                        <label for="logo" class="col-sm-3 text-right ">Logo</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $element->logo_pdf ?>" name="logo" id="logo" disabled>
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Logo"
                            data-content="Ingrasa la url desde la raiz del sistema, consulta con el personal administrativo antes de cambiar esta opción"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="slogan" class="col-sm-3 text-right ">Slogan</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $element->slogan ?>" name="slogan" id="slogan">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Slogan"
                            data-content="Ingresa el slogan de tu empresa"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="direccion" class="col-sm-3 text-right ">Dirección</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $element->direccion ?>" name="direccion" id="address">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Dirección"
                            data-content="Ingresa la dirección de tu empresa."><i class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="tel" class="col-sm-3 text-right ">Teléfono</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $element->tel ?>" name="tel" id="tel">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Teléfono"
                            data-content="Ingresa el teléfono de tu empresa."><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="condiciones" class="col-sm-3 text-right mr-3">Condiciones</label>
                        <textarea class="form-custom col-sm-12" name="condiciones" id="policy" cols="63"
                            rows="4" maxlength="400"><?= $element->condiciones ?></textarea>
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="condiciones"
                            data-content="Agrega los términos y condiciones de tu empresa en el píe de página de las facturas generadas."><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex mt-1">
                        <label for="titulo" class="col-sm-3 text-right mr-3">Título</label>
                        <textarea class="form-custom col-sm-12" name="titulo" id="title" cols="23"
                        rows="2" maxlength="200"><?= $element->titulo ?></textarea>
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Título"
                            data-content="Ingresa un título que haga enfasís en el píe de página"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                </div>
            </div>

            <p class="info-sm mt-2">Los campos marcados con <span class="text-danger">*</span> son obligatorios</p>

            <div class="buttons clearfix">
                <div class="floatButtons">
                    <button type="submit" class="btn-custom btn-green" id="">
                        <i class="fas fa-plus"></i>
                        <p>Guardar</p>
                    </button>
                </div>
            </div>

        <?php endwhile; ?>
    </form>
</div>