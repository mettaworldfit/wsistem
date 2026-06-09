import { getConnection } from '../../config/db.js'

export default class InvoicesRepository {

    /**
     * Factura al contado
     */
    static async cashInvoice(data, database) {

        const query = `CALL vt_facturaVenta(?, ?, ?, ?, ?, ?, ?)`;

        const values = [
            Number(data.customer_id),
            Number(data.method_id),
            data.total_invoice,
            data.bonus || 0,
            Number(data.usuario_id),
            data.observation || null,
            data.date
        ];

        const db = await getConnection(database);

        const [result] = await db.query(query, values);
        return result?.[0] || result;
    }

    /**
     * Factura a credito
     */
    static async creditInvoice(data, database) {

        const query = `CALL vt_facturaAcredito(?, ?, ?, ?, ?, ?, ?)`;

        const values = [
            Number(data.customer_id),
            Number(data.payment_method),
            data.total_invoice,
            data.pay,
            Number(data.usuario_id),
            data.description,
            data.date
        ];

        const db = await getConnection(database);

        const [result] = await db.query(query, values);
        return result?.[0] || result;
    }
}