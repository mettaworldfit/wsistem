<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Consultas de datos </h1>
        </div>

        <div class="float-right">

        </div>

    </div>
</div>

<div class="generalContainer">
    <div class="queryContainer">

        <form action="" onsubmit="event.preventDefault(); Query();">
            <div class="queryData">
                <div class="row">
                    <div class="col-sm-4">
                        <select name="" id="action" class="form-custom search" required>
                            <option value="" selected disabled>seleccionar</option>
                            <option value="productos_vendidos">Cantidad de productos vendidos</option>
                            <option value="piezas_vendidas">Cantidad de piezas vendidas</option>
                            <option value="servicios_vendidos">Cantidad de servicios vendidos</option>
                            <option value="serial_facturado">Consultar serial vendido</option>
                            <option value="detalle_ventas_mes">Detalle de ventas del mes</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4" id="col-query">
                        <input class="form-custom" type="text" name="" id="query" placeholder="Introduce una palabra clave">                        
                    </div>

                    <div class="col-sm-3" id="col-month">
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

                    <div class="col-sm-3" id="col-year">
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

                    <div class="col-sm-2" id="col-dateq1">
                        <input class="form-custom" type="date" name="" id="dateq1">

                    </div>
                    <div class="col-sm-2" id="col-dateq2">
                        <input class="form-custom" type="date" name="" id="dateq2">
                    </div>
                    <div class="col-sm-1" id="queryButtons">
                        <button class="btn-blue btn-custom " type="submit" id="querybtn">
                            <i class="fas fa-search"></i>
                            <p>Consultar</p>
                        </button>
                    </div>
                </div>
            </div>

            <div class="queryResult" id="queryResult"></div>
        </form>
    </div>
</div>