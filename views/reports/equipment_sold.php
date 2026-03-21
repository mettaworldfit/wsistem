<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Equipos vendidos</h1>
        </div>
    </div>
</div>

<!-- Header resumen -->
<div class="generalContainer">

    <h6 class="legend-summary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-funnel-icon lucide-funnel">
            <path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z" />
        </svg>
        Filtros de Busqueda</h6>

    <div class="filters">
        <form method="post" id="formQueryDevice">

            <!-- Filtros -->
            <div class="filter-row">
            
                 <div>
                    <label for="product_id"><i class="fas fa-users"></i> Productos</label>
                    <select class="form-custom search" name="product_id" id="product_id">
                          <option value="" selected disabled>Seleccione un producto</option>
                          <option value="">No filtrar</option>
                        <?php
                        $products = Help::showProducts();
                        while ($product = $products->fetch_assoc()): ?>
                            <option value="<?= $product['IDproducto'] ?>"><?= ucwords($product['nombre_producto']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="provider"><i class="fas fa-users"></i> Proveedor</label>
                    <select class="form-custom search" name="provider" id="provider_id">
                        <option value="0">No filtrar</option>
                        <?php
                        $providers = Help::showProviders();
                        while ($provider = $providers->fetch_assoc()): ?>
                            <option value="<?= $provider['proveedor_id'] ?>"><?= ucwords($provider['nombre_proveedor']) ?> <?= ucwords($provider['apellidos'] ?? "")  ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                 <div>
                    <label for="start-date"><i class="fas fa-mobile-alt"></i> S/N</label>
                    <input class="form-custom" type="number" name="serial" id="serial" placeholder="Serial o IMEI">
                </div>

            </div>

            <div class="filter-buttoms">
                <button class="btn-custom btn-blue" type="submit">
                    <i class="fas fa-search"></i>
                    <p>Buscar datos</p>
                </button>
            </div>

        </form>
    </div>

    <!-- Resumen de venta -->
    <h6 class="legend-summary" style="margin-top: 10px;"><i class="far fa-chart-bar" style="color: #66c532"></i>
        Resumen de resultados</h6>

    <div id="display1">

    </div>
</div>






<!-- <section class="summary-sales">
        <div class="summary-sales-item">
            <div class="sales-total_users">
                <i class="fas fa-users"></i>
            </div>

            <div>
                <span>Total Usuarios</span>
                <p class="summary-sales_total">4 Usuarios</p>
            </div>
        </div>

        <div class="summary-sales-item">
            <div class="sales-total_inventory">
                <i class="fas fa-database"></i>
            </div>
            <div>
                <span>Total Inventario</span>
                <p class="summary-sales_total">RD$22,000</p>
            </div>
        </div>

        <div class="summary-sales-item">
            <div class="sales-total_invoices">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <span>Total Facturado</span>
                <p class="summary-sales_total">RD$31,200</p>
            </div>
        </div>

        <div class="summary-sales-item">
            <div class="sales-total_earning">
                <i class="fas fa-dollar-sign"></i>
            </div>

            <div>
                <span>Total Ganancias</span>
                <p class="summary-sales_total">RD$7,300</p>
            </div>
        </div>
    </section> -->