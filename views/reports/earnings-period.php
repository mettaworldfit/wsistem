<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Ganancias por período </h1>
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
        <form method="post" id="formEarningPeriod">

            <!-- Filtros de fecha -->
            <div class="form-group">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="filtro_rango" name="tipo_filtro" class="custom-control-input" checked>
                    <label class="custom-control-label" for="filtro_rango">Rango de fechas</label>
                </div>

                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="filtro_mes" name="tipo_filtro" class="custom-control-input">
                    <label class="custom-control-label" for="filtro_mes">Mes y año</label>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filter-row">

                <div class="filtroRango">
                    <label for="start-date"><i class="far fa-calendar-alt"></i> Fecha Inicio:</label>
                    <input class="form-custom" type="datetime-local" name="fecha_inicio" id="date-start">
                </div>

                <div class="filtroRango">
                    <label for="end-date"><i class="far fa-calendar-alt"></i> Fecha Final:</label>
                    <input class="form-custom" type="datetime-local" name="fecha_final" id="date-end">
                </div>


                <div class="filtroMes" style="display:none;">
                    <label for="mes"><i class="far fa-calendar-alt"></i> Mes:</label>
                    <select class="form-custom search" name="month" id="month">
                        <option value="">-- Mes --</option>
                        <option value="01">Enero</option>
                        <option value="02">Febrero</option>
                        <option value="03">Marzo</option>
                        <option value="04">Abril</option>
                        <option value="05">Mayo</option>
                        <option value="06">Junio</option>
                        <option value="07">Julio</option>
                        <option value="08">Agosto</option>
                        <option value="09">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>

                <div class="filtroMes" style="display:none;">
                    <label for="anio"><i class="far fa-calendar-alt"></i> Año:</label>
                    <select class="form-custom search" name="year" id="year">
                        <option value="">-- Año --</option>
                        <?php
                        $anio_actual = date('Y');
                        for ($i = $anio_actual; $i >= $anio_actual - 10; $i--) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="filter-buttoms">
                <button class="btn-green btn-custom " type="button" id="earning_report">
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
            <span>Items</span>
            <span id="item_total">0</span>
            <span>En el periodo</span>
        </div>

        <div>
            <span>Ganancias:</span>
            <span id="earning">DOP 0.00</span>
        </div>

        <div>
            <span>Total:</span>
            <span id="total">DOP 0.00</span>
        </div>
    </div>

    <!-- Resultado -->
    <div class="table_earning_period">

    </div>
</div>
