<div class="pos-wrap">

    <div class="pos-product-section">

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
                        <select class="form-custom-icon search " name="" id="cash-in-method">
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
                    <select class="form-custom-icon search" name="" id="cash-in-customer" required>
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
                            <span>Producto</span>
                            <span>aceitunas manzanilla rellenas de pimiento</span>
                            <span>Bebida</span>
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
                            <label class="form-check-label" for="">Precio base</label>
                            <input class="form-custom" type="text" name="" id="">
                        </div>
                        <span>+</span>
                        <div>
                            <label class="form-check-label" for="">Impuesto</label>
                            <select class="form-custom search" name="" id="">
                                <option value="">Itbis</option>
                            </select>
                        </div>
                        <span>=</span>
                        <div>
                            <label class="form-check-label" for="">Precio final</label>
                            <input class="form-custom" type="text" name="" id="">
                        </div>
                    </div>

                    <div class="input-row-two">
                        <div>
                            <label class="form-check-label" for="">Cantidad</label>
                            <input class="form-custom" type="text" name="" id="">
                        </div>

                        <div>
                            <label class="form-check-label" for="">Descuento</label>
                            <input class="form-custom" type="text" name="" id="">
                        </div>
                    </div>
                </div>
            </div>

            <!-- footer -->
            <div class="footer-summary">

                <div class="window-summary">
                    <div class="price-content-pos">
                        <div>
                            <span>Subtotal</span>
                            <span>$0.00</span>
                        </div>

                        <div>
                            <span>Descuento -</span>
                            <span>$0.00</span>
                        </div>

                        <div>
                            <span>Impuestos +</span>
                            <span>$0.00</span>
                        </div>

                        <div class="summary-pos-window">
                            <span>Total</span>
                            <span>$0.00</span>
                        </div>

                    </div>
                </div>

                <!-- Botones -->

                <div class="footer-btn-container">
                    <button class="btn-custom btn-red" type="button">
                        <i class="fas fa-window-close"></i>
                        <p>Cancelar</p>
                    </button>

                    <button class="btn-custom btn-default" type="button">
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
            <button action="button" class="pos-button-cash">
                <p>Facturar</p>
                <span><input type="text" class="invisible-input pos-total" value="" id="in-total" disabled></span>
            </button>
        </div>
    </div>
</div>














<!-- Paginación -->
<!-- <div id="pagination">
    <button id="prev-btn">Anterior</button>
    <span id="page-number">Página: 1</span>
    <div id="page-numbers" class="page-numbers">
     
    </div>
    <button id="next-btn">Siguiente</button>
</div> -->