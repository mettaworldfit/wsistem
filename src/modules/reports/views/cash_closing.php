<div class="section-wrapper">
  <div class="align-content clearfix">
    <div class="float-left">
      <h1>Cierres de caja</h1>
    </div>


    <div class="float-right">
      <div class="float-right">
        <?php if (empty($cashOpening)): ?>
          <a href="#" class="btn-custom btn-green" data-toggle="modal" data-target="#modalCashOpening">
            <i class="fas fa-door-open"></i>
            <p>Abrir caja</p>
          </a>
        <?php endif; ?>

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



<!-- Modal: Cierre de caja -->
<div class="modal fade" id="modalCashClosing" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalCierreCajaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg .modal-cashClosing">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title" id="modalCierreCajaLabel">Cierre de caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="" method="POST" id="formCashClosing">
                    <input type="hidden" name="id" value="" id="closingId">
                    <input type="hidden" name="" value="" id="tickets_invoices">
                    <input type="hidden" name="" value="" id="tickets_payments">
                    <input type="hidden" name="" value="<?= ucwords($_SESSION['identity']->nombre) ?> <?= ucwords($_SESSION['identity']->apellidos ?? '') ?>" id="user_name">

                    <div class="row col-sm-12 invoice-head-modal">
                        <div class="col-sm-3 head-content">
                            <h6>Total Vendido</h6>
                            <input type="text" class="invisible-input text-success" value="" id="total" disabled>
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
                                    <option value="<?= $_SESSION['identity']->usuario_id ?>"><?= ucwords($_SESSION['identity']->nombre) ?> <?= ucwords($_SESSION['identity']->apellidos ?? '') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="grid-form-date">
                            <div class="grid-date-field">
                                <label class="">Fecha Apertura:</label>
                                <input type="datetime-local" class="form-custom color-black" value="" id="opening_date" disabled required>
                            </div>

                            <div class="grid-date-field">

                                <label class="">Fecha Cierre:</label>
                                <input type="datetime-local" class="form-custom" id="closing_date" value="" required>
                            </div>
                        </div>
                    </div>

                    <!-- Información detallada -->
                    <div class="grid-form">
                        <div class="grid-summary">
                            <legend class="summary-legend">Resumen de cierre</legend>

                            <div class="form-field field-plus">
                                <label class="">+ Saldo Apertura:</label>
                                <input type="hidden" class="form-custom" id="initial_balance" value="">
                                <input type="text" class="" id="input_initial_balance" value="" readonly disabled>
                            </div>

                            <div class="form-field">
                                <label class="">+ Ingresos Efectivo:</label>
                                <input type="text" class="" id="cash_income" value="" readonly disabled>
                            </div>

                            <div class="form-field">
                                <label class="">+ Ingresos Tarjeta:</label>
                                <input type="text" class="" id="card_income" value="" readonly disabled>
                            </div>

                            <div class="form-field">
                                <label class="">+ Ingresos Transferencia:</label>
                                <input type="text" class="" id="transfer_income" value="" readonly disabled>
                            </div>

                            <div class="form-field">
                                <label class="">+ Ingresos Cheques:</label>
                                <input type="text" class="" id="check_income" value="" readonly disabled>
                            </div>

                            <div class="form-field field-subtraction">
                                <label class="">- Gastos externos:</label>
                                <input type="text" class="" id="external_expenses" value="" disabled>
                            </div>

                            <div class="form-field field-subtraction">
                                <label class="">- Gastos internos:</label>
                                <input type="text" class="" id="cash_expenses" value="" disabled>
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
                                <label class="fw-bold">Total efectivo en caja:</label>
                                <input type="number" class="form-custom form-closing fw-bold" id="current_total" value="" step="0.01" min="0" inputmode="decimal" required>
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

<!-- Modal: Abrir caja -->
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
                <form action="" method="POST" id="formCashOpening">

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
                            <i class="fas fa-door-open"></i>
                            <p>Abrir caja</p>
                        </button>
                    </div>
                </form>
            </div> <!-- Body -->
        </div>
    </div>
</div>