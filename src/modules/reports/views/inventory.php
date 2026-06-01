<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Valor de inventario</h1>
        </div>
    </div>

    <p class="title-info">Consulta el valor del inventario actual, la cantidad de productos y piezas inventariables que tienes y su costo promedio.</p>

</div>

<!-- Header resumen -->
<div class="generalContainer">

    <h6 class="legend-summary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-funnel-icon lucide-funnel">
            <path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z" />
        </svg>
        Filtros de Busqueda</h6>

    <div class="filters">
        <form method="post" id="formInventory">

            <!-- Filtros -->
            <div class="filter-row">
                <div>
                    <label for="query"><i class="fas fa-search"></i> Palabra clave:</label>
                    <input class="form-custom" type="text" name="query" id="query" placeholder="Introduce una palabra clave">
                </div>

            </div>

            <hr>

            <!-- Filtros -->
            <div class="filter-row">

                <div>
                    <label for="provider"><i class="fas fa-list-ul"></i> Proveedores:</label>
                    <select class="form-custom search" name="provider" id="">
                        <option value="0">No filtrar</option>
                        <?php
                        $providers = Help::showProviders();
                        while ($prov = $providers->fetch_assoc()): ?>
                            <option value="<?= $prov['proveedor_id'] ?>"><?= ucwords($prov['nombre_proveedor']) ?> </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="category"><i class="fas fa-list-ul"></i> Categorias:</label>
                    <select class="form-custom search" name="category" id="category_id">
                        <option value="0">No filtrar</option>
                        <?php
                        $categories = Help::showCategories();
                        while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['categoria_id'] ?>"><?= ucwords($cat['nombre_categoria']) ?> </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="brand"><i class="fas fa-list-ul"></i> Marcas:</label>
                    <select class="form-custom search" name="brand" id="">
                        <option value="0">No filtrar</option>
                        <?php
                        $brands = Help::showBrands();
                        while ($brand = $brands->fetch_assoc()): ?>
                            <option value="<?= $brand['marca_id'] ?>"><?= ucwords($brand['nombre_marca']) ?> </option>
                        <?php endwhile; ?>
                    </select>
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

    <div class="summary-result">
        <div>
            <span>Encontrado</span>
            <span id="items_total">0</span>
            <span>Items</span>
        </div>

        <div>
            <span>Valor real:</span>
            <span class="text-success" id="value_real">DOP 0.00</span>
        </div>

        <div>
            <span>Valor de inventario:</span>
            <span id="value_inv">DOP 0.00</span>
        </div>
    </div>

    <br>
    <div class="table-inventory-result">

    </div>
</div>
