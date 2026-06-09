import EcfRepository from './ecf.repository.js';
import {
    ECF, Transformer, Signature, ENVIRONMENT, P12Reader,
    getCodeSixDigitfromSignature, generateEcfQRCodeURL, getCurrentFormattedDateTime
} from 'dgii-ecf';
import StatusOperation from 'dgii-ecf/dist/networking/types.js';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';


// Obtener __dirname en ES Modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);


function formatToMySQLDatetime(fechaStr) {
    // fechaStr = '08-05-2026 16:30:00'
    const [datePart, timePart] = fechaStr.split(' '); // ['08-05-2026', '16:30:00']
    const [day, month, year] = datePart.split('-');   // ['08','05','2026']
    return `${year}-${month}-${day} ${timePart}`;     // '2026-05-08 16:30:00'
}

function formatToMySQLDate(fechaStr) {
    // fechaStr = '08-05-2026'
    const [day, month, year] = fechaStr.split('-');
    return `${year}-${month}-${day}`; // '2026-05-08'
}

export default class EcfServices {
    

    /**
     * Enviar documentos electronicos a la dgii
     * 
     *  No utilizada aun
     */
    async sendDocumentECF(jsonFile, database) {
        try {

            // 1. Extraer Datos
            const tipoECF = jsonFile.ECF.Encabezado.IdDoc.TipoeCF;
            const rncEmisor = jsonFile.ECF.Encabezado.Emisor.RNCEmisor;
            const rncComprador = jsonFile.ECF.Encabezado.Comprador.RNCComprador;
            const montototal = jsonFile.ECF.Encabezado.Totales.MontoTotal;

            // 2. Fechas
            const fechaFirma = jsonFile.ECF.FechaHoraFirma = getCurrentFormattedDateTime()
            jsonFile.ECF.Encabezado.Emisor.FechaEmision = new Date()
                .toLocaleDateString('es-DO', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                })
                .replace(/\//g, '-');

            const fechaEmision = jsonFile.ECF.Encabezado.Emisor.FechaEmision;

            // 3. Generar secuencia eNCF
            const eNCF = await this.generateENCF(database, tipoECF);
            jsonFile.ECF.Encabezado.IdDoc.eNCF = `E${eNCF}`;

            // 4. Transformar JSON a XML
            const transformer = new Transformer();
            const xml = transformer.json2xml(jsonFile);

            // 5. Obtener Certificado
            const { certs, env } = await this.getClientCertP12(database)

            // 6. Firmar el XML file
            const signature = new Signature(certs.key, certs.cert);
            const signedXml = signature.signXml(xml);

            // 7. Autenticacion
            const ecf = new ECF(certs, env);
            await ecf.authenticate();

            // 8. Generar codigo de seguridad e-CF
            const securityCode = getCodeSixDigitfromSignature(signedXml);

            const urlQr = generateEcfQRCodeURL(
                rncEmisor, rncComprador, eNCF, montototal, fechaEmision, fechaFirma, securityCode, env
            );

            // 9. Enviar Documento 
            const fileName = `${rncEmisor}E${eNCF}.xml`
            const response = await ecf.sendElectronicDocument(signedXml, fileName);

            //. 10 Respuesta 
            const getStatus = await ecf.statusTrackId(response.trackId);

            getStatus.urlQr = urlQr;
            getStatus.securityCode = securityCode;
            getStatus.user_id = req.usuario.user_id

            // 11. Guardar
            const emissions = new Emissions();
            await emissions.saveElectronicInvoice(database, signedXml, getStatus);

            res.status(200).json({
                status: getStatus
            });

        } catch (error) {
            error.user_id = req.usuario.user_id
            await emissions.saveElectronicInvoice(database, signedXml, getStatus);
            return res.status(500).json({
                mensaje: error
            });
        }
    }

