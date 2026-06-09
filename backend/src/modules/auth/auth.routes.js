import express from 'express';
import { authenticate } from './auth.controller.js';

const router = express.Router();

// Iniciar sesion
router.post('/login',authenticate)

export default router;
