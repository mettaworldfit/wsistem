/**
 * Crea un buffer de impresión que puede aceptar comandos para diferentes lenguajes de impresión.
 * 
 * Permite agregar comandos de impresión en cadena para ESCPOS, ZPL, EPL, SBPL, FGL.
 *
 * @param {string} language - Lenguaje de impresión (ej. 'ESCPOS', 'ZPL', 'EPL', 'SBPL', 'FGL')
 * @returns {object} Objeto con los métodos `push` y `get`
 */
export function createPrintBuffer(language = 'ESCPOS') {
    const d = [];

    return {
        /**
         * Agrega uno o más elementos al buffer dependiendo del lenguaje de impresión.
         * 
         * @param {...string} items - Comandos de impresión
         * @returns {object} El objeto del buffer (para encadenar)
         */
        push: (...items) => {
            // Si estamos usando ZPL o EPL, los comandos deben formatearse según el lenguaje.
            if (language === 'ZPL') {
                // Modificar los comandos según ZPL
                d.push(...items.map(item => `^${item}`));  // ZPL usa una sintaxis diferente
            }
            else if (language === 'EPL') {
                // Modificar los comandos según EPL
                d.push(...items.map(item => `N${item}`));  // EPL tiene una estructura diferente
            }
            else if (language === 'SBPL') {
                // Modificar los comandos según SBPL
                d.push(...items.map(item => `SBPL:${item}`)); // Ejemplo para SBPL
            }
            else if (language === 'FGL') {
                // Modificar los comandos según FGL
                d.push(...items.map(item => `FGL:${item}`));  // Ejemplo para FGL
            }
            else {
                // Si es ESCPOS, seguir con la estructura de ESCPOS
                d.push(...items);
            }
            return this;
        },

        /**
         * Obtiene el contenido del buffer como un string.
         * 
         * @returns {string} El contenido del buffer de impresión
         */
        get: () => d.join('')
    };
}