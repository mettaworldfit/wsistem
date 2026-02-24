<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Ventas</h1>
        </div>
    </div>
</div>

<!-- Header resumen -->
<div class="generalContainer">
    <section class="summary-sales">
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
    </section>

    <div class="filters">
        <h6><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-funnel-icon lucide-funnel">
                <path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z" />
            </svg>
            Filtros de Busqueda</h6>

        <form method="post" id="formSales">

            <!-- Filtros de fecha -->
            <div class="date-filter">
                <div>
                    <label for="start-date">Fecha Inicio:</label>
                    <input class="form-custom" type="date" id="start-date" name="fecha_inicio">
                </div>

                <div>
                    <label for="end-date">Fecha Final:</label>
                    <input class="form-custom" type="date" id="end-date" name="fecha_final">
                </div>
            </div>

            <hr>

            <!-- Filtros vendedores -->
            <div class="filter-row">
                <div>
                    <label for="start-date">Usuario:</label>
                    <select class="form-custom search" name="user" id="">

                    </select>
                </div>

                <div>
                    <label for="start-date">Cliente:</label>
                    <select class="form-custom search" name="customer" id="">

                    </select>
                </div>
            </div>

            <button class="btn-custom btn-primary" type="submit">Filtrar</button>
        </form>
    </div>

    <div class="display-result">
       
    </div>
</div>