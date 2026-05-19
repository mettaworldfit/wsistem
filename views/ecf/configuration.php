<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Configuración de facturación electrónica</h1>
        </div>
    </div>
</div>




<div class="generalContainer">


    <div class="container-company">
        <div class="column-company">
            <h3>Datos de la empresa</h3>

            <form id="formEmisor">
                <div class="form-group col-sm-12">
                    <label class="form-check-label label-nomb" for="">RNC<span class="text-danger">*</span></label>
                    <input class="form-custom" type="text" name="rnc" id="rnc" required>
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label label-nomb" for="">Razón social</label>
                    <input class="form-custom" type="text" name="name" id="name" required>
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label label-nomb" for="">Correo</label>
                    <input class="form-custom" type="email" name="mail" id="mail" autocomplete="off">
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label label-nomb" for="">Dirección</label>
                    <input class="form-custom" type="email" name="address" id="address">
                </div>

                <button class="btn-link" type="submit">
                    <p>Guardar</p>
                </button>
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
                                Arrastra el archivo aquí
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
                <input type="password" name="passcert" class="form-custom" id="passcert" autocomplete="off">
            </div>

            <div class="form-group col-sm-12">
                <label class="form-check-label" for="">Subject</label>
                <textarea class="form-custom" name="subject" id="subject"></textarea>
            </div>

            <div class="form-group col-sm-12">
                <label class="form-check-label" for="">Inssuer</label>
                <textarea class="form-custom" name="inssuer" id="inssuer"></textarea>
            </div>

            <div class="form-group col-sm-12">
                <label class="form-check-label" for="">Valido desde</label>
                <input type="text" class="form-custom" name="validFrom" id="validFrom">
            </div>

            <div class="form-group col-sm-12">
                <label class="form-check-label" for="">Valido hasta</label>
                <input type="text" class="form-custom" name="validTo" id="validTo">
            </div>

             <div class="form-group col-sm-12">
                <label class="form-check-label" for="">No Serial</label>
                <input type="text" class="form-custom" name="noSerial" id="noSerial">
            </div>
        </div>
    </div>
</div>