import { getConnection } from '../../config/db.js';

export default class PaymentsRepository {

    static async addPayment(data, database) {

        const query = `CALL pg_crearPago(?, ?, ?, ?, ?, ?, ?, ?)`;

        const values = [
            Number(data.usuario_id),
            Number(data.customer_id),
            data.received,
            Number(data.invoice_id),         // ID de la factura de venta (si aplica)
            Number(data.invoiceRP_id),       // ID de la factura de reparación (si aplica)
            Number(data.method),             // Método de pago
            data.comment || '',              // Observación/comentario
            data.date || ''                  // Fecha (puede venir vacía)
        ];

        const db = await getConnection(database);

        const [result] = await db.query(query, values);
        return result?.[0] || result;

    }

     static async deletePayment(data, database) {

        const query = `CALL pg_eliminarPago(?, ?, ?)`;

        const values = [
            Number(data.payment_id),
            Number(data.inv_id),         // ID de pago factura de venta (si aplica)
            Number(data.invrp_id),       // ID de pago factura de reparación (si aplica)            
        ];

        const db = await getConnection(database);

        const [result] = await db.query(query, values);
        return result?.[0] || result;

    }

}