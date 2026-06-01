<div class="approval-layout">

    <!-- Sidebar -->
    <aside class="approval-sidebar">

        <div class="brand-box">
            <small>Certificacion dgii</small>
        </div>

        <ul class="steps-list">
            <li data-step="1">Firmar XML</li>
            <li data-step="2">Pruebas Datos e-CF</li>
            <li data-step="3">Aprobacion Comercial</li>
            <li data-step="4">Simulacion e-CF</li>
            <li data-step="5">Rep. Impresa</li>
            <li data-step="6">Validacion Rep. Impresa</li>
            <li data-step="7">URL Servicios Prueba</li>
            <li data-step="8">URL Servicios Produccion</li>
            <li data-step="9">Verificacion Estatus</li>
            <li data-step="10">Finalizado</li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="approval-content">

        <!-- Step 2: Pruebas de Datos e-CF -->
        <div class="step" id="step2" data-step="2" style="display:none;">

            <div class="card-box">
                <h6>Entrada JSON - Prueba de Datos e-CF</h6>
                <textarea class="form-custom" id="pruebaInput" placeholder='{"ECF":{...}}' rows="6"></textarea>
                <div class="btn-group">
                    <button class="btn-custom btn-blue" id="sendPrueba">
                        <i class="fas fa-paper-plane"></i>
                        <p>Procesar e-CF</p>
                    </button>
                    <button class="btn-custom btn-neutral" id="clearInput">
                        <i class="fas fa-eraser"></i>
                        <p>Limpiar</p>
                    </button>
                </div>
            </div>

            <!-- Upload -->
            <div class="card-box">
                <h6>Cargar Excel de Pruebas</h6>
                <div class="upload-row">
                    <label class="upload-area" data-step="2" style="width: 54%; height: 65px">
                        <input type="file" id="step2File" accept=".xlsx,.xls">
                        <div class="upload-title">Arrastra o selecciona el Excel</div>
                    </label>

                    <button class="btn-custom btn-blue" id="btnUpload-step2">
                        <i class="fas fa-upload"></i>
                        <p>Subir</p>
                    </button>

                    <button class="btn-custom btn-green" id="btnSendAll-step2">
                        <i class="fas fa-paper-plane"></i>
                        <p>Enviar Todos</p>
                    </button>
                </div>
            </div>

            <!-- Lista -->
            <div class="card-box">
                <h6>Comprobantes del Excel</h6>
                <div class="acecf-list" id="ecfList"></div>
            </div>
        </div>

        <!-- Step 3: Aprobación Comercial -->
        <div class="step" id="step3" data-step="3" style="display:none;">

            <!-- Entrada JSON -->
            <div class="card-box">
                <h6>Entrada JSON - Aprobacion Comercial</h6>
                <textarea class="form-custom" id="jsonInput" placeholder='{"ACECF":{...}}' rows="6"></textarea>

                <div class="btn-group">
                    <button class="btn-custom btn-blue" id="sendACECF">
                        <i class="fas fa-paper-plane"></i>
                        <p>Procesar ACECF</p>
                    </button>
                    <button class="btn-custom btn-neutral" id="clearInput">
                        <i class="fas fa-eraser"></i>
                        <p>Limpiar</p>
                    </button>
                </div>
            </div>

            <!-- Upload -->
            <div class="card-box">
                <h6>Cargar Excel de Pruebas</h6>
                <div class="upload-row">
                    <label class="upload-area" data-step="3" style="width: 54%; height: 65px">
                        <input type="file" id="excelFile" accept=".xlsx,.xls">
                        <div class="upload-title">Arrastra o selecciona el Excel</div>
                    </label>

                    <button class="btn-custom btn-blue" id="btnUpload-step3">
                        <i class="fas fa-upload"></i>
                        <p>Subir</p>
                    </button>

                    <button class="btn-custom btn-green" id="btnSendAll-step3">
                        <i class="fas fa-paper-plane"></i>
                        <p>Enviar Todos</p>
                    </button>
                </div>
            </div>

            <!-- Lista -->
            <div class="card-box">
                <h6>Comprobantes del Excel</h6>
                <div class="acecf-list" id="acecfList"></div>
            </div>
        </div>

        <!-- Step 4: Simulación e-CF -->
        <div class="step" id="step4" data-step="4" style="display:none;">
            <div class="card-box certification-box">
                <div class="cert-header">
                    <h3>Pruebas de Facturación</h3>
                    <p>
                        Envie los comprobantes de prueba a la DGII
                        para validar su sistema
                    </p>
                </div>

                <!-- RESUMEN -->
                <div class="summary-grid">
                    <div class="summary-card" data-id="31">
                        <div class="summary-info">
                            <h4>E31</h4>
                            <span>Crédito Fiscal</span>
                        </div>
                        <div class="summary-total">4</div>
                    </div>

                    <div class="summary-card" data-id="32">
                        <div class="summary-info">
                            <h4>E32</h4>
                            <span>Consumo < 250k</span>
                        </div>
                        <div class="summary-total">4</div>
                    </div>

                    <div class="summary-card" data-id="32_250k">
                        <div class="summary-info">
                            <h4>E32</h4>
                            <span>Consumo >= 250k</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>

                    <div class="summary-card" data-id="33">
                        <div class="summary-info">
                            <h4>E33</h4>
                            <span>Notas de Débito</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>

                    <div class="summary-card" data-id="34">
                        <div class="summary-info">
                            <h4>E34</h4>
                            <span>Notas de Crédito</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>

                    <div class="summary-card" data-id="41">
                        <div class="summary-info">
                            <h4>E41</h4>
                            <span>Compras</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>

                    <div class="summary-card" data-id="43">
                        <div class="summary-info">
                            <h4>E43</h4>
                            <span>Gastos Menores</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>

                    <div class="summary-card" data-id="44">
                        <div class="summary-info">
                            <h4>E44</h4>
                            <span>Regimen Especial</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>

                    <div class="summary-card" data-id="45">
                        <div class="summary-info">
                            <h4>E45</h4>
                            <span>Gubernamental</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>

                    <div class="summary-card" data-id="46">
                        <div class="summary-info">
                            <h4>E46</h4>
                            <span>Exportanciones</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>

                    <div class="summary-card" data-id="47">
                        <div class="summary-info">
                            <h4>E47</h4>
                            <span>Pagos al Exterior</span>
                        </div>
                        <div class="summary-total">2</div>
                    </div>
                </div>

                <div class="btn-group">
                    <button class="btn-custom btn-green" id="sendAllStep4">
                        <i class="fas fa-paper-plane"></i>
                        <p>Enviar Todos</p>
                    </button>

                    <button class="btn-custom btn-neutral" id="clearSimulation">
                        <i class="fas fa-eraser"></i>
                        <p>Limpiar</p>
                    </button>
                </div>
            </div>

            <!-- Lista -->
            <div class="card-box" style="margin-bottom: 3rem;">
                <h6>Comprobantes</h6>
                <div class="acecf-list" id="simulationList"></div>
            </div>
        </div>

        <!-- Atras  y siguiente -->
        <div class="step-navigation">
            <button type="button" class="btn-custom btn-neutral" id="backStep">
                <p>Atrás</p>
            </button>

            <button type="button" class="btn-custom btn-green" id="nextStep">
                <p>Finalizar</p>
            </button>
        </div>

    </main>

    <!-- Resultados -->
    <aside class="approval-results">
        <h6>Resultados</h6>
        <div class="results-box" id="results">
            <!-- Aquí se agregan los resultados -->
            <div class="wait-result">
                <i class="fas fa-inbox"></i>
                <p>Los resultados apareceran aqui</p>
            </div>
        </div>
    </aside>

</div>