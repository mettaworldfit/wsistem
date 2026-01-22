$(document).ready(function () {

    // Configuracion de los bonos 
    $('#formBonus').on('submit', function (e) {
        e.preventDefault();
        const data = {
            min: $('#min_invoice').val(),
            value: $('#bonus_value').val(),
            status: $('#status').val(),
            action: 'configuracion_bonos'
        }

        sendAjaxRequest({
            url: "services/config.php",
            data,
            successCallback: (res) => {

                if (res.includes("ready")) {
                    notifyAlert("Datos guardados correctamente");
                } else {
                    notifyAlert("Ha ocurrido un error", "error");
                }
            },
            errorCallback: (res) => {
                console.error(res);
            }
        })
    })

    /**============================================================= 
    * CONFIGURACION DE LAS ETIQUETAS
    ===============================================================*/

    $('#formLabel').on('submit', function (e) {
        e.preventDefault();

        const data = {
            nombre_config: $('input[name="nombre_config"]').val(),
            descripcion: $('input[name="descripcion"]').val(),

            ancho_mm: $('input[name="ancho_mm"]').val(),
            alto_mm: $('input[name="alto_mm"]').val(),
            orientacion: $('select[name="orientacion"]').val(),

            tipo_barcode: $('select[name="tipo_barcode"]').val(),
            mostrar_texto_barcode: $('select[name="mostrar_texto_barcode"]').val(),
            barcode_font_size: $('input[name="barcode_font_size"]').val(),
            barcode_x: $('input[name="barcode_x"]').val(),
            barcode_y: $('input[name="barcode_y"]').val(),
            barcode_width: $('input[name="barcode_width"]').val(),
            barcode_height: $('input[name="barcode_height"]').val(),

            mostrar_descripcion: $('select[name="mostrar_descripcion"]').val(),
            descripcion_font_size: $('input[name="descripcion_font_size"]').val(),
            descripcion_x: $('input[name="descripcion_x"]').val(),
            descripcion_y: $('input[name="descripcion_y"]').val(),
            descripcion_width: $('input[name="descripcion_width"]').val(),
            descripcion_height: $('input[name="descripcion_height"]').val(),

            mostrar_precio: $('select[name="mostrar_precio"]').val(),
            precio_font_size: $('input[name="precio_font_size"]').val(),
            precio_x: $('input[name="precio_x"]').val(),
            precio_y: $('input[name="precio_y"]').val(),
            precio_width: $('input[name="precio_width"]').val(),
            precio_height: $('input[name="precio_height"]').val(),

            impresora: $('input[name="impresora"]').val(),
            activo: $('select[name="activo"]').val(),
            action: "agregar_etiqueta"
        };

        sendAjaxRequest({
            url: "services/config.php",
            data,
            successCallback: (res) => {
                if (res > 0) {
                    notifyAlert("Datos guardados correctamente");
                } else {
                    notifyAlert("Ha ocurrido un error", "error");
                }
            },
            errorCallback: (res) => {
                console.error(res);
            }
        });
    });

    // Preview de la etiqueta
    $('#generate_code').on('click', function (e) {
        e.preventDefault();

        const params = new URLSearchParams({

            // CONFIG ETIQUETA
            nombre_config: $('input[name="nombre_config"]').val(),
            descripcion: $('input[name="descripcion"]').val(),

            ancho_mm: $('input[name="ancho_mm"]').val(),
            alto_mm: $('input[name="alto_mm"]').val(),
            orientacion: $('select[name="orientacion"]').val(),

            tipo_barcode: $('select[name="tipo_barcode"]').val(),
            mostrar_texto_barcode: $('select[name="mostrar_texto_barcode"]').val(),
            barcode_font_size: $('input[name="barcode_font_size"]').val(),
            barcode_x: $('input[name="barcode_x"]').val(),
            barcode_y: $('input[name="barcode_y"]').val(),
            barcode_width: $('input[name="barcode_width"]').val(),
            barcode_height: $('input[name="barcode_height"]').val(),

            mostrar_descripcion: $('select[name="mostrar_descripcion"]').val(),
            descripcion_font_size: $('input[name="descripcion_font_size"]').val(),
            descripcion_x: $('input[name="descripcion_x"]').val(),
            descripcion_y: $('input[name="descripcion_y"]').val(),
            descripcion_width: $('input[name="descripcion_width"]').val(),
            descripcion_height: $('input[name="descripcion_height"]').val(),

            mostrar_precio: $('select[name="mostrar_precio"]').val(),
            precio_font_size: $('input[name="precio_font_size"]').val(),
            precio_x: $('input[name="precio_x"]').val(),
            precio_y: $('input[name="precio_y"]').val(),
            precio_width: $('input[name="precio_width"]').val(),
            precio_height: $('input[name="precio_height"]').val(),

            impresora: $('input[name="impresora"]').val(),
            activo: $('select[name="activo"]').val(),

            // DATOS DEL PRODUCTO
            codigo: '555555',
            descripcion_producto: 'Nombre producto',
            precio: 1000
        });

        const width = 800;
        const height = 600;
        const x = (screen.width - width) / 2;
        const y = (screen.height - height) / 2;

        const url = SITE_URL + 'src/tcpdf/preview.php?' + params.toString();

        window.open(url, 'EtiquetaPreview', `left=${x},top=${y},width=${width},height=${height},scrollbars=yes`
        );
    })

    // Eliminar label
    $(document).on('click', '.erase_label', function () {

        const data = {
            label: $(this).data('id'),
            name: $(this).data('name')
        };

        alertify.confirm("Eliminar etiqueta", "¿Estas seguro que deseas eliminar la etiqueta N°"+ data.name + " ?",
            function () {
                sendAjaxRequest({
                    url: "services/config.php",
                    data: {
                        label_id: data.label,
                        action: "eliminar_etiqueta"
                    },
                    successCallback: (res) => {
                        dataTablesInstances['labels'].ajax.reload(null, false)
                    },
                    errorCallback: (err) => {
                        console.error(err)
                    },
                    verbose: true
                })

            },
            function () {

            });
    })

    /**============================================================= 
    * CONFIGURACION DE LAS FACTURAS
    ===============================================================*/

    // Configuracion de correo electronico
    $('#formMail').on('submit', function (e) {
        e.preventDefault()

        const data = {
            company: $('#company').val(),
            logo: $('#logo').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            host: $('#host').val(),
            port: $('#port').val(),
            smtps: $('#smtps').val(),
            facebook: $('#facebook').val(),
            whatsapp: $('#whatsapp').val(),
            instagram: $('#instagram').val(),
            action: 'configuracion_correo'
        }

        sendAjaxRequest({
            url: "services/config.php",
            data,
            successCallback: (res) => {
                if (res.includes("ready")) {
                    notifyAlert("Datos guardados correctamente");
                } else {
                    notifyAlert("Ha ocurrido un error", "error");
                }
            },
            errorCallback: (res) => {
                console.error(res);
            }

        })
    })

    // Configuracion de PDF
    $('#configPdf').on('submit', function (e) {
        e.preventDefault()

        const data = {
            logo: $('#logo').val(),
            slogan: $('#slogan').val(),
            address: $('#address').val(),
            tel: $('#tel').val(),
            policy: $('#policy').val(),
            title: $('#title').val(),
            action: 'configuracion_pdf'
        }

        sendAjaxRequest({
            url: "services/config.php",
            data,
            successCallback: (res) => {
                if (res.includes("ready")) {
                    notifyAlert("Datos guardados correctamente");
                } else {
                    notifyAlert("Ha ocurrido un error", "error");
                }
            },
            errorCallback: (res) => {
                console.error(res);
            }

        })
    })

})