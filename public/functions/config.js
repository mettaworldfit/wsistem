// import * as qz from "/public/test.js?v=1.0.2";

import * as qz from "../test.js";

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
            codigo: '777788866',
            descripcion_producto: 'Nombre del producto',
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

        alertify.confirm("Eliminar etiqueta", "¿Estas seguro que deseas eliminar la etiqueta N°" + data.name + " ?",
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
            email: $('#email').val(),
            password: $('#password').val(),
            host: $('#host').val(),
            port: $('#port').val(),
            smtps: $('#smtps').val(),
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
            errorCallback: (err) => {
                console.log('%c[CONFIG]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 1500)
            }

        })
    })

    // Configuracion de PDF
    $('#configPdf').on('submit', function (e) {
        e.preventDefault()

        const data = {
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
            errorCallback: (err) => {
                console.log('%c[CONFIG]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 1500)
            }

        })
    })

    /**============================================================= 
    * CONFIGURACION DE LOS TICKETS
    ===============================================================*/

    // Boton de diagnostico
    $('#btnQzDiagnostico').on('click', function () {
        qz.runQzDiagnostic();
    });


    // Boton de test
    $("#printTest").on('click', function () {

        // sendAjaxRequest({
        //     url: "services/bills.php",
        //     data: {
        //         action: 'datos_impresion'
        //     },
        //     successCallback: (res) => {
        //         try {
        //             var data = JSON.parse(res)
        //             console.log(data)

        //          qz.gastos(data[0],data[1]);
        //         } catch (error) {
        //         console.log(error)
        //         }
        //     },
        //     errorCallback: (err) => {
        //         console.error(err)
        //     }
        // })

    });

    // Guardar configuracion
    $('#formPrinter').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('action', 'configuracion_printer');

        sendAjaxRequest({
            url: "services/config.php",
            data: formData,
            successCallback: (response) => {
                notifyAlert("Datos guardados", "success", 2000)
            },
            errorCallback: (err) => {
                console.log('%c[CONFIG]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "warning", 3000)
            }
        })
    })

    // Actualizar configuracion
    $('#formUpdatePrinter').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('action', 'actualizar_printer');

        sendAjaxRequest({
            url: "services/config.php",
            data: formData,
            successCallback: (response) => {
                notifyAlert("Datos actualizados correctamente", "success", 2000)
            },
            errorCallback: (err) => {
                console.log('%c[CONFIG]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "warning", 3000)
            }
        })
    })

    // Subir imagen
    function uploadImage() {
        let formData = new FormData();
        let archivo = $('input[name="logo"]')[0].files[0];

        if (!archivo) {
            alert('No se seleccionó ningún archivo');
            return;
        }

        formData.append('logo', archivo);
        formData.append('action', 'subir_logo');

        sendAjaxRequest({
            url: "services/config.php",
            data: formData,
            successCallback: (response) => {
                try {
                    var data = JSON.parse(response)

                    if (data.success) {
                        notifyAlert(data.success[1])
                        console.log('%c[CONFIG]', 'color:#00a3f1;font-weight:bold;', data)
                    } else {
                        notifyAlert(data.error, 'error')
                    }

                } catch (error) {
                    notifyAlert('Error de respuesta al subir la imagen', 'error')
                }
            },
            errorCallback: (err) => {
                console.log('%c[CONFIG]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            },
            verbose: false
        })
    }

    // Eliminar imagen al cambiar
    function unsetImagen() {
        // Verificar si existe la imagen antes de guardar
        sendAjaxRequest({
            url: "services/config.php",
            data: {
                action: "borrar_imagen"
            },
            successCallback: (res) => {
                try {
                    var data = JSON.parse(res);

                } catch (error) {
                    console.error("Error en respuesta del servidor ", error)
                }
            }, verbose: true
        });
    }

    // Preview logo
    $('input[name="logo"]').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            $('#preview-img').html(
                `<img src="${e.target.result}" style="max-width:100%; height:auto;">`
            );
        };
        reader.readAsDataURL(file);

        unsetImagen() // Quitar imagen anterior
        uploadImage() //Subir imagen
    });


    // Guardar datos del sitio
    $('#formSite').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('action', 'guardar_datos');

        sendAjaxRequest({
            url: "services/config.php",
            data: formData,
            successCallback: (response) => {
                notifyAlert("Datos actualizados", "success", 2000)
            },
            errorCallback: (err) => {
                console.log('%c[CONFIG]', 'color:#b51717;font-weight:bold;', err)
                notifyAlert(err, "error", 3000)
            }
        })
    })


    // Eliminar printer
    $(document).on('click', '.erase_printer', function () {

        const data = {
            printer: $(this).data('id'),
            name: $(this).data('name')
        };

        alertify.confirm("Eliminar printer", "¿Estas seguro que deseas eliminar " + data.name + " ?",
            function () {
                sendAjaxRequest({
                    url: "services/config.php",
                    data: {
                        printer_id: data.printer,
                        action: "eliminar_printer"
                    },
                    successCallback: (res) => {
                        dataTablesInstances['printers'].ajax.reload(null, false)
                    },
                    errorCallback: (err) => {
                        console.error(err)
                    },
                    verbose: false
                })

            },
            function () {

            });
    })
})