import PaymentsServices from "./payments.services.js";


export const addPayment = async (req, res) => {
    try {

        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id

        // 1. Agregar Pago
        const method = new PaymentsServices()
        const result = await method.addPayment(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'new.invoice',
            resource: {
                type: 'invoice',
                id: result[0].msg || 0
            },
            action: 'add_payment',
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


export const deletePayment = async (req, res) => {
    try {

         const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id

        // 1. Agregar Pago
        const method = new PaymentsServices()
        const result = await method.deletePayment(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'new.invoice',
            resource: {
                type: 'invoice',
                id: result[0].msg || 0
            },
            action: 'delete_payment',
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