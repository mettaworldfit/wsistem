<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1><i class="fas fa-at"></i> Configuración factura electrónica</h1>
        </div>
    </div>

    <p class="title-info">Configura los datos de las facturas electrónicas.</p>
</div>

<div class="generalContainer-medium">

    <form action="" onsubmit="event.preventDefault(); ConfigElectronicInv();">

        <?php $config = Help::ConfigElectronicInvoice(); ?>

            <div class="container row">

                <div class="form-group col-md-8">
                    <div class="form-group d-flex">
                        <label for="compañía" class="col-sm-3 text-right ">Compañía</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['empresa_name'] ?>" name="compañía" id="company">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Compañía"
                            data-content="Escribe el nombre de tu negocio."><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="logo" class="col-sm-3 text-right ">Logo_url</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['logo_url'] ?>" name="logo" id="logo">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Logo URL"
                            data-content="Carga un logo con dimensiones 200x100 desde imgur.com"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="facebook" class="col-sm-3 text-right ">Facebook</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['link_facebook'] ?>" name="facebook" id="facebook">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Facebook"
                            data-content="Ingresa el link de tu perfil de facebook"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="instagram" class="col-sm-3 text-right ">Instagram</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['link_instagram'] ?>" name="instagram" id="instagram">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Instagram"
                            data-content="Ingresa el link de tu perfil de instagram"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="whatsapp" class="col-sm-3 text-right ">Whatsapp</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['link_whatsapp'] ?>" name="whatsapp" id="whatsapp">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Whatsapp"
                            data-content="Ingresa el link de tu perfil de whatsapp"><i
                                class="far fa-question-circle"></i></a>
                    </div>


                    <div class="form-group d-flex">
                        <label for="email" class="col-sm-3 text-right ">Correo</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['correo_servidor'] ?>" name="email" id="email">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Correo"
                            data-content="Utiliza un correo gmail o un correo con nombre de dominio"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="contraseña" class="col-sm-3 text-right ">Contraseña</label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['password'] ?>" name="contraseña" id="password" >
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Contraseña"
                            data-content="Utiliza la contraseña de tu servidor o correo"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="host" class="col-sm-3 text-right ">Servidor<span class="text-danger">*</span></label>
                        <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['servidor'] ?>" name="host" id="host" required>
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Servidor"
                            data-content="Escribe el nombre de tu servidor SMTP, si es gmail utiliza smtp.gmail.com o el de tu proveedor de servicio SMTP"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="port" class="col-sm-3 text-right ">SMTP Secure</label>
                        <select class="form-custom col-sm-12 ml-3" name="" id="smtps">
                            <option value="no">Ninguno</option>
                            <option value="<?= $config['smtps'] ?>" selected><?= $config['smtps'] ?></option>
                            <option value="ssl">SSL (Secure Sockets Layer)</option>
                            <option value="tls">TLS (Transport Layer Security)</option>
                        </select>
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="SMTP Secure (SMTPS)"
                            data-content="Es un protocolo que protege el envío de correos electrónicos entre servidores mediante la encriptación y autenticación de los mensajes."><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group d-flex">
                        <label for="port" class="col-sm-3 text-right ">Puerto</label>
                        <input class="form-custom col-sm-4 ml-3" type="text" value="<?= $config['puerto'] ?>" name="port" id="port">
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Puerto"
                            data-content="Utiliza el puerto del servidor SMTP que utilizas si es gmail usa 587 o el de tu servidor SMTP"><i
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

    </form>
</div>