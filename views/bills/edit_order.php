<?php
$id = $_GET['id'];
$verify = Expenses_Utils::verify_order_status($id);
if ($verify == "permitir") {

?>

    <div class="section-wrapper">
        <div class="align-content clearfix">
            <div class="float-left">
                <h3> Orden de compra OC-00<?= $_GET['id'] ?></h3>
            </div>

        </div>
    </div>


    <div class="generalContainer padding-10 box-shadow-low">

        <table id="Detalle" class="table-custom table">
            <thead>
                <th>Descripción</th>
                <th>Cant</th>
                <th>Precio</th>
                <th>Impuestos</th>
                <th>Descuentos</th>
                <th>Observación</th>
                <th>Importe</th>
                <th></th>
            </thead>

            <?php $orden = Help::SHOW_ORDERS_ID($_GET['id']);
            while ($element = $orden->fetch_object()) : ?>

                <tbody id="rows_rp">
                    <tr>
                        <td>
                            <?php
                            if ($element->nombre_producto) {
                                echo ucwords($element->nombre_producto);
                            } else if ($element->nombre_pieza) {
                                echo ucwords($element->nombre_pieza);
                            }
                            ?>
                        </td>
                        <td><?= number_format($element->cantidad_total, 2) ?></td>
                        <td><?= number_format($element->precio, 2) ?></td>
                        <td><?= number_format($element->impuestos, 2) ?></td>
                        <td><?= number_format($element->descuentos, 2) ?></td>
                        <td class="note-width"><?= $element->comentario ?></td>
                        <td><?= number_format(($element->cantidad_total * $element->precio) + $element->impuestos - $element->descuentos, 2) ?></td>

                        <td>
                            <a class="text-danger pointer" style="font-size: 16px;" onclick="DELETE_ITEM_ORDER_PRCHSE('<?= $element->detalle_id ?>','<?= $element->orden_id ?>')"><i class="fas fa-times"></i></a>
                        </td>


                    </tr>
                </tbody>

            <?php endwhile; ?>

        </table>
        <br>

        <!-- Información -->


        <div class="row col-sm-12">
            <div class="form-group col-sm-8">
                <textarea class="form-custom" name="" value="" id="observation" cols="30" rows="6" maxlength="150" placeholder="Observaciones"></textarea>
            </div>

            <!-- Precio total -->

            <div class="form-group col-sm-4">
                <div class="price-container">
                    <div class="price-content bold">
                        <span>Subtotal</span>
                        <span>Descuento -</span>
                        <span>impuesto +</span>
                        <span>Total</span>
                    </div>

                    <div class="price-content" id="price">
                        <input type="hidden" name="" value="<?= $_GET['id'] ?>" id="order">
                        <span><input type="text" class="invisible-input" value="" id="in-subtotal" disabled></span>
                        <span><input type="text" class="invisible-input" value="" id="in-discount" disabled></span>
                        <span><input type="text" class="invisible-input" value="" id="in-taxes" disabled></span>
                        <span><input type="text" class="invisible-input" value="" id="in-total" disabled></span>
                    </div>
                </div>

            </div>

        </div> <!-- Row -->
        <br>


        <button class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#update_order" id=""><i class="far fa-edit"></i> Actualizar datos</button>
        <button class="btn btn-sm btn-info" type="button" data-toggle="modal" data-target="#add_detail" id=""><i class="fas fa-search-plus"></i> Agregar detalle</button>
        <button class="btn btn-sm btn-danger" type="button" id="cancel_detail"><i class="fas fa-window-close"></i> Cancelar</button>

    </div> <!-- generalConntainer -->



    <!--Modal agregar detalle-->
    <div class="modal fade" id="add_detail" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Agregar detalle de orden</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" onsubmit="event.preventDefault(); ADD_ITEM_DETAIL_ORDER();">

                    <div class="row col-sm-12">
                        <div class="col-sm-12 row">

                            <div class="radio-list">
                                <div class="radio-item ">
                                    <input type="radio" name="tipo" value="producto" id="radio1" checked>
                                    <label for="radio1">Productos</label>
                                </div>

                                <div class="radio-item ml-2">
                                    <input type="radio" name="tipo" value="pieza" id="radio2">
                                    <label for="radio2">Piezas</label>
                                </div>

                            </div>

                        </div>
                    </div>

                        <!-- Content -->
                        <div class="row col-sm-12">

                            <div class="form-group col-sm-6 or_piece">
                                <label class="form-check-label" for="">Piezas</label>
                                <div class="input-div">
                                    <div class="i b-right">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <select class="form-custom-icon search" name="pieza" id="piece" required>
                                        <option value="" disabled selected>Buscar piezas</option>
                                        <?php $pieces = Help::showPieces();
                                        while ($piece = $pieces->fetch_object()) : ?>
                                            <option value="<?= $piece->pieza_id ?>"><?= $piece->nombre_pieza ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-sm-6 product">
                                <label class="form-check-label" for="">Productos</label>
                                <div class="input-div">
                                    <div class="i">
                                        <i class="far fa-clipboard"></i>
                                    </div>
                                    <select class="form-custom-icon search" name="producto" id="product" required>
                                        <option value="" disabled selected>Buscar productos</option>
                                        <?php $products = Help::showProducts();
                                        while ($product = $products->fetch_object()) : ?>
                                            <option value="<?= $product->IDproducto ?>"><?= $product->nombre_producto ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-sm-3">
                                <label class="form-check-label" for="">Cantidad</label>
                                <div class="input-div">
                                    <div class="i">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <input type="number" class="form-custom-icon b-left" name="cantidad" step="0.01" min="0" max="999.99" id="or_quantity" required>
                                </div>
                            </div>


                            <div class="form-group col-sm-3">
                                <label class="form-check-label" for="">Precio</label>
                                <div class="input-div">
                                    <div class="i">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <input type="text" class="form-custom-icon b-left" name="precio" id="or_price_out" required>
                                </div>
                            </div>



                        </div> <!-- Row -->

                        <div class="row col-sm-12 mt-1">

                            <div class="form-group col-sm-3">
                                <label class="form-check-label" for="">Descuento</label>
                                <div class="input-div">
                                    <div class="i">
                                        <i class="fas fa-level-down-alt"></i>
                                    </div>
                                    <input type="number" class="form-custom-icon b-left" name="descuento" id="or_discount">
                                </div>
                            </div>

                            <div class="form-group col-sm-4">
                                <label class="form-check-label" for="">Impuesto</label>
                                <div class="input-div">
                                    <div class="i">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <select class="form-custom-icon search" name="producto" id="or_taxes">
                                        <option value="0" disabled selected>Buscar impuesto</option>
                                        <?php $taxes = Help::showTaxes();
                                        while ($tax = $taxes->fetch_object()) : ?>
                                            <option value="<?= $tax->valor ?>"><?= $tax->nombre_impuesto ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-sm-5">
                                <label class="form-check-label" for="">Observación</label>
                                <textarea class="form-custom" name="" value="" id="observation_item" cols="10" rows="2" maxlength="50" placeholder=""></textarea>
                            </div>

                        </div> <!-- Row -->


                        <div class="mt-4 modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id=""><i class="fas fa-window-close"></i> Salir</button>
                            <button type="submit" class="btn btn-sm btn-info" id="or_add_item"><i class="fas fa-plus-square"></i> Agregar</button>
                        </div>

                    </form>
                </div> <!-- Body -->
            </div>
        </div>
    </div>


    <!--Modal actualizar orden-->
    <div class="modal fade" id="update_order" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Guardar orden de compra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" onsubmit="event.preventDefault(); update_order('<?= $_GET['id'] ?>');">

                        <?php $data = Help::SHOW_ORDER_INFO_ID($_GET['id']);
                        while ($info = $data->fetch_object()) : ?>
                            <!-- Content -->
                            <div class="row col-sm-12">

                                <div class="row col-sm-10 mb-2">
                                    <div class="form-group col-sm-4">
                                        <label class="form-check-label" for="">Fecha</label>
                                        <div class="input-div">

                                            <input type="date" name="" class="form-custom-icon" value="<?= $info->fecha_creacion ?>" id="date" required>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label class="form-check-label" for="">Expiración</label>
                                        <div class="input-div">

                                            <input type="date" name="" class="form-custom-icon" value="<?= $info->expiracion ?>" id="expiration">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form-check-label" for="">Proveedores</label>
                                    <div class="input-div">
                                        <div class="i b-right">
                                            <i class="fas fa-list"></i>
                                        </div>
                                        <select class="form-custom-icon search" name="" id="provider" required>
                                            <option value="<?= $info->proveedor_id ?>" selected><?= ucwords($info->nombre_proveedor) ?> <?= ucwords($info->apellidos) ?></option>
                                            <?php $providers = Help::showProviders();
                                            while ($provider = $providers->fetch_object()) : ?>
                                                <option value="<?= $provider->proveedor_id ?>"><?= ucwords($provider->nombre_proveedor) ?> <?= ucwords($provider->apellidos) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form-check-label" for="">Ordenado por</label>
                                    <div class="input-div">
                                        <div class="i">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <input class="form-custom-icon b-left" type="text" name="" value="<?= ucwords($info->nombre) ?> <?= ucwords($info->apellidos_usuario) ?>" id="emisor" disabled>
                                    </div>
                                </div>

                            </div> <!-- Row -->

                        <?php endwhile; ?>
                        <div class="mt-4 modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id=""><i class="fas fa-window-close"></i> Salir</button>
                            <button type="submit" class="btn btn-sm btn-success" data-dismiss="modal" id=""><i class="far fa-edit"></i> Actualizar</button>
                        </div>

                    </form>
                </div> <!-- Body -->
            </div>
        </div>
    </div>



<?php } else { ?>


<!-- Mensaje de no permitido -->

<?php } ?>