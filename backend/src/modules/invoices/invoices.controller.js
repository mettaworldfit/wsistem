import InvoicesServices from "./invoices.services.js";


/**
 *Guardar factura al contado y hacer broadcast a todos los clientes
 */
 export const cashInvoice = async (req, res) => {
    try {

        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Agregar detalle a la base de datos
        const method = new InvoicesServices();
        const result = await method.cashInvoice(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'new.invoice',
            resource: {
                type: 'invoice',
                id: result[0].msg
            },
            action: 'insert_invoice',
            timestamp: Date.now()
        }, database);

        // 3. Responder al cliente HTTP
        return res.status(200).json({
            ok: true,
            data: result[0].msg || 0
        });

    } catch (error) {
        console.error(error);

        return res.status(500).json({
            ok: false,
            mensaje: error.message
        });
    }
}


/**
 *Guardar factura a credito y hacer broadcast a todos los clientes
 */
 export const creditInvoice = async (req, res) => {
    try {

        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Agregar detalle a la base de datos
        const method = new InvoicesServices();
        const result = await method.creditInvoice(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'new.invoice',
            resource: {
                type: 'invoice',
                id: result[0].msg
            },
            action: 'insert_invoice',
            timestamp: Date.now()
        }, database);

        // 3. Responder al cliente HTTP
        return res.status(200).json({
            ok: true,
            data: result[0].msg
        });

    } catch (error) {
        console.error(error);
        return res.status(500).json({
            ok: false,
            mensaje: error.message
        });
    }
}