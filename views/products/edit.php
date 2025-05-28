<?php while ($element = $product->fetch_object()): ?>

    <input type="hidden" name="" value="<?= $element->IDproducto ?>" id="product_id">
    <input type="hidden" name="" value="<?= $avg ?>" id="average_cost">

    <div class="section-wrapper">
        <div class="float-left align-content clearfix">
            <h1><i class="far fa-edit"></i> Editar producto</h1>
        </div>

        <div class="float-right">
            <button type="button" class="btn-custom btn-orange" data-toggle="modal" data-target="#history" id="product_history">
                <i class="fas fa-poll-h"></i>
                <p>Historial</p>
            </button>
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

                    <?php $vverify = Help::Count_Variant_pID($_GET['id']); ?>

                    <div class="radio-list">
                        <input type="hidden" name="" value="<?= $vverify->variante_total ?>" id="TotalVariant">
                        <div <?php if ($vverify->variante_total == 0) { ?> class="radio-item ml-3" <?php } else { ?>
                                style="display:none" <?php } ?>>
                            <input type="radio" name="tipoproducto" value="novariante" id="radio1" <?php if ($vverify->variante_total == 0) { ?> checked <?php } ?>>
                            <label for="radio1">Normal</label>
                        </div>

                        <div <?php if ($vverify->variante_total > 0) { ?> class="radio-item ml-3" <?php } else { ?>
                                style="display:none" <?php } ?>>
                            <input type="radio" name="tipoproducto" value="variante" id="radio2" <?php if ($vverify->variante_total > 0) { ?> checked <?php } ?>>
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
                        <input class="form-custom col-sm-12" type="text" name="name"
                            value="<?= $element->nombre_producto ?>" id="product_name" placeholder="" required>
                    </div>

                    <div class="form-group col-sm-3 mb-3">
                        <label class="form-check-label" for="">Código <a href="#" class="example-popover"
                                data-toggle="popover" title="Código producto"
                                data-content="Agrega un código único para identificar este producto. Ejemplo: CAS002"><i
                                    class="far fa-question-circle"></i></a></label>
                        <input class="form-custom col-sm-12" type="text" name="product_code"
                            value="<?= $element->cod_producto ?>" placeholder="" id="product_code">
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
                                data-content="Activa una alerta y al vender este producto sabrás si has llegado al stock mínimo de tu inventario."><i
                                    class="far fa-question-circle"></i></a></label>
                        <input class="form-custom col-sm-12" type="number" name="inventary_min"
                            value="<?= $element->cantidad_min ?>" id="input_min_quantity" placeholder="Vacío">
                    </div>

                    <div class="form-group col-sm-3 mb-3">
                        <label class="form-check-label" for="">Marca <a href="#" class="example-popover"
                                data-toggle="popover" title="Marca" data-content="Agrega la marca de este producto"><i
                                    class="far fa-question-circle"></i></a></label>
                        <select class="form-custom search col-sm-12" name="brand" id="brand">
                            <?php if ($element->marca_id > 0) { ?>
                                <option value="0">Vacío</option>
                                <option value="<?= $element->marca_id ?>" selected><?= ucwords($element->nombre_marca) ?>
                                </option>
                            <?php } else { ?>
                                <option value="0" selected>Vacío</option>
                            <?php } ?>

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
                            <?php if ($element->categoria_id > 0) { ?>
                                <option value="0">Vacío</option>
                                <option value="<?= $element->categoria_id ?>" selected>
                                    <?= ucwords($element->nombre_categoria) ?></option>
                            <?php } else { ?>
                                <option value="0" selected>Vacío</option>
                            <?php } ?>

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
                        <select class="form-custom  search col-sm-12" name="position" id="position">
                            <?php if ($element->posicion_id > 0) { ?>
                                <option value="0">Vacío</option>
                                <option value="<?= $element->posicion_id ?>" selected><?= $element->referencia ?></option>
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
                                data-content="Agrega el proveedor que vendió este producto."><i
                                    class="far fa-question-circle"></i></a></label>
                        <select class="form-custom search col-sm-12" name="provider" id="providerID">
                            <option value="0" selected>Vacío</option>
                            <?php $providers = Help::showProviders();
                            while ($provider = $providers->fetch_object()): ?>
                                <option value="<?= $provider->proveedor_id ?>"><?= ucwords($provider->nombre_proveedor) ?>
                                    <?= ucwords($provider->apellidos) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>



                </div>

            </div> <!-- col-data-1 -->


            <div class="col-data">
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

                    <?php if ($_SESSION['identity']->nombre_rol == 'administrador'): ?>

                        <div class="row col-sm-12">
                            <div class="form-group col-sm-4">
                                <label class="form-check-label" for="">Proveedor <a href="#" class="example-popover"
                                        data-toggle="popover" title="Proveedor"
                                        data-content="Agrega el proveedor que vendió este producto."><i
                                            class="far fa-question-circle"></i></a></label>
                                <select class="form-custom  search col-sm-12" name="provider" id="provider">
                                    <?php if ($element->proveedor_id > 0) { ?>
                                        <option value="0">Vacío</option>
                                        <option value="<?= $element->proveedor_id ?>" selected>
                                            <?= ucwords($element->nombre_proveedor) ?></option>
                                    <?php } else { ?>
                                        <option value="0" selected>Vacío</option>
                                    <?php } ?>

                                    <?php $providers = Help::showProviders();
                                    while ($provider = $providers->fetch_object()): ?>
                                        <option value="<?= $provider->proveedor_id ?>"><?= ucwords($provider->nombre_proveedor) ?>
                                            <?= ucwords($provider->apellidos) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row col-sm-12">
                            <div class="form-group col-sm-6">
                                <label class="form-check-label label-imei" for="">Imei</label>
                                <input class="form-custom col-sm-12" type="text" name="name" maxlength="15" id="imei"
                                    placeholder="">
                            </div>

                            <div class="form-group col-sm-6">
                                <label class="form-check-label label-serial" for="">Serial</label>
                                <input class="form-custom col-sm-12" type="text" name="name" maxlength="17" id="serial"
                                    placeholder="">
                            </div>
                        </div>


                        <div class="row col-sm-12">
                            <div class="form-group col-sm-3">
                                <label class="form-check-label label-cost" for="">Costo unitario</label>
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
                                <label class="form-check-label label-box" for="">Producto en caja</label>
                                <select class="form-custom search col-sm-12" name="caja" id="box">
                                    <option value="No" selected>No</option>
                                    <option value="Si">Si</option>

                                </select>
                            </div>
                        </div>


                        <div class="add_variant">
                            <i class="fas fa-plus-circle"></i>
                            <a href="#" onClick="addVariantDb();">Agregar variante</a>
                        </div>


                    <?php endif; ?>

                    <br><br><br>
                    <!-- Variantes -->
                    <div class="col-sm-12 scroll-table">
                        <table id="Detalle" class="table-view table-view-success">
                            <thead>
                                <th>Proveedor</th>
                                <th>Imei</th>
                                <th>Serial</th>
                                <th>Color</th>
                                <th>Costo unitario</th>
                                <th>Caja</th>
                                <th>Entrada</th>
                                <th></th>
                            </thead>


                            <tbody id="variant_list">

                                <?php $variants = Help::showVariant_with_productID($element->IDproducto);
                                while ($variant = $variants->fetch_object()): ?>
                                    <tr>
                                        <td><?= ucwords($variant->nombre_proveedor ?? '') ?></td>
                                        <td><?= $variant->imei ?></td>
                                        <td><?= $variant->serial ?></td>
                                        <td><?= ucwords($variant->color ?? '') ?></td>
                                        <td><?= number_format($variant->costo_unitario) ?></td>
                                        <td><?= $variant->caja ?></td>
                                        <td><?= $variant->entrada ?></td>
                                        <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?>
                                            <td> <span class="action-delete"
                                                    onclick="deleteVariantDb('<?= $variant->var_id ?>','<?= $variant->costo_unitario ?>')"><i
                                                        class="far fa-minus-square"></i></span></td>
                                        <?php } else { ?>
                                            <td></td>
                                        <?php } ?>

                                    </tr>
                                <?php endwhile; ?>

                            </tbody>



                        </table>
                    </div>



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

                        <?php $lists_product = Help::loadProductPriceListsId($element->IDproducto);
                        while ($list_product = $lists_product->fetch_object()): ?>
                            <div class="form-group col-sm-6 list">
                                <input class="form-custom col-sm-12" type="text" name=""
                                    value="<?= $list_product->nombre_lista ?>" identity="<?= $list_product->lista_id ?>"
                                    disabled>
                            </div>

                            <div class="form-group col-sm-6 list">
                                <input class="form-custom col-sm-10" type="text" name=""
                                    value="<?= number_format($list_product->valor) ?>" identity="<?= $list_product->lista_id ?>"
                                    disabled>
                                <?php if ($_SESSION['identity']->nombre_rol == 'administrador'): ?>
                                    <span class="action-delete"
                                        onclick="deleteItemPriceList('<?= $list_product->producto_lista_id ?>');"
                                        identity="<?= $list_product->lista_id ?>"><i class="far fa-minus-square"></i></span>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>

                    </div>

                    <?php if ($_SESSION['identity']->nombre_rol == 'administrador'): ?>
                        <div class="add_list_to_product">
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
                        <input type="number" name="price_in" class="form-custom col-sm-12"
                            value="<?= $element->precio_costo ?>" id="inputPrice_in" placeholder="0.00" <?php if ($_SESSION['identity']->nombre_rol != 'administrador') {
                                  echo 'disabled';
                              } ?>>
                    </div>

                    <div class="form-group col-sm-12">
                        <label class="form-check-label label-price" for="">Precio<span class="text-danger">*</span> </label>
                        <input type="number" name="price_out" class="form-custom" value="<?= $element->precio_unitario ?>"
                            placeholder="0.00" id="inputPrice_out" required>

                    </div>

                    <div class="form-group col-sm-6 mb-3">
                        <label class="form-check-label" for="">Impuesto</label>
                        <select class="form-custom search col-sm-12" name="tax" id="tax">
                            <?php if ($element->impuesto_id > 0) { ?>
                                <option value="0">Vacío</option>
                                <option value="<?= $element->impuesto_id ?>" selected><?= $element->nombre_impuesto ?></option>
                            <?php } else { ?>
                                <option value="0" selected>Vacío</option>
                            <?php } ?>

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
                                    value="<?= number_format($element->precio_unitario, 2); ?>" id="totalPrice" disabled>
                            </div>
                            <input type="hidden" name="" value="" id="FinalPrice_out">
                        </div>
                    </div>

                    <br>
                    <div class="col-sm-12 d-flex justify-content-end">
                        <a class="btn btn-sm btn-danger" href="<?= base_url ?>product/index">Cancelar</a>
                        <input class="btn btn-sm btn-primary ml-2" type="button" onClick="editProduct();" value="Guardar" />
                    </div>

                </div>

            </div>
        </div> <!-- Area price -->

    </div> <!-- container-area -->


<?php endwhile; ?>


<!-- Modal -->
<div class="modal fade" id="history" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Historial de variantes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="col-sm-12 scroll-table">
                    <table id="Detalle" class="table-view table-view-danger">
                        <thead>
                            <th>Id</th>
                            <th>Proveedor</th>
                            <th>Imei</th>
                            <th>Serial</th>
                            <th>Color</th>
                            <th>Costo unitario</th>
                            <th>Caja</th>
                            <th>Entrada</th>
                        </thead>


                        <tbody>

                            <?php $variants = Help::showVariant_history($_GET['id']);
                            while ($variant = $variants->fetch_object()): ?>
                                <tr>
                                    <td><?= $variant->variante_id ?></td>
                                    <td><?= ucwords($variant->nombre_proveedor) ?></td>
                                    <td><?= $variant->imei ?></td>
                                    <td><?= $variant->serial ?></td>
                                    <td><?= ucwords($variant->color) ?></td>
                                    <td><?= number_format($variant->costo_unitario) ?></td>
                                    <td><?= $variant->caja ?></td>
                                    <td><?= $variant->entrada ?></td>

                                </tr>
                            <?php endwhile; ?>

                        </tbody>

                    </table>
                </div>

            </div>

        </div>
    </div>
</div>