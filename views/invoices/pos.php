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
                <!-- <i class="fas fa-barcode"></i> -->
                <!-- Botón scanner -->
                <button type="button" class="pos-scanner-svg" id="scannerPos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-scan-barcode-icon lucide-scan-barcode">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                        <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                        <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                        <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                        <path d="M8 7v10" />
                        <path d="M12 7v10" />
                        <path d="M17 7v10" />
                    </svg>
                </button>
                <input class="form-custom" type="text" id="search-input" placeholder="Buscar producto por nombre o código de barra" autocomplete="off">

                <!-- Overlay del scanner -->
                <div id="scanner-overlay">
                    <div id="reader"></div>
                    <button id="closeScanner">✕</button>
                </div>
            </div>
        </div>

        <!-- Contenedor de productos en grid -->
        <div id="product-grid" class="product-grid">
        </div>


    </div>

    <!-- Detalles del producto -->
    <div class="pos-sidebar">
        <div class="pos-section-invoice">
            <div class="pos-sidebar-header">
                <h5>Factura de venta</h5>
                <div>

                    <button type="button" id="pos-order-update" data-title="Editar orden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-basket-icon lucide-shopping-basket">
                            <path d="m15 11-1 9" />
                            <path d="m19 11-4-7" />
                            <path d="M2 11h20" />
                            <path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4" />
                            <path d="M4.5 15.5h15" />
                            <path d="m5 11 4-7" />
                            <path d="m9 11 1 9" />
                        </svg>
                    </button>

                    <button type="button" id="pos-print-order" data-title="Imprimir orden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-printer-icon lucide-printer">
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6" />
                            <rect x="6" y="14" width="12" height="8" rx="1" />
                        </svg>
                    </button>

                    <?php if (empty($cashOpening)): ?>
                        <button type="button" data-toggle="modal" data-target="#modalCashOpening" data-title="Abrir caja">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-keyhole-open-icon lucide-lock-keyhole-open">
                                <circle cx="12" cy="16" r="1" />
                                <rect width="18" height="12" x="3" y="10" rx="2" />
                                <path d="M7 10V7a5 5 0 0 1 9.33-2.5" />
                            </svg>
                        </button>
                    <?php endif; ?>

                    <?php if ($cashOpening): ?>
                        <button type="button" data-toggle="modal" data-target="#modalCashClosing" data-title="Cerrar caja" id="cash_closing">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-keyhole-icon lucide-lock-keyhole">
                                <circle cx="12" cy="16" r="1" />
                                <rect x="3" y="10" width="18" height="12" rx="2" />
                                <path d="M7 10V7a5 5 0 0 1 10 0v3" />
                            </svg>
                        </button>
                    <?php endif; ?>

                    <button type="button" id="pos-config" data-title="Ajustes">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sliders-horizontal-icon lucide-sliders-horizontal">
                            <path d="M10 5H3" />
                            <path d="M12 19H3" />
                            <path d="M14 3v4" />
                            <path d="M16 17v4" />
                            <path d="M21 12h-9" />
                            <path d="M21 19h-5" />
                            <path d="M21 5h-7" />
                            <path d="M8 10v4" />
                            <path d="M8 12H3" />
                        </svg>
                    </button>
                </div>
            </div>
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
                    <div class="input-div v_method">
                        <div class="i b-right">
                            <i class="fas fa-list"></i>
                        </div>
                        <select class="form-custom-icon search " name="" id="method_id">
                        </select>
                    </div>
                </div>
            </div>

            <div class="pos-field-row">
                <label class="form-check-label">Cliente</label>

                <div class="form-group_pos">
                    <div class="input-div v_customer">
                        <div class="i b-right">
                            <i class="fas fa-portrait"></i>
                        </div>
                        <select class="form-custom-icon" name="" id="customer_id" required>
                        </select>
                    </div>

                    <!-- Crear cliente -->
                    <button class="btn-custom btn-default" type="button" id="pos-add_customer">
                        <i class="fas fa-user-plus"></i>
                        <p>Nuevo</p>
                    </button>
                </div>
            </div>
        </div>


        <!-- Capa de fondo transparente -->
        <div class="overlay"></div>

        <div class="pos-order-add">
            <div class="modal-header">
                <h5 class="modal-title">
                    Nueva orden
                </h5>
                <button type="button" class="close" id="close-window">
                    <span class="close-window">x</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="" method="POST" id="orderForm">
                    <div class="row">
                        <div class="form-group col-sm-8">
                            <label class="form-check-label" for="">Cliente<span class="text-danger">*</span></label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon" name="cliente" id="pos_customer_id" required>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="form-check-label" for="">Dirección de entrega</label>
                            <textarea class="form-custom" name="" value="" id="pos_direction" cols="30" rows="3"
                                maxlength="254" placeholder="Dirección completa"></textarea>
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Nombre del receptor</span></label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="receptor" id="pos_fullname">
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Teléfono</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="telefono" id="pos_tel">
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Tipo de entrega</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="entrega" id="pos_delivery">
                                    <option value="-" selected>Nínguno</option>
                                    <option value="envio">Envío</option>
                                    <option value="retiro">Retiro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="form-check-label" for="">Comentarios o instrucciones especiales</label>
                            <textarea class="form-custom" name="" value="" id="pos_comment" cols="30" rows="3"
                                maxlength="254" placeholder="Observaciones"></textarea>
                        </div>
                    </div>

                    <div class="footer-order_pos">
                        <!-- Botones -->
                        <div class="footer-btn-container">
                            <button class="btn-custom btn-red" type="button" id="cancel-window">
                                <i class="fas fa-window-close"></i>
                                <p>Cancelar</p>
                            </button>

                            <button class="btn-custom btn-green" type="submit" id="">
                                <i class="fas fa-plus"></i>
                                <p>Registrar</p>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- end -->

        <!-- Agregar cliente -->
        <div class="pos-customer-add">
            <div class="modal-header">
                <h5 class="modal-title">
                    Agregar cliente
                </h5>
                <button type="button" class="close" id="close-window">
                    <span class="close-window">x</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="" method="POST" id="contactForm">

                    <div class="form-group col-sm-12">
                        <p class="title-info">
                            Incluye los datos principales de tu nuevo contacto
                        </p>
                    </div>

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
                                <option value="0" selected> --- </option>
                                <?php require_once "./views/contacts/includes/direcciones.php"; ?>
                            </select>
                        </div>

                    </div> <!-- Row col-md-12 -->
                    <br>

                    <div class="col-legend">
                        <h3>Información de contacto</h3>
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

                    <div class="footer-window_pos">
                        <!-- Botones -->
                        <div class="footer-btn-container">
                            <button class="btn-custom btn-red" type="button" id="cancel-window">
                                <i class="fas fa-window-close"></i>
                                <p>Cancelar</p>
                            </button>

                            <button class="btn-custom btn-green" type="submit" id="">
                                <i class="fas fa-plus"></i>
                                <p>Registrar</p>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- end -->


        <!-- Configuraciones de la factura -->
        <div class="pos-config-window">
            <div class="modal-header">
                <h5 class="modal-title">
                    Configuraciones
                </h5>
                <button type="button" class="close" id="close-window">
                    <span class="close-window">x</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="" method="POST" id="configForm">

                    <div class="footer-config_window">
                        <!-- Botones -->
                        <div class="footer-btn-container">
                            <button class="btn-custom btn-red" type="button" id="cancel-window">
                                <i class="fas fa-window-close"></i>
                                <p>Cancelar</p>
                            </button>

                            <button class="btn-custom btn-green" type="submit" id="">
                                <i class="fas fa-plus"></i>
                                <p>Guardar</p>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- end -->

        <!-- Datos de la orden -->
        <div class="pos-order-window">
            <div class="modal-header">
                <h5 class="modal-title">
                    Datos de la orden
                </h5>
                <button type="button" class="close" id="close-window">
                    <span class="close-window">x</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="" method="POST" id="updateOrderForm">

                    <div class="row">
                        <div class="form-group col-sm-8">
                            <label class="form-check-label" for="">Cliente<span class="text-danger">*</span></label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon" name="cliente" id="pos_edit_customer" required>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="form-check-label" for="">Dirección de entrega</label>
                            <textarea class="form-custom" name="" value="" id="pos_edit_direction" cols="30" rows="3"
                                maxlength="254" placeholder="Dirección completa"></textarea>
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Nombre del receptor</span></label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="receptor" id="pos_edit_fullname">
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Teléfono</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="telefono" id="pos_edit_tel">
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Tipo de entrega</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search" name="entrega" id="pos_edit_delivery">
                                    <option value="-" selected>Nínguno</option>
                                    <option value="envio">Envío</option>
                                    <option value="retiro">Retiro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="form-check-label" for="">Comentarios o instrucciones especiales</label>
                            <textarea class="form-custom" name="" value="" id="pos_edit_comment" cols="30" rows="3"
                                maxlength="254" placeholder="Observaciones"></textarea>
                        </div>
                    </div>

                    <div class="footer-order_window">
                        <!-- Botones -->
                        <div class="footer-btn-container">
                            <button class="btn-custom btn-red" type="button" id="cancel-window">
                                <i class="fas fa-window-close"></i>
                                <p>Cancelar</p>
                            </button>

                            <button class="btn-custom btn-green" type="submit" id="">
                                <i class="fas fa-plus"></i>
                                <p>Guardar</p>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- end -->

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
                            <img src="#" alt="">
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

                    <button type="button" class="close" id="erase_window_item">
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
                                while ($tax = $taxes->fetch_object()): ?>
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
                            <input class="form-custom" type="number" name="cantidad" id="quantity" min="0">
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

            <div class="group-button-row">
                <button action="button" class="pos-button-cash" disabled>
                    <p>Facturar al contado</p>
                    <span class="pos-total">$0.00</span>
                    <input type="hidden" name="" id="total_pos">
                </button>

                <button action="button" class="pos-button-credit" data-toggle="modal" data-target="#pos-credit">
                    <p>Crédito</p>
                </button>
            </div>

            <div class="group-button-row">
                <input type="number" class="form-custom pos_cash" id="cash_received" placeholder="Dinero recibido" min="0" step="0.01">

                <button action="button" class="pos-count-item">
                    <p>-</p>
                    <span class="pos-erase">Cancelar</span>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal: Factura a crédito -->
