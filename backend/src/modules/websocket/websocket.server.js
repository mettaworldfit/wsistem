import { WebSocketServer, WebSocket } from 'ws';
import jwt from 'jsonwebtoken';
import { authenticateSocket } from './websocket.auth.js';
import { broadcast } from './websocket.broadcast.js';

/**
 * Inicializa WebSocket
 * @param {*} server
 * @returns
 */
export function initWebSocket(server) {

    const wss = new WebSocketServer({ server });

    /*
    |---------------------------------------------------
    | Conexion
    |---------------------------------------------------
    */
    wss.on('connection', (ws, req) => {

        console.log('WebSocket conectado');

       authenticateSocket(ws,req);

        // Escuchar mensajes
        ws.on('message', (message) => {
            try {
                const data = JSON.parse(message);
                console.log( 'Mensaje recibido:',data);
            } catch (err) {
                console.error('Mensaje inválido',err);
            }
        });

        // Desconexion
        ws.on('close', () => {
            console.log('Cliente desconectado:',ws.tenantId);
        });
    });

    return { 
        wss,  
        broadcast: (data, tenantId = null) =>
            broadcast(wss, data, tenantId) 
    };
}