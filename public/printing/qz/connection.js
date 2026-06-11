/* =========================
   QZ TRAY CLEAN CONNECTION
   ========================= */

const QZ_VERBOSE = true;

const qzLog = (...args) => QZ_VERBOSE && console.log('%c[QZ]', 'color:#1976d2;font-weight:bold;', ...args);
const qzError = (...args) => QZ_VERBOSE && console.error('%c[QZ]', 'color:#d32f2f;font-weight:bold;', ...args);

/* =========================
   FLAGS DE CONTROL
   ========================= */

window.__QZ = window.__QZ || {
    connecting: false,
    ready: false
};

/* =========================
   CERTIFICADO
   ========================= */

qz.security.setCertificatePromise((resolve, reject) => {

    qzLog('Solicitando certificado...');

    fetch(SITE_URL + 'public/printing/get-cert.php', { cache: 'no-store' })
        .then(res => {
            if (!res.ok) throw new Error('Error cargando certificado');
            return res.text();
        })
        .then(cert => {

            if (!cert.includes('BEGIN CERTIFICATE')) {
                throw new Error('Certificado inválido');
            }

            qzLog('Certificado OK');
            resolve(cert);
        })
        .catch(err => {
            qzError('Error certificado:', err);
            reject(err);
        });
});

/* =========================
   FIRMA
   ========================= */

qz.security.setSignatureAlgorithm('SHA512');

qz.security.setSignaturePromise(toSign => {

    return (resolve, reject) => {

        qzLog('Solicitando firma...');

        fetch(SITE_URL + 'public/printing/sign.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request: toSign })
        })
            .then(res => {
                if (!res.ok) throw new Error('Error generando firma');
                return res.text();
            })
            .then(signature => {
                qzLog('Firma OK');
                qzLog(signature)
                resolve(signature.trim());
            })
            .catch(err => {
                qzError('Error firma:', err);
                reject(err);
            });
    };
});

/* =========================
   CONEXIÓN SEGURA
   ========================= */

async function initQZ() {

    if (window.__QZ.connecting) {
        qzLog('Conexión ya en progreso...');
        return;
    }

    if (qz.websocket.isActive()) {
        qzLog('QZ ya está conectado');
        return loadPrinters();
    }

    try {

        window.__QZ.connecting = true;

        qzLog('🔌 Conectando a QZ Tray...');

        await qz.websocket.connect();

        window.__QZ.ready = true;

        qzLog('✅ QZ conectado');

        await loadPrinters();

    } catch (err) {
        qzError('Error conexión QZ:', err);
    } finally {
        window.__QZ.connecting = false;
    }
}

/* =========================
   LISTAR IMPRESORAS
   ========================= */

async function loadPrinters() {

    try {

        const printers = await qz.printers.find();

        qzLog('🖨 Impresoras:', printers);

        const $select = $('#impresoraSelect');
        $select.empty().append('<option value=""></option>');

        printers.forEach(p => {
            $select.append(`<option value="${p}">${p}</option>`);
        });

        const defaultPrinter = 'POS-80';

        if (printers.includes(defaultPrinter)) {
            $select.val(defaultPrinter).trigger('change');
            qzLog('✔ Impresora por defecto:', defaultPrinter);
        }

        // TEST PRINT (opcional)
        // testPrint(printers[0]);

    } catch (err) {
        qzError('Error listando impresoras:', err);
    }
}

/* =========================
   TEST PRINT
   ========================= */

async function testPrint(printer) {

    try {

        const config = qz.configs.create(printer);

        const data = [{
            type: 'raw',
            format: 'plain',
            data:
                '\x1B\x40' +
                '*** TEST QZ ***\n' +
                'Conexion OK\n' +
                new Date().toLocaleString() +
                '\n\n\n\x1D\x56\x41'
        }];

        await qz.print(config, data);

        qzLog('🧾 Test print enviado');

    } catch (err) {
        qzError('Error test print:', err);
    }
}

/* =========================
   AUTO START
   ========================= */

document.addEventListener('DOMContentLoaded', () => {
    initQZ();
});