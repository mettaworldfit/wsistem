import { WebSocket } from 'ws';

/**
 * Enviar mensaje a clientes
 * @param {*} wss
 * @param {*} data
 * @param {*} tenantId
 */
export function broadcast(wss, data, tenantId = null) {
    wss.clients.forEach(client => {

        /*
        |---------------------------------------------------
        | Validaciones
        |---------------------------------------------------
        */
        if (
            client.readyState === WebSocket.OPEN &&
            client.isAuthenticated &&
            (!tenantId || client.tenantId === tenantId)
        ) {
            client.send(JSON.stringify(data));
        }
    });
}