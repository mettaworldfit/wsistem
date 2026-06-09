import { getConnection } from "../../config/db.js";

export default class PosRepository {

    static async insertDetail(procedure, body, database) {

        try {

            const {
                order_id,
                usuario_id,
                cantidad,
                costo,
                precio,
                item_id
            } = body;

            const query = `CALL ${procedure}(?, ?, ?, ?, ?, ?)`;

            const values = [
                order_id,
                usuario_id,
                cantidad,
                costo,
                precio,
                item_id
            ];

            const db = await getConnection(database);

            const [result] = await db.query(query, values);

            return result;

        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    /**
     * Obtiene detalles para actualización de precios según orden, producto o usuario
     */
    static async getDetailsForPriceUpdate(order_id, product_id, user_id, database) {

        let whereClause = '';

        if (product_id > 0) {

            whereClause = `
                WHERE p.producto_id = ?
                AND d.usuario_id = ?
                ORDER BY d.detalle_venta_id DESC
                LIMIT 1
            `;

        } else if (order_id == 0) {

            whereClause = `
                WHERE d.usuario_id = ?
                AND d.comanda_id IS NULL
                AND d.factura_venta_id IS NULL
            `;

        } else {
            whereClause = `WHERE d.comanda_id = ?`;
        }

        const query = `
            SELECT
                d.detalle_venta_id,
                p.producto_id,
                p.precio_unitario,
                pl.valor
            FROM detalle_facturas_ventas d
            INNER JOIN detalle_ventas_con_productos dp
                ON dp.detalle_venta_id = d.detalle_venta_id
            INNER JOIN productos_con_lista_de_precios pl
                ON pl.producto_id = dp.producto_id
            INNER JOIN productos p
                ON p.producto_id = pl.producto_id
            ${whereClause}
        `;

        const db = await getConnection(database);

        let params = [];

        if (product_id > 0) {
            params = [product_id, user_id];
        } else if (order_id == 0) {
            params = [user_id];
        } else {
            params = [order_id];
        }

        const [rows] = await db.query(query, params);

        return rows;
    }

    /**
     * Actualiza el precio de un detalle específico
     */
    static async updateDetailPrice(detalle_venta_id, price, database) {

        const db = await getConnection(database);

        const query = `
            UPDATE detalle_facturas_ventas
            SET precio = ?
            WHERE detalle_venta_id = ?
        `;

        await db.query(query, [price, detalle_venta_id]);
    }

    /**
     * Elimina un detalle de venta
     */
    static async deleteDetail(detailId, database) {

        const db = await getConnection(database);

        const query = `CALL vt_eliminarDetalleVenta(?)`;

        const [result] = await db.query(query, [detailId]);
        return result;
    }

    /**
    * Edita un detalle
    */
    static async editDetail(data, database) {

        const query = `CALL pos_update_detalle(?, ?, ?, ?, ?, ?, ?, ?, ?)`;

        const values = [
            Number(data.product_id),
            Number(data.piece_id),
            Number(data.service_id),
            Number(data.detail_id),
            Number(data.usuario_id),
            data.discount || 0,
            data.taxes || 0,
            data.base_price,
            data.quantity
        ];

        const db = await getConnection(database);

        const [result] = await db.query(query, values);
        return result?.[0] || result;
    }

    /**
     * Eliminar todo el detalle de una orden
     */
    static async deleteAll(data, database) {

        const db = await getConnection(database);
        const query = `CALL pos_eliminar_todo(?,?)`;

        const [result] = await db.query(query, [data.order_id, data.usuario_id]);
        return result;
    }


    /**
     * Actualizar datos de la orden (comanda o pre factura)
     */
    static async updateOrder(body, database) {

        const db = await getConnection(database);

        try {
            const query = ` CALL ov_editarOrden(?, ?, ?, ?, ?, ?, ?, ?) `;
            const values = [
                Number(body.order_id),
                Number(body.customer),
                Number(body.usuario_id),
                body.observation,
                body.delivery || '-',
                body.address,
                body.receiver,
                body.telephone
            ];

            const db = await getConnection(database);

            const [result] = await db.query(query, values);
            return result;

        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    /**
     * Agregar orden (comanda o pre factura)
     */
    static async addOrder(data, database) {

        const query = `CALL ov_agregarOrden(?, ?, ?, ?, ?, ?, ?, ?)`;

        const values = [
            Number(data.customer),
            Number(data.usuario_id),
            Number(data.status) || 6, // Pendiente           
            data.observation,
            data.delivery || '-',
            data.address,
            data.receiver,
            data.telephone
        ];

        const db = await getConnection(database);

        const [result] = await db.query(query, values);
        return result?.[0] || result;
    }


    /**
     * Obtiene la cantidad actual en stock de un producto o pieza para validar disponibilidad al actualizar la cantidad de un detalle
     */
    static async getItemStock(data, database) {

        const db = await getConnection(database);

        let query = '';

        switch (data.item_type) {
            case 'producto':
                query = `SELECT cantidad FROM productos WHERE producto_id = ?`;
                break;

            case 'pieza':
                query = ` SELECT cantidad FROM piezas WHERE pieza_id = ?
                `;
                break;

            default:
                return null;
        }

        const [rows] = await db.query(query, [data.item_id]);

        return rows[0] || null;
    }

    /**
     * Obtiene la cantidad actual de un detalle para calcular el stock disponible considerando la cantidad actual
     * @param {*} tableDetail 
     * @param {*} tableId 
     * @param {*} detailId 
     * @param {*} database 
     * @returns 
     */
    static async getDetailQuantity(tableDetail, tableId, detailId, database) {

        const db = await getConnection(database);

        const query = `
            SELECT cantidad
            FROM ${tableDetail}
            WHERE ${tableId} = ?
        `;

        const [rows] = await db.query(query, [detailId]);

        return rows[0] || null;
    }

    /**
     *  Actualizar la cantidad de un detalle y validar stock para productos y piezas
     * @param {*} tableInfo 
     * @param {*} body 
     * @param {*} database 
     * @returns 
     */
    static async updateDetailQuantity(tableInfo, body, database) {

        const db = await getConnection(database);

        const query = `
            UPDATE ${tableInfo.tableDetail}   
            SET cantidad = ?
            WHERE ${tableInfo.tableId} = ?
        `;

        const [result] = await db.query(query, [body.quantity, body.id]);
        return result;
    }


    /**
     * Factura al contado
     */
    static async cashInvoice(data, database) {

        const query = `CALL pos_factura_venta(?, ?, ?, ?, ?)`;

        const values = [
            Number(data.usuario_id),
            Number(data.customer_id),
            Number(data.method_id),
            Number(data.order_id),
            data.total_invoice,
        ];

        const db = await getConnection(database);

        const [result] = await db.query(query, values);
        return result?.[0] || result;
    }

    /**
     * Factura a credito
     */
    static async creditInvoice(data, database) {

        const query = `CALL pos_factura_credito(?, ?, ?, ?, ?, ?, ?)`;

        const values = [
            Number(data.usuario_id),
            Number(data.customer_id),
            Number(data.method_id),
            Number(data.order_id),
            data.total_invoice,
            data.pay,
            data.date
        ];

        const db = await getConnection(database);

        const [result] = await db.query(query, values);
        return result?.[0] || result;
    }
}