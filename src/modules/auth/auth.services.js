$(document).ready(function () {

    const refreshTime = 59 * 60 * 1000;

    async function authToken() {
        try {
            sendAjaxRequest({
                url: "src/config/auth.php",
                data: {},
                successCallback: async (res) => {

                    var credentials = JSON.parse(res)

                    const response = await fetch(API_URL + 'api/auth/login',
                        {
                            method: 'POST',
                            credentials: 'include',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(credentials)
                        }
                    );

                    const data = await response.json();
                    console.log(data)

                    // Guardar fecha de la última petición
                    localStorage.setItem('api_last_connection', Date.now());

                },
                errorCallback: (err) => console.log(err)
            })

        } catch (error) {
            console.error(error);
        }
    }


    // Obtener última autenticación
    const lastConnection = Number(localStorage.getItem('api_last_connection'));

    // Tiempo transcurrido
    const elapsed = Date.now() - lastConnection;

    // Si nunca se autenticó o expiró
    if (!lastConnection || elapsed >= refreshTime) {

        authToken();
        setInterval(authToken, refreshTime);

    } else {

        // Esperar el tiempo restante para renovar
        const remainingTime = refreshTime - elapsed;

        setTimeout(() => {
            authToken();

            // Renovar continuamente
            setInterval(authToken, refreshTime);

        }, remainingTime);
    }



})