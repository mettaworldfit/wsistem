import {
    ECF,
    Transformer,
    Signature,
    getCodeSixDigitfromSignature,
    convertECF32ToRFCE,
    getCurrentFormattedDateTime,
    CustomAuthentication,
    P12Reader,
    RestApi,
    ENVIRONMENT,
    generateEcfQRCodeURL,
    generateFcQRCodeURL
} from 'dgii-ecf';

import { SenderReceiver, ReceivedStatus } from 'dgii-ecf/dist/senderReceiver/SenderReceiver.js';
import { getConnection } from '../../config/db.js';
import StatusOperation from 'dgii-ecf/dist/networking/types.js';
import Helpers from '../../utils/helpers.js'
import EcfServices from './ecf.services.js';
import EcfConverter from './ecf.converter.js';

import FormData from 'form-data';
import axios from 'axios';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';
import 'dotenv/config';
import multer from 'multer';
import xlsx from 'xlsx';


// Obtener __dirname equivalente
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const CERT_NAME = process.env.CERTIFICATE_NAME || '';
const secret = process.env.CERT_PASSWORD || '';

// Leer certificado
const reader = new P12Reader(secret);
const cert = reader.getKeyFromFile(
    path.resolve(__dirname, `../../certificates/${CERT_NAME}`)
);

/*      
| ------------------------------------------------------
| Recepciones de eCF
| ------------------------------------------------------
*/

/**
 * Devuelve semilla al Receptor
 */
export const getSeed = async (req, res) => {
    try {
        const customAuth = new CustomAuthentication(cert);
        const seed = customAuth.generateSeed();

        return res
            .status(200)
            .type('application/xml')
            .send(seed);
    } catch (err) {
        console.error(err);
        return res.status(500).json({ message: 'Error generating seed' });
    }
};

/**
 * Validar semilla recibida del Receptor
 */
export const validateSeed = async (req, res) => {
    try {
        if (!req.file) return res.status(400).json({ message: 'XML file required' });

        const signedSeed = req.file.buffer.toString('utf-8');
        const customAuth = new CustomAuthentication(cert);
        const token = await customAuth.verifySignedSeed(signedSeed);

        return res.status(200).json({
            token,
            expiracion: new Date(Date.now() + 60 * 60 * 1000).toISOString(),
        });
    } catch (err) {
        console.error(err);
        return res.status(500).json({ message: 'Validation error' });
    }
};

/**
 * Recepción de e-CF
 */
export const receiveECF = async (req, res) => {
    try {
        // Validar token
        const authHeader = req.headers.authorization;
        if (!authHeader) return res.status(401).json({ codigo: 401, estado: 'Rechazado', mensaje: 'Authorization token required' });

        const token = authHeader.replace('Bearer ', '');
        const customAuth = new CustomAuthentication(cert);
        try { await customAuth.verifyToken(token); }
        catch { return res.status(401).json({ codigo: 401, estado: 'Rechazado', mensaje: 'Token invalid' }); }

        // Validar XML
        if (!req.file) return res.status(400).json({ codigo: 400, estado: 'Rechazado', mensaje: 'XML file required' });
        const xmlContent = req.file.buffer.toString('utf-8');
        const help = new Helpers();
        help.hasXmlSignature(xmlContent);

        // Validar duplicados
        const serv = new EcfServices();
        const exists = await serv.checkECFExists(xmlContent);
        if (exists) return res.status(409).json({ codigo: 409, estado: 'Rechazado', mensaje: 'eNCF ya ha sido recibido' });

        // Validar RNC del contribuyente
        const { rnc } = await serv.getRNCFromXML(xmlContent);

        // Enviar acuse de recibido
        const senderReceiver = new SenderReceiver();
        const acuseXML = senderReceiver.getECFDataFromXML(xmlContent, rnc, ReceivedStatus['e-CF Recibido']);

        res.set('Content-Type', 'text/xml');
        return res.status(200).send(acuseXML);
    } catch (err) {
        console.error('Error al recibir e-CF', err.message);
        return res.status(500).json({ codigo: 500, estado: 'Error', mensaje: 'Internal server error', detalles: err.message });
    }
};

/**
 * Recepción de ACECF
 */
export const receiveCommercialApproval = async (req, res) => {
    try {
        // Validar token
        const authHeader = req.headers.authorization;
        if (!authHeader) return res.status(401).json({ codigo: 401, estado: 'Rechazado', mensaje: 'Token required' });

        const token = authHeader.replace('Bearer ', '');
        const customAuth = new CustomAuthentication(cert);
        try { await customAuth.verifyToken(token); }
        catch { return res.status(401).json({ codigo: 401, estado: 'Rechazado', mensaje: 'Token invalid' }); }

        // Validar XML
        if (!req.file) return res.status(400).json({ codigo: 400, estado: 'Rechazado', mensaje: 'XML file required' });
        const xmlContent = req.file.buffer.toString('utf-8');
        const help = new Helpers();
        help.hasXmlSignature(xmlContent);

        // Guardar aprobación comercial en DB
        const serv = new EcfServices();
        await serv.saveCommercialApproval(xmlContent);

        return res.status(200).json({ estado: "Recibido", mensaje: 'Aprobación comercial recibida correctamente' });
    } catch (err) {
        console.error(err);
        return res.status(500).json({ message: 'Error procesando la aprobación comercial' });
    }
};

