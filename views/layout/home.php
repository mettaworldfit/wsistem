<div class="section-wrapper mt-2">
<h1><i class="fas fa-globe"></i> Panel de control</h1>
</div> <br>



<div class="navigation">

  <div class="nav">
    <a href="<?= base_url ?>reports/day">
      <div class="icon" icon="1">
        <i class="fas fa-dollar-sign"></i>
      </div>
    </a>

    <div class="info">
      <p>Ventas - Hoy</p>
     <p>$<?= number_format(floatval($total_purchase),2) ?></p> 
    </div>
  </div>

  <div class="nav">
    <a href="#">
      <div class="icon" icon="2">
        <i class="fas fa-dollar-sign"></i>
      </div>
    </a>

    <div class="info">
      <p>Gastos - Hoy</p>
    <p> $<?= number_format(floatval($total_expenses),2) ?></p> 
    </div>
  </div>


  <div class="nav">
    <a href="#">
      <div class="icon" icon="8">
      <i class="fas fa-file-invoice-dollar"></i>
      </div>
    </a>

    <div class="info">
      <p>Fuera de stock</p>
    </div>
  </div>


  <div class="nav">
    <a href="#">
      <div class="icon" icon="3">
      <i class="fas fa-truck"></i>
      </div>
    </a>

    <div class="info">
      <p>Proveedores</p>
     <p> <?= number_format($providers) ?></p>
    </div>
  </div>


  <div class="nav">
    <a href="<?= base_url ?>contacts/customers">
      <div class="icon" icon="4">
      <i class="fas fa-male"></i>
      </div>
    </a>

    <div class="info">
      <p>Clientes</p>
     <p> <?= number_format($customers) ?></p>
    </div>
  </div>


  <div class="nav">
    <a href="<?= base_url ?>products/index">
      <div class="icon" icon="5">
      <i class="fas fa-box-open"></i>
      </div>
    </a>

    <div class="info">
      <p>Productos</p>
     <p> <?= number_format($products) ?></p>
    </div>
  </div>

  <div class="nav">
    <a href="<?= base_url ?>pieces/index">
      <div class="icon" icon="6">
      <i class="fas fa-toolbox"></i>
      </div>
    </a>

    <div class="info">
      <p>Piezas</p>
     <p><?= number_format($pieces) ?></p>
    </div>
  </div>


  <div class="nav">
    <a href="#">
      <div class="icon" icon="7">
      <i class="fas fa-hammer"></i>
      </div>
    </a>

    <div class="info">
      <p>Equipos en taller</p>
     <p> <?= number_format($workshop) ?></p>
    </div>
  </div>

  

</div>

<br><br>


<!-- Gráficos -->

<div class="container-chart">
  <div class="charts col-sm-12">

    <div class="col-sm-6 chart">
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

    <div class="col-sm-6 chart">
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

  </div> <!-- container-chart -->

  <br><br>
