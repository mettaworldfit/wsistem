/* ===== DIAGNOSTICO ===== */

console.log('QZ version:', qz.api?.getVersion ? qz.api.getVersion() : 'N/A');
console.log('WebSocket activo:', qz.websocket.isActive());

if (window.__QZ_CONNECTING__) {
    console.warn('⚠️ Ya existe un intento de conexión en progreso');
} else {
    window.__QZ_CONNECTING__ = true;
}

/* ===== CONEXION ===== */

if (!qz.websocket.isActive()) {

    console.log('🔌 Iniciando conexión QZ...');

    qz.websocket.connect()
        .then(() => {

            console.log('✅ QZ conectado');
            console.log('WebSocket activo:', qz.websocket.isActive());

            return qz.printers.find();
        })
        .then(printers => {

            console.log('🖨 Impresoras encontradas:', printers);

            const $select = $('#impresoraSelect');
            $select.empty().append('<option value=""></option>');

            printers.forEach(printer => {
                $select.append(
                    $('<option>', {
                        value: printer,
                        text: printer
                    })
                );
            });

            const defaultPrinter = 'POS-80';

            if (printers.includes(defaultPrinter)) {
                console.log('✅ Impresora por defecto encontrada:', defaultPrinter);
                $select.val(defaultPrinter).trigger('change');
            } else {
                console.warn('⚠️ POS-80 no encontrada');
            }

            /* ===== PRUEBA DE IMPRESION ===== */

            if (printers.length > 0) {

                const config = qz.configs.create(printers[0]);

                const data = [{
                    type: 'raw',
                    format: 'plain',
                    data:
                        '\x1B\x40' +
                        '\n' +
                        '*** TEST QZ TRAY ***\n' +
                        'Conexion OK\n' +
                        'Firma OK\n' +
                        'Certificado OK\n' +
                        new Date().toLocaleString() +
                        '\n\n\n\n' +
                        '\x1D\x56\x41'
                }];

                console.log('🧪 Enviando prueba a:', printers[0]);

                return qz.print(config, data)
                    .then(() => {
                        console.log('✅ Prueba enviada correctamente');
                    });
            }
        })
        .catch(err => {
            console.error('❌ QZ Tray error:', err);
        })
        .finally(() => {
            window.__QZ_CONNECTING__ = false;
        });

} else {

    console.log('✅ QZ ya estaba conectado');

    qz.printers.find()
        .then(printers => {
            console.log('🖨 Impresoras:', printers);
        });

}