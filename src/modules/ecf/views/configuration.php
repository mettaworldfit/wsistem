<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Configuración de facturación electrónica</h1>
        </div>
    </div>
</div>

<div class="generalContainer">
    <?php while ($row = $data->fetch_object()): ?>
        <div class="container-company">
            <div class="column-company">
                <h3>Datos de la empresa</h3>

                <form id="formEmisor">

                    <button class="btn-link" type="submit">
                        <p>Guardar</p>
                    </button>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="enviroment">Ambiente</label>
                        <select class="form-custom" name="enviroment" id="enviroment">
                            <option value="test" <?= $row->ambiente === 'test' ? 'selected' : '' ?>>TesteCF</option>
                            <option value="certificacion" <?= $row->ambiente === 'cert' ? 'selected' : '' ?>>CerteCF</option>
                            <option value="produccion" <?= $row->ambiente === 'produccion' ? 'selected' : '' ?>>eCF</option>
                        </select>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="">Cédula/RNC<span class="text-danger">*</span></label>
                        <input class="form-custom" type="text" name="rnc" value="<?= $row->rnc ?>" id="rnc" required>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="">Nombre/Razón social</label>
                        <input class="form-custom" type="text" name="name" value="<?= $row->razon_social ?>" id="name" readonly>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="">Nombre Comercial</label>
                        <input class="form-custom" type="text" name="coname" value="<?= $row->nombre_comercial ?>" id="coname" readonly>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label" for="">Actividad Economica</label>
                        <textarea class="form-custom" name="activ_econ" id="activ_econ" rows="4" readonly><?= trim($row->actividad_economica) ?></textarea>
                    </div>


                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="">Régimen de pagos</label>
                        <input class="form-custom" type="text" name="regime" value="<?= $row->regimen ?>" id="regime" readonly>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="">Facturador Electrónico</label>
                        <input class="form-custom" type="text" name="emisore" value="<?= strtoupper($row->emisor_electronico) ?>" id="emisore" readonly>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="">Estado</label>
                        <input class="form-custom" type="text" name="status" value="<?= strtoupper($row->estado) ?>" id="status" readonly>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="">Correo</label>
                        <input class="form-custom" type="email" name="mail" id="mail" autocomplete="off">
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-nomb" for="">Dirección</label>
                        <input class="form-custom" type="email" name="address" id="address">
                    </div>
                </form>
            </div>

            <div class="column-company">
                <h3>Certificado<span class="text-danger">*</span></h3>

                <label class="form-check-label" for="">
                    Carga el certificado digital para firma los comprobantes.
                    <a href="#" class="example-popover" data-toggle="popover" title="Certificado Digital"
                        data-content="Es un archivo expedido por una entidad certificación que garantiza que una persona o empresa es quien dice ser y que permite firmar documentos electrónicos"><i
                            class="far fa-question-circle"></i>
                    </a>
                </label>

                <div class="form-group">
                    <form id="uploadFile">
                        <div class="upload-container">

                            <label class="upload-box">
                                <input type="file" name="cert" accept=".pfx,.p12" id="cert" required>

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-upload-icon lucide-cloud-upload">
                                    <path d="M12 13v8" />
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                                    <path d="m8 17 4-4 4 4" />
                                </svg>

                                <div class="upload-title">
                                    <?php
                                    if ($row->route_cert) {
                                        echo $row->route_cert;
                                    } else {
                                        echo "Arrastra el archivo aquí";
                                    }
                                    ?>
                                </div>

                                <div class="upload-format">
                                    Formato PFX o P12
                                </div>

                                <div class="upload-link">
                                    Selecciónalo desde tu computador
                                </div>
                            </label>
                        </div>
                    </form>
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label" for="">Clave del certificado<span class="text-danger">*</span></label>
                    <input type="password" name="passcert" class="form-custom" value="<?= $row->passphrase_cert ?>" id="passcert" autocomplete="off">
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label" for="">Subject</label>
                    <textarea class="form-custom" name="subject" id="subject" rows="5" readonly><?= trim($row->subject_name) ?></textarea>
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label" for="">Issuer</label>
                    <textarea class="form-custom" name="issuer" id="issuer" rows="4" readonly><?= $row->issuer_name ?></textarea>
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label" for="">Valido desde</label>
                    <input type="text" class="form-custom" name="validFrom" value="<?= $row->valid_from ?>" id="validFrom" readonly>
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label" for="">Valido hasta</label>
                    <input type="text" class="form-custom" name="validTo" value="<?= $row->valid_to ?>" id="validTo" readonly>
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label" for="">No Serial</label>
                    <input type="text" class="form-custom" name="noSerial" value="<?= $row->serial_number ?>" id="noSerial" readonly>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>