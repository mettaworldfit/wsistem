$(document).ready(function() {

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

    // Ventas de todos los meses

    var sales_of_the_months = document.querySelector("#sales_of_the_months");

    if (sales_of_the_months != null) {
        $.ajax({
            type: "post",
            url: SITE_URL + "services/home.php",
            data: {
                action: "ventas_meses",
            },
            success: function(res) {
                if (res != "") {
                    $("#sales_of_the_months").show();
                    $("#chart1").css("display", "none"); // No hay datos para mostrar
                    var data = JSON.parse(res);

                    Sales_of_the_months(data);
                } else {
                    $("#sales_of_the_months").hide();
                    $("#chart1").css("display", "flex"); // No hay datos para mostrar
                }
            },
        });
    }

    function Sales_of_the_months(data) {
        var ctx = document.getElementById("sales_of_the_months").getContext("2d");

        let labels = [];
        let datos = [];

        // Loop
        for (let index = 0; index < data.length; index++) {
            labels.push(
                data[index][0].charAt(0).toUpperCase() +
                data[index][0].slice(1).toLowerCase()
            );
            datos.push(data[index][1]);
        }

        var sales_of_the_months = new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Ventas",
                    data: datos,
                    backgroundColor: ["#2cc09866"],
                    borderColor: ["#2cc098"],
                    borderWidth: 1,
                    tension: 0.1
                }, ],
            },
            options: {
                locale: "en-IN",
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(value) {
                                return abbreviateNumber(value);
                            },
                        },
                    }, ],
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                        }
                    },
                    backgroundColor: ["#353535"]
                }
            },
        }); // Chart
    } // Function

    // Gastos de todos los meses

    var expenses_of_the_months = document.querySelector("#expenses_of_the_months");

    if (expenses_of_the_months != null) {
        $.ajax({
            type: "post",
            url: SITE_URL + "services/home.php",
            data: {
                action: "gastos_meses",
            },
            success: function(res) {
                if (res != "") {
                    $("#expenses_of_the_months").show();
                    $("#chart2").css("display", "none"); // No hay datos para mostrar
                    var data = JSON.parse(res);

                    Expenses_of_the_months(data);
                } else {
                    $("#expenses_of_the_months").hide();
                    $("#chart2").css("display", "flex"); // No hay datos para mostrar
                }
            },
        });
    }

    function Expenses_of_the_months(data) {
        var ctx = document.getElementById("expenses_of_the_months").getContext("2d");

        let labels = [];
        let datos = [];

        // Loop
        for (let index = 0; index < data.length; index++) {
            labels.push(
                data[index][0].charAt(0).toUpperCase() +
                data[index][0].slice(1).toLowerCase()
            );
            datos.push(data[index][1]);
        }

        var expenses_of_the_months = new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Gastos",
                    data: datos,
                    backgroundColor: [
                        "#db2b2b87"
                    ],
                    borderColor: [
                        "#db2b2b"
                    ],
                    borderWidth: 1,
                    tension: 0.1
                }, ],
            },
            options: {
                locale: "en-IN",
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(value) {
                                return abbreviateNumber(value);
                            },
                        },
                    }, ],
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                        }
                    },
                    backgroundColor: ["#353535"]
                }
            },
        }); // Chart
    } // Function

    // Ventas diarias

    var month = document.querySelector("#month");

    if (month != null) {
        $.ajax({
            type: "post",
            url: SITE_URL + "services/home.php",
            data: {
                action: "ventas_mes",
            },
            success: function(res) {
                if (res != "") {
                    $("#month").show();
                    $("#chart3").css("display", "none"); // No hay datos para mostrar
                    var data = JSON.parse(res);

                    Month(data);
                } else {
                    $("#month").hide();
                    $("#chart3").css("display", "flex"); // No hay datos para mostrar
                }
            },
        });
    }

    function Month(data) {
        var ctx = document.getElementById("month").getContext("2d");

        let labels = [];
        let datos = [];

        // Loop
        for (let index = 0; index < data.length; index++) {
            labels.push(
                data[index][0].charAt(0).toUpperCase() +
                data[index][0].slice(1).toLowerCase()
            );
            datos.push(data[index][1]);
        }

        var month = new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(new Date()),
                    data: datos,
                    backgroundColor: ["#822cc066"],
                    tension: 0.1,
                    borderColor: ["#822cc0"],
                    borderWidth: 1,
                }, ],
            },
            options: {
                locale: "en-IN",
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(value) {
                                return abbreviateNumber(value);
                            },
                        },
                    }, ],
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                        }
                    },
                    backgroundColor: ["#353535"]
                }
            },
        }); // Chart
    } // Function


}); // Ready