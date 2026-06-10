import { initWebSocket } from './src/modules/websocket/websocket.server.js';
import authToken from './src/middlewares/auth.middlewares.js';

import express from 'express';
import jwt from 'jsonwebtoken';
import logger from 'morgan';
import bodyParser from 'body-parser';
import cors from 'cors';
import 'dotenv/config';
import cookieParser from 'cookie-parser';
import http from 'http';

const app = express();
const port = process.env.PORT || 3002;

/*      
| ------------------------------------------------------
| Middleware 
| ------------------------------------------------------
*/

app.use(cors({
    origin: true,
    credentials: true
}));
app.use(express.json());
app.use(cookieParser());
app.use(logger('dev'));

/*      
| ------------------------------------------------------
| Rutas
| ------------------------------------------------------
*/

// app.use('/test', testRoutes); // Pruebas
import authRoutes from './src/modules/auth/auth.routes.js';
import ecfRoutes from './src/modules/ecf/ecf.routes.js';
import customersRoutes from './src/modules/customers/customer.routes.js';
import posRoutes from './src/modules/pos/pos.routes.js'; 
import reportsRoutes from './src/modules/reports/reports.routes.js'; 
import invoicesRoutes from './src/modules/invoices/invoices.routes.js'; 
import paymentsRoutes from './src/modules/payments/payments.routes.js';

app.use('/api/auth',authRoutes) // Modulo Autenticacion
app.use('/ecf',authToken, ecfRoutes); // Modulo eCF
app.use('/api',authToken, posRoutes); // Modulo Punto de Venta
app.use('/api',authToken, customersRoutes); // Modulo Clientes
app.use('/api',authToken, reportsRoutes); // Modulo Reportes
app.use('/api',authToken, invoicesRoutes); // Modulo Facturas
app.use('/api',authToken, paymentsRoutes); // Modulo de Pagos

/*      
| ------------------------------------------------------
| Websocket + Server
| ------------------------------------------------------
*/

const server = http.createServer(app);

const { broadcast } = initWebSocket(server);

// Disponible globalmente
app.locals.broadcast = broadcast;

server.listen(port, () => {
    console.log(`Servidor corriendo en http://localhost:${port}`);
});
