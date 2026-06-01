<?php while ($element = $piece->fetch_object()): ?>

    <input type="hidden" name="" value="<?= $element->IDpieza ?>" id="piece_id">

    <div class="section-wrapper">
        <div class="align-content clearfix">
            <h1><i class="far fa-edit"></i> Editar pieza</h1>
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


                    <div class="form-group col-sm-9">
                        <label class="form-check-label label-nomb" for="">Nombre<span class="text-danger">*</span></label>
                        <input class="form-custom col-sm-12" type="text" value="<?= ucwords($element->nombre_pieza) ?>"
                            name="name" id="piece_name" placeholder="" required>
                    </div>

                    <div class="form-group col-sm-3 mb-3">
                        <label class="form-check-label" for="">Código <a href="#" class="example-popover"
                                data-toggle="popover" title="Código pieza"
                                data-content="Agrega un código único para identificar esta pieza. Ejemplo: CAS002"><i
                                    class="far fa-question-circle"></i></a></label>
                        <input class="form-custom col-sm-12" type="text" value="<?= $element->cod_pieza ?>"
                            name="piece_code" placeholder="" id="input_piece_code">
                    </div>

                    <div class="form-group col-sm-5 mb-3">
                        <label class="form-check-label label-cant" for="">Cantidad<span class="text-danger">*</span></label>
                        <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?>
                            <input class="form-custom col-sm-12" type="number" value="<?= $element->cantidad ?>" name="quantity"
                                placeholder="Vacío" id="input_quantity" required>
                        <?php } else { ?>
                            <input class="form-custom col-sm-12" type="number" value="<?= $element->cantidad ?>" name="quantity"
                                placeholder="Vacío" disabled>
                            <input type="hidden" name="quantity" value="<?= $element->cantidad ?>" id="input_quantity">

                        <?php } ?>
                    </div>

                    <div class="form-group col-sm-4 mb-3">
                        <label class="form-check-label" for="">Miníma cantidad <a href="#" class="example-popover"
                                data-toggle="popover" title="Miníma cantidad"
                                data-content="Activa una alerta y al vender esta pieza sabrás si has llegado al stock mínimo de tu inventario."><i
                                    class="far fa-question-circle"></i></a></label>
                        <input class="form-custom col-sm-12" type="number" name="inventary_min"
                            value="<?= $element->cantidad_min ?>" id="input_min_quantity" placeholder="Vacío">
                    </div>

                    <div class="form-group col-sm-3 mb-3">
                        <label class="form-check-label" for="">Marca <a href="#" class="example-popover"
                                data-toggle="popover" title="Marca" data-content="Agrega la marca de esta pieza"><i
                                    class="far fa-question-circle"></i></a></label>
                        <select class="form-custom search col-sm-12" name="brand" id="brand">
                            <?php if ($element->marca_id > 0) { ?>
                                <option value="<?= $element->marca_id ?>" selected><?= $element->nombre_marca ?></option>
                                <option value="0">Vacío</option>
                            <?php } else { ?>
                                <option value="0" selected>Vacío</option>
                            <?php } ?>

                            <?php $brands = Help::showBrands();
                            while ($brand = $brands->fetch_object()): ?>
                                <option value="<?= $brand->marca_id ?>"><?= $brand->nombre_marca ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>


                    <div class="form-group col-sm-5 mb-3">
                        <label class="form-check-label" for="">Categorías <a href="#" class="example-popover"
                                data-toggle="popover" title="Categorías"
                                data-content="Selecciona la categoría a la que pertenece esta pieza."><i
                                    class="far fa-question-circle"></i></a></label>
                        <select class="form-custom search col-sm-12" name="category" id="category">
                            <?php if ($element->categoria_id > 0) { ?>
                                <option value="<?= $element->categoria_id ?>" selected><?= $element->nombre_categoria ?>
                                </option>
                                <option value="0">Vacío</option>
                            <?php } else { ?>
                                <option value="0" selected>Vacío</option>
                            <?php } ?>

                            <?php $categories = Help::showCategories();
                            while ($categorie = $categories->fetch_object()): ?>
                                <option value="<?= $categorie->categoria_id ?>"><?= $categorie->nombre_categoria ?></option>
                            <?php endwhile; ?>
                        </select>

                    </div>

                    <div class="form-group col-sm-4">
                        <label class="form-check-label" for="">Posición <a href="#" class="example-popover"
                                data-toggle="popover" title="Posición"
                                data-content="Agrega la posición donde se encuentra esta pieza: Ejemplo: A1."><i
                                    class="far fa-question-circle"></i></a></label>
                        <select class="form-custom  search col-sm-12" name="position" id="position">
                            <?php if ($element->posicion_id > 0) { ?>
                                <option value="<?= $element->posicion_id ?>" selected><?= $element->referencia ?></option>
                                <option value="0">Vacío</option>
                            <?php } else { ?>
                                <option value="0" selected>Vacío</option>
                            <?php } ?>

                            <?php $positions = Help::showPositions();
                            while ($position = $positions->fetch_object()): ?>
                                <option value="<?= $position->posicion_id ?>"><?= $position->referencia ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-5 active">
                        <label class="form-check-label" for="">Proveedor <a href="#" class="example-popover"
                                data-toggle="popover" title="Proveedor"
                                data-content="Agrega el proveedor que vendió esta pieza."><i
                                    class="far fa-question-circle"></i></a></label>
                        <select class="form-custom search col-sm-12" name="provider" id="provider">
                            <?php if ($element->proveedor_id > 0) { ?>
                                <option value="<?= $element->proveedor_id ?>" selected>
                                    <?= ucwords($element->nombre_proveedor) ?>
                                </option>
                                <option value="0">Vacío</option>
                            <?php } else { ?>
                                <option value="0" selected>Vacío</option>
                            <?php } ?>


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
                                data-content="Agrega el almacen donde se encuentra esta pieza."><i
                                    class="far fa-question-circle"></i></a></label>
                        <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?>
                            <select class="form-custom  search col-sm-12" name="warehouse" id="warehouse">
                                <option value="<?= $element->almacen_id ?>" selected><?= $element->nombre_almacen ?></option>
                                <?php $warehouses = Help::showWarehouses();
                                while ($warehouse = $warehouses->fetch_object()): ?>
                                    <option value="<?= $warehouse->almacen_id ?>"><?= $warehouse->nombre_almacen ?></option>
                                <?php endwhile; ?>
                            </select>
                        <?php } else { ?>
                            <select class="form-custom  search col-sm-12" name="warehouse" id="warehouse" disabled>
                                <option value="<?= $element->almacen_id ?>" selected><?= $element->nombre_almacen ?></option>
                            </select>
                        <?php } ?>

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
                            <?php $lists = Help::loadPriceLists();
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

                    <div id="priceList">

                        <?php $lists_piece = Help::loadPiecePriceListsId($element->IDpieza);
                        while ($list_piece = $lists_piece->fetch_object()): ?>
                            <div class="form-group col-sm-6 list">
                                <input class="form-custom col-sm-12" type="text" name=""
                                    value="<?= $list_piece->nombre_lista ?>" identity="<?= $list_piece->lista_id ?>" disabled>
                            </div>

                            <div class="form-group col-sm-6 list">
                                <input class="form-custom col-sm-10" type="text" name=""
                                    value="<?= number_format($list_piece->valor) ?>" identity="<?= $list_piece->lista_id ?>"
                                    disabled>
                                <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?>
                                    <span class="action-delete"
                                        onclick="deleteItemPriceList('<?= $list_piece->pieza_lista_id ?>')"
                                        identity="<?= $list_piece->lista_id ?>"><i class="far fa-minus-square"></i></span>
                                <?php } ?>
                            </div>
                        <?php endwhile; ?>

                    </div>

                    <?php if ($_SESSION['identity']->nombre_rol == 'administrador'): ?>
                        <div class="add_list_to_piece">
                            <i class="fas fa-plus-circle"></i>
                            <a href="#" onclick="addPriceListsDb('<?= $id ?>');">Agregar lista de precio</a>
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
                        <input type="number" name="price_in" class="form-custom" value="<?= $element->precio_costo ?>"
                            id="inputPrice_in" placeholder="0.00" <?php if ($_SESSION['identity']->nombre_rol != 'administrador') {
                                echo 'disabled';
                            } ?>>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-price" for="">Precio<span class="text-danger">*</span> </label>
                        <input type="number" name="price_out" class="form-custom" value="<?= $element->precio_unitario ?>"
                            placeholder="0.00" id="inputPrice_out" required>
                    </div>

                    <div class="form-group col-sm-12 mb-3">
                        <label class="form-check-label" for="">Oferta</label>
                        <select class="form-custom search col-sm-12" name="offer" id="offer" <?php if ($_SESSION['identity']->nombre_rol != 'administrador'): ?> disabled <?php endif; ?>>
                            <?php if ($element->oferta_id > 0) { ?>
                                <option value="0">Vacío</option>
                                <option value="<?= $element->oferta_id ?>" selected><?= ucwords($element->nombre_oferta) ?>
                                </option>
                            <?php } else { ?>
                                <option value="0" selected>Vacío</option>
                            <?php } ?>

                            <?php $offers = Help::showOffers();
                            while ($offer = $offers->fetch_object()): ?>
                                <option value="<?= $offer->oferta_id ?>"><?= ucwords($offer->nombre_oferta) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-12">
                        <div class="form-group">
                            <div class="row-price">
                                <span>DOP</span>
                                <input type="text" class="invisible-input col-sm-12 text-left"
                                    value="<?= number_format($element->precio_unitario, 2) ?>" id="precioTotal" disabled>
                            </div>
                            <input type="hidden" name="" value="" id="FinalPrice_out">
                        </div>
                    </div>

                    <div class="col-sm-12 d-flex justify-content-end">
                        <a class="btn-custom btn-red" href="<?= base_url ?>pieces/index">
                            <i class="fas fa-times"></i>
                            <p>Cancelar</p>
                        </a>
                        <button type="submit" class="btn-custom btn-blue ml-2" id="editPiece">
                            <i class="fas fa-plus"></i>
                            <p>Guardar</p>
                        </button>
                    </div>

                </div>

            </div>
        </div> <!-- Area price -->

    </div> <!-- container-area -->

<?php endwhile; ?>