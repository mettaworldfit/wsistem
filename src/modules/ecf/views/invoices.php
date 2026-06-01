<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Facturas electronicas</h1>
        </div>
    </div>
</div>

<div class="generalContainer">
    <table class="table-custom table" id="ecfs">
        <thead>
            <tr>
                <th>No.</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total Neto</th>
                <th>NCF</th>
                <th>Cajero</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalNCF" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Estado de Factura Electrónica
                </h4>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <input type="hidden" name="" id="RNCEmisor">
                <input type="hidden" name="" id="ecfId">

                <div class="combine-input">
                    <label for="">e-NCF</label>
                    <input type="text" name="" id="encf" disabled>
                </div>

                <div class="combine-input">
                    <label for="">Estado</label>
                    <input type="text" name="" id="status" disabled>
                    <div>
                        <button class="btn-custom btn-blue" type="button" id="forward">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <div class="combine-input">
                    <label for="">Fecha Recepción e-NCF</label>
                    <input type="text" name="" id="date" disabled>
                </div>

                <div class="combine-input">
                    <label for="">Código de seguridad e-NCF</label>
                    <input type="text" name="" id="securityCode" disabled>
                </div>

                 <div class="combine-input">
                    <label for="">XML firmado</label>
                    <input type="text" name="" id="xmlFile" disabled>
                    <div>
                        <button class="btn-custom btn-blue" type="button" id="downloadFile">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>

                  <div class="combine-input">
                    <label for="">TrackId</label>
                    <input type="text" name="" id="trackId" disabled>
                </div>

                <div class="combine-input">
                    <label for="">Repuesta</label>
                    <p id="response"></p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-custom btn-blue" data-dismiss="modal">
                    <p>Cerrar</p>
                </button>
            </div>
        </div>
    </div>
</div>