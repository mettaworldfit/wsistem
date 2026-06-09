import mysql from 'mysql2/promise';
import 'dotenv/config';

const pools = {}; // almacena pools por base de datos

/**
 * Devuelve un pool de conexión a cualquier base de datos por nombre
 * @param {string} database - nombre de la base de datos
 * @param {object} [config] - opcional, host, user, pass
 * @returns {Pool} - pool de conexión
 */
export function getConnection(database, config) {
  if (!pools[database]) {
    pools[database] = mysql.createPool({
      host: config?.host || process.env.DB_HOST,
      user: config?.user || process.env.DB_USER,
      password: config?.password || process.env.DB_PASSWORD,
      database,
      waitForConnections: true,
      connectionLimit: 10
    });

    console.log(`Pool creado para la base de datos: ${database}`);
  }

  return pools[database];
}

/**
 * Devuelve un pool de conexión al tenant según RNC
 * esta funcion solo se utiliza para asignar conexiones de Recepcion de e-CF 
 * y Aprobaciones Comerciales
 * @param {string} rncComprador
 * @returns {Promise<Pool>}
 */
export async function getTenantPool(rncComprador) {
  if (!rncComprador) {
    throw new Error('RNC del comprador es requerido');
  }

  // 1. Conexión temporal a tenant_auth
  const centralDb = await mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: 'tenant_auth'
  });

  // 2. Buscar info del tenant
  const [rows] = await centralDb.execute(
    'SELECT db_name, db_host, db_user, db_pass FROM empresas WHERE rnc = ?',
    [rncComprador]
  );

  await centralDb.end();

  if (!rows.length) {
    throw new Error(`No se encontró empresa registrada con RNC ${rncComprador}`);
  }

  const tenant = rows[0];
  const database = tenant.db_name;

  // 3. Devolver pool usando getConnection
  return getConnection(database, {
    host: tenant.db_host,
    user: tenant.db_user,
    password: tenant.db_pass
  });
}