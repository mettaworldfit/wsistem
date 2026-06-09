import ReportsRepository from './reports.repository.js';

export default class ReportsService {

    async cashClosing(body, database) {

        if (!database) {
            throw new Error('Base de datos no especificada');
        }

        return await ReportsRepository.cashClosing(body, database);

    }

    async cashOpening(body, database) {
        if (!database) {
            throw new Error('Base de datos no especificada');
        }

        return await ReportsRepository.cashOpening(body, database);
    }
}