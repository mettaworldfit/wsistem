$(document).ready(function () {

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
     * Renderiza un gráfico de línea utilizando Chart.js.
     *
     * @param {string} elementId - ID del elemento <canvas> donde se renderiza el gráfico.
     * @param {string} label - Etiqueta del dataset (ej. "Ventas", "Gastos").
     * @param {Array<Object>} data - Arreglo de objetos con los datos, cada objeto debe tener `mes` o `dia` y `total`.
     * @param {string} color - Color base del gráfico en formato hexadecimal (ej. "#2cc098").
     */
    function renderLineChart(elementId, label, data, color) {
        // Obtener contexto del canvas
        const ctx = document.getElementById(elementId).getContext("2d");

        // Generar etiquetas: si el objeto tiene "mes" se usa ese, si no, "dia"
        const labels = data.map(item =>
            item.mes ? formatMonthName(item.mes) : item.dia
        );

        // Extraer los valores numéricos (totales)
        const valores = data.map(item => item.total);

        // Crear el gráfico
        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: valores,
                    backgroundColor: [color + "66"], // Color con transparencia
                    borderColor: [color],            // Borde del gráfico
                    borderWidth: 1,
                    tension: 0.1                     // Suavizado de línea
                }],
            },
            options: {
                locale: "en-IN", // Localización para números
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: value => abbreviateNumber(value), // Formatea los valores del eje Y
                        },
                    }],
                },
                tooltips: {
                    callbacks: {
                        // Formatea el número con coma como separador de miles y dos decimales
                        label: tooltipItem =>
                            tooltipItem.yLabel
                                .toFixed(2)
                                .replace(/\d(?=(\d{3})+\.)/g, '$&,'),
                    },
                    backgroundColor: ["#353535"], // Color de fondo del tooltip
                },
            },
        });
    }


    /**
     * Carga y renderiza un gráfico tipo línea utilizando Chart.js.
     *
     * @param {Object} options - Parámetros de configuración.
     * @param {string} options.idCanvas - ID del elemento canvas donde se dibuja el gráfico.
     * @param {string} options.idContainer - ID del contenedor del gráfico (para mostrar/ocultar).
     * @param {string} options.idFallback - ID del contenedor de mensaje alternativo (cuando no hay datos).
     * @param {string} options.action - Nombre de la acción enviada al backend vía AJAX.
     * @param {string} options.label - Etiqueta del gráfico (ej. 'Ventas', 'Gastos').
     * @param {string} options.color - Color hexadecimal base para el gráfico.
     */
    function loadChart({ idCanvas, idContainer, idFallback, action, label, color }) {
        // Obtener el canvas del DOM
        const canvas = document.querySelector(`#${idCanvas}`);
        if (!canvas) return; // Si no existe, salir

        // Enviar la petición AJAX
        sendAjaxRequest({
            url: "services/home.php",
            data: { action: action },
            successCallback: (res) => {
                const data = JSON.parse(res); // Convertir la respuesta a objeto JS

                const container = $(`#${idContainer}`); // Contenedor del gráfico
                const fallback = $(`#${idFallback}`);   // Contenedor de mensaje de error

                if (Array.isArray(data)) {
                    // Si hay datos válidos, mostrar gráfico y ocultar el fallback
                    container.show();
                    fallback.hide();

                    // Renderizar el gráfico usando Chart.js
                    renderLineChart(idCanvas, label, data, color);
                } else {
                    // Si no hay datos válidos, ocultar gráfico y mostrar mensaje
                    container.hide();
                    fallback.show();
                }
            },
        });
    }


    // 📈 Cargar gráficas
    loadChart({
        idCanvas: "sales_of_the_months",
        idContainer: "sales_of_the_months",
        idFallback: "chart1",
        action: "ventas_meses",
        label: "Ventas",
        color: "#2cc098",
    });

    loadChart({
        idCanvas: "expenses_of_the_months",
        idContainer: "expenses_of_the_months",
        idFallback: "chart2",
        action: "gastos_meses",
        label: "Gastos",
        color: "#db2b2b",
    });

    loadChart({
        idCanvas: "month",
        idContainer: "month",
        idFallback: "chart3",
        action: "ventas_mes",
        label: new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(new Date()),
        color: "#822cc0",
    });




}); // Ready