/*      
| ------------------------------------------------------
| Certificacion dgii
| ------------------------------------------------------
*/

export const receptionTest = async (req, res) => {
    const { jsonFile } = req.body;
    const database = req.usuario?.database;
    const eNCF = jsonFile.ECF.Encabezado.IdDoc.eNCF;
    const rncEmisor = jsonFile.ECF.Encabezado.Emisor.RNCEmisor;

    try {
        // Transformar JSON a XML
        const transformer = new Transformer();
        const xml = transformer.json2xml(jsonFile);

        // Obtener certificado
        const cert = new EcfServices();
        const { certs, env } = await cert.getClientCertP12(database);

        // Firmar el XML
        const signature = new Signature(certs.key, certs.cert);
        const signedXml = signature.signXml(xml);

        // Autenticación
        const ecf = new ECF(certs, env);
        await ecf.authenticate();

        const fileName = `${rncEmisor}E${eNCF}.xml`;
        const response = await ecf.sendElectronicDocument(signedXml, fileName);

        res.json({ ok: true, mensaje: response.data || response, encf: eNCF });
    } catch (error) {
        console.log(error);
        res.status(500).json({ ok: false, mensaje: error, encf: eNCF });
    }
};

export const commercialApproval = async (req, res) => {
    const { jsonFile } = req.body;

    const database = req.usuario?.database;

    const detalle = jsonFile.ACECF.DetalleAprobacionComercial;
    const rncComprador = detalle.RNCComprador;
    const eNCF = detalle.eNCF;

    try {
        const cert = new EcfServices();
        const { certs, env } = await cert.getClientCertP12(database);

        const ecf = new ECF(certs, env);
        await ecf.authenticate();

        const transformer = new Transformer();
        const xml = transformer.json2xml(jsonFile);

        const signature = new Signature(certs.key, certs.cert);
        const signedXml = signature.signXml(xml);

        const fileName = `${rncComprador}${eNCF}.xml`;
        const response = await ecf.sendCommercialApproval(signedXml, fileName);

        console.log('respuesta', response);
        res.json({ ok: true, mensaje: response.data || response, ACECF: 'ACECF' });
    } catch (error) {
        console.log(error);
        res.status(500).json({ ok: false, mensaje: error, ACECF: 'ACECF' });
    }
};