<div class="modal fade" id="pos-credit" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Factura a crédito</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="invoiceCredit">

                    <!-- Head -->
                    <div class="row col-sm-12 invoice-head-modal">

                        <div class="col-sm-4 head-content">
                            <h6>Total Pagado</h6>
                            <input type="text" class="invisible-input text-success" value="" id="credit-received" disabled>
                        </div>

                        <div class="col-sm-4 head-content">
                            <h6>Monto a Pagar</h6>
                            <input type="text" class="invisible-input text-primary" value="" id="credit-topay" disabled>
                        </div>

                        <div class="col-sm-4 head-content">
                            <h6>Monto Pendiente</h6>
                            <input type="text" class="invisible-input text-danger" value="" id="credit-pending" disabled>
                        </div>

                    </div>
                    <br>

                    <!-- Content -->
                    <div class="row col-sm-12">

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Cliente</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-portrait"></i>
                                </div>
                                <select class="form-custom-icon search" name="customer" id="modal-customer_id" required>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Método</label>
                            <div class="input-div">
                                <div class="i b-right">
                                    <i class="fas fa-list"></i>
                                </div>
                                <select class="form-custom-icon search " name="method" id="modal-method_id" required>
                                    <?php $methods = Help::showPaymentMethod();
                                    while ($method = $methods->fetch_object()): ?>
                                        <option value="<?= $method->metodo_pago_id ?>"><?= $method->nombre_metodo ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Fecha</label>
                            <div class="input-div">

                                <input type="date" name="date" class="form-custom-icon" id="modal-date" value="<?php date_default_timezone_set('America/New_York');
                                                                                                                echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                    </div> <!-- Row -->

                    <div class="row col-sm-12 mt-1">
                        <div class="form-group col-sm-4">
                            <label class="form-check-label" for="">Vendedor</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="text" name="" value="<?= $_SESSION['identity']->nombre ?>" id="modal-seller" disabled>
                            </div>
                        </div>

                        <div class="form-group col-sm-4 pay">
                            <label class="form-check-label" for="">Monto</label>
                            <div class="input-div">
                                <div class="i">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <input class="form-custom-icon b-left" type="number" name="pay" value="" id="modal-pay" required>
                            </div>
                        </div>
                    </div> <!-- Row -->

                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>

                        <button type="submit" class="btn-custom btn-green">
                            <i class="fas fa-dollar-sign"></i>
                            <p>Facturar</p>
                        </button>
                    </div>
                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>

