import express from 'express';
import { getRNC } from './customer.controller.js';


const router = express.Router();

// Obtener RNC del cliente
router.post('/buscar_rnc', getRNC);


export default router;