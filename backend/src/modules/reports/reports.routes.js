import express from 'express';
import { cashClosing, cashOpening } from './reports.controller.js';
const router = express.Router();

// Abrir caja
router.post('/reports/abrir_caja', cashOpening);

// Cierre de caja
router.post('/reports/cierre_caja', cashClosing);

export default router;