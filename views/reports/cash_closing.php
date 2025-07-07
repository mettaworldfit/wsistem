<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Cierres de caja</h1>
        </div>


        <div class="float-right">
            <div class="float-right">
                <a href="#" class="btn-custom btn-green" data-toggle="modal" data-target="#modalCashOpening">
                    <i class="fas fa-door-open"></i>
                    <p>Abrir caja</p>
                </a>

                <?php if ($cashOpening): ?>
                    <a href="#" class="btn-custom btn-red" data-toggle="modal" data-target="#modalCashClosing" id="cash_closing">
                        <i class="fas fa-door-closed"></i>
                        <p>Cerrar caja</p>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="generalContainer">
    <table id="cashClosing" class="table-custom table">
        <thead>
            <tr>
                <th class="hide-cell">N°</th>
                <th>Usuario</th>
                <th class="hide-cell">Total real</th>
                <th class="hide-cell">Gastos</th>
                <th class="hide-cell">Diferencia</th>
                <th>Fecha apertura</th>
                <th>Fecha cierre</th>
                <th class="hide-cell">Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>


<!-- Cierre de caja -->
<div class="modal fade" id="modalCashClosing" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalCierreCajaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-light border-bottom">
        <h5 class="modal-title" id="modalCierreCajaLabel">Cierre de Caja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form action="" onsubmit="event.preventDefault(); cashClosing();">
          <input type="hidden" name="id" value="<?= $cashOpening->cierre_id ?>" id="closingId">

          <div class="row col-sm-12 invoice-head-modal">
            <div class="col-sm-3 head-content">
              <h6>Total vendido</h6>
              <input type="text" class="invisible-input text-success" value="<?= number_format($totalPurchase ?? 0) ?>" id="total" disabled>
            </div>

            <div class="col-sm-3 head-content">
              <h6>Total Esperado</h6>
              <input type="text" class="invisible-input text-primary" value="" id="total_expected" disabled>
            </div>

            <div class="col-sm-3 head-content">
              <h6>Efectivo</h6>
              <input type="text" class="invisible-input text-primary" value="0" id="real" disabled>
            </div>

            <div class="col-sm-3 head-content">
              <h6>Diferencia</h6>
              <input type="text" class="invisible-input" value="0" id="total_difference" disabled>
            </div>
          </div>

          <div class="grid-date-container">

            <div class="grid-form-user">
              <div class="grid-date-field">
                <label class="">Usuario:</label>
                <select class="form-custom search col-sm-12" name="user" id="user_id" required>
                  <option value="" selected disabled>Selecionar usuario</option>
                  <?php $users = Help::loadUsers();
                  while ($user = $users->fetch_object()): ?>
                    <option value="<?= $user->usuario_id ?>"><?= ucwords($user->nombre) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="cash-closing-check">
                <div class="cash-closing-radio">
                  <input class="" type="radio" name="radioDefault" id="generatePDF" checked>
                  <label class="" for="generatePDF"><i class="fas fa-file-pdf"></i> PDF</label>
                </div>

                <div class="cash-closing-radio">
                  <input class="" type="radio" name="radioDefault" id="generateMail">
                  <label class="" for="generateMail"><i class="fas fa-envelope"></i> Email</label>
                </div>
              </div>
            </div>

            <div class="grid-form-date">
              <div class="grid-date-field">
                <label class="">Fecha Apertura:</label>
                <input type="datetime-local" class="form-custom color-black" value="<?= $cashOpening->fecha_apertura ?? '' ?>" id="opening_date" disabled required>
              </div>

              <div class="grid-date-field">
                <?php
                date_default_timezone_set('America/Santo_Domingo');
                $datetimeRD = date('Y-m-d\TH:i');
                ?>
                <label class="">Fecha Cierre:</label>
                <input type="datetime-local" class="form-custom" id="closing_date" value="<?= $datetimeRD ?>" required>
              </div>
            </div>
          </div>


          <!-- Información detallada -->
          <div class="grid-form">

            <div class="grid-summary">

              <legend class="summary-legend">Resumen de cierre</legend>

              <div class="form-field">
                <label class="">+ Ingresos Efectivo:</label>
                <input type="text" class="" id="cash_income" value="<?= number_format($cash ?? 0.00, 2) ?>" readonly disabled>
              </div>

              <div class="form-field">
                <label class="">+ Ingresos Tarjeta:</label>
                <input type="text" class="" id="card_income" value="<?= number_format($card ?? 0.00, 2) ?>" readonly disabled>
              </div>

              <div class="form-field">
                <label class="">+ Ingresos Transferencia:</label>
                <input type="text" class="" id="transfer_income" value="<?= number_format($transfers ?? 0.00, 2) ?>" readonly disabled>
              </div>

              <div class="form-field">
                <label class="">+ Ingresos Cheques:</label>
                <input type="text" class="" id="check_income" value="<?= number_format($checks ?? 0.00, 2) ?>" readonly disabled>
              </div>


              <div class="form-field field-subtraction">
                <label class="">- Gastos externos:</label>
                <input type="text" class="" id="external_expenses" value="<?= number_format($externalExpenses ?? 0.00, 2) ?>" disabled>
              </div>

              <div class="form-field field-subtraction">
                <label class="">- Gastos internos:</label>
                <input type="text" class="" id="cash_expenses" value="<?= number_format($cashExpenses ?? 0.00, 2) ?>" disabled>
              </div>

              <div class="form-field field-subtraction-active">
                <label class="">- Reembolsos:</label>
                <input type="number" class="field-active" id="refund" placeholder="0.00">
              </div>

              <div class="form-field field-subtraction-active">
                <label class="">- Retiros:</label>
                <input type="number" class="field-active" id="withdrawals" placeholder="0.00">
              </div>

            </div><!-- grid-summary -->


            <div class="grid-cash-closing">
              <legend class="cash-closing-legend">Datos de cierre</legend>

              <label class="">Observaciones:</label>
              <textarea class="form-custom" rows="3" id="notes" placeholder="Notas adicionales..."></textarea>

              <div class="field-cash-closing">
                <label class="">Saldo Inicial:</label>
                <input type="number" class="form-custom" id="initial_balance" value="<?= $cashOpening->saldo_inicial ?? 0.00 ?>">

                <label class="fw-bold">Total efectivo en caja:</label>
                <input type="number" class="form-custom fw-bold" id="current_total" value="" required>
              </div>
            </div> <!-- grid-cash-closing-->

          </div><!-- grid-form -->

      </div> <!-- body -->
      <div class="modal-footer bg-light">

        <button type="button" class="btn-custom btn-red" data-dismiss="modal" id="">
          <i class="fas fa-window-close"></i>
          <p>Salir</p>
        </button>
        <button type="submit" class="btn-custom btn-green" id="">
          <i class="fas fa-door-closed"></i>
          <p>Cerrar caja</p>
        </button>
      </div>

      </form>
    </div>
  </div>
