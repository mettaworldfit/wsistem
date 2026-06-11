/* ===== QZ-TRAY VERBOSE MODE ===== */
const QZ_VERBOSE = true;

function qzLog(...args) {
    if (!QZ_VERBOSE) return;
    console.log('%c[QZ]', 'color:#1976d2;font-weight:bold;', ...args);
}

function qzWarn(...args) {
    if (!QZ_VERBOSE) return;
    console.warn('%c[QZ]', 'color:#f9a825;font-weight:bold;', ...args);
}

function qzError(...args) {
    if (!QZ_VERBOSE) return;
    console.error('%c[QZ]', 'color:#d32f2f;font-weight:bold;', ...args);
}

/* ===== SEGURIDAD QZ-TRAY | CERTIFICADO ===== */

qz.security.setCertificatePromise(function (resolve, reject) {

    qzLog('Solicitando certificado…');

    fetch(SITE_URL + "public/printing/get-cert.php", {
        cache: 'no-store'
    })
        .then(res => {
            qzLog('HTTP status certificado:', res.status);
            if (!res.ok) throw new Error('Cert not loaded');
            return res.text();
        })
        .then(cert => {

            qzLog('Certificado recibido');
            qzLog('Longitud:', cert.length);
            qzLog('BEGIN:', cert.slice(0, 40));
            qzLog('END:', cert.slice(-40));

            // Validación dura
            if (
                !cert.includes('-----BEGIN CERTIFICATE-----') ||
                !cert.includes('-----END CERTIFICATE-----')
            ) {
                throw new Error('Contenido NO es un certificado X509');
            }

            qzLog('Certificado X509 válido ✔');
            resolve(cert);
        })
        .catch(err => {
            qzError('❌ Error certificado:', err);
            reject(err);
        });
});

/* ===== SEGURIDAD QZ-TRAY | FIRMA ===== */

qz.security.setSignatureAlgorithm('SHA512');
qz.security.setSignaturePromise(function (toSign) {

    return function (resolve, reject) {

        qzLog('Solicitud de firma enviada');
        qzLog('Payload:', toSign);

        fetch(SITE_URL + 'public/printing/sign.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request: toSign })
        })
            .then(res => {
                qzLog('HTTP status firma:', res.status);
                if (!res.ok) throw new Error('Firma no generada');
                return res.text();
            })
            .then(signature => {

                qzLog('Firma recibida');
                qzLog('Longitud firma:', signature.length);

                resolve(signature.trim());
            })
            .catch(err => {
                qzError('❌ Error firma:', err);
                reject(err);
            });
    };
});

/* ===== CONEXION ===== */
qz.websocket.connect()
    .then(() => qz.printers.find())
    .then(printers => {

        const $select = $('#impresoraSelect');
        $select.empty().append('<option value=""></option>');

        printers.forEach(printer => {
            $select.append(
                $('<option>', { value: printer, text: printer })
            );
        });

        const defaultPrinter = 'POS-80';
        if (printers.includes(defaultPrinter)) {
            $select.val(defaultPrinter).trigger('change');
        }
    })
    .catch(err => {
        console.error('QZ Tray error:', err);
    });