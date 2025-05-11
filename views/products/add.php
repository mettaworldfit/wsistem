<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Nuevo producto</h1>
        </div>

        <div class="float-right">
            <a href="" class="btn-custom btn-default" id="last_product_edit">
                <i class="far fa-edit"></i>
                <p>Editar producto</p>
            </a>
        </div>
    </div>

</div>


<div class="container-area">

    <div class="area-data">

        <div class="col-data">
            <div class="col-legend">
                <h3>información general</h3>
            </div>


            <!-- Información general -->

            <div class="row col-content col-sm-12">

                <div class="radio-list">
                    <div class="radio-item ml-3">
                        <input type="radio" name="tipoproducto" value="novariante" id="radio1" checked>
                        <label for="radio1">Normal</label>
                    </div>

                    <div class="radio-item ml-2">
                        <input type="radio" name="tipoproducto" value="variante" id="radio2">
                        <label for="radio2">Variante</label>
                    </div>
                </div>

                <div class="form-group col-sm-12">
                    <p class="title-info">
                        Indica si manejas productos con variantes como color, imei u otra.
                    </p>
                </div>


                <div class="form-group col-sm-9">
                    <label class="form-check-label label-nomb" for="">Nombre<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-12" type="text" name="name" id="product_name" placeholder=""
                        required>
                </div>

                <div class="form-group col-sm-3 mb-3">
                    <label class="form-check-label" for="">Código <a href="#" class="example-popover"
                            data-toggle="popover" title="Código producto"
                            data-content="Agrega un código único para identificar este producto. Ejemplo: CAS002"><i
                                class="far fa-question-circle"></i></a></label>
                    <input class="form-custom col-sm-12" type="text" name="product_code" placeholder=""
                        id="product_code">
                </div>

                <div class="form-group col-sm-5 mb-3">
                    <label class="form-check-label label-cant" for="">Cantidad<span class="text-danger">*</span></label>
                    <input class="form-custom col-sm-12" type="number" name="quantity" placeholder="Vacío"
                        id="product_quantity" required>
                </div>

                <div class="form-group col-sm-4 mb-3">
                    <label class="form-check-label" for="">Miníma cantidad <a href="#" class="example-popover"
                            data-toggle="popover" title="Miníma cantidad"
                            data-content="Activa una alerta y al vender este producto sabrás si has llegado al stock mínimo de tu inventario."><i
                                class="far fa-question-circle"></i></a></label>
                    <input class="form-custom col-sm-12" type="number" name="inventary_min" id="min_quantity"
                        placeholder="Vacío">
                </div>

                <div class="form-group col-sm-3 mb-3">
                    <label class="form-check-label" for="">Marca <a href="#" class="example-popover"
                            data-toggle="popover" title="Marca" data-content="Agrega la marca de este producto"><i
                                class="far fa-question-circle"></i></a></label>
                    <select class="form-custom search col-sm-12" name="brand" id="brand">
                        <option value="0" selected>Vacío</option>
                        <?php $brands = Help::showBrands();
                        while ($brand = $brands->fetch_object()): ?>
                            <option value="<?= $brand->marca_id ?>"><?= ucwords($brand->nombre_marca) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>


                <div class="form-group col-sm-5 mb-3">
                    <label class="form-check-label" for="">Categorías <a href="#" class="example-popover"
                            data-toggle="popover" title="Categorías"
                            data-content="Selecciona la categoría a la que pertenece este producto."><i
                                class="far fa-question-circle"></i></a></label>
                    <select class="form-custom search col-sm-12" name="category" id="category">
                        <option value="0" selected>Vacío</option>
                        <?php $categories = Help::showCategories();
                        while ($categorie = $categories->fetch_object()): ?>
                            <option value="<?= $categorie->categoria_id ?>"><?= ucwords($categorie->nombre_categoria) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group col-sm-4">
                    <label class="form-check-label" for="">Posición <a href="#" class="example-popover"
                            data-toggle="popover" title="Posición"
                            data-content="Agrega la posición donde se encuentra este producto: Ejemplo: A1."><i
                                class="far fa-question-circle"></i></a></label>
                    <select class="form-custom search col-sm-12" name="position" id="position">
                        <option value="0" selected>Vacío</option>
                        <?php $positions = Help::showPositions();
                        while ($position = $positions->fetch_object()): ?>
                            <option value="<?= $position->posicion_id ?>"><?= $position->referencia ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group col-sm-5 active">
                    <label class="form-check-label" for="">Proveedor <a href="#" class="example-popover"
                            data-toggle="popover" title="Proveedor"
                            data-content="Agrega el proveedor que vendió este producto."><i
                                class="far fa-question-circle"></i></a></label>
                    <select class="form-custom search col-sm-12" name="provider" id="providerID">
                        <option value="0" selected>Vacío</option>
                        <?php $providers = Help::showProviders();
                        while ($provider = $providers->fetch_object()): ?>
                            <option value="<?= $provider->proveedor_id ?>"><?= ucwords($provider->nombre_proveedor) ?>
                                <?= ucwords($provider->apellidos) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>



            </div>

        </div> <!-- col-data-1 -->


        <div class="col-data variant">
            <div class="col-legend">
                <h3>variantes</h3>
            </div>

            <!-- Variantes -->
            <div class="row col-content">

                <div class="form-group col-sm-12">
                    <p class="title-info">
                        Asigna variantes al producto para identificarlo por sus diferencias.
                    </p>
                </div>

                <div class="row col-sm-12">
                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Proveedor <a href="#" class="example-popover"
                                data-toggle="popover" title="Proveedor"
                                data-content="Agrega el proveedor que vendió este producto."><i
                                    class="far fa-question-circle"></i></a></label>
                        <select class="form-custom search col-sm-12" name="provider" id="provider">
                            <option value="0" selected>Vacío</option>
                            <?php $providers = Help::showProviders();
                            while ($provider = $providers->fetch_object()): ?>
                                <option value="<?= $provider->proveedor_id ?>"><?= ucwords($provider->nombre_proveedor) ?>
                                    <?= ucwords($provider->apellidos) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="row col-sm-12">
                    <div class="form-group col-sm-4">
                        <label class="form-check-label label-imei" for="">Imei</label>
                        <input class="form-custom col-sm-12" type="text" name="imei" maxlength="15" id="imei"
                            placeholder="">
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label label-imei" for="">Serial</label>
                        <input class="form-custom col-sm-12" type="text" name="serial" maxlength="17" id="serial"
                            placeholder="">
                    </div>
                </div>


                <div class="row col-sm-12">
                    <div class="form-group col-sm-4">
                        <label class="form-check-label label-imei" for="">Costo unitario</label>
                        <input class="form-custom col-sm-12" type="text" name="costo" id="cost" placeholder="">
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label label-colour" for="">Color</label>
                        <select class="form-custom search col-sm-12" name="colour" id="colour">
                            <option value="0" selected>Vacío</option>
                            <?php $colours = Help::showColours();
                            while ($colour = $colours->fetch_object()): ?>
                                <option value="<?= $colour->color_id ?>"><?= ucwords($colour->color) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label label-colour" for="">Producto en caja</label>
                        <select class="form-custom search col-sm-12" name="caja" id="box">
                            <option value="No" selected>No</option>
                            <option value="Si">Si</option>

                        </select>
                    </div>
                </div>

                <div class="add_variant">
                    <i class="fas fa-plus-circle"></i>
                    <a href="#" onClick="addVariantLocalStorage();">Agregar variante</a>
                </div>

                <br><br><br>
                <!-- Variantes -->
                <div class="col-sm-12 scroll-table">
                    <table id="Detalle" class="table-view table-view-success table_variant">
                        <thead>
                            <th>Proveedor</th>
                            <th>Imei</th>
                            <th>Serial</th>
                            <th>Color</th>
                            <th>Costo unitario</th>
                            <th>Caja</th>
                            <th></th>
                        </thead>

                        <tbody id="variant_list">

                        </tbody>
                    </table>
                </div>

                <!-- <div id="variant">

                </div> -->




            </div>
        </div><!-- col-data-2 -->

        <div class="col-data">
            <div class="col-legend">
                <h3>detalle de inventario</h3>
            </div>

            <!-- Detalle de inventario -->

            <div class="row col-content">

                <div class="form-group col-sm-12">
                    <p class="title-info">
                        Distribuye y controla las cantidades de tus productos en diferentes lugares
                    </p>
                </div>

                <div class="form-group col-sm-4">
                    <label class="form-check-label" for="">Almacen <a href="#" class="example-popover"
                            data-toggle="popover" title="Almacen"
                            data-content="Agrega el almacen donde se encuentra este producto."><i
                                class="far fa-question-circle"></i></a></label>
                    <select class="form-custom  search col-sm-12" name="warehouse" id="warehouse" <?php if ($_SESSION['identity']->nombre_rol != 'administrador') {
                        echo 'disabled';
                    } ?>>
                        <?php $warehouses = Help::showWarehouses();
                        while ($warehouse = $warehouses->fetch_object()): ?>
                            <option value="<?= $warehouse->almacen_id ?>"><?= ucwords($warehouse->nombre_almacen) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

            </div>
        </div><!-- col-data-3 -->


        <div class="col-data">
            <div class="col-legend">
                <h3>lista de precios</h3>
            </div>

            <!-- Lista de precios -->
            <div class="row col-content">

                <div class="form-group col-sm-12">
                    <p class="title-info">
                        Asigna varios precios con valor fijo de descuento sobre el precio base.
                    </p>
                </div>

                <div class="form-group col-sm-6">
                    <select class="form-custom search col-sm-12" name="price_list" id="price_list" <?php if ($_SESSION['identity']->nombre_rol != 'administrador') {
                        echo 'disabled';
                    } ?>>
                        <option value="0" selected>Vacío</option>
                        <?php $lists = Help::showPrice_lists();
                        while ($list = $lists->fetch_object()): ?>
                            <option value="<?= $list->lista_id ?>"><?= $list->nombre_lista ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group col-sm-6">
                    <input class="form-custom col-sm-12" type="number" name="" id="list_value" <?php if ($_SESSION['identity']->nombre_rol != 'administrador') {
                        echo 'disabled';
                    } ?>>
                </div>

                <br><br>
                <!-- Listas -->
                <div id="list">

                </div>

                <?php if ($_SESSION['identity']->nombre_rol == 'administrador'): ?>
                    <div class="add_price_list">
                        <i class="fas fa-plus-circle"></i>
                        <a href="#">Agregar lista de precio</a>
                    </div>
                <?php endif; ?>


            </div>
        </div><!-- col-data-4 -->


    </div> <!-- area-data -->

    <div class="area-price">
        <div class="col-price">

            <div class="row col-content">

                <div class="form-group col-sm-12">
                    <label class="form-check-label" for="">Costo Promedio</label>
                    <input type="number" name="price_in" class="form-custom" id="inputPrice_in" placeholder="0.00">
                </div>

                <div class="form-group col-sm-12">
                    <label class="form-check-label label-price" for="">Precio<span class="text-danger">*</span> </label>
                    <input type="number" name="price_out" class="form-custom" placeholder="0.00" id="inputPrice_out"
                        required>
                </div>

                <div class="form-group col-sm-6 mb-3">
                    <label class="form-check-label" for="">Impuesto</label>
                    <select class="form-custom search col-sm-12" name="tax" id="tax">
                        <option value="Vacío" selected>Vacío</option>
                        <?php $taxes = Help::showTaxes();
                        while ($tax = $taxes->fetch_object()): ?>
                            <option value="<?= $tax->impuesto_id ?>"><?= $tax->nombre_impuesto ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group col-sm-6 mb-3">
                    <label class="form-check-label" for="">Oferta</label>
                    <select class="form-custom search col-sm-12" name="offer" id="offer" <?php if ($_SESSION['identity']->nombre_rol != 'administrador') {
                        echo 'disabled';
                    } ?>>
                        <option value="Vacío" selected>Vacío</option>
                        <?php $offers = Help::showOffers();
                        while ($offer = $offers->fetch_object()): ?>
                            <option value="<?= $offer->oferta_id ?>"><?= $offer->nombre_oferta ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group col-sm-12">
                    <div class="form-group">
                        <div class="row-price">
                            <span>DOP</span>
                            <input type="text" class="invisible-input col-sm-12 text-left" id="totalPrice" disabled>
                        </div>
                        <input type="hidden" name="" value="" id="FinalPrice_out">
                    </div>
                </div>

                <br>
                <div class="col-sm-12 d-flex justify-content-end">
                    <a class="btn-custom btn-red" href="<?= base_url ?>product/index">
                        <i class="fas fa-times"></i>
                        <p>Cancelar</p>
                    </a>
                    <a href="#" class="btn-custom btn-blue" id="createProduct">
                        <i class="fas fa-plus"></i>
                        <p>Guardar</p>
                        </a>
                </div>

            </div>

        </div>
    </div> <!-- Area price -->

</div> <!-- container-area -->