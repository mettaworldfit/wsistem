<div class="section-wrapper mt-2">
  <h1><i class="far fa-chart-bar"></i> Panel de control</h1>
</div> <br>



<div class="grid-container">


  <div class="card-grid">
    <div>
      <p>Ventas</p>
      <p><?= date("d/m/Y"); ?></p>
    </div>

    <span title="<?= number_format(floatval($total_purchase), 2) ?>">
      $<?= number_format_short(floatval($total_purchase)) ?>
    </span>
  </div>

  <div class="card-grid">
    <div>
      <p>Gastos</p>
      <p><?= date("d/m/Y"); ?></p>
    </div>
    <span title="<?= number_format(floatval($total_expenses), 2) ?>">
      $<?= number_format_short(floatval($total_expenses)) ?>
    </span>
  </div>

  <div class="card-grid">
    <div>
      <p>Clientes</p>
      <p>Total clientes</p>
    </div>
    <span><?= number_format($customers) ?></span>
  </div>

  <div class="card-grid">
    <div>
      <p>Proveedores</p>
      <p>Total proveedores</p>
    </div>
    <span><?= number_format($providers) ?></span>
  </div>

  <div class="card-grid">
    <div>
      <p>Productos</p>
      <p>Total productos</p>
    </div>

    <span><?= number_format($products) ?></span>
  </div>

  <div class="card-grid">
    <div>
      <p>Piezas</p>
      <p>Total piezas</p>
    </div>

    <span><?= number_format($pieces) ?></span>
  </div>


  <div class="card-grid">
    <canvas id="sales_of_the_months"></canvas>

    <div class="chart-empty" id="chart1">
      <span>Ingresos - Mensuales</span>
      <p>Aún no tienes suficientes datos para mostrar</p>
      <div class="loadingio-spinner-gear-zp4adgmadk">
        <div class="ldio-f9ntw0vxf94">
          <div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="card-grid">
    <canvas id="month"></canvas>

    <div class="chart-empty" id="chart3">
      <span>Ventas del mes</span>
      <p>Aún no tienes suficientes datos para mostrar</p>
      <div class="loadingio-spinner-gear-zp4adgmadk">
        <div class="ldio-f9ntw0vxf94">
          <div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="card-grid">
    <canvas id="expenses_of_the_months"></canvas>

    <div class="chart-empty" id="chart2">
      <span>Egresos - Mensuales</span>
      <p>Aún no tienes suficientes datos para mostrar</p>
      <div class="loadingio-spinner-gear-zp4adgmadk">
        <div class="ldio-f9ntw0vxf94">
          <div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  



</div>