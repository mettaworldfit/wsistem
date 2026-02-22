<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1><i class="fas fa-receipt"></i> Configuración tickets</h1>
        </div>
    </div>

    <p class="title-info">Configura los datos de impresión para los tickets.</p>
</div>

<div class="generalContainer-medium">
    <form action="" method="POST" id="formPrinter" enctype="multipart/form-data">

        <div class="container row">
            <div class="form-group col-md-8">

                <!-- ================= IMPRESORA ================= -->
                <h5 class="mb-2">Impresora</h5>

                <div class="form-group d-flex">
                    <label class="col-sm-5 text-right">
                        Nombre Impresora <span class="text-danger">*</span>
                    </label>

                    <select class="form-custom col-sm-12 ml-3 mb-3"
                        id="impresoraSelect"
                        name="printer_name"
                        required>
                        <option value="" selected></option>
                    </select>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-5 text-right">
                        Usuario <span class="text-danger">*</span>
                    </label>

                    <select class="form-custom search col-sm-6 ml-3" name="user_id" required>
                        <?php $users = Help::loadUsers();
                        while ($element = $users->fetch_assoc()): ?>
                            <option value="<?= $element['usuario_id'] ?>"><?= ucwords($element['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-5 text-right">
                        Tipo de Impresora <span class="text-danger">*</span>
                    </label>

                    <select class="form-custom search col-sm-4 ml-3" name="printer_type" required>
                        <option value="">Seleccione</option>
                        <option value="main">Principal</option>
                        <option value="kitchen">Cocina</option>
                        <option value="warehouse">Almacén</option>
                    </select>
                </div>

                <!-- Lenguaje -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Lenguaje<span class="text-danger">*</span></label>
                    <select class="form-custom search col-sm-4 ml-3" name="printer_language" required>
                        <option value="">Seleccione</option>
                        <option value="ESCPOS">ESC / POS</option>
                        <option value="ZPL">ZPL</option>
                        <option value="TSPL">TSPL</option>
                        <option value="EPL">EPL</option>
                    </select>
                </div>

                <!-- Método -->
                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Método</label>
                    <select class="form-custom search col-sm-4 ml-3" name="print_method">
                        <option value="RAW">RAW</option>
                        <option value="HTML">HTML</option>
                        <option value="IMAGE">IMAGE</option>
                    </select>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Escala logo</label>
                    <select class="form-custom search col-sm-4 ml-3" name="logo_density">
                        <option value="single">Normal</option>
                        <option value="double">Alta calidad</option>
                    </select>
                </div>

                <hr>

                <!-- ================= PAPEL ================= -->
                <h5 class="mb-2">Configuración de papel</h5>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Papel (mm)<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3"
                        type="number"
                        value="80"
                        step="0.01"
                        name="paper_width"
                        required>
                </div>
                <hr>

                <!-- ================= OPCIONES ================= -->
                <h5 class="mb-2">Opciones de impresión</h5>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Copias<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-4 ml-3"
                        type="number"
                        name="copies"
                        value="1"
                        min="1"
                        max="99"
                        required>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Auto corte</label>
                    <select class="form-custom search col-sm-4 ml-3" name="auto_cut">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>

                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Abrir gaveta</label>
                    <select class="form-custom search col-sm-4 ml-3" name="open_cash_drawer">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Utilizar firma</label>
                    <select class="form-custom search col-sm-4 ml-3" name="signature">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Footer Políticas</label>
                    <textarea class="form-custom col-sm-8 ml-3" name="policy_footer" rows="3">No hay devoluciones</textarea>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Footer ticket</label>
                    <textarea class="form-custom col-sm-8 ml-3" name="ticket_footer" rows="3">Gracias por su visita</textarea>
                </div>

                <hr>

                <!-- ================= CÓDIGO DE BARRAS ================= -->
                <h5 class="mb-2">Código de barras</h5>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Usar barcode</label>
                    <select class="form-custom search col-sm-4 ml-3" name="use_barcode">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Tipo</label>
                    <select class="form-custom search col-sm-4 ml-3" name="barcode_type">
                        <option value="CODE39">Code 39</option>
                        <option value="CODE128">Code 128</option>
                        <option value="EAN13">EAN-13</option>
                    </select>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Altura</label>
                    <input class="form-custom col-sm-4 ml-3" type="number" name="barcode_height"
                        value="80" min="1" max="200">
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Ancho</label>
                    <input class="form-custom col-sm-4 ml-3" type="number" value="2" name="barcode_width">
                </div>

                <hr>

                <!-- ================= QR ================= -->
                <h5 class="mb-2">Código QR</h5>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Usar QR</label>
                    <select class="form-custom search col-sm-4 ml-3" name="use_qr">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Tamaño</label>
                    <input class="form-custom col-sm-4 ml-3" type="number" name="qr_size"
                        value="4" min="3" max="12">
                </div>
                <hr>

                <!-- ================= ESPACIADO ================= -->
                <h5 class="mb-2">Espaciado del ticket</h5>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Feed inicial</label>
                    <input class="form-custom col-sm-4 ml-3" type="number" value="1" name="feed_start"
                        min="0" max="10">
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Feed final</label>
                    <input class="form-custom col-sm-4 ml-3" type="number" value="4" name="feed_end">
                </div>

            </div>
        </div>

        <p class="info-sm mt-2">
            Los campos marcados con <span class="text-danger">*</span> son obligatorios
        </p>

        <div class="buttons clearfix">
            <div class="floatButtons">

                <button type="button" class="btn-custom btn-info" id="btnQzDiagnostico">
                    <i class="fas fa-stethoscope"></i>
                    <p>Diagnóstico</p>
                </button>

                <button type="button" class="btn-custom btn-yellow" id="printTest">
                    <p>Probar</p>
                </button>

                <button type="submit" class="btn-custom btn-green">
                    <i class="fas fa-save"></i>
                    <p>Guardar</p>
                </button>

            </div>
        </div>
    </form>
</div>