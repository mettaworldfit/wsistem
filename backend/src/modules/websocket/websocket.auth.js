import jwt from 'jsonwebtoken';
import cookie from 'cookie';

export function authenticateSocket(ws, req) {

    // Parsear cookies
    const cookies = cookie.parse(
        req.headers.cookie || ''
    );

    const token = cookies.access_token;

    // Verificar token
    if (!token) {
        console.log('No se encontró token');
        ws.close();

        return false;
    }

    // Validar JWT
    try {

        const decoded = jwt.verify(
            token,
            process.env.JWT_SECRET
        );

        // Guardar datos en socket
        ws.tenantId = decoded.database;
        ws.userId = decoded.user_id;
        ws.usuario = decoded.usuario;
        ws.isAuthenticated = true;

        console.log(`Cliente autenticado: ${ws.tenantId}`);

        return true;

    } catch (err) {
        console.log('Token inválido:', err.message);

        ws.close();
        return false;
    }
}