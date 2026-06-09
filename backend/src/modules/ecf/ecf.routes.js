import express from 'express';
import multer from 'multer';

import {
    getSeed,
    validateSeed,
    receiveECF,
    receiveCommercialApproval,
    receptionTest,
    commercialApproval,
    simulationECF,
    sendECF,
    sendRFCE,
    voidENCF,
    sendCommercialApproval,
    simulationTest,
    statusTrackId,
    trackStatuses,
    readCertificate,
    getCustomerDirectory,
    cloudServiceStatus,
    eCFQrCode,
    FCQrCode,
    summaryRFCE,
    getSequence,
    downloadECFXML,
    convertStep2XML,
    convertStep3XML,
    getXMLSimulation
} from '../../modules/ecf/ecf.controller.js';

const router = express.Router();

const upload = multer({
    dest: './src/storage/uploads'
});

/*      
| ------------------------------------------------------
| Recepciones de eCF
| ------------------------------------------------------
*/

// Semilla
router.get('/fe/autenticacion/api/semilla', getSeed);

// Validar semilla
router.post('/fe/autenticacion/api/validacioncertificado', upload.single('xml'), validateSeed);

// Recepción e-CF,
router.post('/fe/recepcion/api/ecf', upload.single('xml'), receiveECF);

// Recepción ACECF
router.post('/fe/aprobacioncomercial/api/ecf', upload.single('xml'), receiveCommercialApproval);

/*      
| ------------------------------------------------------
| Envios a eCF
| ------------------------------------------------------
*/

// Enviar e-CF
router.post('/recepcion', sendECF);

// Enviar resumen factura de consumo RFCE <= 250k
router.post('/recepcionrnc', sendRFCE)

// Anulación de e‐NCF
router.post('/anular_rango', voidENCF)

// Enviar Aprobacion Comercial ACECF
router.post('/aprobacion_comercial', sendCommercialApproval)

/*      
| ------------------------------------------------------
| Certificacion dgii
| ------------------------------------------------------
*/

// Paso 2: Prueba De Datos e-CF
router.post('/cert/recepcion_prueba', receptionTest);

// Paso 3: Aprobacion Comercial
router.post('/cert/aprobacion_comercial', commercialApproval);

// Paso 4: Simulacion eCF
router.post('/cert/simulacion_ecf', simulationECF);

/*      
| ------------------------------------------------------
| Consultas
| ------------------------------------------------------
*/

// Consultar resultado de eCF
router.post('/resultado_ecf', statusTrackId);

// Consultar estado real del eCF
router.post('/consultar_ecf', trackStatuses);

// Consultar estado de un certificado 
router.get('/estado_cert', readCertificate);

// Consulta URL Directorio de Servicios
router.post('/consultar_directorio', getCustomerDirectory);

/**
 * Servicio web responsable de proporcionar el estatus y disponibilidad de los servicios
 * de facturación electrónica, como también las ventanas de mantenimientos de estos.
 */
router.post('/obtener_status', cloudServiceStatus);

// Consulta timbre eCF (QR) 
router.post('/consultar_timbre', eCFQrCode);

// Consulta timbre FC (QR) 
router.post('/consultar_timbrefc',FCQrCode);

// Consulta de Resumen de Factura (RFCE)
router.post('/consultar_rfce',summaryRFCE);

/*      
| ------------------------------------------------------
| Test
| ------------------------------------------------------
*/

// Simulacionde un cliente y un receptor
router.get('/fe/cliente/simular', simulationTest);

/*      
| ------------------------------------------------------
| Utilidades
| ------------------------------------------------------
*/

// Genera un numero de secuencia
router.post('/generar_num_ecf',getSequence);

// Endpoint para descargar XML
router.get('/download/xml/:id',downloadECFXML);

// Paso 2: Prueba de Datos Excel → JSON > ECF,RFCE32
router.post('/convert_pruebas', upload.single('excel'),convertStep2XML);

// Paso 3: Convertir dataset .xlsx, .xls aprobacion comercial a JSON
router.post('/aprobaciones_convert', upload.single('excel'), convertStep3XML);

// Paso 4. Simulacion e-CF ->  Obtener cada uno de los eCF a enviar
router.get('/cert/simulacion_ecf',getXMLSimulation);




export default router;

