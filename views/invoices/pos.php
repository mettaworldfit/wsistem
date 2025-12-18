<div class="pos-wrap">

    <div class="pos-product-section">

        <!-- Ordenes sidebar -->
        <input type="hidden" id="order_id">
        <div class="pos-sidebar-order">

            <button type="button" class="btn-pos_home">
                <i class="fas fa-home"></i>
            </button>

            <button type="button" class="btn-add_order">
                <i class="fas fa-plus"></i>
            </button>

            <!-- Ordenes -->
            <div class="sidebar_order">
            </div>
        </div>



        <div class="pos-search">
            <div class="pos-search-bar">
                <i class="fas fa-barcode"></i>
                <input class="form-custom" type="text" id="search-input" placeholder="Buscar producto">
            </div>
        </div>

        <!-- Contenedor de productos en grid -->
        <div id="product-grid" class="product-grid">
        </div>


    </div>

    <!-- Detalles del producto -->
    <div class="pos-sidebar">
        <div class="pos-section-invoice">
            <div class="pos-invoice-info">

                <div class="pos-field-sm">
                    <label class="form-check-label">Lista de precio</label>
                    <div class="input-div">
                        <div class="i b-right">
                            <i class="fas fa-list"></i>
                        </div>
                        <select class="form-custom-icon search" name="" id="list_price" required>
                            <option value="0">General</option>
                            <?php $lists = Help::loadPriceLists();
                            while ($list = $lists->fetch_object()): ?>
                                <option value="<?= $list->lista_id ?>"><?= ucwords($list->nombre_lista) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="pos-field-sm">
                    <label class="form-check-label">Método</label>
                    <div class="input-div">
                        <div class="i b-right">
                            <i class="fas fa-list"></i>
                        </div>
                        <select class="form-custom-icon search " name="" id="method_id">
                            <?php $methods = Help::showPaymentMethod();
                            while ($method = $methods->fetch_object()): ?>
                                <option value="<?= $method->metodo_pago_id ?>"><?= $method->nombre_metodo ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pos-field-row">
                <label class="form-check-label">Cliente</label>
                <div class="input-div">
                    <div class="i b-right">
                        <i class="fas fa-portrait"></i>
                    </div>
                    <select class="form-custom-icon search" name="" id="customer_id" required>
                        <?php $customers = Help::showCustomers();
                        while ($customer = $customers->fetch_object()): ?>
                            <option value="<?= $customer->cliente_id ?>"><?= ucwords($customer->nombre) . " " . ucwords($customer->apellidos ?? '') ?></option>

                        <?php endwhile; ?>
                    </select>
                </div>

            </div>
        </div>


        <!-- Capa de fondo transparente -->
        <div class="overlay"></div>

        <!-- ventana de editar item -->
        <div class="pos-product-edit">
            <div class="modal-header">
                <h5 class="modal-title">
                    Editar venta
                </h5>
                <button type="button" class="close" id="close-window">
                    <span class="close-window">x</span>
                </button>
            </div>

            <!-- body -->
            <div class="pos-item-data">
                <div class="content-product-info">
                    <div class="item-info">
                        <div class="item-thumb">
                            <img src="<?= base_url ?>/public/imagen/presidente.jpg" alt="">
                        </div>

                        <div class="d-flex flex-column">
                            <span>-</span>
                            <span>-</span>
                            <span>-</span>
                            <input type="hidden" name="" id="windowId">
                            <input type="hidden" name="" id="w_product_id">
                            <input type="hidden" name="" id="w_piece_id">
                            <input type="hidden" name="" id="w_service_id">
                        </div>
                    </div>

                    <button type="button" class="close">
                        <span class="close-window">
                            <i class="far fa-trash-alt"></i>
                        </span>
                    </button>
                </div>

                <!-- inputs -->
                <div class="content-inputs-item">
                    <div class="input-row-one">
                        <div>
                            <label class="form-check-label">Precio base</label>
                            <input class="form-custom" type="number" name="precio" id="base_price">
                        </div>
                        <span>+</span>
                        <div>
                            <label class="form-check-label">Impuesto</label>
                            <select class="form-custom search" name="impuesto" id="tax_id">
                                <option value="0">Sin impuestos</option>
                                <?php 
                                $taxes = help::showTaxes();
                                while($tax = $taxes->fetch_object()): ?>
                                  <option value="<?= $tax->valor ?>"><?= ucwords($tax->nombre_impuesto); ?> (<?= $tax->valor ?>)%</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <span>=</span>
                        <div>
                            <label class="form-check-label">Precio final</label>
                            <input class="form-custom" type="number" name="precio_final" id="final_price">
                        </div>
                    </div>

                    <div class="input-row-two">
                        <div>
                            <label class="form-check-label">Cantidad</label>
                            <input class="form-custom" type="number" name="cantidad" id="quantity">
                        </div>

                        <div>
                            <label class="form-check-label">Descuento</label>
                            <input class="form-custom" type="number" name="descuento" id="discount">
                        </div>
                    </div>
                </div>
            </div>

            <!-- footer -->
            <div class="footer-summary">

                <div class="window-summary">
                    <div class="price-content-pos">
                        <div id="row-subtotal">
                            <span>Subtotal</span>
                            <span class="item-subtotal">$0.00</span>
                        </div>

                        <div id="row-discount">
                            <span>Descuento -</span>
                            <span class="item-discount">$0.00</span>
                        </div>

                        <div id="row-tax">
                            <span>Impuestos +</span>
                            <span class="item-taxes">$0.00</span>
                        </div>

                        <div class="summary-pos-window">
                            <span>Total</span>
                            <span class="item-total">$0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="footer-btn-container">
                    <button class="btn-custom btn-red" type="button" id="cancel-window">
                        <i class="fas fa-window-close"></i>
                        <p>Cancelar</p>
                    </button>

                    <button class="btn-custom btn-default" type="button" id="updatePosItem">
                        <i class="fas fa-plus"></i>
                        <p>Guardar</p>
                    </button>
                </div>

            </div>

        </div> <!-- end -->

        <!-- Contenedor detalles -->
        <div class="pos-detail-item" id="pos-detail-item">
        </div>

        <!-- Resumen -->
        <div class="pos-section-summary">
            <div class="price-content-pos">
                <div id="pos-subtotal">
                    <span>Subtotal</span>
                    <span class="pos-subtotal">$0.00</span>
                </div>

                <div id="pos-discount">
                    <span>Descuento -</span>
                    <span class="pos-discount">$0.00</span>
                </div>

                <div id="pos-taxes">
                    <span>Impuestos +</span>
                    <span class="pos-taxes">$0.00</span>
                </div>
            </div>

            <button action="button" class="pos-button-cash" disabled>
                <p>Facturar</p>
                <span class="pos-total">$0.00</span>
                <input type="hidden" name="" id="total_pos">
            </button>

            <button action="button" class="pos-count-item">
                <p>-</p>
                <span class="pos-erase">Cancelar</span>
            </button>
        </div>
    </div>
</div>