$(document).ready(function () {

    function formatForMySQL(dateStr) {
        const d = new Date(dateStr);

        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const hh = String(d.getHours()).padStart(2, '0');
        const min = String(d.getMinutes()).padStart(2, '0');
        const ss = String(d.getSeconds()).padStart(2, '0');

        return `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;
    }


    const refreshTime = 59 * 60 * 1000;

    const credentials = {
        usuario: 'local',
        password: '1234',
        database: 'proyecto'
    };

    async function authToken() {
        try {

            const response = await fetch('http://localhost:3000/api/login',
                {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(credentials)
                }
            );

            const data = await response.json();

            // Guardar fecha de la última petición
            localStorage.setItem('api_last_connection', Date.now());

        } catch (error) {
            console.error(error);
        }
    }

    // Obtener última autenticación
    const lastConnection = Number(localStorage.getItem('api_last_connection'));

    // Tiempo transcurrido
    const elapsed = Date.now() - lastConnection;

    // Si nunca se autenticó o expiró
    if (!lastConnection || elapsed >= refreshTime) {
        authToken();
    }

    // Esperar el tiempo restante para renovar
    setTimeout(() => {

        authToken();

        // Renovar continuamente
        setInterval(authToken, refreshTime);

    }, Math.max(refreshTime - elapsed, 0));


    /*
    | ------------------------------------------------------
    | FACTURAS ELECTRONICAS ECF
    | ------------------------------------------------------
    */

    // Abrir Modal eNCF index facturas
    $(document).on('click', '.this-ncf', function () {

        const encf = $(this).data('ncf');

        $('#modalNCF').on('shown.bs.modal', function () {
            sendAjaxRequest({
                url: "services/ecf.php",
                data: {
                    action: "consultar_ecf",
                    encf: encf
                },
                successCallback: (res) => {

                    const data = JSON.parse(res)[0];

                    $('#ecfId').val(data.id)
                    $('#encf').val(data.encf)
                    $('#RNCEmisor').val(data.rnc_cliente)
                    $('#status').val(data.estado)
                    $('#date').val(data.creado_en)
                    $('#trackId').val(data.track_id)
                    $('#securityCode').val(data.securityCode)
                    $('#response').html(data.respuesta || '')

                    $('#status').addClass(data.estado.toLowerCase().replace(/\s+/g, '-'))
                }
            })
        });
    });

    // Consultar estado Ecf
    $('#forward').on('click', async (e) => {
        e.preventDefault()

        const response = await fetch('http://localhost:3000/api/consultar_ecf', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                RNCEmisor: $('#RNCEmisor').val(),
                noEcf: $('#encf').val()
            })
        });

        const data = await response.json();

        // Actualizar
        dataTablesInstances['ecfs'].ajax.reload(null, false)
        $("#status").val(data.estado)
    })


    // Obtener cliente por RNC
    $('#identity, #rnc').on('blur', async function () {

        const response = await fetch('http://localhost:3000/api/buscar_rnc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                rnc: $(this).val()
            })
        });

        const data = await response.json();

        //Razón social
        $('#name').val(data["Nombre/Razón Social"])
        $('#coname').val(data["Nombre Comercial"])
        $('#activ_econ').val(data["Actividad Economica"])
        $('#regime').val(data["Régimen de pagos"])
        $('#emisore').val(data["Facturador Electrónico"])
        $('#status').val(data["Estado"])
    })

    /*
    | ------------------------------------------------------
    | DATOS DEL CONTRIBUYENTE
    | ------------------------------------------------------
    */

    $('#formEmisor').on('submit', function (e) {
        e.preventDefault()

        const formData = new FormData(this);
        formData.append('action', 'datos_contribuyente');

        formData.append('subject', $('#subject').val())
        formData.append('issuer', $('#issuer').val())
        formData.append('validFrom', $('#validFrom').val())
        formData.append('validTo', $('#validTo').val())
        formData.append('noSerial', $('#noSerial').val())

        sendAjaxRequest({
            url: "services/ecf.php",
            data: formData,
            successCallback: (res) => {
                notifyAlert("Datos guardados correctamente")
            },
            errorCallback: (err) => {
                console.error(err)
                notifyAlert("Ha ocurrido un error al procesar los datos")
            }
        })
    })


    /*
    | ------------------------------------------------------
    | OBTENER DATOS DEL CERTIFICADO
    | ------------------------------------------------------
    */

    async function getCertInfo() {

        const response = await fetch('http://localhost:3000/api/estado_cert', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include'
        });

        const data = await response.json();

        $('#subject').html(data.subject)
        $('#issuer').html(data.issuer)

        const validTo = formatForMySQL(data.validTo);
        const validFrom = formatForMySQL(data.validFrom);

        $('#validTo').val(validTo)
        $('#validFrom').val(validFrom)
        $('#noSerial').val(data.serialNumber)
    }


    /*
    | ------------------------------------------------------
    | MANEJO DE ARCHIVOS Y DOCUMENTOS
    | ------------------------------------------------------
    */

    // Descargar XML Firmado
    $('#downloadFile').on('click', async () => {

        const id = $('#ecfId').val()
        const eNCF = $('#encf').val()
        const RNCEmisor = $('#RNCEmisor').val()

        const url = `http://localhost:3000/api/download/xml/${id}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include'
        })
            .then(res => res.blob())
            .then(blob => {
                const a = document.createElement('a');
                const urlBlob = window.URL.createObjectURL(blob);

                a.href = urlBlob;
                a.download = `${RNCEmisor}${eNCF}.xml`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(urlBlob);
            })
            .catch(err => console.error(err));
    })


    // Subir archivo
    function uploadFile() {
        const file = $('#cert')[0].files[0];
        const passcert = $('#passcert').val();

        if (!passcert) return notifyAlert("Debes introducir la contraseña del certificado", "error");

        if (!file) {
            return notifyAlert('Seleccione un certificado', 'error');
        }

        const extension = file.name.split('.').pop().toLowerCase();

        if (!['pfx', 'p12'].includes(extension)) {
            return notifyAlert('Solo se permiten PFX o P12', 'error');
        }

        $('.upload-title').text(file.name);

        const formData = new FormData();
        formData.append('cert', file);
        formData.append('passcert', passcert)
        formData.append('action', 'subir_certificado');

        $.ajax({
            url: SITE_URL + 'services/ecf.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: response => {
                try {
                    const data = JSON.parse(response);

                    notifyAlert(
                        data.message,
                        data.success
                            ? 'success'
                            : 'error'
                    );

                    // Obtener datos del certificado
                    getCertInfo()

                } catch (error) {
                    console.error(error);
                }
            }
        });
    }

    // Selección manual
    $('#cert').on('change', uploadFile);

    // Drag & Drop
    $('.upload-box')
        .on('dragover', function (e) {
            e.preventDefault();
            $(this).addClass('dragging');
        })
        .on('dragleave drop', function () {
            $(this).removeClass('dragging');
        })
        .on('drop', function (e) {
            e.preventDefault();

            const files = e.originalEvent.dataTransfer.files;

            if (files.length) {
                $('#cert')[0].files = files;
                uploadFile();
            } F
        });


    /*      
    | ------------------------------------------------------
    | Reutilizables
    | ------------------------------------------------------
    */

    // Botón limpiar
    $('#clearInput').on('click', function () {
        $('#jsonInput, #pruebaInput').val('');
    });



    // Contadores
    let successCount = 0;
    let errorCount = 0;

    // Función para agregar un resultado
    function addResultVisual(index, resJson) {
        const resultsDiv = document.getElementById('results');

        const itemDiv = document.createElement('div');
        itemDiv.classList.add('result-item');

        const headerDiv = document.createElement('div');
        headerDiv.classList.add('result-header');

        // Clase según éxito/error
        const isOk = resJson.ok !== false; // true si no hay ok: false
        itemDiv.classList.add(isOk ? 'result-success' : 'result-error');

        let idList = resJson.encf || `ACECF ${index}`;

        // Título
        const titleSpan = document.createElement('span');
        titleSpan.textContent = isOk ? `${idList} - Enviado correctamente` : `${idList} - Error: respuesta del servidor`;
        headerDiv.appendChild(titleSpan);

        // Flecha para desplegar
        const arrowSpan = document.createElement('span');
        arrowSpan.innerHTML = '<i class="fas fa-caret-right"></i> ver detalle';
        headerDiv.appendChild(arrowSpan);

        itemDiv.appendChild(headerDiv);

        // Detalle JSON
        const detailDiv = document.createElement('div');
        detailDiv.classList.add('result-detail');
        detailDiv.style.display = 'none'; // oculto por defecto
        detailDiv.textContent = JSON.stringify(resJson, null, 2);
        itemDiv.appendChild(detailDiv);

        // Toggle detalle al click
        headerDiv.addEventListener('click', () => {
            if (detailDiv.style.display === 'none') {
                detailDiv.style.display = 'block';
                arrowSpan.innerHTML = '<i class="fas fa-caret-down"></i> ocultar';
            } else {
                detailDiv.style.display = 'none';
                arrowSpan.innerHTML = '<i class="fas fa-caret-right"></i> ver detalle';
            }
        });

        resultsDiv.appendChild(itemDiv);
        resultsDiv.scrollTop = resultsDiv.scrollHeight;

        // Actualizar contadores
        if (isOk) successCount++;
        else errorCount++;

        document.getElementById('success-count').textContent = successCount;
        document.getElementById('error-count').textContent = errorCount;
    }

    /*      
    | ------------------------------------------------------
    | PASO 2. Prueba DE Datos e-CF
    | ------------------------------------------------------
    */

    // Renderizar 
    function renderECFList(list) {
        let html = '';

        // Combinar data y archivos
        const combined = [
            ...list.data.map((item, index) => ({ type: 'ecf', item, index })),
            ...list.files.map((file, index) => ({ type: 'file', file, index }))
        ];

        combined.forEach(entry => {
            if (entry.type === 'file') {
                html += `
            <div class="acecf-item" data-index="${entry.index}" data-type="file" data-filename="${entry.file.fileName}">
                <div class="acecf-left">
                    <span class="acecf-number"><i class="far fa-file-code"></i></span>
                    <span class="acecf-title">${entry.file.fileName}</span>
                </div>
                <div class="acecf-right"><i class="fas fa-download"></i></div>
            </div>
            `;
            } else if (entry.type === 'ecf') {
                const eNCF = entry.item.ECF?.Encabezado?.IdDoc?.eNCF || `#${entry.index + 1}`;
                html += `
            <div class="acecf-item" data-index="${entry.index}" data-type="ecf">
                <div class="acecf-left">
                    <span class="acecf-number">${entry.index + 1}</span>
                    <span class="acecf-title">${eNCF}</span>
                </div>
                <div class="acecf-right"><i class="fas fa-caret-right"></i></div>
            </div>
            `;
            }
        });

        $('#ecfList').html(html);

        // Descargar al click
        $('#ecfList .acecf-item[data-type="file"]').on('click', function () {
            const index = $(this).data('index');
            const file = list.files[index];
            if (!file) return;

            const blob = new Blob([file.xml], { type: 'application/xml' });
            const url = window.URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = file.fileName;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        });

        // Mostrar codigo json en el input
        $('#ecfList').on('click', '.acecf-item', function () {
            const index = $(this).data('index');
            const ecfData = window.ecfData[index];

            if (ecfData) {
                $('#pruebaInput').val(JSON.stringify(ecfData, null, 2));
            } else {
                console.warn('ECF no encontrado para el índice', index);
            }
        });
    }

    // Cargar ECF y Descargar XML
    $('#btnUpload-step2').on('click', async function () {
        const file = $('#step2File')[0].files[0];
        if (!file) return alert('Seleccione un Excel');

        const formData = new FormData();
        formData.append('excel', file);

        try {
            const response = await fetch('http://localhost:3000/api/convert_pruebas', {
                method: 'POST',
                credentials: 'include',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                window.ecfData = data.data;
                renderECFList(data);

            } else {
                alert(data.error);
            }

        } catch (error) {
            console.error(error);
            alert('Error al procesar el archivo: ' + error.message);
        }
    });


    // Enviar dataset completo
    $('#btnSendAll-step2').on('click', async function () {
        if (!window.ecfData || window.ecfData.length === 0) {
            return alert('No hay ECF cargado para enviar.');
        }

        // Resetear resultados y contadores
        document.getElementById('results').innerHTML = '';
        successCount = 0;
        errorCount = 0;

        // Div de resumen inicial
        const resultsDiv = document.getElementById('results');
        const startDiv = document.createElement('div');
        startDiv.classList.add('result-item', 'result-start');

        // Usar innerHTML para poder incluir <span>
        startDiv.innerHTML = `
            <span id="success-count">0</span> exitosos, 
            <span id="error-count">0</span> con error.
        `;
        resultsDiv.appendChild(startDiv);

        document.getElementById('success-count').textContent = 0;
        document.getElementById('error-count').textContent = 0;

        const statusDiv = document.createElement('div');
        statusDiv.classList.add('result-item');
        statusDiv.classList.add('result-success');
        statusDiv.textContent = `Iniciando envío automático de ${window.ecfData.length} aprobaciones...`;
        // statusDiv.style.fontStyle = 'italic';
        resultsDiv.appendChild(statusDiv);

        for (let i = 0; i < window.ecfData.length; i++) {
            const item = window.ecfData[i];

            // Agregar la clase ecf-send al elemento correspondiente
            const ecfElement = $(`.acecf-item[data-index="${i}"]`);
            ecfElement.addClass('ecf-send');

            try {
                const response = await fetch('http://localhost:3000/api/cert/recepcion_prueba', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ jsonFile: item })
                });

                const resJson = await response.json();
                addResultVisual(i + 1, resJson);

            } catch (error) {
                addResultVisual(i + 1, { ok: false, error: error.message });
            }
        }
    });


    // Enviar pruebas individual
    $('#sendPrueba').on('click', async function () {

        const texto = $('#pruebaInput').val();

        if (!texto || texto.trim().length === 0) {
            return alert('No hay ECF cargados para enviar.');
        }

        const ECF = JSON.parse(texto)

        // Resetear resultados y contadores
        document.getElementById('results').innerHTML = '';
        successCount = 0;
        errorCount = 0;

        // Div de resumen inicial
        const resultsDiv = document.getElementById('results');
        const startDiv = document.createElement('div');
        startDiv.classList.add('result-item', 'result-start');

        // Usar innerHTML para poder incluir <span>
        startDiv.innerHTML = `
            <span id="success-count">0</span> exitosos, 
            <span id="error-count">0</span> con error.
        `;
        resultsDiv.appendChild(startDiv);

        document.getElementById('success-count').textContent = 0;
        document.getElementById('error-count').textContent = 0;

        const statusDiv = document.createElement('div');
        statusDiv.classList.add('result-item');
        statusDiv.classList.add('result-success');
        statusDiv.textContent = `Iniciando envío de aprobacion comercial...`;

        resultsDiv.appendChild(statusDiv);

        try {
            const response = await fetch('http://localhost:3000/api/cert/recepcion_prueba', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ jsonFile: ECF })
            });

            const resJson = await response.json();
            addResultVisual(1, resJson);

        } catch (error) {
            addResultVisual(1, { ok: false, error: error.message });
        }

    });


    /*      
    | ------------------------------------------------------
    | PASO 3. Aprobacion Comercial
    | ------------------------------------------------------
    */


    // Renderizar Paso 3 - Aprobaciones comerciales
    function renderACECFList(list) {
        let html = '';

        // <input type="checkbox" class="acecf-checkbox" data-index="${index}"></input>
        list.forEach((item, index) => {
            html += `
            <div class="acecf-item" data-index="${index}">
                <div class="acecf-left">
                    <span class="acecf-number">${index + 1}</span>
                    <span class="acecf-title">ACECF ${index + 1}</span>
                </div>
                <div class="acecf-right"><i class="fas fa-caret-right"></i></div>
            </div>
        `;
        });

        $('#acecfList').html(html);
    }


    // Delegación de eventos para elementos dinámicos
    $('#acecfList').on('click', '.acecf-item', function () {
        const index = $(this).data('index');
        const acecfData = window.acecfData[index];

        if (acecfData) {
            $('#jsonInput').val(JSON.stringify(acecfData, null, 2));
        } else {
            console.warn('ACECF no encontrado para el índice', index);
        }
    });

    // Cuando subas el Excel, guardamos los datos globalmente
    $('#btnUpload-step3').on('click', async function () {
        const file = $('#excelFile')[0].files[0];
        if (!file) return alert('Seleccione un Excel');

        const formData = new FormData();
        formData.append('excel', file);

        const response = await fetch('http://localhost:3000/api/aprobaciones_convert', {
            method: 'POST',
            credentials: 'include',
            body: formData
        });

        const resJson = await response.json();
        window.acecfData = resJson.data;  // Guardamos globalmente
        renderACECFList(resJson.data);
    });


    // Enviar dataset completo
    $('#btnSendAll-step3').on('click', async function () {
        if (!window.acecfData || window.acecfData.length === 0) {
            return alert('No hay ACECF cargados para enviar.');
        }

        // Resetear resultados y contadores
        document.getElementById('results').innerHTML = '';
        successCount = 0;
        errorCount = 0;

        // Div de resumen inicial
        const resultsDiv = document.getElementById('results');
        const startDiv = document.createElement('div');
        startDiv.classList.add('result-item', 'result-start');

        // Usar innerHTML para poder incluir <span>
        startDiv.innerHTML = `
            <span id="success-count">0</span> exitosos, 
            <span id="error-count">0</span> con error.
        `;
        resultsDiv.appendChild(startDiv);

        document.getElementById('success-count').textContent = 0;
        document.getElementById('error-count').textContent = 0;

        const statusDiv = document.createElement('div');
        statusDiv.classList.add('result-item');
        statusDiv.classList.add('result-success');
        statusDiv.textContent = `Iniciando envío automático de ${window.acecfData.length} aprobaciones...`;
        // statusDiv.style.fontStyle = 'italic';
        resultsDiv.appendChild(statusDiv);

        for (let i = 0; i < window.acecfData.length; i++) {
            const item = window.acecfData[i];

            // Agregar la clase ecf-send al elemento correspondiente
            const acecfElement = $(`.acecf-item[data-index="${i}"]`);
            acecfElement.addClass('ecf-send');

            try {
                const response = await fetch('http://localhost:3000/api/cert/aprobacion_comercial', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ jsonFile: item })
                });

                const resJson = await response.json();
                addResultVisual(i + 1, resJson);

            } catch (error) {
                addResultVisual(i + 1, { ok: false, error: error.message });
            }
        }
    });

    // Enviar ACECF aprobacion comercial
    $('#sendACECF').on('click', async function () {

        const texto = $('#jsonInput').val();

        if (!texto || texto.trim().length === 0) {
            return alert('No hay ACECF cargados para enviar.');
        }

        const ACECF = JSON.parse(texto)

        // Resetear resultados y contadores
        document.getElementById('results').innerHTML = '';
        successCount = 0;
        errorCount = 0;

        // Div de resumen inicial
        const resultsDiv = document.getElementById('results');
        const startDiv = document.createElement('div');
        startDiv.classList.add('result-item', 'result-start');

        // Usar innerHTML para poder incluir <span>
        startDiv.innerHTML = `
            <span id="success-count">0</span> exitosos, 
            <span id="error-count">0</span> con error.
        `;
        resultsDiv.appendChild(startDiv);

        document.getElementById('success-count').textContent = 0;
        document.getElementById('error-count').textContent = 0;

        const statusDiv = document.createElement('div');
        statusDiv.classList.add('result-item');
        statusDiv.classList.add('result-success');
        statusDiv.textContent = `Iniciando envío de aprobacion comercial...`;

        resultsDiv.appendChild(statusDiv);

        try {
            const response = await fetch('http://localhost:3000/api/cert/aprobacion_comercial', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ jsonFile: ACECF })
            });

            const resJson = await response.json();
            addResultVisual(1, resJson);

        } catch (error) {
            addResultVisual(1, { ok: false, error: error.message });
        }

    });


    /*      
    | ------------------------------------------------------
    | PASO 4. Simulacion e-CF
    | ------------------------------------------------------
    */


    // Renderizar Paso 4 - Simulaciones
    function renderSimulationList(list) {
        let html = '';

        list.files.forEach((item, index) => {
            html += `
                <div class="acecf-item" data-index="${index}" data-type="file" data-filename="${item.fileName}">
                <div class="acecf-left">
                    <span class="acecf-number"><i class="far fa-file-code"></i></span>
                    <span class="acecf-title">${item.fileName}</span>
                </div>
                <div class="acecf-right"><i class="fas fa-download"></i></div>
            </div>
        `;
        });

        $('#simulationList').append(html);

        // Descargar al click
        $('#simulationList .acecf-item[data-type="file"]').on('click', function () {
            const index = $(this).data('index');
            const file = list.files[index];
            if (!file) return;

            const blob = new Blob([file.xml], { type: 'application/xml' });
            const url = window.URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = file.fileName;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        });
    }

    // Obtener los eCF de prueba de cada card individual
    $('.summary-card').on('click', async function () {
        const id = $(this).data('id')

        $(document).on('click', '.summary-card', function () {
            $(this).addClass('active');
        });

        try {
            const response = await fetch(`http://localhost:3000/api/cert/simulacion_ecf?tipo_ecf=${id}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const resJson = await response.json();

            // error backend
            if (!response.ok || !resJson.success) {

                notifyAlert(resJson.error || resJson.mensaje || 'Error desconocido', "error");
                return;
            }

            // éxito
            window.simulationData = window.simulationData || [];
            window.simulationData.push(...resJson.data);

            renderSimulationList(resJson);

        } catch (error) {

            console.error(error);

            notifyAlert(
                error.message || 'Error de conexión',
                "error"
            );
        }
    })


    // Enviar todos los eCF 
    $('#sendAllStep4').on('click', async function () {
        if (!window.simulationData || window.simulationData.length === 0) {
            return alert('No hay ningun tipo de comprobante cargado para enviar.');
        }

        // Resetear resultados y contadores
        document.getElementById('results').innerHTML = '';
        successCount = 0;
        errorCount = 0;

        // Div de resumen inicial
        const resultsDiv = document.getElementById('results');
        const startDiv = document.createElement('div');
        startDiv.classList.add('result-item', 'result-start');

        // Usar innerHTML para poder incluir <span>
        startDiv.innerHTML = `
            <span id="success-count">0</span> exitosos, 
            <span id="error-count">0</span> con error.
        `;
        resultsDiv.appendChild(startDiv);

        document.getElementById('success-count').textContent = 0;
        document.getElementById('error-count').textContent = 0;

        const statusDiv = document.createElement('div');
        statusDiv.classList.add('result-item');
        statusDiv.classList.add('result-success');
        statusDiv.textContent = `Iniciando envío automático de ${window.simulationData.length} aprobaciones...`;
        // statusDiv.style.fontStyle = 'italic';
        resultsDiv.appendChild(statusDiv);

        for (let i = 0; i < window.simulationData.length; i++) {
            const item = window.simulationData[i];

            // Agregar la clase ecf-send al elemento correspondiente
            const simulationElement = $(`.acecf-item[data-index="${i}"]`);
            simulationElement.addClass('ecf-send');

            console.log(item)

            try {
                const response = await fetch('http://localhost:3000/api/cert/simulacion_ecf', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ jsonFile: item })
                });

                const resJson = await response.json();
                addResultVisual(i + 1, resJson);

            } catch (error) {
                addResultVisual(i + 1, { ok: false, error: error });
            }
        }
    });


    // Borrar eCF cargados y desactivar summary-cards
    $('#clearSimulation').on('click', () => {

        // vaciar lista de eCF
        $('#simulationList').empty();

        // quitar active de todas las summary-card
        $('.summary-card').removeClass('active');

        // opcional: resetear arreglo global si lo estás usando
        window.simulationData = [];

    });






    /*      
     | ------------------------------------------------------
     | Inicializar funciones
     | ------------------------------------------------------
     */

    $(document).on('click', '#backStep', function () {

        // Obtener step actual activo
        const currentStep = parseInt($('.steps-list li.active').data('step'));

        if (currentStep <= 1) return; // No hacer nada si ya estamos en el primer step

        const previousStep = currentStep - 1;
        console.log(previousStep)

        sendAjaxRequest({
            url: "services/ecf.php",
            data: {
                action: "volver_al_step",
                step: previousStep
            },
            successCallback: (res) => {
                // recargar estados y mostrar step anterior
                loadCompletedSteps();

            },
            errorCallback: (err) => console.error(err)
        });
    });

    // Finalizar paso
    $(document).on('click', '#nextStep', function () {

        // Obtener step actual activo
        const currentStep = $('.steps-list li.active').data('step');

        sendAjaxRequest({
            url: "services/ecf.php",
            data: {
                action: "completar_step",
                step: currentStep
            },
            successCallback: (res) => {

                // recargar estados
                loadCompletedSteps();
            },
            errorCallback: (err) => console.error(err)
        });
    });

    // Cargar steps completados
    function loadCompletedSteps() {

        sendAjaxRequest({
            url: "services/ecf.php",
            data: {
                action: "consultar_steps",
            },
            successCallback: (res) => {

                try {
                    const data = JSON.parse(res);

                    const completedSteps = data.map(item =>
                        parseInt(item.step_numero)
                    );

                    // siguiente step
                    const currentStep = Math.max(...completedSteps) + 1;

                    document.querySelectorAll('.steps-list li').forEach(li => {

                        const step = parseInt(li.dataset.step);

                        // completados
                        if (completedSteps.includes(step)) {
                            li.classList.add('done');
                        } else {
                            li.classList.remove('done');
                        }

                        // activo
                        if (step === currentStep) {
                            li.classList.add('active');
                        } else {
                            li.classList.remove('active');
                        }

                    });

                    // ocultar todos
                    $('.step').hide();

                    // mostrar actual
                    $(`.step[data-step="${currentStep}"]`).show();
                } catch (error) {

                    console.warn("No se pudieron obtener los pasos, se selecciona step 2 por defecto");

                    const defaultStep = 1;

                    // marcar step default como activo
                    document.querySelectorAll('.steps-list li').forEach(li => {
                        const step = parseInt(li.dataset.step);
                        li.classList.remove('done', 'active');
                        if (step === defaultStep) li.classList.add('active');
                    });

                    // ocultar todos
                    $('.step').hide();

                    // mostrar step
                    $(`.step[data-step="${defaultStep}"]`).show();

                }

            },
            errorCallback: (err) => console.error(err)
        });

    }

    // Cargar steps completados
    loadCompletedSteps();


    // Función genérica para cualquier step
    function initStepUpload(stepNumber, inputSelector) {
        const areaSelector = `.upload-area[data-step="${stepNumber}"]`;
        const titleSelector = `${areaSelector} .upload-title`;

        // Selección manual
        $(inputSelector).on('change', function () {
            const file = this.files[0];
            if (!file) return;
            $(titleSelector).text(file.name);
        });

        // Drag & Drop
        $(areaSelector)
            .on('dragover', function (e) {
                e.preventDefault();
                $(this).addClass('dragging');
            })
            .on('dragleave', function () {
                $(this).removeClass('dragging');
            })
            .on('drop', function (e) {
                e.preventDefault();
                $(this).removeClass('dragging');

                const files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    $(inputSelector)[0].files = files;
                    $(titleSelector).text(files[0].name);
                }
            });
    }

    // Inicializar uploads para cada step
    initStepUpload(2, '#step2File'); // step 2
    initStepUpload(3, '#excelFile'); // step 3



})