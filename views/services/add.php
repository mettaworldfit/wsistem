<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Nuevo servicio</h1>
        </div>


    </div>
</div>


<div class="generalContainer-medium">
    <form action="" onsubmit="event.preventDefault(); addService();">
        <div class="container row">

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
                <button class="btn-custom btn-green" type="submit" id="">
                    <i class="fas fa-plus"></i>
                    <p>Guardar</p>
                </button>
            </div>
        </div>
    </form>
</div>