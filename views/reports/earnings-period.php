<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Ganancias por período </h1>
        </div>
    </div>
</div>

<div class="generalContainer">
    <h6 class="legend-summary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-funnel-icon lucide-funnel">
            <path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z" />
        </svg>
        Filtros de Busqueda</h6>

    <div class="filters">
        <form action="" id="queryForm">
            <!-- Filtros de fecha -->
            <div class="date-filter">
                <div>
                    <label for="mes"><i class="far fa-calendar-alt"></i> Mes:</label>
                    <select class="form-custom search" name="mes" id="month">
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

                <div>
                    <label for="anio"><i class="far fa-calendar-alt"></i> Año:</label>
                    <select class="form-custom search" name="anio" id="year">
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
                <button class="btn-green btn-custom " type="submit" id="querybtn">
                    <i class="fas fa-file-excel"></i>
                    <p>Excel</p>
                </button>
            </div>
        </form>
    </div>
</div>
