import Customers from "./customers.services.js";


// Consultar datos RNC
export const getRNC = async (req, res) => {
    try {
        const { rnc } = req.body;

        if (!rnc) {
            return res.status(400).json({
                error: 'Faltan parámetros en la solicitud'
            });
        }

        const customer = new Customers;
        const response = await customer.getRNC(rnc)

        res.json(response);

    } catch (error) {

        res.status(500).json({
            error: "rnc no encontrado"
        });
    }

}