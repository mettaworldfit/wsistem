import express, { Router } from 'express';
import { addPayment, deletePayment } from './payments.controller.js';

const router = express.Router();

router.post('/payments/agregar_pago', addPayment);
router.post('/payments/eliminar_pago', deletePayment);

export default router;