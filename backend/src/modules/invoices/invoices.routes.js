import express from "express";
import { cashInvoice, creditInvoice } from "./invoices.controller.js";

const router = express.Router();

router.post('/invoices/factura_contado',cashInvoice);

router.post('/invoices/factura_credito', creditInvoice)

export default router;