    /**
    * Guardar documento electronico
    */
    async saveElectronicInvoice(database, xmlFile, response) {
        try {
            if (!xmlFile || !database) {
                throw new Error('Faltan parametros');
            }

            const transformer = new Transformer;
            const jsonFile = transformer.xml2Json(xmlFile);

            const insertId = await EcfRepository.saveECF(database, jsonFile)

            if (!insertId) {
                throw new Error('No se pudo guardar la factura electrónica');
            }

        } catch (error) {
            console.log(error)
            throw error;
        }
    }


    /**
    * Guarda aprobaciones comerciales recibidas en la base de datos.
    * @param {string} xmlContent - Contenido completo del XML
    */
    async saveCommercialApproval(xmlContent) {
        if (!xmlContent) {
            throw new Error('XML es son requeridos');
        }

        const transformer = new Transformer();
        const jsonFile = transformer.xml2Json(xmlContent);

        const result = await EcfRepository.saveACECF(jsonFile);
        return result;
    }

    /**
     * Genera un nuevo eNCF utilizando la secuencia configurada
     * en la base de datos del cliente.
     *
     * @async
     * @function generateENCF
     *
     * @param {string} database
     * Nombre de la base de datos del cliente.
     *
     * @param {string} tipoECF
     * Tipo de eCF a generar.
     * Ejemplos:
     * - E31 = Factura Crédito Fiscal
     * - E32 = Factura Consumo
     * - E33 = Nota Débito
     * - E34 = Nota Crédito
     *
     */
    async generateENCF(database, tipoECF) {

        const db = await EcfRepository.getTransaction(database);

        try {

            const rows = await EcfRepository.getSequencesByType(db, tipoECF);

            if (!rows.length) {
                throw new Error(`No existen secuencias para ${tipoECF}`);
            }

            let secuenciaValida = null;

            for (const secuencia of rows) {

                const hoy = new Date();
                const fechaVencimiento = new Date(secuencia.fecha_vencimiento);

                switch (secuencia.estado) {
                    case 'Activa':
                        // Validar vencimiento
                        if (fechaVencimiento < hoy) {
                            await EcfRepository.updateStatus(
                                db,
                                secuencia.id,
                                'Vencida'
                            );
                            continue;
                        }

                        // Validar agotamiento
                        if (secuencia.secuencia_actual > secuencia.secuencia_final) {
                            await EcfRepository.updateStatus(
                                db,
                                secuencia.id,
                                'Agotada'
                            );
                            continue;
                        }

                        secuenciaValida = secuencia;
                        break;

                    case 'Agotada':
                        throw new Error('La secuencia está agotada.');
                    case 'Vencida':
                        throw new Error('La secuencia está vencida.');
                    case 'Pendiente':
                        throw new Error('La secuencia aún no está activa.');
                    case 'Cancelada':
                        throw new Error('La secuencia ha sido cancelada.');
                    case 'Inactiva':
                        throw new Error('La secuencia está inactiva.');
                    default:
                        throw new Error(`Estado desconocido en la secuencia: ${secuencia.estado}`);
                }

                if (secuenciaValida) {
                    break;
                }

            }

            if (!secuenciaValida) {
                throw new Error('No hay secuencias disponibles.');
            }

            const numeroActual = secuenciaValida.secuencia_actual;
            const encf = `${tipoECF}${String(numeroActual).padStart(10, '0')}`;

            const nuevaSecuencia = numeroActual + 1;

            let nuevoEstado = secuenciaValida.estado;

            // Validar si se agotó
            if (nuevaSecuencia > secuenciaValida.secuencia_final) {
                nuevoEstado = 'Agotada';
            }

            await EcfRepository.updateSequence(
                db,
                secuenciaValida.id,
                nuevaSecuencia,
                nuevoEstado
            );

            await db.commit();

            return encf;

        } catch (error) {

            await db.rollback();
            throw error;

        } finally {
            db.release();
        }
    }

