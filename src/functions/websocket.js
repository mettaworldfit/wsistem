let ws = null;
let wsConnected = false;
const listeners = [];

let reconnectAttempts = 0;
const maxReconnectAttempts = 10;   // Máximo de intentos
const baseDelay = 1000;            // 1 segundo

function getReconnectDelay(attempt) {
    // Delay exponencial con jitter
    return Math.min(baseDelay * 2 ** attempt, 30000) + Math.random() * 500;
}

export function initWebSocket() {

    if (ws && (ws.readyState === WebSocket.OPEN || ws.readyState === WebSocket.CONNECTING)) {
        return ws;
    }

    const token = localStorage.getItem('access_token');
    const isLocal = location.hostname === 'localhost' || location.hostname === '127.0.0.1';
    const protocol = (location.protocol === 'https:' || !isLocal) ? 'wss://' : 'ws://';
    const host = isLocal ? 'localhost:3001' : 'ws.wsistems.com/';
    const wsURL = `${protocol}${host}?token=${token}`;

    ws = new WebSocket(wsURL);

    ws.onopen = () => {
        console.group('%c[WEBSOCKET]', 'color:#007bff;font-weight:bold;');
        console.log('Conexión establecida:', wsURL);
        console.groupEnd();
        wsConnected = true;
        reconnectAttempts = 0; // Resetear intentos
    };

    ws.onclose = (e) => {
        console.group('%c[WEBSOCKET]', 'color:#df040e;font-weight:bold;');
        console.log('Conexión cerrada', e.reason || '');
        console.groupEnd();
        wsConnected = false;
        attemptReconnect();
    };

    ws.onerror = (err) => {
        console.error('[WEBSOCKET ERROR]', err);
        wsConnected = false;
        ws.close();
    };

    ws.onmessage = (e) => {
        try {
            const data = JSON.parse(e.data);
            listeners.forEach(cb => {
                try { cb(data); } catch (err) { console.error('[WS LISTENER ERROR]', err); }
            });
        } catch (err) {
            console.error('[WS PARSE ERROR]', err);
        }
    };

    return ws;
}

function attemptReconnect() {
    if (reconnectAttempts >= maxReconnectAttempts) {
        console.group('%c[WEBSOCKET]', 'color:#df040e;font-weight:bold;');
        console.log('Se alcanzó el máximo de reintentos');
        console.groupEnd();

        return;
    }

    const delay = getReconnectDelay(reconnectAttempts);
    reconnectAttempts++;

    console.group('%c[WEBSOCKET]', 'color:#f2941a;font-weight:bold;');
    console.log(`Reintentando conexión en ${Math.round(delay)}ms (intento ${reconnectAttempts})`);
    console.groupEnd();

    setTimeout(() => {
        initWebSocket();
    }, delay);
}

// Suscribirse a mensajes
export function subscribe(callback) {
    if (typeof callback === 'function') listeners.push(callback);
    return () => {
        const index = listeners.indexOf(callback);
        if (index > -1) listeners.splice(index, 1);
    };
}

export function getWebSocket() { return ws; }
export function isWebSocketConnected() { return wsConnected; }