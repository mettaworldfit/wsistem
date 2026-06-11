import PosServices from './pos.services.js';


/**
 * Agrega detalle y hace broadcast a clientes conectados
 * @param {object} req - Objeto de solicitud Express
 * @param {object} res - Objeto de respuesta Express
 * @param {function} broadcast - función de broadcast WS
 */
export const addDetail = async (req, res) => {
    try {
        req.body.usuario_id = req.usuario.user_id;
        const database = req.usuario.database;

        if (!req.usuario) {
            return res.status(401).json({
                ok: false,
                message: 'Usuario no autenticado'
            });
        }

        // 1. Agregar detalle a la base de datos
        const pos = new PosServices();
        const result = await pos.addDetail(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'datail.updated',
            resource: {
                type: 'detail',
                id: 0
            },
            action: 'insert_detail',
            timestamp: Date.now()
        }, database);

        // 3. Responder al cliente HTTP
        return res.status(200).json({
            ok: true
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
 * Editar detalle y hacer broadcast a clientes conectados
 */
export const editDetail = async (req, res) => {
    try {
        req.body.usuario_id = req.usuario.user_id;
        const database = req.usuario.database;

        console.log(req.body)

        // 1. Editar detalle
        const pos = new PosServices();
        const result = await pos.editDetail(
            req.body,
            database
        )

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'detail.updated',
            resource: {
                type: 'detail',
                id: 0
            },
            action: 'update_detail',
            timestamp: Date.now()
        }, database);

        return res.status(200).json({
            ok: true,
            mensaje: result
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
 * Cambiar precio de un detalle y hacer broadcast a clientes conectados
 */
export const changePrice = async (req, res) => {
    try {
        req.body.usuario_id = req.usuario.user_id;
        const database = req.usuario.database;

        const pos = new PosServices();
        const result = await pos.updatePricesByList(
            req.body,
            database
        );

        req.app.locals.broadcast({
            event: 'pricelist.updated',
            resource: {
                type: 'detail',
                id: 0
            },
            action: 'update_price',
            timestamp: Date.now()
        }, database);

        console.log('Precios actualizados:', result);

        res.status(200).json({
            ok: true,
            data: result
        });

    } catch (error) {

        console.error(error);
        res.status(500).json({
            ok: false,
            mensaje: error.message
        });
    }
}

/**
 * Eliminar detalle y hacer broadcast a clientes conectados
 */
export const deleteDetail = async (req, res) => {
    try {

        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Eliminar detalle de la base de datos

        const pos = new PosServices();
        const result = await pos.deleteDetail(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'detail.deleted',
            resource: {
                type: 'detail',
                id: 0
            },
            action: 'delete_detail',
            timestamp: Date.now()
        }, database);

        res.status(200).json({
            ok: true,
            data: result
        });


    } catch (error) {
        console.error(error);
        res.status(500).json({
            ok: false,
            mensaje: error.message
        });
    }
}

/**
 * Eliminar todo el detalle de una orden y hacer broadcast a clientes conectados
 */
export const deleteAll = async (req, res) => {
    try {
        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        console.log('Eliminando todo el detalle de la orden:', req.body);

        // 1. Eliminar detalle de la base de datos
        const pos = new PosServices();
        const result = await pos.deleteAll(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'detail.deleted',
            resource: {
                type: 'detail',
                id: 0
            },
            action: 'delete_all_details',
            timestamp: Date.now()
        }, database);

        res.status(200).json({
            ok: true,
            data: result
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({
            ok: false,
            mensaje: error.message
        });
    }
}

/**
 * Actualizar datos de la orden (ej: cliente, vendedor) y hacer broadcast a clientes conectados
 */
export const updateOrder = async (req, res) => {
    try {

        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Actualizar datos de la orden en la base de datos
        const pos = new PosServices();
        const result = await pos.updateOrder(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'order.updated',
            resource: {
                type: 'order',
                id: req.body.order_id
            },
            action: 'update_order',
            timestamp: Date.now()
        }, database);

        res.status(200).json({
            ok: true,
            data: result
        });

    } catch (error) {
        console.error(error);
        res.status(500).json({
            ok: false,
            mensaje: error.message
        });
    }
}

/**
 * Agregar orden (comanda o pre factura) y hacer broadcast a clientes conectados
 */
export const addOrder = async (req, res) => {
    try {
        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Agregar orden a la base de datos
        const pos = new PosServices();
        const result = await pos.addOrder(req.body, database);

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'order.created',
            resource: {
                type: 'order',
                id: result || 0
            },
            action: 'create_order',
            timestamp: Date.now()
        }, database);

        res.status(200).json({
            ok: true,
            data: result
        });

    } catch (error) {
        console.error(error);
        res.status(500).json({
            ok: false,
            mensaje: error.message
        });
    }
}

/**
 * Actualizar cantidad de un detalle y hacer broadcast a clientes conectados
 */
export const updateQuantity = async (req, res) => {
    try {
        const database = req.usuario.database;

        // 1. Actualizar cantidad en la base de datos
        const pos = new PosServices();
        const result = await pos.updateDetailQuantity(
            req.body,
            database
        );

        // 2. Broadcast a clientes conectados
        req.app.locals.broadcast({
            event: 'detail.updated',
            resource: {
                type: 'detail',
                id: req.body.id
            },
            action: 'update_quantity',
            timestamp: Date.now()
        }, database);

        res.status(200).json({
            ok: true,
            data: result
        });

    } catch (error) {

        res.status(500).json({
            error: true,
            message: error.message
        });

    }
};


/**
 *Guardar factura al contado y hacer broadcast a todos los clientes
 */
export const cashInvoice = async (req, res) => {
    try {

        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Agregar detalle a la base de datos
        const pos = new PosServices();
        const result = await pos.cashInvoice(req.body, database);

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


/**
 *Guardar factura a credito y hacer broadcast a todos los clientes
 */
export const creditInvoice = async (req, res) => {
    try {

        const database = req.usuario.database;
        req.body.usuario_id = req.usuario.user_id;

        // 1. Agregar detalle a la base de datos
        const pos = new PosServices();
        const result = await pos.creditInvoice(req.body, database);

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