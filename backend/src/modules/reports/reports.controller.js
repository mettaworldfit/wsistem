import ReportsService from './reports.services.js';


/**
 * Cierre de caja  y hacer broadcast a todos los clientes
 */
export const cashClosing = async (req, res) => {
    try {
        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Guardar cierre de caja
        const method = new ReportsService()
        const result = await method.cashClosing(
            req.body,
            database
        );

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'cash.closing',
            resource: {
                type: 'reports',
                id: result[0].msg
            },
            action: 'cash_closing',
            timestamp: Date.now()
        }, database);

        res.json({ ok: true, id: result[0].msg });
    } catch (error) {
        console.error('Error en el cierre de caja:', error);
        res.status(500).json({ ok: false, mensaje: 'Error al realizar el cierre de caja' });
    }
};

/**
 * Abrir caja y hacer broadcast a todos los clientes
 */
export const cashOpening = async (req, res) => {
    try {
        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Guardar cierre de caja
        const method = new ReportsService()
        const result = await method.cashOpening(
            req.body,
            database
        );

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'cash.opening',
            resource: {
                type: 'reports',
                id: result[0].msg
            },
            action: 'cash_opening',
            timestamp: Date.now()
        }, database);

        res.json({ ok: true, id: result[0].msg });
    } catch (error) {
        console.error('Error al abrir caja:', error);
        res.status(500).json({ ok: false, mensaje: 'Error al abrir caja' });

    }
}