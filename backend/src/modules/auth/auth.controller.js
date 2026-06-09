import jwt from 'jsonwebtoken';
import ECF from 'dgii-ecf';
import bcrypt from 'bcrypt';
import { getConnection } from '../../config/db.js';
import 'dotenv/config';

const JWT_SECRET = process.env.JWT_SECRET;

// Iniciar sesion
export const authenticate = async (req, res) => {
    const { usuario, password, database } = req.body;

    if (!usuario || !password || !database) {
        return res.status(400).json({ error: "Faltan parámetros en la solicitud" });
    }

    try {
        const pool = getConnection(database);

        const [rows] = await pool.query(
            'SELECT * FROM usuarios WHERE username = ?',
            [usuario]
        );

        if (rows.length === 0) {
            return res.status(401).json({ error: 'Usuario no encontrado' });
        }

        const user = rows[0];

        // Si la contraseña esta encryptada usar bcrypt
        // const passwordValid = await bcrypt.compare(password, user.password);

        // Verificar password
        if (password !== user.password) {
            return res.status(401).json({ error: 'Contraseña incorrecta' });
        }

        // Generar token con info del user
        const token = jwt.sign(
            {
                user_id: user.usuario_id,
                usuario: user.username,
                database: database
            },
            JWT_SECRET,
            { expiresIn: '1h' }
        );

        // Guardar cookie
        res.cookie('access_token', token, {
            httpOnly: true,
            secure: process.env.PRODUCTION,
            sameSite: 'lax',
            maxAge: 1000 * 60 * 60 // 1 hora
        });

        res.json({ token });

    } catch (err) {
        console.error(err);
        res.status(500).json({
            error: 'Error en el servidor',
            sqlMensaje: err.sqlMessage || null
        });
    }
}