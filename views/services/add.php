<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Nuevo servicio</h1>
        </div>
    </div>
</div>

<div class="generalContainer-medium">
    <form action="">
        <div class="container row">

            <div class="form-group col-sm-5">
                <!-- Aquí se mostrará el mensaje de éxito o error -->
                <div id="service-content-img">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags-icon lucide-tags">
                        <path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z" />
                        <path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193" />
                        <circle cx="10.5" cy="6.5" r=".5" fill="currentColor" />
                    </svg>
                </div>

                <form action="" method="POST" enctype="multipart/form-data" id="uploadImg">
                    <input class="form-custom" type="file" name="service_image" id="service_image" accept="image/*" required>
                </form>
            </div>

            <div class="form-group col-md-8">
                <div class="form-group d-flex">
                    <label for="" class="col-sm-3 text-right ">Nombre<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-12 ml-3" type="text" name="" id="service_name" required>
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Popover title"
                        data-content="And here's some amazing content. It's very engaging. Right?"><i
                            class="far fa-question-circle"></i></a>
                </div>
            </div>

            <div class="form-group col-md-8">
                <div class="form-group d-flex">
                    <label for="" class="col-sm-3 text-right ">Costo</label>
                    <input class="form-custom col-sm-6 ml-3" type="number" name="" id="service_cost">
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Popover title"
                        data-content="And here's some amazing content. It's very engaging. Right?"><i
                            class="far fa-question-circle"></i></a>
                </div>
            </div>

            <div class="form-group col-md-8">
                <div class="form-group d-flex">
                    <label for="" class="col-sm-3 text-right ">Precio</label>
                    <input class="form-custom col-sm-6 ml-3" type="number" name="" id="service_price">
                    <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Popover title"
                        data-content="And here's some amazing content. It's very engaging. Right?"><i
                            class="far fa-question-circle"></i></a>
                </div>
            </div>

        </div>

        <p class="info-sm mt-2">Los campos marcados con <span class="text-danger">*</span> son obligatorios</p>

        <div class="buttons clearfix">
            <div class="floatButtons">
                <button class="btn-custom btn-green" type="button" id="addService">
                    <i class="fas fa-plus"></i>
                    <p>Guardar</p>
                </button>
            </div>
        </div>
    </form>
</div>