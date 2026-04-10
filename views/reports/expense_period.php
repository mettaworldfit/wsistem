<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Reportes de gastos</h1>
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
        <form method="post" id="formExpensePeriod">

            <!-- Filtros de fecha -->
            <div class="date-filter">
                <div>
                    <label for="reason"><i class="fas fa-list-ul"></i> Motivo de gasto</label>
                    <select class="form-custom search" name="reason" id="reason">
                        <option value="0">No filtrar</option>
                        <?php $reasons = Help::loadReasons();
                        while ($reason = $reasons->fetch_object()): ?>
                            <option value="<?= $reason->motivo_id ?>"><?= $reason->descripcion ?></option>
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
            </div>

            <hr>

            <!-- Filtros -->
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
            <span>Facturas</span>
            <span id="inv_total">0</span>
            <span>En el periodo</span>
        </div>

        <div>
            <span>Total Gastado:</span>
            <span id="total">DOP 0.00</span>
        </div>
    </div>

    <!-- Resultado -->
    <div class="table_expense_period">

    </div>
</div>