export const simulationECF = async (req, res) => {
    const { jsonFile } = req.body;
    const database = req.usuario?.database;

    if (!jsonFile) return res.status(400).json({ error: 'Faltan parámetros en la solicitud' });

    const eNCF = jsonFile.ECF.Encabezado.IdDoc.eNCF;
    const tipoeCF = jsonFile.ECF.Encabezado.IdDoc.TipoeCF;
    const rncEmisor = jsonFile.ECF.Encabezado.Emisor.RNCEmisor;

    jsonFile.ECF.FechaHoraFirma = getCurrentFormattedDateTime();
    jsonFile.ECF.Encabezado.Emisor.FechaEmision = new Date()
        .toLocaleDateString('es-DO', { day: '2-digit', month: '2-digit', year: 'numeric' })
        .replace(/\//g, '-');

    try {
        const transformer = new Transformer();
        const xml = transformer.json2xml(jsonFile);

        const cert = new EcfServices();
        const { certs, env } = await cert.getClientCertP12(database);

        const signature = new Signature(certs.key, certs.cert);
        const signedXml = signature.signXml(xml);

        const ecf = new ECF(certs, env);
        await ecf.authenticate();

        const fileName = `${rncEmisor}${eNCF}.xml`;
        const response = await ecf.sendElectronicDocument(signedXml, fileName);

        let rfce32;
        if (tipoeCF === 32) {
            const securityCode = await getCodeSixDigitfromSignature(signedXml);
            rfce32 = await convertECF32ToRFCE(signedXml, securityCode);
        }

        res.status(200).json({ ok: true, response, encf: eNCF, rfce32 });
    } catch (error) {
        res.status(500).json({ ok: false, mensaje: error, encf: eNCF });
    }
};

/*      
| ------------------------------------------------------
| Envios de eCF
| ------------------------------------------------------
*/


/**
 * Recepcion de e-CF
 * 
 * Servicio web responsable de recibir un e‐CF tentativo (XML firmado digitalmente) y un
 * token asociado a una sesión válida y en respuesta retornar un objeto que contiene un
 * string denominado TrackId a modo de acuse de recibo, con el cual, el contribuyente
 * podrá consultar el estado de su validación
 */
export const sendECF = async (req, res) => {

    const { jsonFile } = req.body;
    const database = req.usuario.database;

    if (!jsonFile) {
        return res.status(400).json({ error: "Faltan parámetros en la solicitud" });
    }

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
    const serv = new EcfServices()
    const eNCF = await serv.generateENCF(database, tipoECF);

    jsonFile.ECF.Encabezado.IdDoc.eNCF = `E${eNCF}`;

    // 4. Transformar JSON a XML
    const transformer = new Transformer();
    const xml = transformer.json2xml(jsonFile);

    // 5. Obtener Certificado
    const { certs, env } = await serv.getClientCertP12(database);

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

    let getStatus;

    try {
        // 9. Enviar Documento 
        const fileName = `${rncEmisor}E${eNCF}.xml`
        const response = await ecf.sendElectronicDocument(signedXml, fileName);

        //. 10 Respuesta 
        const responseTrack = await ecf.statusTrackId(response.trackId);

        getStatus = responseTrack;

        getStatus.urlQr = urlQr;
        getStatus.securityCode = securityCode;
        getStatus.user_id = req.usuario.user_id

        // 11. Guardar
        await serv.saveElectronicInvoice(database, signedXml, getStatus);

        res.status(200).json({
            status: getStatus
        });

    } catch (error) {
        error.user_id = req.usuario.user_id
        await serv.saveElectronicInvoice(database, signedXml, getStatus);
        return res.status(500).json({
            mensaje: error
        });
    }
}


// Enviar resumen factura de consumo RFCE <= 250k
export const sendRFCE = async (req, res) => {
    const { jsonFile } = req.body;

    if (!jsonFile) {
        return res.status(400).json({ error: "Faltan parámetros en la solicitud" });
    }

    const database = req.usuario.database;
    const rncEmisor = jsonFile.ECF.Encabezado.Emisor.RNCEmisor;
    const eNCF = jsonFile.ECF.Encabezado.IdDoc.eNCF;

    try {

        // Transformar JSON a XML
        const transformer = new Transformer();
        const xml = transformer.json2xml(jsonFile);

        // Obtener certificado del emisor
        const cert = new EcfServices()
        const { certs, env } = await cert.getClientCertP12(database);

        // Firmar el XML file
        const signature = new Signature(certs.key, certs.cert);
        const signedXml = signature.signXml(xml);

        // Autenticacion
        const ecf = new ECF(certs, env);
        await ecf.authenticate();

        // Crear el nombre RNCEmisor + eCF.xml
        const fileName = `${rncEmisor}${eNCF}.xml`;

        // Enviar documento a la DGII
        const response = await ecf.sendSummary(signedXml, fileName);

        res.json(response.data || response);

    } catch (error) {
        res.json(error)
    }
}

/*
*Anulación de e‐NCF

*Servicio web responsable de recibir y anular los rangos de secuencias no utilizados
*(e‐NCF) a través de un XML de solicitud que contiene el código de comprobante
*electrónico, una serie de rangos, desde y hasta, así como un token asociado a una
*sesión válida.
*/

export const voidENCF = async (req, res) => {
    try {
        const { jsonFile } = req.body;

        if (!jsonFile) {
            return res.status(400).json({
                error: "Faltan parámetros en la solicitud"
            });
        }

        const database = req.usuario.database
        const rncEmisor = jsonFile.ANECF.Encabezado.RncEmisor;

        // Transformar JSON a XML
        const transformer = new Transformer();
        const xml = transformer.json2xml(jsonFile);

        // Obtener certificado del emisor
        const cert = new EcfServices()
        const { certs, env } = await cert.getClientCertP12(database);

        // Firmar XML
        const signature = new Signature(certs.key, certs.cert);
        const signedXml = signature.signXml(xml);

        // Autenticación
        const ecf = new ECF(certs, env);
        await ecf.authenticate();

        // Crear el nombre RNCEmisor + eCF.xml
        const fileName = `${rncEmisor}.xml`;

        // Enviar a DGII
        const response = await ecf.voidENCF(signedXml, fileName);

        console.log(JSON.stringify(response, null, 2));

        res.json(response.data || response);

    } catch (error) {
        console.error(error);

        res.status(500).json({
            error: true,
            message: error.message,
            details: error.response?.data || error
        });
    }
}

/**
 * Aprobación Comercial
 * 
 * Servicio web encargado de recibir las autorizaciones comerciales emitidas por los contribuyentes receptores, 
 * que consiste en la aprobación de una transacción realizada entre dos contribuyentes y para la cual se recibió 
 * un recibo electrónico de un emisor.
 */
export const sendCommercialApproval = async (req, res) => {
    try {

        const { jsonFile } = req.body;
        console.log(jsonFile)

        if (!jsonFile) {
            return res.status(400).json({
                error: "Faltan parámetros en la solicitud"
            });
        }

        const database = req.usuario.database;
        const rncComprador = jsonFile.ACECF.DetalleAprobacionComercial.RNCComprador;
        const eNCF = jsonFile.ACECF.DetalleAprobacionComercial.eNCF;

        // Obtener certificado del emisor
        const cert = new EcfServices()
        const { certs, env } = await cert.getClientCertP12(database);

        // Autenticación
        const ecf = new ECF(certs, env);
        await ecf.authenticate();

        // Transformar JSON a XML
        const transformer = new Transformer();
        const arecf = transformer.json2xml(jsonFile);

        // Firmar XML
        const signature = new Signature(certs.key, certs.cert);
        const signedXml = signature.signXml(arecf);


        // RNCComprador+eNCF.xml
        const fileName = `${rncComprador}${eNCF}.xml`;

        const response = await ecf.sendCommercialApproval(signedXml, fileName);

        console.log("respuesta", response)
        res.json({
            ok: true,
            mensaje: response.data || response
        })

    } catch (error) {
        res.status(500).json({
            ok: false,
            mensaje: error
        });
        console.log(error)
    }
}

/*      
| ------------------------------------------------------
| Consultas
| ------------------------------------------------------
*/

// Consultar resultado de eCF
export const statusTrackId = async (req, res) => {
    const { trackId } = req.body;
    const database = req.usuario.database;

    try {

        if (!trackId) {
            return res.status(400).json({
                error: "Faltan parámetros en la solicitud"
            });
        }

        const cert = new EcfServices()

        const { certs, env } = await cert.getClientCertP12(database);

        const ecf = new ECF(certs, env);
        const response = await ecf.statusTrackId(trackId);

        res.json(response)
    } catch (error) {
        res.json({
            error
        })
    }
}

// Consultar estado real del eCF
export const trackStatuses = async (req, res) => {
    const { RNCEmisor, noEcf, RNCComprador, securityCode } = req.body;
    const database = req.usuario.database;

    if (!RNCEmisor || !noEcf) {
        return res.status(400).json({ error: "Faltan parámetros en la solicitud" });
    }

    // Obtiener los datos del certificado del cliente
    const cert = new EcfServices();
    const { certs, env } = await cert.getClientCertP12(database)

    // Autenticacion
    const ecf = new ECF(certs, env);
    await ecf.authenticate();


    // const statusResponse = await ecf.inquiryStatus(
    //     RNCEmisor,
    //     noEcf,
    //     RNCComprador,
    //     securityCode
    // );

    try {

        const statusResponse = await ecf.trackStatuses(
            RNCEmisor,
            noEcf
        );

        const pool = getConnection(database);
        const db = await pool.getConnection();

        const query = "UPDATE facturas_electronicas SET estado = ? WHERE encf = ?";
        const params = [statusResponse[0].estado, noEcf];

        await db.query(query, params);

        console.log(statusResponse[0].estado)
        res.json(statusResponse[0]);

    } catch (error) {
        res.json({
            error: error || error.message
        });
    }
}

/**
* Servicio web responsable de responder a la validez o estado de un e-CF 
* a un receptor o incluso a un emisor, mediante la presentación del RNC 
* emisor, e-NCF y dos campos condicionales a la validez del comprobante, 
* RNC del comprador y el código de seguridad.
*/
export const readCertificate = async (req, res) => {
    const database = req.usuario.database;

    try {
        const cert = new EcfServices()
        const certInfo = await cert.readCert(database);

        res.json(certInfo)
    } catch (error) {
        res.json(error)
    }
}

/**
 * Consulta Directorio de Servicios
 * 
 * Se utiliza para establecer comunicación
 * entre Emisor ↔ Receptor en facturación electrónica.
 */
export const getCustomerDirectory = async (req, res) => {
    const { RNCEmisor } = req.body;
    const database = req.usuario.database

    try {
        if (!RNCEmisor) {
            return res.status(400).json({ error: "Faltan parámetros en la solicitud" });
        }

        // Obtiener los datos del certificado del cliente
        const cert = new EcfServices();
        const { certs, env } = await cert.getClientCertP12(database)

        // Obtener semilla
        const ecf = new ECF(certs, env);
        await ecf.authenticate();

        const response = await ecf.getCustomerDirectory(RNCEmisor);

        res.json(response.data || response);
    } catch (error) {

        res.status(500).json({
            error: true,
            message: error.message,
            details: error.response?.data || error
        });

    }
}

/**
 * Estatus Servicios DGII
 * 
 * Servicio web responsable de proporcionar el estatus y disponibilidad de los servicios
 * de facturación electrónica, como también las ventanas de mantenimientos de estos.
 */
export const cloudServiceStatus = async (req, res) => {
    try {
        const { apiKey, typeStatus } = req.body;

        if (!apiKey || typeStatus === undefined) {
            return res.status(400).json({
                error: "Faltan parámetros en la solicitud"
            });
        }

        let statusOperation;

        switch (String(typeStatus)) {

            case "1":
                statusOperation = StatusOperation.SERVICES_STATUS;
                break;

            case "2":
                statusOperation = StatusOperation.SERVICE_MAINTENANCE_WINDOW;
                break;

            case "3":
                statusOperation = StatusOperation.SERVICE_VERIFICATION;
                break;

            default:
                return res.status(400).json({
                    error: "typeStatus inválido"
                });
        }

        // Instanciar RestApi
        const restApi = new RestApi(ENVIRONMENT.PROD);

        // Consultar DGII
        const response = await restApi.dgiiCloudServicesStatusApi(
            statusOperation,
            apiKey
        );

        res.json(response);

    } catch (error) {
        console.error(error);

        res.status(500).json({
            error: true,
            message: error.message,
            details: error.response?.data || error
        });
    }
}

/**
 * Consulta timbre eCF (QR) 
 * 
 * Responsable de responder la validez de un e-CF remitido exclusivamente por el
 * servicio web de recepción de e‐CF, a partir de los datos incluidos en el timbre de su
 * representación impresa (RI). 
 */
export const eCFQrCode = async (req, res) => {
    try {

        const { jsonFile } = req.body;
        const database = req.usuario.database;

        if (!jsonFile) {
            return res.status(400).json({
                error: "Faltan parámetros en la solicitud"
            });
        }

        // Validar estructura ECF
        if (!jsonFile.ECF) {
            return res.status(400).json({
                error: true,
                message: "El nodo ECF es requerido"
            });
        }

        const ecfData = jsonFile.ECF;
        const rncemisor = ecfData.Encabezado.Emisor.RNCEmisor;
        const fechaEmision = ecfData.Encabezado.Emisor.FechaEmision;
        const rnccomprador = ecfData.Encabezado.Comprador.RNCComprador;
        const encf = ecfData.Encabezado.IdDoc.eNCF;
        const montototal = ecfData.Encabezado.Totales.MontoTotal;
        const fechaFirma = ecfData.FechaHoraFirma;

        // Transformar JSON a XML
        const transformer = new Transformer();
        const xml = transformer.json2xml(jsonFile,);

        // Obtiener los datos del certificado del cliente
        const cert = new EcfServices();
        const { certs, env } = await cert.getClientCertP12(database)

        // Firmar XML
        const signature = new Signature(certs.key, certs.cert);
        const signedXml = signature.signXml(xml);

        const securityCode = getCodeSixDigitfromSignature(signedXml);

        const urlQr = generateEcfQRCodeURL(
            rncemisor,
            rnccomprador,
            encf, montototal,
            fechaEmision,
            fechaFirma,
            securityCode,
            env
        );

        res.json({
            success: true,
            data: {
                rncemisor,
                encf,
                montototal,
                securityCode,
                urlQr,
                fechaEmision,
                fechaFirma,
                rnccomprador
            }
        });

    } catch (error) {
        console.error(error);

        res.status(500).json({
            error: true,
            message: error.message || "Error interno",
            details: error.response?.data || error
        });
    }
}

/**
 * Consulta timbre FC (QR).
 * 
 * Responsable de responder la validez de un Resumen de Factura de Consumo
 * Electrónica remitida exclusivamente por el servicio web de recepción FC, con un monto
 * inferior a los RD$250,000.00 a partir de los datos incluidos en el timbre de su
 * representación impresa (RI).
 */
export const FCQrCode = async (req, res) => {
    try {

        const { jsonFile } = req.body;
        const database = req.usuario.database;

        if (!jsonFile) {
            return res.status(400).json({
                error: true,
                message: "Faltan parámetros en la solicitud"
            });
        }

        // Validar estructura ECF
        if (!jsonFile.ECF) {
            return res.status(400).json({
                error: true,
                message: "El nodo ECF es requerido"
            });
        }

        const ecfData = jsonFile.ECF;

        // Extraer datos
        const rncemisor = ecfData?.Encabezado?.Emisor?.RNCEmisor;
        const fechaEmision = ecfData?.Encabezado?.Emisor?.FechaEmision;
        const rnccomprador = ecfData?.Encabezado?.Comprador?.RNCComprador;
        const encf = ecfData?.Encabezado?.IdDoc?.eNCF;
        const montototal = ecfData?.Encabezado?.Totales?.MontoTotal;
        const fechaFirma = ecfData?.FechaHoraFirma;

        // Validaciones obligatorias
        if (!rncemisor || !encf || !montototal) {
            return res.status(400).json({
                error: true,
                message: "Faltan datos requeridos para generar el QR"
            });
        }

        // Convertir JSON -> XML
        const transformer = new Transformer();
        const xml = transformer.json2xml(jsonFile);

        // Obtiener los datos del certificado del cliente
        const cert = new EcfServices();
        const { certs, env } = await cert.getClientCertP12(database)

        // Firmar XML
        const signature = new Signature(
            certs.key,
            certs.cert
        );

        const signedXml = signature.signXml(xml);

        // Obtener código seguridad
        const securityCode = getCodeSixDigitfromSignature(signedXml);

        // Generar URL QR FC
        const urlQrFC = generateFcQRCodeURL(
            rncemisor,
            encf,
            montototal,
            securityCode,
            env
        );

        // Respuesta
        res.json({
            success: true,
            data: {
                rncemisor,
                encf,
                montototal,
                securityCode,
                urlQrFC,
                fechaEmision,
                fechaFirma,
                rnccomprador
            }
        });

    } catch (error) {

        console.error(error);

        res.status(500).json({
            error: true,
            message: error.message || "Error interno",
            details: error.response?.data || error
        });
    }
}

/**
 * Consulta de Resumen de Factura (RFCE)
 * 
 * Servicio web responsable de responder la validez o estado de un ENCF a un receptor o
 * incluso a un emisor, a través de la presentación del RNC emisor, e‐NCF y el código de
 * seguridad.
 * 
 * Solo para produccion
 */
export const summaryRFCE = async (req, res) => {
    try {

        const { jsonFile } = req.body;
        const database = req.usuario.database;

        if (!jsonFile) {
            return res.status(400).json({
                error: "Faltan parámetros en la solicitud"
            });
        }

        const ecfData = jsonFile.ECF;

        // Extraer datos
        const rncemisor = ecfData?.Encabezado?.Emisor?.RNCEmisor;
        const encf = ecfData?.Encabezado?.IdDoc?.eNCF;

        // Obtiener los datos del certificado del cliente
        const cert = new EcfServices();
        const { certs } = await cert.getClientCertP12(database)

        // Autenticación
        const ecf = new ECF(certs, ENVIRONMENT.PROD);
        await ecf.authenticate();

        // Transformar JSON a XML
        const transformer = new Transformer();
        const ecf32Xml = transformer.json2xml(jsonFile);

        // Firmar XML
        const signature = new Signature(certs.key, certs.cert);
        const signedXml = signature.signXml(ecf32Xml);

        const { xml, securityCode } = convertECF32ToRFCE(signedXml);

        const response = await ecf.getSummaryInvoiceInquiry(
            rncemisor,
            encf,
            securityCode
        );

        // Respuesta
        res.json({
            success: true,
            data: {
                rncemisor,
                encf,
                securityCode,
            },
            response: response.data || response
        });

    } catch (error) {
        console.error(error);

        res.status(500).json({
            error: true,
            details: error
        });
    }
}

/*      
| ------------------------------------------------------
| Test
| ------------------------------------------------------
*/

export const simulationTest = async (req, res) => {
    try {
        const CERT_PASSWORD = process.env.CERT_PASSWORD || '';
        const CERT_NAME = process.env.CERTIFICATE_NAME || '';

        // -------------------------------
        // 1. Obtener semilla de tu API
        // -------------------------------
        const seedRes = await axios.get("http://localhost:3000/ecf/fe/autenticacion/api/semilla", {
            headers: { 'Accept': 'application/xml' }
        });

        const seedXml = seedRes.data;

        // -------------------------------
        // 2. Leer certificado
        // -------------------------------
        const reader = new P12Reader(CERT_PASSWORD);
        const certs = reader.getKeyFromFile(path.resolve(`./certificates/${CERT_NAME}`));

        if (!certs.key || !certs.cert) {
            return res.status(500).json({ message: 'Certificado o clave no encontrados' });
        }

        // -------------------------------
        // 3. Firmar la semilla
        // -------------------------------
        const signature = new Signature(certs.key, certs.cert);
        const seedSigned = signature.signXml(seedXml);

        // -------------------------------
        // 4. Enviar semilla firmada para obtener token
        // -------------------------------
        const form = new FormData();
        form.append('xml', Buffer.from(seedSigned, 'utf-8'), {
            filename: 'seedSigned.xml',
            contentType: 'application/xml'
        });

        const tokenRes = await axios.post(
            'http://localhost:3000/ecf/fe/autenticacion/api/validacioncertificado',
            form,
            { headers: form.getHeaders() }
        );

        const token = tokenRes.data.token;


        // -------------------------------
        // 5. Enviar XML de prueba al endpoint de recepción
        // -------------------------------
        const testXml = `
<ECF>
  <Encabezado>
    <IdDoc>
      <TipoeCF>31</TipoeCF>
      <eNCF>E310000000110</eNCF>
      <FechaVencimientoSecuencia>23-05-2026</FechaVencimientoSecuencia>
    </IdDoc>
    <Emisor>
      <RNCEmisor>131880681</RNCEmisor>
      <RazonSocialEmisor>Empresa Prueba SRL</RazonSocialEmisor>
    </Emisor>
    <Comprador>
      <RNCComprador>131870198</RNCComprador>
      <RazonSocialComprador>Cliente Prueba</RazonSocialComprador>
    </Comprador>
    <Totales>
      <MontoTotal>1000</MontoTotal>
    </Totales>
  </Encabezado>
  <Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
    <SignedInfo>
      <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
      <SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/>
    </SignedInfo>
    <SignatureValue>FIRMA_DE_PRUEBA</SignatureValue>
  </Signature>
</ECF>
`;

        const form2 = new FormData();
        form2.append('xml', Buffer.from(testXml, 'utf-8'), {
            filename: 'ecfPrueba.xml',
            contentType: 'application/xml'
        });

        const receptionRes = await axios.post(
            'http://localhost:3000/ecf/fe/recepcion/api/ecf',
            form2,
            {
                headers: {
                    ...form2.getHeaders(),
                    Authorization: `Bearer ${token}`
                }
            }
        );


        // -------------------------------
        // 6. Enviar XML Aprobacion comercial
        // -------------------------------

        const ACECF = `
<ACECF>
    <DetalleAprobacionComercial>
        <Version>1.0</Version>
        <RNCEmisor>131703836</RNCEmisor>
        <eNCF>E310000000058</eNCF>
        <FechaEmision>08-05-2026</FechaEmision>
        <MontoTotal>2000</MontoTotal>
        <RNCComprador>131870198</RNCComprador>
        <Estado>1</Estado>
        <DetalleMotivoRechazo>Recibí la factura electrónica</DetalleMotivoRechazo>
        <FechaHoraAprobacionComercial>08-05-2026 16:30:00</FechaHoraAprobacionComercial>
    </DetalleAprobacionComercial>
    <Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
        <SignedInfo>
            <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" />
            <SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256" />
            <Reference URI="">
                <Transforms>
                    <Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" />
                </Transforms>
                <DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256" />
                <DigestValue>7pIYI1mlClA+aPOf0q3/S0TIlTEZSCsgohs7IwNZrO8=</DigestValue>
            </Reference>
        </SignedInfo>
        <SignatureValue>
            JADEwMc07w7R+S9AtRbZgfCIyqsCnAbElpRUp7YFzfBeX6hI98MCZ1QNT1tDHM5lMIpq8oeY50KNrAYtjbztz7IWpdW2Diz0Mlcc3OX5duOjnIM7KpksxGUrBuLuhyvBj9fzJ2UBZ3Gcb8a7faEviNL3NCCAuqJ18G1KCbsQt8FfkUbrH7BnyBo6ggmXd+nPVdSZGDXi64i6bmrY+l4PmMdqlAfMMxSLOYAVYTYGX1+jrtKhbENRhXXfZ0H9o7H0O6nAgOlis8F5eAthluiRueUUUakBcVC0RZaMW/Oen9zZG8Xmae3wxdSK2x3KREKrsqlxkDygRoArtb+CzIKAyg==
        </SignatureValue>
    </Signature>
</ACECF>
`;

        const form3 = new FormData();
        form3.append('xml', Buffer.from(ACECF, 'utf-8'), {
            filename: 'acecf.xml',
            contentType: 'application/xml'
        });

        const aprobacionRes = await axios.post(
            'http://localhost:3000/ecf/fe/aprobacioncomercial/api/ecf',
            form3,
            {
                headers: {
                    ...form3.getHeaders(),
                    Authorization: `Bearer ${token}`
                }
            }
        );


        // -------------------------------
        // 7. Devolver la respuesta de recepción
        // -------------------------------

        return res.status(200).json({
            message: 'Simulación completada',
            semilla: seedXml,
            semillaFirmada: seedSigned,
            token: token,
            recepcionResponse: receptionRes.data,
            aprobacionComercial: aprobacionRes.data
        });

    } catch (err) {
        console.error('Error en simulación de cliente:', err.message);
        return res.status(500).json({
            message: 'Error en simulación de cliente',
            error: err.message
        });
    }
}

/*      
| ------------------------------------------------------
| Utilidades
| ------------------------------------------------------
*/

// Genera un numero de secuencia
export const getSequence = async (req, res) => {
    try {
        const { database, tipoECF } = req.body;

        if (!database || !tipoECF) {
            return res.status(400).json({
                error: 'Faltan parámetros en la solicitud'
            });
        }

        const serv = new EcfServices()

        const encf = await serv.generateENCF(database, tipoECF);
        res.json({ ok: true, encf });
    } catch (error) {

        res.status(500).json({
            error: error.message,
        });

    }
}

// Endpoint para descargar XML
export const downloadECFXML = async (req, res) => {
    try {
        const { id } = req.params;
        const database = req.usuario.database;

        // Conexion a la base de datos
        const pool = getConnection(database);

        const [rows] = await pool.query(
            'SELECT xml_firmado,rnc_cliente,encf FROM facturas_electronicas WHERE id = ?',
            [id]
        );

        if (!rows.length) {
            return res.status(404).send('No se encontró el XML');
        }

        const xml = rows[0].xml_firmado;
        const rncEmisor = rows[0].rnc_cliente;
        const eNCF = rows[0].encf;

        // Crear el nombre RNCEmisor + eCF.xml
        const fileName = `${rncEmisor}${eNCF}.xml`

        // Forzar descarga
        res.setHeader('Content-Disposition', `attachment; filename=${fileName}.xml`);
        res.setHeader('Content-Type', 'application/xml');

        res.send(xml);

    } catch (error) {
        console.error(error);
        res.status(500).send('Error al descargar XML');
    }
}

// Paso 2: Prueba de Datos Excel → JSON > ECF,RFCE32
export const convertStep2XML = async (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({
                success: false,
                error: 'No se recibió archivo'
            });
        }

        const database = req.usuario.database;

        // Leer Excel
        const workbook = xlsx.readFile(req.file.path);

        // Hoja 1 → ECF
        const sheetECF = workbook.SheetNames[0];
        const rowsECF = xlsx.utils.sheet_to_json(workbook.Sheets[sheetECF]);

        // Hoja 2 → RFCE
        const sheetRFCE = workbook.SheetNames[1];
        const rowsRFCE = xlsx.utils.sheet_to_json(workbook.Sheets[sheetRFCE]);

        // Transformar dataset
        const jsonResult = await EcfConverter.convertExcel2ECF(rowsECF);
        const jsonRFCE = await EcfConverter.convertExcel2RFCE(rowsRFCE);

        const transformer = new Transformer();
        const cert = new EcfServices();
        const { certs } = await cert.getClientCertP12(database);

        const files = [];

        // Generar XML y firmar
        for (const row of jsonRFCE) {
            const ecfXml = transformer.json2xml(row);
            const signature = new Signature(certs.key, certs.cert);
            const signedXml = signature.signXml(ecfXml);

            // Convertir a RFCE32
            const { xml } = convertECF32ToRFCE(signedXml);

            // Nombre de archivo
            const rncEmisor = row.ECF.Encabezado.Emisor.RNCEmisor || 'ECF';
            const eNCF = row.ECF.Encabezado.IdDoc.eNCF || '0000';
            const fileName = `${rncEmisor}${eNCF}.xml`;

            files.push({ fileName, xml });
        }

        // Eliminar Excel temporal
        fs.unlinkSync(req.file.path);

        return res.json({
            success: true,
            total: jsonResult.length,
            data: jsonResult,
            files
        });

    } catch (error) {
        console.error(error);
        return res.status(500).json({
            success: false,
            error: error.message
        });
    }
}

