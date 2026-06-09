import express from 'express';
import { 
    addDetail, editDetail, changePrice, deleteDetail, updateOrder, addOrder, 
    deleteAll, updateQuantity, cashInvoice, creditInvoice 
} from './pos.controller.js';

const router = express.Router();

// Agregar detalle 
router.post('/pos/agregar_detalle', addDetail);

// Actualizar precio
router.put('/pos/actualizar_precio', changePrice);

// Editar detalle
router.put('/pos/editar_detalle', editDetail)

// Eliminar detalle
router.delete('/pos/eliminar_detalle', deleteDetail);

// Eliminar todo el detalle
router.delete('/pos/eliminar_todo', deleteAll);

// agregar orden (comanda o pre factura)
router.post('/pos/agregar_orden', addOrder);

// Actualizar datos de la orden
router.put('/pos/actualizar_orden', updateOrder);

// Actualizar cantidad del detalle
router.put('/pos/actualizar_cantidad', updateQuantity);

// Guardar factura al contado
router.post('/pos/factura_contado', cashInvoice);

// Guardar factura a credito 
router.post('/pos/factura_credito', creditInvoice);

export default router;