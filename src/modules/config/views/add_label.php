<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1><i class="fas fa-tag"></i> Crear etiqueta</h1>
        </div>

        <div class="float-right">
            <a href="" class="btn-custom btn-success" id="generate_code">
                <i class="fas fa-barcode"></i>
                <p>Imprimir codigo</p>
            </a>
        </div>
    </div>

    <p class="title-info">Crea modelos de impresión adaptables para tus etiquetas.</p>
</div>

<div class="generalContainer-medium">
    <form action="" method="POST" id="formLabel">

        <div class="container row">
            <div class="form-group col-md-8">

                <!-- Nombre configuración -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Nombre<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-12 ml-3" type="text" name="nombre_config" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Nombre de configuración"
                        data-content="Nombre corto para identificar este formato de etiqueta.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <!-- Descripción -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Descripción</label>
                    <input class="form-custom col-sm-12 ml-3" type="text" name="descripcion">
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Descripción"
                        data-content="Descripción detallada del diseño de la etiqueta.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <hr>

                <!-- Tamaño -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Ancho (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="ancho_mm" value="35" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Ancho de etiqueta"
                        data-content="Ancho físico de la etiqueta en milímetros.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Alto (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="alto_mm" value="25" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Alto de etiqueta"
                        data-content="Altura física de la etiqueta en milímetros.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Orientación<span class="text-danger">*</span></label>
                    <select class="form-custom col-sm-4 ml-3" name="orientacion" required>
                        <option value="L">Horizontal</option>
                        <option value="P">Vertical</option>
                    </select>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Orientación"
                        data-content="Define si la etiqueta se imprime en forma horizontal o vertical.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <hr>

                <!-- Barcode -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Tipo Barcode<span class="text-danger">*</span></label>
                    <select class="form-custom col-sm-4 ml-3" name="tipo_barcode" required>
                        <option value="C128">Code 128</option>
                        <option value="EAN13">EAN 13</option>
                        <option value="EAN8">EAN 8</option>
                        <option value="UPC">UPC</option>
                    </select>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Tipo de código de barras"
                        data-content="Selecciona el tipo de código de barras que se imprimirá en la etiqueta.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Mostrar texto</label>
                    <select class="form-custom col-sm-4 ml-3" name="mostrar_texto_barcode">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Texto del barcode"
                        data-content="Indica si se mostrará el texto del código debajo de las barras.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Barcode Font<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" name="barcode_font_size" value="5" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Fuente del barcode"
                        data-content="Tamaño de la fuente del texto del código de barras.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Barcode X / Y<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-3 ml-3" type="number" step="0.01" name="barcode_x" value="2" required>
                    <input class="form-custom col-sm-3 ml-2" type="number" step="0.01" name="barcode_y" value="2" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Posición del barcode"
                        data-content="Posición horizontal (X) y vertical (Y) del código de barras en milímetros.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Ancho (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="barcode_width" value="31" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Ancho del código de barras"
                        data-content="Ancho total del código de barras.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Alto (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="barcode_height" value="9" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Altura del código de barras"
                        data-content="Altura total del código de barras.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>
                <hr>

                <!-- Descripción -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Mostrar descripción</label>
                    <select class="form-custom col-sm-4 ml-3" name="mostrar_descripcion">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Descripción"
                        data-content="Define si se imprimirá la descripción del producto.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Desc Font<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" name="descripcion_font_size" value="5" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Fuente descripción"
                        data-content="Tamaño de la fuente del texto de la descripción.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Desc X / Y<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-3 ml-3" type="number" step="0.01" name="descripcion_x" value="2" required>
                    <input class="form-custom col-sm-3 ml-2" type="number" step="0.01" name="descripcion_y" value="11" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Posición de la descripción"
                        data-content="Posición horizontal (X) y vertical (Y) de la descripción en milímetros.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Ancho (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="descripcion_width" value="31" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Ancho de la descripción"
                        data-content="Ancho máximo de la descripción.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Alto (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="descripcion_height" value="4" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Altura de la descripción"
                        data-content="Altura máxima de la descripción.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <hr>

                <!-- Precio -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Mostrar precio</label>
                    <select class="form-custom col-sm-4 ml-3" name="mostrar_precio">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Precio"
                        data-content="Indica si el precio del producto se mostrará en la etiqueta.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Precio Font<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" name="precio_font_size" value="6" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Fuente precio"
                        data-content="Tamaño de la fuente utilizada para mostrar el precio.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Precio X / Y<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-3 ml-3" type="number" step="0.01" name="precio_x" value="2" required>
                    <input class="form-custom col-sm-3 ml-2" type="number" step="0.01" name="precio_y" value="17" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Posición del precio"
                        data-content="Posición horizontal (X) y vertical (Y) del precio en milímetros.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Ancho (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="precio_width" value="31" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Ancho del precio"
                        data-content="Ancho máximo del bloque del precio.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Alto (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3" type="number" step="0.01" name="precio_height" value="4" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Altura del precio"
                        data-content="Altura máxima del bloque del precio.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>
                <hr>

                <!-- Impresora -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Impresora<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-12 ml-3" type="text" name="impresora" value="2C-LP427B" required>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Impresora"
                        data-content="Modelo o nombre de la impresora térmica utilizada para imprimir etiquetas.">
                        <i class="far fa-question-circle"></i>
                    </a>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Activo</label>
                    <select class="form-custom col-sm-4 ml-3" name="activo">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                    <a href="#" class="ml-1 example-popover" data-toggle="popover" title="Estado"
                        data-content="Indica si esta configuración de etiqueta está activa y disponible para impresión.">
                        <i class="far fa-question-circle"></i>
                    </a>
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
    </form>
</div>