// Paso 3: Convertir dataset .xlsx, .xls aprobacion comercial a JSON
export const convertStep3XML = async (req, res) => {
    try {

        // Validar archivo
        if (!req.file) {
            return res.status(400).json({
                success: false,
                error: 'No se recibió archivo'
            });
        }

        // Leer excel
        const workbook = xlsx.readFile(req.file.path);

        // Primera hoja
        const sheetName = workbook.SheetNames[0];

        // Convertir hoja a JSON
        const rows = xlsx.utils.sheet_to_json(workbook.Sheets[sheetName]);

        // Transformar dataset
        const jsonResult = rows.map(row => ({
            ACECF: {
                DetalleAprobacionComercial: {
                    Version: '1.0',
                    RNCEmisor: row.RNCEmisor,
                    eNCF: row.eNCF,
                    FechaEmision: row.FechaEmision,
                    MontoTotal: Number(row.MontoTotal),
                    RNCComprador: row.RNCComprador,
                    Estado: Number(row.Estado),
                    DetalleMotivoRechazo: row.DetalleMotivoRechazo,
                    FechaHoraAprobacionComercial: row.FechaHoraAprobacionComercial
                }
            }
        }));

        // Eliminar archivo temporal
        fs.unlinkSync(req.file.path);

        // Respuesta
        return res.json({
            success: true,
            total: jsonResult.length,
            data: jsonResult
        });

    } catch (error) {
        console.error(error);
        return res.status(500).json({
            success: false,
            error: error.message
        });
    }
}

