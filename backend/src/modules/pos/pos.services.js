import PosRepository from "./pos.repository.js";

export default class PosServices {

    $item_id = null;
    $procedure = null;

    // Agregar detalle
    async addDetail(body, database) {

        let item_id = null;
        let procedure = null;

        if (Number(body.product_id) !== 0) {
            procedure = 'pos_agregar_producto';
            item_id = Number(body.product_id)

        } else if (Number(body.service_id) !== 0) {

            procedure = 'pos_agregar_servicio';
            item_id = Number(body.service_id)

        } else if (Number(body.piece_id) !== 0) {
            procedure = 'pos_agregar_pieza';
            item_id = Number(body.piece_id)
        }

        if (!item_id || !procedure) {
            throw new Error('Debe enviar producto, pieza o servicio válido');
        }

        return await PosRepository.insertDetail(procedure, {
            procedure,
            order_id: body.order_id || null,
            usuario_id: body.usuario_id || null,
            cantidad: body.quantity || 0,
            costo: body.cost || 0,
            precio: body.price || 0,
            item_id
        }, database);
    }


    /**
     * Actualizar precios de detalles según lista de precios
     */
    async updatePricesByList(body, database) {

        const details = await PosRepository.getDetailsForPriceUpdate(
            body.order_id,
            body.product_id,
            body.usuario_id,
            database
        );

        for (const item of details) {

            const newPrice = body.list_id > 0
                ? item.valor
                : item.precio_unitario;

            await PosRepository.updateDetailPrice(
                item.detalle_venta_id,
                newPrice,
                database
            );
        }

        return { ok: true, updated: details.length };
    }

    /**
     * Eliminar detalle
     */
    async deleteDetail(body, database) {

        const { detail_id, usuario_id } = body;

        if (!detail_id || detail_id <= 0) {
            throw new Error('ID inválido');
        }

        const result = await PosRepository.deleteDetail(
            detail_id,
            database
        );

        return result;
    }

    /**
     * Actualizar datos de la orden
     */
    async updateOrder(body, database) {

        if (!body.order_id || body.order_id <= 0) {
            throw new Error('ID de orden inválido');
        } else if (!body.customer || body.customer <= 0) {
            throw new Error('ID de cliente inválido');
        }

        return await PosRepository.updateOrder(body, database);
    }

    /*
    * Agregar orden (comanda o pre factura)
     */
    async addOrder(body, database) {

        if (!body.customer) {
            throw new Error('Datos incompletos para agregar orden');
        } else if (!database) {
            throw new Error('Base de datos no especificada');
        }

        const result = await PosRepository.addOrder(body, database);

        return result;
    }

    /**
     * Eliminar todo el detalle de una orden
     */
    async editDetail(body, database) {

        if (!body.detail_id) {
            throw new Error('Datos incompletos para editar el detalle');
        } else if (!database) {
            throw new Error('Base de datos no especificada');
        }

        const result = await PosRepository.editDetail(body, database);
        return result;
    }

    /**
     * Eliminar todo el detalle de una orden
     */
    async deleteAll(body, database) {

        if (!database) {
            throw new Error('Base de datos no especificada');
        }
        const result = await PosRepository.deleteAll(body, database);
        return result;
    }

    /** 
    * Actualizar cantidad de un detalle con validación de stock para productos y piezas
    */
    async updateDetailQuantity(body, database) {

        if (!body.id || !body.quantity || !body.item_id || !body.item_type) {
            throw new Error('Datos inválidos.');
        }

        const tableDetail = 'detalle_facturas_ventas';
        const tableId = 'detalle_venta_id';

        // 1. Si es servicio, actualizar sin validar stock
        if (body.item_type === 'servicio') {

            await PosRepository.updateDetailQuantity(
                {
                    tableDetail,
                    tableId
                }, body, database
            );

            return {
                error: false,
                message: 'Detalle actualizado con éxito (Servicio).'
            };
        }

        // 2. Validar stock para productos y piezas
        const stockData = await PosRepository.getItemStock(body, database);

        if (!stockData) {
            throw new Error('Error al verificar stock.');
        }

        // 3. Obtener cantidad actual del detalle para calcular el stock disponible considerando la cantidad actual
        const detailData = await PosRepository.getDetailQuantity(tableDetail, tableId, body.id, database);

        if (!detailData) {
            throw new Error('Error al obtener detalle.');
        }

        const currentStock = Number(stockData.cantidad);
        const currentDetailQuantity = Number(detailData.cantidad);
        const totalAvailable = currentStock + currentDetailQuantity;

        // 4. Validar si hay stock suficiente para la cantidad solicitada
        if (totalAvailable < body.quantity) {

            return {
                error: true,
                message: 'No hay suficiente stock para realizar la actualización.'
            };
        }

        // 5. Actualizar cantidad en el detalle
        await PosRepository.updateDetailQuantity(
            {
                tableDetail,
                tableId
            }, body, database
        );

        return {
            error: false,
            message: 'Detalle modificado con éxito.'
        };
    }

    /**
     * Factura al contado
     */
    async cashInvoice(body, database) {
        if (!database) {
            throw new Error('Base de datos no especificada');
        }
        const result = await PosRepository.cashInvoice(body, database);
        return result;
    }

    /**
     * Factura a credito
     */
    async creditInvoice(body, database) {
        if (!database) {
            throw new Error('Base de datos no especificada');
        }
        const result = await PosRepository.creditInvoice(body, database);
        return result;
    }

}