<!-- Modal: Cierre de caja -->
<div class="modal fade" id="modalCashClosing" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalCierreCajaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg .modal-cashClosing">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title" id="modalCierreCajaLabel">Cierre de caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="" method="POST" id="formCashClosing">
                    <input type="hidden" name="id" value="" id="closingId">
                    <input type="hidden" name="" value="" id="tickets_invoices">
                    <input type="hidden" name="" value="" id="tickets_payments">
                    <input type="hidden" name="" value="<?= ucwords($_SESSION['identity']->nombre) ?> <?= ucwords($_SESSION['identity']->apellidos ?? '') ?>" id="user_name">

                    <div class="row col-sm-12 invoice-head-modal">
                        <div class="col-sm-3 head-content">
                            <h6>Total Vendido</h6>
                            <input type="text" class="invisible-input text-success" value="" id="total" disabled>
                        </div>

                        <div class="col-sm-3 head-content">
                            <h6>Total Esperado</h6>
                            <input type="text" class="invisible-input text-primary" value="" id="total_expected" disabled>
                        </div>

                        <div class="col-sm-3 head-content">
                            <h6>Efectivo</h6>
                            <input type="text" class="invisible-input text-primary" value="0" id="real" disabled>
                        </div>

                        <div class="col-sm-3 head-content">
                            <h6>Diferencia</h6>
                            <input type="text" class="invisible-input" value="0" id="total_difference" disabled>
                        </div>
                    </div>

                    <div class="grid-date-container">

                        <div class="grid-form-user">
                            <div class="grid-date-field">
                                <label class="">Usuario:</label>
                                <select class="form-custom search col-sm-12" name="user" id="user_id" required>
                                    <option value="<?= $_SESSION['identity']->usuario_id ?>"><?= ucwords($_SESSION['identity']->nombre) ?> <?= ucwords($_SESSION['identity']->apellidos ?? '') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="grid-form-date">
                            <div class="grid-date-field">
                                <label class="">Fecha Apertura:</label>
                                <input type="datetime-local" class="form-custom color-black" value="" id="opening_date" disabled required>
                            </div>

                            <div class="grid-date-field">

                                <label class="">Fecha Cierre:</label>
                                <input type="datetime-local" class="form-custom" id="closing_date" value="" required>
                            </div>
                        </div>
                    </div>

                    <!-- Información detallada -->
                    <div class="grid-form">
                        <div class="grid-summary">
                            <legend class="summary-legend">Resumen de cierre</legend>

                            <div class="form-field field-plus">
                                <label class="">+ Saldo Apertura:</label>
                                <input type="hidden" class="form-custom" id="initial_balance" value="">
                                <input type="text" class="" id="input_initial_balance" value="" readonly disabled>
                            </div>

                            <div class="form-field">
                                <label class="">+ Ingresos Efectivo:</label>
                                <input type="text" class="" id="cash_income" value="" readonly disabled>
                            </div>

                            <div class="form-field">
                                <label class="">+ Ingresos Tarjeta:</label>
                                <input type="text" class="" id="card_income" value="" readonly disabled>
                            </div>

                            <div class="form-field">
                                <label class="">+ Ingresos Transferencia:</label>
                                <input type="text" class="" id="transfer_income" value="" readonly disabled>
                            </div>

                            <div class="form-field">
                                <label class="">+ Ingresos Cheques:</label>
                                <input type="text" class="" id="check_income" value="" readonly disabled>
                            </div>

                            <div class="form-field field-subtraction">
                                <label class="">- Gastos externos:</label>
                                <input type="text" class="" id="external_expenses" value="" disabled>
                            </div>

                            <div class="form-field field-subtraction">
                                <label class="">- Gastos internos:</label>
                                <input type="text" class="" id="cash_expenses" value="" disabled>
                            </div>

                            <div class="form-field field-subtraction-active">
                                <label class="">- Reembolsos:</label>
                                <input type="number" class="field-active" id="refund" placeholder="0.00">
                            </div>

                            <div class="form-field field-subtraction-active">
                                <label class="">- Retiros:</label>
                                <input type="number" class="field-active" id="withdrawals" placeholder="0.00">
                            </div>

                        </div><!-- grid-summary -->


                        <div class="grid-cash-closing">
                            <legend class="cash-closing-legend">Datos de cierre</legend>

                            <label class="">Observaciones:</label>
                            <textarea class="form-custom" rows="3" id="notes" placeholder="Notas adicionales..."></textarea>

                            <div class="field-cash-closing">
                                <label class="fw-bold">Total efectivo en caja:</label>
                                <input type="number" class="form-custom form-closing fw-bold" id="current_total" value="" step="0.01" min="0" inputmode="decimal" required>
                            </div>
                        </div> <!-- grid-cash-closing-->

                    </div><!-- grid-form -->

            </div> <!-- body -->
            <div class="modal-footer bg-light">

                <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
                    <i class="fas fa-window-close"></i>
                    <p>Salir</p>
                </button>
                <button type="submit" class="btn-custom btn-green" id="">
                    <i class="fas fa-door-closed"></i>
                    <p>Cerrar caja</p>
                </button>
            </div>

            </form>
        </div>
    </div>
