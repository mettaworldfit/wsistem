import fs from 'fs';
import path from 'path';
import axios from 'axios';
import 'dotenv/config';
import * as cheerio from 'cheerio';

const DATASET_URL = process.env.DATASET_URL;

export default class Customers {

    /**
     * Consulta información de un RNC/Cédula en el portal de la DGII.
     * Realiza un scraping a la página oficial utilizando VIEWSTATE (ASP.NET WebForms)
     * para simular la búsqueda del formulario.
     *
     * @async
     * @function getRNC
     * @param {string|number} rnc - Número de RNC o cédula a consultar.
     * @returns {Promise<Object|string>} Retorna un objeto con los datos encontrados
     * o un mensaje de error en caso de fallo.
     */
    async getRNC(rnc) {
        try {

            // Primero cargar la página
            const page = await axios.get(
                DATASET_URL,
                {
                    headers: {
                        'User-Agent': 'Mozilla/5.0'
                    }
                }
            );

            const $ = cheerio.load(page.data);

            // Obtener VIEWSTATE
            const VIEWSTATE = $('#__VIEWSTATE').val();
            const EVENTVALIDATION = $('#__EVENTVALIDATION').val();
            const VIEWSTATEGENERATOR = $('#__VIEWSTATEGENERATOR').val();

            // Crear formulario
            const formData = new URLSearchParams();

            formData.append('__VIEWSTATE', VIEWSTATE);
            formData.append('__EVENTVALIDATION', EVENTVALIDATION);
            formData.append('__VIEWSTATEGENERATOR', VIEWSTATEGENERATOR);
            formData.append('ctl00$cphMain$txtRNCCedula', rnc);

            formData.append(
                'ctl00$cphMain$btnBuscarPorRNC',
                'BUSCAR'
            );

            // Hacer POST
            const response = await axios.post(
                DATASET_URL,
                formData,
                {
                    headers: {
                        'Content-Type':
                            'application/x-www-form-urlencoded',
                        'User-Agent': 'Mozilla/5.0'
                    }
                }
            );

            const $$ = cheerio.load(response.data);

            const result = {};

            $$('table tr').each((i, el) => {

                const tds = $$(el).find('td');

                if (tds.length >= 2) {

                    const key = $$(tds[0])
                        .text()
                        .trim()
                        .replace(':', '');

                    const value = $$(tds[1])
                        .text()
                        .trim();

                    result[key] = value;
                }
            });

            return result;

        } catch (error) {

            return error.message
            console.error(error.message);
        }
    }

}

