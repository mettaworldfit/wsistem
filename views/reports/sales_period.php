<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Reportes de ventas</h1>
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
        <form method="post" id="formSales">

            <!-- Filtros de fecha -->
            <div class="date-filter">
                <div>
                    <label for="start-date"><i class="far fa-calendar-alt"></i> Fecha Inicio:</label>
                    <input class="form-custom" type="datetime-local" name="fecha_inicio" id="date-start" required>
                </div>

                <div>
                    <label for="end-date"><i class="far fa-calendar-alt"></i> Fecha Final:</label>
                    <input class="form-custom" type="datetime-local" name="fecha_final" id="date-end" required>
                </div>
            </div>

            <hr>

            <!-- Filtros -->
            <div class="filter-row">
                <div>
                    <label for="usuario_id"><i class="fas fa-user"></i> Usuario:</label>
                    <select class="form-custom search" name="usuario_id" id="user_id" required>
                        <option value="0">No filtrar</option>
                        <?php
                        $users = Help::loadUsers();
                        while ($user = $users->fetch_assoc()): ?>
                            <option value="<?= $user['usuario_id'] ?>"><?= ucwords($user['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>

                </div>

                <div>
                    <label for="customer"><i class="fas fa-users"></i> Cliente:</label>
                    <select class="form-custom search" name="customer" id="customer_id">
                        <option value="0">No filtrar</option>
                        <?php
                        $customers = Help::showCustomers();
                        while ($customer = $customers->fetch_assoc()): ?>
                            <option value="<?= $customer['cliente_id'] ?>"><?= ucwords($customer['nombre']) ?> <?= ucwords($customer['apellidos'] ?? "")  ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                 <div>
                    <label for="method"><i class="fas fa-list-ul"></i> Método:</label>
                    <select class="form-custom search" name="method" id="method_id">
                        <option value="0">No filtrar</option>
                        <?php
                        $methods = Help::showPaymentMethod();
                        while ($method = $methods->fetch_assoc()): ?>
                            <option value="<?= $method['metodo_pago_id'] ?>"><?= ucwords($method['nombre_metodo']) ?> </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="filter-buttoms">
                <button class="btn-custom btn-green" type="button" id="excelSales">
                    <i class="fas fa-file-excel"></i>
                    <p>Excel</p>
                </button>

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
            <span>Total Facturado:</span>
            <span id="total">DOP 0.00</span>
        </div>

        <div>
            <span>Pendiente:</span>
            <span id="pending">DOP 0.00</span>
        </div>
    </div>

    <div class="display-result">

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