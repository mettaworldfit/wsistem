import jwt from 'jsonwebtoken';
import 'dotenv/config';

export default function authToken(req, res, next) {
    let token = null;

    if (req.method === 'OPTIONS') return next();

    // 1. Cookie
    if (req.cookies?.access_token) {
        token = req.cookies.access_token;
    }

    // 2. Authorization header (ESTÁNDAR)
    if (!token && req.headers.authorization) {
        const authHeader = req.headers.authorization;

        token = authHeader.startsWith('Bearer ')
            ? authHeader.split(' ')[1]
            : authHeader;
    }

    // 3. x-api-key (fallback)
    if (!token && req.headers['x-api-key']) {
        const authHeader = req.headers['x-api-key'];

        token = authHeader.startsWith('Bearer ')
            ? authHeader.split(' ')[1]
            : authHeader;
    }

    // 4. Validar existencia
    if (!token) {
        return res.status(401).json({
            error: 'Token no enviado'
        });
    }

    // 5. Verificar JWT

    try {
        const decoded = jwt.verify(
            token,
            process.env.JWT_SECRET
        );

        req.usuario = decoded;

        next(); // Seguir
    } catch (error) {

        console.error(error.message);
        return res.status(403).json({
            error: 'Token inválido o expirado'
        });

    }

}