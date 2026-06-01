<?php $warehouse = Help::showWarehouseID($_GET['id']);
while ($element = $warehouse->fetch_object()): ?>

    <div class="section-wrapper">
        <div class="align-content clearfix">
            <div class="float-left">
                <h1><i class="far fa-edit"></i> Editar Almacen</h1>
            </div>


        </div>
    </div>



    <div class="generalContainer-medium">
        <form action="" onsubmit="event.preventDefault(); UpdateWarehouse('<?= $element->almacen_id ?>');">
            <div class="container row">

                <div class="form-group col-md-8">
                    <div class="form-group d-flex">
                        <label for="Nombre" class="col-sm-3 text-right ">Nombre<span class="text-danger">*</span></label>
                        <input class="form-custom col-sm-12 ml-3" type="text" name="" id="warehouse_name"
                            value="<?= $element->nombre_almacen ?>" required>
                        <a href="#" class=" ml-1 example-popover" data-toggle="popover" title="Popover title"
                            data-content="And here's some amazing content. It's very engaging. Right?"><i
                                class="far fa-question-circle"></i></a>
                    </div>

                    <div class="form-group mt-3 d-flex">
                        <label for="Descripción" class="col-sm-4 text-right ">Descripción</label>
                        <textarea class="form-control" name="" id="warehouse_comment" cols="26" rows="5"
                            maxlength="200"><?= $element->descripcion ?></textarea>
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

<?php endwhile; ?>