</div>
</div>

<!-- Abrir caja -->
<div class="modal fade" id="modalCashOpening" data-bs-backdrop="static" data-keyboard="false" tabindex="-1"
  aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Abrir caja</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form action="" onsubmit="event.preventDefault(); cashOpening();">

          <div class="form-group col-sm-12">
            <p class="title-info">
              Abre el saldo incial con el cual iniciaste en caja
            </p>
          </div>

          <div class="row col-md-12">

            <div class="form-group col-md-6">
              <label for="saldo_inicial" class="form-check-label label-nomb">Saldo Inicial:<span class="text-danger">*</span></label>
              <input class="form-custom" type="number" name="saldo_inicial" id="cash_initial" required>
            </div>

            <div class="form-group col-md-6">

              <?php
              date_default_timezone_set('America/Santo_Domingo');
              $datetimeRD = date('Y-m-d\TH:i');
              ?>
              <label for="fecha_apertura" class="form-check-label label-nomb">Fecha apertura:<span class="text-danger">*</span></label>
              <input class="form-custom" type="datetime-local" name="fecha_apertura" value="<?= $datetimeRD ?>" id="opening" required>
            </div>

          </div> <!-- Row col-md-12 -->


          <div class="mt-4 modal-footer">
            <button type="button" class="btn-custom btn-red" data-dismiss="modal">
              <i class="fas fa-window-close"></i>
              <p>Salir</p>
            </button>

            <button type="submit" href="#" class="btn-custom btn-green">
              <i class="fas fa-plus"></i>
              <p>Registrar</p>
            </button>
          </div>

        </form>

      </div> <!-- Body -->
    </div>
  </div>
</div>