import { getConnection, getTenantPool } from '../../config/db.js';

export default class EcfRepository {

    /**
     * Obtener conexion transaction
     */
    static async getTransaction(database) {

        const pool = getConnection(database);
        const db = await pool.getConnection();

        await db.beginTransaction();
        return db;
    }

    /**
     * Obtener secuencias bloqueadas
     */
    static async getSequencesByType(db, tipoECF) {
        const [rows] = await db.query(`SELECT * FROM secuencias_encf WHERE tipo_ecf = ? FOR UPDATE`, [tipoECF]);
        return rows;
    }

    /**
     * Actualizar secuencia
     */
    static async updateSequence(db, id, secuenciaActual, estado) {
        await db.query(`UPDATE secuencias_encf SET secuencia_actual = ?, estado = ? WHERE id = ?
            `,
            [secuenciaActual, estado, id]
        );
    }

    /**
     * Obtener los datos del contribuyente
     */
    static async getTaxPayerConfig(database) {

        // const pool = getConnection(database);
        const db = await getConnection(database);

        const [rows] = await db.query(
            'SELECT route_cert, passphrase_cert, ambiente FROM datos_contribuyente WHERE id = ?',
            [1]
        );

        if (rows.length === 0) {
            throw new Error(`No se encontró configuracion`);
        }

        return rows[0] || null;
    }

    /**
     * Obtiene el certificado del clienet
     */
    static async getCert(database) {
        const db = getConnection(database);

        // Buscar datos del cliente
        const [rows] = await db.query(
            'SELECT route_cert, passphrase_cert FROM datos_contribuyente WHERE id = ?',
            [1]
        );

        if (rows.length === 0) {
            throw new Error(`No se encontró configuracion`);
        }

        return rows[0] || null;
    }

    /**
     * Actualizar datos del certificado del cliente
     */
    static async updateCert(database, certData) {
        const pool = getConnection(database);
        try {
            const query = `
            UPDATE datos_contribuyente
            SET 
                serial_number = ?,
                subject_name = ?,
                issuer_name = ?,
                valid_from = ?,
                valid_to = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
            `;

            const params = [
                certData.serialNumber,
                certData.subject,
                certData.issuer,
                certData.validFrom,
                certData.validTo,
                1
            ];

            const [result] = await db.query(query, params);

            if (result.affectedRows === 0) {
                throw new Error('No se encontraron datos del contribuyente');
            }

            return {
                mensaje: 'Certificado actualizado correctamente'
            };

        } finally {
            db.release();
        }
    }

    /**
     * Guardar documento electronico 
     */
    static async saveECF(database, jsonFile) {

        const ecf = jsonFile.ECF;
        const encabezado = ecf?.Encabezado;
        const idDoc = encabezado?.IdDoc;
        const encf = idDoc?.eNCF?._text;
        const tipoECF = idDoc?.TipoeCF?._text;
        const emisor = encabezado?.Emisor;
        const rncEmisor = emisor?.RNCEmisor?._text;
        const razonSocialEmisor = emisor?.RazonSocialEmisor?._text;
        const nombreComercial = emisor?.NombreComercial?._text;
        const direccionEmisor = emisor?.DireccionEmisor?._text;
        const comprador = encabezado?.Comprador;
        const rncComprador = comprador?.RNCComprador?._text;
        const razonSocialComprador = comprador?.RazonSocialComprador?._text;
        const totales = encabezado?.Totales;
        const montoGravadoTotal = totales?.MontoGravadoTotal?._text || 0;
        const totalITBIS = totales?.ITBIS1?._text || totales?.TotalITBIS?._text || 0;
        const montoTotal = totales?.MontoTotal?._text;

        // Extraer respuesta JSON 
        const track_id = response?.trackId || null;
        const estado = response?.estado || 'Error';
        const respuesta = response?.mensaje || ''
        const securityCode = response?.securityCode || null;
        const urlQr = response?.urlQr || null;
        const userId = response?.user_id


        const query = `CALL fe_insert_factura_electronica(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`;

        const values = [
            userId,
            encf,
            tipoECF,
            rncEmisor,
            rncComprador,
            razonSocialComprador,
            montoGravadoTotal,
            totalITBIS,
            montoTotal,
            xmlFile,
            track_id,
            urlQr,
            securityCode,
            respuesta,
            estado
        ];

        const db = getConnection(database);

        const [result] = db.query(query, values);

        const insertId = result?.[0]?.[0]?.insertId;

        return insertId;
    }


    /**
     * Guardar Aprobacion Comercial
     */
    static async saveACECF(jsonFile) {

        const detalle = jsonFile.ACECF?.DetalleAprobacionComercial;
        if (!detalle) {
            return res.status(400).json({
                codigo: 400,
                estado: 'Rechazado',
                mensaje: 'Missing DetalleAprobacionComercial element'
            });
        }

        const rncEmisor = detalle.RNCEmisor._text || detalle.RNCEmisor;
        const rncComprador = detalle.RNCComprador._text || detalle.RNCComprador;
        const eNCF = detalle.eNCF._text || detalle.eNCF;
        const fechaEmision = detalle.FechaEmision._text || detalle.FechaEmision;
        const fechaAprobacion = detalle.FechaHoraAprobacionComercial._text || detalle.FechaHoraAprobacionComercial;
        const montoTotal = detalle.MontoTotal._text || detalle.MontoTotal;
        const estado = (detalle.Estado._text || detalle.Estado) === '1' ? 'Aprobada' : 'Rechazado';
        const motivoRechazo = detalle.DetalleMotivoRechazo?._text || detalle.DetalleMotivoRechazo || null;

        console.log(fechaAprobacion)

        // Obtener pool del tenant usando el RNC del comprador
        const tenantPool = await getTenantPool(rncComprador);

        await tenantPool.execute(
            'CALL fe_aprobacion_comercial(?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                rncEmisor,
                rncComprador,
                eNCF,
                formatToMySQLDate(fechaEmision),
                formatToMySQLDatetime(fechaAprobacion),
                estado,
                motivoRechazo,
                montoTotal,
                xmlContent
            ]
        );

        return {
            estado: 'OK',
            mensaje: `Aprobación comercial de ${eNCF} guardada correctamente`
        };
    }


    /**
    * Verificar si el ECF ya ha sido recibido
    */
    static async checkECFExists(rncComprador) {
        // Obtener pool del tenant usando el RNC del comprador
        const tenantPool = await getTenantPool(rncComprador);

        const [alreadyExists] = await tenantPool.query(
            'SELECT 1 FROM facturas_electronicas WHERE eNCF = ? LIMIT 1',
            [eNCF]
        );

        // Devuelve true o false en vez de usar res
        return alreadyExists.length > 0;
    }


    /**
     *  Verificar si el RNCReceptor es el mismo del contribuyente
     */
    static async getRNCFromXML(rncComprador) {
        // Obtener pool del tenant usando el RNC del comprador
        const db = await getTenantPool(rncComprador);

        // 2. Consultar la base de datos
        const [rows] = await db.execute(
            `SELECT rnc, razon_social, nombre_comercial FROM datos_contribuyente WHERE rnc = ? LIMIT 1`,
            [rncComprador]
        );

        if (rows.length === 0) {
            throw new Error(`RNC ${rncComprador} no encontrado en datos_contribuyente`);
        }

        // 3. Retornar el RNC y datos del contribuyente
        return rows[0]; // { rnc, razon_social, nombre_comercial }
    }
}