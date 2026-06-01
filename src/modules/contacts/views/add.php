<div class="section-wrapper">
    <div class="align-content clearfix">
        <h1><i class="fas fa-id-card-alt"></i> Nuevo contacto</h1>
    </div>
</div>

<div class="area-data">
    <div class="col-data">
        <div class="col-legend">
            <h3>Datos generales</h3>
        </div>

        <div class="radio-list mt-2">
            <div class="radio-item ml-3">
                <input type="radio" name="contact" value="cliente" <?php if($_GET['type'] == 1) { ?> checked <?php } ?> id="radio1">
                <label for="radio1">Cliente</label>
            </div>

            <div class="radio-item ml-2">
                <input type="radio" name="contact" value="proveedor" <?php if($_GET['type'] == 0) { ?> checked <?php } ?> id="radio2" >
                <label for="radio2">Proveedor</label>
            </div>
        </div>

        <div class="form-group col-sm-12">
            <p class="title-info">
                Incluye los datos principales de tu nuevo contacto
            </p>
        </div>



        <form action="" onsubmit="event.preventDefault(); AddContact();">
            <div class="row col-md-12">

                <div class="form-group col-sm-7">
                    <label for="Nombre" class="form-check-label label-nomb">Nombre/Razón social<span
                            class="text-danger">*</span></label>
                    <input class="form-custom" type="text" name="" id="name" required>
                </div>

                <div class="form-group col-sm-5">
                    <label for="Apellidos" class="form-check-label">Apellidos</label>
                    <input class="form-custom" type="text" name="" id="lastname">
                </div>

                <div class="form-group col-sm-6" id="cod_client">
                    <label class="form-check-label" for="">RNC o Cédula</label>
                    <input class="form-custom" type="text" name="" maxlength="11" id="identity">
                </div>

                <div class="form-group col-sm-6">
                    <label class="form-check-label" for="">Dirección</label>
                    <select class="form-custom search" name="" id="address">
                        <option value="" selected disabled> --- </option>
                        <?php require_once "includes/direcciones.php"; ?>
                    </select>
                </div>

            </div> <!-- Row col-md-12 -->
            <br>

            <div class="col-legend">
                <h3>Información de contacto</h3>
            </div>

            <br>

            <div class="form-group col-sm-12">
                <p class="title-info">
                    Agrega estos datos para comunicarte en cualquier momento con tu contacto.
                </p>
            </div>

            <div class="row col-md-12">

                <div class="form-group col-sm-6 ">
                    <label class="form-check-label" for="">Télefono 1</label>
                    <input class="form-custom" type="number" name="" id="tel1">
                </div>

                <div class="form-group col-sm-6 ">
                    <label class="form-check-label" for="">Télefono 2</label>
                    <input class="form-custom" type="number" name="" id="tel2">
                </div>

                <div class="form-group col-sm-6 ">
                    <label class="form-check-label" for="">E-mail</label>
                    <input class="form-custom" type="email" name="" id="email">
                </div>

            </div>

            <div class="row col-content col-sm-12 d-flex justify-content-end">
                <button class="btn-green btn-custom " type="submit" id="">
                    <i class="fas fa-plus"></i>
                    <p>Guardar</p>
                </button>
            </div>
        </form>
    </div>
</div>