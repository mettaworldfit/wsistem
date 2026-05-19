$(document).ready(function () {


    const refreshTime = 59 * 60 * 1000;

    const credentials = {
        usuario: 'local',
        password: '1234',
        database: 'proyecto'
    };

    async function authToken() {
        try {

            const response = await fetch('http://localhost:3000/dgii/login',
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

        const response = await fetch('http://localhost:3000/dgii/consultar_ecf', {
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

        const response = await fetch('http://localhost:3000/dgii/buscar_rnc', {
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

    })

    /*
    | ------------------------------------------------------
    | DATOS DEL CONTRIBUYENTE
    | ------------------------------------------------------
    */

    $('#formEmisor').on('submit', function(e){
        e.preventDefault()

        const passcert = $('#passcert').val();

        const formData = new FormData(this);
        formData.append('action','datos_contribuyente');
        formData.append('passcert',passcert)

        if(!passcert) return notifyAlert("Debes introducir la contraseña del certificado","error");

        sendAjaxRequest({
            url: "services/ecf.php",
            data: formData,
            successCallback: (res) => {
               console.log(res)
            },
            errorCallback: (err) => {
                console.error(err)
            }
        })        
    })


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

        const url = `http://localhost:3000/dgii/download/xml/${id}`;

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

        if (!file) {
            return notifyAlert('Seleccione un certificado', 'error');
        }

        const extension = file.name.split('.').pop().toLowerCase();

        if (!['pfx', 'p12'].includes(extension)) {
            return notifyAlert('Solo se permiten PFX o P12', 'error'
            );
        }

        $('.upload-title').text(file.name);

        const formData = new FormData();
        formData.append('cert', file);
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



})