    /**
      * Obtiene el certificado .p12 del cliente según su 
      * base de datos y devuelve certificado y entorno
      * 
      * @param {string} database - Base de datos del cliente
      * @returns {Promise<{certs: object, env: string}>}
      */
    async getClientCertP12(database) {
        try {
            // 2. Datos del cliente
            const cliente = await EcfRepository.getTaxPayerConfig(database)

            // 3.Obtener la ruta absoluta del certificado
            const certPath = path.resolve(__dirname, '../../certificates/', cliente.route_cert);

            if (!fs.existsSync(certPath)) {
                throw new Error(`Archivo certificado no encontrado: ${certPath}`);
            }

            // 4. Crear el lector del certificado con la passphrase del cliente
            const reader = new P12Reader(cliente.passphrase_cert);

            // 5. Obtener el certificados del cliente
            const certs = reader.getKeyFromFile(certPath);

            // 6. Configurar el entorno de la Api
            const environments = {
                test: ENVIRONMENT.DEV,
                certificacion: ENVIRONMENT.CERT,
                produccion: ENVIRONMENT.PROD
            };

            const env = environments[cliente.ambiente];

            if (!env) {
                throw new Error('Ambiente inválido');
            }

            return { certs, env };
        } catch (error) {
            console.error(error)
            return error
        }
    }


    /**
         * Lee el certificado .p12 del cliente desde la base de datos
         * y devuelve la información del certificado utilizando dgii-ecf.
         *
         * La función:
         * 1. Se conecta a la base de datos del cliente.
         * 2. Obtiene la ruta y passphrase del certificado almacenados
         *    en la tabla `clientes`.
         * 3. Lee el archivo `.p12` desde el sistema de archivos.
         * 4. Convierte el certificado a Base64.
         * 5. Obtiene la información del certificado utilizando `P12Reader`.
         *
         * @param {string} database
         * Nombre de la base de datos del cliente.
         *
         */
    async readCert(database) {
        try {

            const cliente = await EcfRepository.getCert(database)

            const reader = new P12Reader(cliente.passphrase_cert);

            // Read the .p12 file as base64 string
            const p12Base64 = fs.readFileSync(
                path.resolve(__dirname, '../../certificates/', cliente.route_cert),
                'base64'
            );

            // Get certificate info from base64 string
            const certInfo = reader.getCertificateInfoFromBase64(p12Base64);

            return certInfo;

        } finally {
            db.release();
        }
    }

    /**
   * Actualiza los datos del certificado de un contribuyente en la base de datos.
   *
   * @async
   * @function updateCertData
   * @param {string} database - Nombre de la base de datos del cliente.
   * @param {Object} certData - Datos del certificado devueltos por readCert.
   * @param {string} certData.serial_number
   * @param {string} certData.subject_name
   * @param {string} certData.issuer_name
   * @param {string|Date} certData.valid_from
   * @param {string|Date} certData.valid_to
   * @param {string} certData.passphrase_cert
   *
   * @returns {Promise<void>}
   */
    async updateCertData(database, certData) {
        EcfRepository.updateCert(database, certData)
    }

    /**
     *  Verificar si el ECF ya ha sido recibido
     * @param {String} xmlContent 
     */
    async checkECFExists(xmlContent) {

        if (!xmlContent) {
            throw new Error('XML es son requeridos');
        }

        const transformer = new Transformer();
        const jsonFile = transformer.xml2Json(xmlContent);

        const rncComprador = jsonFile.ECF.Encabezado.Comprador.RNCComprador._text;
        const eNCF = jsonFile.ECF.Encabezado.IdDoc.eNCF._text;

        const result = await EcfRepository.checkECFExists(rncComprador);
        return result;
    }


    /**
     *  Verificar si el RNCReceptor es el mismo del contribuyente
     * @param {String} xmlContent 
     * @returns 
     */
    async getRNCFromXML(xmlContent) {
        try {

            if (!xmlContent) {
                throw new Error('XML es son requeridos');
            }

            const transformer = new Transformer();
            const result = transformer.xml2Json(xmlContent);

            // Ajusta según tu XML real
            const rncComprador = result.ECF?.Encabezado?.Comprador?.RNCComprador._text || null;
 
           const data = await EcfRepository.getRNCFromXML(rncComprador);
           return data;

        } catch (error) {
            console.error('Error en getRNCFromXML:', error.message);
            throw error;
        }
    }
}