/**
 * Obtiene la configuración de impresión desde el servidor.
 *
 * Realiza una petición POST a services/config.php enviando
 * la acción "configuracion_de_impresion".
 *
 * @async
 * @function getData
 * @returns {Promise<Object|null>} Retorna un objeto con la configuración
 * o null si ocurre un error.
 */
export async function getData() {
    try {

        const response = await fetch(SITE_URL + "src/modules/config/config.repository.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                action: "configuracion_de_impresion"
            })
        });

        if (!response.ok) {
            throw new Error("Error en la petición");
        }

        const data = await response.json();

        return data;

    } catch (error) {
        console.log('%c[CONFIG]', 'color:#b51717;font-weight:bold;', error);
        return null;
    }
}