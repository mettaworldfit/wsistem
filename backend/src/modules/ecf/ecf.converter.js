import { getCurrentFormattedDateTime } from "dgii-ecf";

const EcfConverter = {

    // Función auxiliar para validar valor (equivalente a EsValorValido)
    esValorValido(valor) {
        return (
            valor !== undefined &&
            valor !== null &&
            valor.toString().trim() !== "" &&
            valor.toString() !== "#e"
        );
    },

    // Función auxiliar para agregar un campo si es válido (equivalente a AddElementIfValid)
    addElementIfValid(obj, key, value) {
        if (this.esValorValido(value)) {
            obj[key] = value;
        }
    },

    /**
     *  Convierte filas JSON obtenidas de un Excel a formato de un e-CF valido
     * @param {object} rowsECF filas
     * @returns JSON con estructura ECF
     */
    async convertExcel2ECF(rowsECF) {
        // Transformar dataset a ECF
        const jsonResult = rowsECF.map((row) => {
            const Encabezado = {
                Version: "1.0",
                IdDoc: {},
                Emisor: {},
                Comprador: {},
                Totales: {}
            };

            // IdDoc
            this.addElementIfValid(Encabezado.IdDoc, "TipoeCF", row["TipoeCF"]);
            this.addElementIfValid(Encabezado.IdDoc, "eNCF", row["eNCF"] || row['ENCF']);
            this.addElementIfValid(Encabezado.IdDoc, "IndicadorMontoGravado", row["IndicadorMontoGravado"]);
            this.addElementIfValid(Encabezado.IdDoc, "TipoIngresos", row["TipoIngresos"]);
            this.addElementIfValid(Encabezado.IdDoc, "TipoPago", row["TipoPago"]);

            // Emisor
            this.addElementIfValid(Encabezado.Emisor, "RNCEmisor", row["RNCEmisor"]);
            this.addElementIfValid(Encabezado.Emisor, "RazonSocialEmisor", row["RazonSocialEmisor"]);
            this.addElementIfValid(Encabezado.Emisor, "DireccionEmisor", row["DireccionEmisor"]);
            this.addElementIfValid(Encabezado.Emisor, "FechaEmision", row["FechaEmision"]);

            // Comprador
            this.addElementIfValid(Encabezado.Comprador, "RNCComprador", row["RNCComprador"]);
            this.addElementIfValid(Encabezado.Comprador, "RazonSocialComprador", row["RazonSocialComprador"]);

            // Totales
            this.addElementIfValid(Encabezado.Totales, "MontoGravadoTotal", row["MontoGravadoTotal"]);
            this.addElementIfValid(Encabezado.Totales, "MontoGravadoI1", row["MontoGravadoI1"]);
            this.addElementIfValid(Encabezado.Totales, "TotalITBIS", row["TotalITBIS"]);
            this.addElementIfValid(Encabezado.Totales, "TotalITBIS1", row["TotalITBIS1"]);
            this.addElementIfValid(Encabezado.Totales, "MontoTotal", row["MontoTotal"]);

            // DetallesItems
            const DetallesItems = { Item: [] };

            for (let i = 1; i <= 62; i++) {
                if (this.esValorValido(row[`NumeroLinea[${i}]`])) {
                    const item = {};
                    this.addElementIfValid(item, "NumeroLinea", row[`NumeroLinea[${i}]`]);
                    this.addElementIfValid(item, "NombreItem", row[`NombreItem[${i}]`]);
                    this.addElementIfValid(item, "CantidadItem", row[`CantidadItem[${i}]`]);
                    this.addElementIfValid(item, "PrecioUnitarioItem", row[`PrecioUnitarioItem[${i}]`]);
                    this.addElementIfValid(item, "MontoItem", row[`MontoItem[${i}]`]);
                    this.addElementIfValid(item, "IndicadorFacturacion", row[`IndicadorFacturacion[${i}]`]);
                    this.addElementIfValid(item, "IndicadorBienoServicio", row[`IndicadorBienoServicio[${i}]`]);
                    this.addElementIfValid(item, "UnidadMedida", row[`UnidadMedida[${i}]`]);

                    DetallesItems.Item.push(item);
                }
            }

            return {
                ECF: {
                    Encabezado,
                    DetallesItems,
                    FechaHoraFirma: getCurrentFormattedDateTime()
                }
            };
        });

        return jsonResult;
    },

    /**
     *  Convierte filas JSON obtenidas de un Excel a formato de un RFCE valido
     * @param {object} rowsECF filas
     * @returns JSON con estructura RFCE
     */
    async convertExcel2RFCE(rowsECF) {

        // Transformar dataset a ECF
        const jsonResult = rowsECF.map((row) => {
            const Encabezado = {
                Version: "1.0",
                IdDoc: {},
                Emisor: {},
                Comprador: {},
                Totales: {}
            };

            // IdDoc
            this.addElementIfValid(Encabezado.IdDoc, "TipoeCF", row["TipoeCF"]);
            this.addElementIfValid(Encabezado.IdDoc, row["eNCF"] || row['ENCF']);
            this.addElementIfValid(Encabezado.IdDoc, "IndicadorMontoGravado", row["IndicadorMontoGravado"]);
            this.addElementIfValid(Encabezado.IdDoc, "TipoIngresos", row["TipoIngresos"]);
            this.addElementIfValid(Encabezado.IdDoc, "TipoPago", row["TipoPago"]);

            // Emisor
            this.addElementIfValid(Encabezado.Emisor, "RNCEmisor", row["RNCEmisor"]);
            this.addElementIfValid(Encabezado.Emisor, "RazonSocialEmisor", row["RazonSocialEmisor"]);
            this.addElementIfValid(Encabezado.Emisor, "DireccionEmisor", row["DireccionEmisor"]);
            this.addElementIfValid(Encabezado.Emisor, "FechaEmision", row["FechaEmision"]);

            // Comprador
            this.addElementIfValid(Encabezado.Comprador, "RNCComprador", row["RNCComprador"]);
            this.addElementIfValid(Encabezado.Comprador, "RazonSocialComprador", row["RazonSocialComprador"]);

            // Totales
            this.addElementIfValid(Encabezado.Totales, "MontoGravadoTotal", row["MontoGravadoTotal"]);
            this.addElementIfValid(Encabezado.Totales, "MontoGravadoI1", row["MontoGravadoI1"]);
            this.addElementIfValid(Encabezado.Totales, "TotalITBIS", row["TotalITBIS"]);
            this.addElementIfValid(Encabezado.Totales, "TotalITBIS1", row["TotalITBIS1"]);
            this.addElementIfValid(Encabezado.Totales, "MontoTotal", row["MontoTotal"]);

            return {
                ECF: {
                    Encabezado,
                    FechaHoraFirma: getCurrentFormattedDateTime()
                }
            };
        });

        return jsonResult;
    }
}

export default EcfConverter;