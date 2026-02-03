$(document).ready(function () {

    /* ===== SEGURIDAD QZ (OBLIGATORIO) ===== */

    qz.security.setCertificatePromise(function (resolve, reject) {
        fetch(SITE_URL + "src/qz-tray/get-cert.php", {
            cache: 'no-store'
        })
            .then(res => {
                console.log("HTTP status:", res.status);
                if (!res.ok) throw new Error("Cert not loaded");
                return res.text();
            })
            .then(cert => {
                console.log("===== CERT RAW START =====");
                console.log(cert);
                console.log("===== CERT RAW END =====");

                console.log("Length:", cert.length);
                console.log("Starts with:", cert.slice(0, 40));
                console.log("Ends with:", cert.slice(-40));

                // Validación dura
                if (
                    !cert.includes("-----BEGIN CERTIFICATE-----") ||
                    !cert.includes("-----END CERTIFICATE-----")
                ) {
                    throw new Error("Contenido NO es un certificado X509");
                }

                resolve(cert);
            })
            .catch(err => {
                console.error("❌ Error certificado:", err);
                reject(err);
            });
    });


    qz.security.setSignatureAlgorithm("SHA512");
    qz.security.setSignaturePromise(function (toSign) {
        return function (resolve, reject) {
            fetch(SITE_URL + 'src/qz-tray/sign.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ request: toSign })
            })
                .then(res => {
                    if (!res.ok) throw new Error("Firma no generada");
                    return res.text();
                })
                .then(signature => {
                    console.log("Firma recibida:", signature);
                    resolve(signature.trim());
                })
                .catch(err => {
                    console.error("Error firma:", err);
                    reject(err);
                });
        };
    });

    $('#launch').on('click', function () {
        /* ===== CONEXIÓN SEGURA ===== */
        const connectQZ = qz.websocket.isActive()
            ? Promise.resolve()
            : qz.websocket.connect();

        connectQZ.then(() => {
            console.log("Conexión establecida con QZ Tray.");
            return qz.printers.find("POS-80");
        })
            .then(printer => {
                console.log("Impresora encontrada:", printer);
                const config = qz.configs.create(printer, {
                    copies: 1,
                    units: "mm",
                    size: { width: 80 },
                    margins: { top: 0, right: 0, bottom: 0, left: 0 }
                });

                const printData = [
                    '\x1B\x40',       // INIT (inicia la impresora)
                    '\x1B\x61\x01',   // CENTRAR
                    'Prueba de Impresión\n',  // Texto de prueba
                    '\x1B\x61\x00',   // IZQUIERDA
                    'Texto de prueba\n',  // Más texto
                    '\x1D\x56\x00'    // CORTE (corta el papel)
                ];

                return qz.print(config, printData);
            })
            .then(() => {
                console.log('✅ Impresion de prueba realizada correctamente');
            })
            .catch(err => {
                console.error('❌ Error QZ Tray:', err);
            });
    })


}); // Ready