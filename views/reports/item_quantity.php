<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Reportes de cantidades</h1>
        </div>
    </div>
</div>

<div class="generalContainer">

    <h6 class="legend-summary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-funnel-icon lucide-funnel">
            <path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z" />
        </svg>
        Filtros de Busqueda</h6>

    <div class="filters">
        <form method="post" id="formItemsQuantity">

            <!-- Filtros de fecha -->
            <div class="date-filter">
                <div>
                    <label for="query"><i class="fas fa-search"></i> Palabra clave:</label>
                    <input class="form-custom" type="text" name="query" id="query" placeholder="Introduce una palabra clave" required>
                </div>

                <!-- <div>
                    <label for="usuario_id">Usuario:</label>
                    <select class="form-custom search" name="usuario_id" id="user_id">
                        <option value="0">No filtrar</option>
                        <?php
                        $users = Help::loadUsers();
                        while ($user = $users->fetch_assoc()): ?>
                            <option value="<?= $user['usuario_id'] ?>"><?= ucwords($user['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div> -->

            </div>

            <hr>

            <!-- Filtros vendedores -->
            <div class="filter-row">
                
                <div>
                    <label for="start-date"><i class="far fa-calendar-alt"></i> Fecha Inicio:</label>
                    <input class="form-custom" type="datetime-local" name="fecha_inicio" id="date-start" required>
                </div>

                <div>
                    <label for="end-date"><i class="far fa-calendar-alt"></i> Fecha Final:</label>
                    <input class="form-custom" type="datetime-local" name="fecha_final" id="date-end" required>
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
            <span>Items</span>
            <span id="items_total">0</span>
            <span>En el periodo</span>
        </div>

        <div>
            <span>Cantidades:</span>
            <span id="quantitys">0.00</span>
        </div>

        <div>
            <span>Ganancias:</span>
            <span id="earning">DOP 0.00</span>
        </div>
    </div>

    <!-- Resultado -->
    <div class="table_items_quantity">

    </div>
</div>