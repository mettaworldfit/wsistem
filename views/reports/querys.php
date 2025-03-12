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
                            <option value="imei_facturado">Consultar imei vendido</option>

                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <input class="form-custom" type="text" name="" id="query"
                            placeholder="Introduce una palabra clave">
                    </div>

                    <div class="col-sm-2" id="col-dateq1">
                        <input class="form-custom" type="date" name="" id="dateq1">

                    </div>
                    <div class="col-sm-2" id="col-dateq2">
                        <input class="form-custom" type="date" name="" id="dateq2">
                    </div>
                    <div class="col-sm-1">
                        <button class="btn-blue btn-custom " type="submit" id="querybtn">
                            <i class="fas fa-search"></i>
                            <p>Consultar</p>
                        </button>
                    </div>
                </div>
            </div>

            <div class="queryResult" id="queryResult">


            </div>
        </form>
    </div>
</div>