// Paso 4. Simulacion e-CF ->  Obtener cada uno de los eCF a enviar
export const getXMLSimulation = async (req, res) => {
    const { tipo_ecf } = req.query;
    const database = req.usuario?.database;

    if (!tipo_ecf) {
        return res.status(400).json({
            success: false,
            error: 'No se recibió el tipo de ecf a generar'
        })
    }

    if (!database) {
        return res.status(400).json({
            success: false,
            error: 'Usuario no identificado'
        })
    }

    try {

        const folder = path.resolve(__dirname, `../../storage/xml/${tipo_ecf}.xlsx`)

        if (!fs.existsSync(folder)) {
            throw new Error(`Archivo certificado no encontrado: ${folder}`);
        }

        // 1. Leer Excel
        const workbook = xlsx.readFile(folder);
        const sheetName = workbook.SheetNames[0]

        // 2. Convertir filas a JSON
        const rowsECF = xlsx.utils.sheet_to_json(workbook.Sheets[sheetName])

        // 3. Procesar todo
        const files = [];
        const data = [];

        const transformer = new Transformer();
        const serv = new EcfServices()

        for (const row of rowsECF) {

            let eNCF;

            // Generar secuencia
            try {

                eNCF = await serv.generateENCF(database, row.TipoeCF);

            } catch (error) {

                return res.status(500).json({
                    success: false,
                    error: error.message || error.toString()
                });

            }

            const fullENCF = `E${eNCF}`;

            // actualizar el row original
            row.eNCF = fullENCF;

            const fileName = `${row.RNCEmisor}${fullENCF}.xml`;

            // convertir excel -> ecf
            const [ecf] = await EcfConverter.convertExcel2ECF([row]);

            // json -> xml
            const xml = transformer.json2xml(ecf);

            // guardar
            files.push({ fileName, xml });
            data.push(ecf);
        }

        res.status(200).json({
            success: true,
            total: data.length,
            data: data,
            files: files
        })
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error
        })
        console.error(error);
    }
}

