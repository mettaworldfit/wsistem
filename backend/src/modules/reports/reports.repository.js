import { getConnection } from '../../config/db.js';

export default class ReportsRepository {

    static async cashClosing(data, database) {

        const db = await getConnection(database);

        const query = `CALL c_cierreCaja(?,?,?,?,?,?,?,?,?,?,?,?,?,?)`;
        const values = [
            data.usuario_id,
            data.closing_date,
            data.initial_balance,
            data.cash_income,
            data.card_income,
            data.transfer_income,
            data.check_income,
            data.cash_expenses,
            data.external_expenses,
            data.withdrawals,
            data.refunds,
            data.total,
            data.current_total,
            data.notes || ""
        ]

        const [result] = await db.query(query, values);
        return result?.[0] || result;
    }

    static async cashOpening(data, database) {
        const db = await getConnection(database);

        const query = `CALL c_aperturaCaja(?,?,?)`;
        const values = [
            data.usuario_id,
            data.opening_date,
            data.initial_balance
        ]

        const [result] = await db.query(query, values);
        return result?.[0] || result;
    }

}