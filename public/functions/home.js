import { initWebSocket, isWebSocketConnected, getUpdatedTotal } from "/public/functions.js";

// import { initWebSocket, isWebSocketConnected, getUpdatedTotal } from "/functions.js";

$(document).ready(function () {

    let wsConnection = initWebSocket();
    let wsConnected = isWebSocketConnected();

    // Manejar el mensaje recibido
    wsConnection.onmessage = (e) => {
        const data = JSON.parse(e.data);

        console.log('%c[WS LOG]', 'color:#007bff;font-weight:bold;', data)

        if (data.type === "nueva_venta") {
            getUpdatedTotal()
        }

        if (data.type === "caja_abierta") {
            // Actualizar el contenido de los elementos específicos usando .html()
            $('.float-right').load(window.location.href + ' .float-right > *');
            $('.pos-sidebar-header div').load(window.location.href + ' .pos-sidebar-header div > *');
        }

        if (data.type === "caja_cerrada") {
            // Actualizar el contenido de los elementos específicos usando .html()
            $('.float-right').load(window.location.href + ' .float-right > *');
            $('.pos-sidebar-header div').load(window.location.href + ' .pos-sidebar-header div > *');
        }
    };



    // Abreviar cifras
    function abbreviateNumber(num) {
        if (num >= 1e9) {
            return (num / 1e9).toFixed(1) + "B"; // Billones
        } else if (num >= 1e6) {
            return (num / 1e6).toFixed(1) + "M"; // Millones
        } else if (num >= 1e3) {
            return (num / 1e3).toFixed(1) + "K"; // Miles
        }
        return num; // Menos de mil
    }

    const format = new Intl.NumberFormat("en-CA");

    function formatMonthName(monthName) {
        return monthName.charAt(0).toUpperCase() + monthName.slice(1).toLowerCase();
    }
  /**
 * Renderiza un gráfico de línea con múltiples datasets utilizando Chart.js.
 *
 * @param {string} elementId - ID del elemento <canvas> donde se renderiza el gráfico.
 * @param {Array<Object>} datasets - Arreglo de datasets, cada uno debe tener:
 *    - label: etiqueta del dataset (ej. "Ventas", "Ganancias")
 *    - data: arreglo de objetos con `mes` o `dia` y `total`
 *    - color: color hexadecimal base (ej. "#2cc098")
 */
    function renderLineChart(elementId, datasets) {
        const ctx = document.getElementById(elementId).getContext("2d");

        // Suponemos que todos los datasets tienen los mismos días o meses
        const labels = datasets[0].data.map(item =>
            item.mes ? formatMonthName(item.mes) : item.dia
        );

        // Mapear cada dataset a un formato compatible con Chart.js
        const chartDatasets = datasets.map(ds => ({
            label: ds.label,
            data: ds.data.map(item => item.total),
            backgroundColor: ds.color + "66", // con transparencia
            borderColor: ds.color,
            borderWidth: 1,
            tension: 0.1
        }));

        // Crear el gráfico
        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: chartDatasets
            },
            options: {
                locale: "en-IN",
                scales: {
                    y: {
                        ticks: {
                            callback: value => abbreviateNumber(value),
                        },
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => {
                                let value = context.parsed.y;
                                return value
                                    .toFixed(2)
                                    .replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            }
                        },
                        backgroundColor: "#353535"
                    }
                }
            }
        });
    }


    /**
     * Carga y renderiza múltiples datasets en un gráfico tipo línea con Chart.js.
     *
     * @param {Object} options
     * @param {string} options.idCanvas - ID del canvas
     * @param {string} options.idContainer - ID del contenedor del gráfico
     * @param {string} options.idFallback - ID del contenedor alternativo (sin datos)
     * @param {Array<Object>} options.sources - Arreglo con objetos como:
     *     { action: 'ventas_diarias', label: 'Ventas', color: '#36a2eb' }
     */
    function loadChart({ idCanvas, idContainer, idFallback, sources }) {
        const canvas = document.querySelector(`#${idCanvas}`);
        if (!canvas) return;

        const container = $(`#${idContainer}`);
        const fallback = $(`#${idFallback}`);

        const datasets = [];
        let pending = sources.length;

        sources.forEach(({ action, label, color }) => {
            sendAjaxRequest({
                url: "services/home.php",
                data: { action: action },
                successCallback: res => {
                    try {
                        const data = JSON.parse(res);
                        if (Array.isArray(data)) {
                            datasets.push({ label, data, color });
                        }
                    } catch (e) {
                        console.error("Error parsing response for", action);
                    } finally {
                        pending--;
                        if (pending === 0) {
                            if (datasets.length > 0) {
                                container.show();
                                fallback.hide();
                                renderLineChart(idCanvas, datasets);
                            } else {
                                container.hide();
                                fallback.show();
                            }
                        }
                    }
                }
            });
        });
    }


    /**
     * Carga y renderiza un gráfico con múltiples datasets desde una sola acción AJAX.
     *
     * @param {Object} options
     * @param {string} options.idCanvas - ID del canvas
     * @param {string} options.idContainer - ID del contenedor del gráfico
     * @param {string} options.idFallback - ID del contenedor alternativo (sin datos)
     * @param {string} options.action - Acción AJAX
     * @param {Array<Object>} options.datasets - Configuración de cada dataset esperada en la respuesta.
     *     Cada uno debe tener: { key: "ventas", label: "Ventas", color: "#2cc098" }
     */
    function loadCombinedChart({ idCanvas, idContainer, idFallback, action, datasets }) {
        const canvas = document.querySelector(`#${idCanvas}`);
        if (!canvas) return;

        const container = $(`#${idContainer}`);
        const fallback = $(`#${idFallback}`);

        sendAjaxRequest({
            url: "services/home.php",
            data: { action },
            successCallback: (res) => {
                try {
                    const json = JSON.parse(res); // Esto es un array, no objeto

                    const validDatasets = [];

                    datasets.forEach(({ index, label, color }) => {
                        if (Array.isArray(json[index])) {
                            validDatasets.push({
                                label,
                                color,
                                data: json[index]
                            });
                        }
                    });

                    if (validDatasets.length > 0) {
                        container.show();
                        fallback.hide();
                        renderLineChart(idCanvas, validDatasets);
                    } else {
                        container.hide();
                        fallback.show();
                    }
                } catch (e) {
                    console.error("Error al parsear JSON:", e);
                    container.hide();
                    fallback.show();
                }
            }
        });
    }


    // 📈 Cargar gráficas
    loadChart({
        idCanvas: "sales_of_the_months",
        idContainer: "sales_of_the_months",
        idFallback: "chart1",
        sources: [
            {
                action: "ventas_meses",
                label: "Ventas",
                color: "#2cc098",
            }
        ]
    });

    loadChart({
        idCanvas: "expenses_of_the_months",
        idContainer: "expenses_of_the_months",
        idFallback: "chart2",
        sources: [
            {
                action: "gastos_meses",
                label: "Gastos",
                color: "#db2b2b",
            }
        ]
    });

    loadCombinedChart({
        idCanvas: "month",
        idContainer: "month",
        idFallback: "chart3",
        action: "ventas_dias", // esta acción en tu backend debe retornar ambos
        datasets: [
            { index: 0, label: "Ventas", color: "#147ae0" },
            { index: 1, label: "Ganancias", color: "#05c65c" }
        ]
    });
    /**============================================================= 
    * INICIAR FUNCIONES
    ===============================================================*/
    
    getUpdatedTotal()


}); // Ready
