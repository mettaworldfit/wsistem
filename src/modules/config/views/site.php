<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1> Datos del sitio</h1>
        </div>
    </div>

    <p class="title-info">Configura los datos principles del negocio.</p>
</div>

<div class="generalContainer-medium">
    <form action="" method="POST" id="formSite" enctype="multipart/form-data">

        <?php $config = Help::configSiteData(); ?>

        <div class="container row">
            <div class="form-group col-md-8">

                <!-- ================= LOGO ================= -->

                <div id="preview-img" style="width: 60%;">
                    <?php if ($config['logo_path'] != "") : ?>
                        <img src="<?= dir_root . $config['logo_path'] ?>" onerror="this.onerror=null; this.src='<?= base_url ?>public/imagen/sistem/no-imagen.png';" alt="">
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 50%;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags-icon lucide-tags">
                            <path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z" />
                            <path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193" />
                            <circle cx="10.5" cy="6.5" r=".5" fill="currentColor" />
                        </svg>
                    <?php endif; ?>
                </div>

                <div class="form-group d-flex">
                    <label class="col-sm-3 text-right">Imagen</label>
                    <input class="form-custom col-sm-6 ml-3"
                        type="file" name="logo" accept="image/*">
                </div>

                <div class="form-group d-flex">
                    <label for="logo" class="col-sm-3 text-right ">Logo_url</label>
                    <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['logo_url'] ?>" name="logo_url">
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Logo URL"
                        data-content="Carga un logo con dimensiones 200x100 desde imgur.com"><i
                            class="far fa-question-circle"></i></a>
                </div>

                <div class="form-group d-flex">
                    <label for="slogan" class="col-sm-3 text-right ">Slogan</label>
                    <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['slogan'] ?>" name="slogan">
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Slogan"
                        data-content="Ingresa el slogan de tu empresa"><i
                            class="far fa-question-circle"></i></a>
                </div>

                <div class="form-group d-flex">
                    <label for="compañía" class="col-sm-3 text-right ">Nombre del negocio</label>
                    <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['empresa_name'] ?>" name="site_name">
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Compañía"
                        data-content="Escribe el nombre de tu negocio."><i
                            class="far fa-question-circle"></i></a>
                </div>

                <div class="form-group d-flex">
                    <label for="direccion" class="col-sm-3 text-right ">Dirección</label>
                    <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['direccion'] ?>" name="address">
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Dirección"
                        data-content="Ingresa la dirección de tu empresa."><i class="far fa-question-circle"></i></a>
                </div>

                <div class="form-group d-flex">
                    <label for="direccion" class="col-sm-3 text-right ">Correo</label>
                    <input class="form-custom col-sm-12 ml-3" type="mail" value="<?= $config['correo_adm'] ?>" name="mail">
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Correo administración"
                        data-content="Ingresa tu correo electronico."><i class="far fa-question-circle"></i></a>
                </div>

                <div class="form-group d-flex">
                    <label for="tel" class="col-sm-3 text-right ">Teléfono</label>
                    <input class="form-custom col-sm-12 ml-3" type="text" value="<?= $config['telefono'] ?>" name="tel">
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Teléfono"
                        data-content="Ingresa el teléfono de tu empresa."><i
                            class="far fa-question-circle"></i></a>
                </div>

            </div>
        </div>

        <p class="info-sm mt-2">
            Los campos marcados con <span class="text-danger">*</span> son obligatorios
        </p>

        <div class="buttons clearfix">
            <div class="floatButtons">
                <button type="submit" class="btn-custom btn-green">
                    <i class="fas fa-save"></i>
                    <p>Guardar</p>
                </button>
            </div>
        </div>
    </form>
</div>