</div>

<!-- Modal: Abrir caja -->
<div class="modal fade" id="modalCashOpening" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Abrir caja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="formCashOpening">

                    <div class="form-group col-sm-12">
                        <p class="title-info">
                            Abre el saldo incial con el cual iniciaste en caja
                        </p>
                    </div>

                    <div class="row col-md-12">
                        <div class="form-group col-md-6">
                            <label for="saldo_inicial" class="form-check-label label-nomb">Saldo Inicial:<span class="text-danger">*</span></label>
                            <input class="form-custom" type="number" name="saldo_inicial" id="cash_initial" required>
                        </div>

                        <div class="form-group col-md-6">
                            <?php
                            date_default_timezone_set('America/Santo_Domingo');
                            $datetimeRD = date('Y-m-d\TH:i');
                            ?>
                            <label for="fecha_apertura" class="form-check-label label-nomb">Fecha apertura:<span class="text-danger">*</span></label>
                            <input class="form-custom" type="datetime-local" name="fecha_apertura" value="<?= $datetimeRD ?>" id="opening" required>
                        </div>
                    </div> <!-- Row col-md-12 -->

                    <div class="mt-4 modal-footer">
                        <button type="button" class="btn-custom btn-red" data-dismiss="modal">
                            <i class="fas fa-window-close"></i>
                            <p>Salir</p>
                        </button>

                        <button type="submit" href="#" class="btn-custom btn-green">
                            <i class="fas fa-door-open"></i>
                            <p>Abrir caja</p>
                        </button>
                    </div>
                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>