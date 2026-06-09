import InvoicesRepository from "./invoices.repository.js";

export default class InvoicesServices {

    /**
     * Factura al contado
     */
    async cashInvoice(body, database) {
        if (!database) {
            throw new Error('Base de datos no especificada');
        }
        const result = await InvoicesRepository.cashInvoice(body, database);
        return result;
    }

    /**
     * Factura a credito
     */
    async creditInvoice(body, database) {
        if (!database) {
            throw new Error('Base de datos no especificada');
        }
        const result = await InvoicesRepository.creditInvoice(body, database);
        return result;
    }
}