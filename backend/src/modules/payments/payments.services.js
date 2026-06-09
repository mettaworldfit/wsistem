import PaymentsRepository from "./payments.repository.js";

export default class PaymentsServices {


    async addPayment(body, database) {

        if (!database) {
            throw new Error('No hay base de datos seleccionada');
        }

        return await PaymentsRepository.addPayment(body, database)

    }

    async deletePayment(body, database) {

        if (!database) {
            throw new Error('No hay base de datos seleccionada');
        }

        return await PaymentsRepository.deletePayment(body, database)

    }

}