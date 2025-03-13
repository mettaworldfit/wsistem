$(document).ready(function () {

    const format = new Intl.NumberFormat('en-CA');


    // Ventas de todos los meses
    
    var sales_of_the_months = document.querySelector("#sales_of_the_months");

    if (sales_of_the_months != null) {
      $.ajax({
        type: "post",
        url: SITE_URL + "services/home.php",
        data: {
          action: "ventas_meses",
        },
        success: function (res) {
         
          if (res != '') {
  
           $('#sales_of_the_months').show()
           $('#chart1').css("display","none") // No hay datos para mostrar
           var data = JSON.parse(res);
      
           Sales_of_the_months(data);
  
          } else {
            $('#sales_of_the_months').hide()
            $('#chart1').css("display","flex") // No hay datos para mostrar
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
        labels.push(data[index][0].charAt(0).toUpperCase() + data[index][0].slice(1).toLowerCase());
        datos.push(data[index][1]);
      }
  
      var sales_of_the_months = new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Ventas - Mensuales",
              data: datos,
              backgroundColor: [
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab"
              ],
              borderColor: [
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab",
                "#7a00b9ab"
              ],
              borderWidth: 1,
            },
          ],
        },
        options: {
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                },
              },
            ],
          },
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
      success: function (res) {
        if (res != '') {

         $('#expenses_of_the_months').show()
         $('#chart2').css("display","none") // No hay datos para mostrar
         var data = JSON.parse(res);
    
         Expenses_of_the_months(data);

        } else {
          $('#expenses_of_the_months').hide()
          $('#chart2').css("display","flex") // No hay datos para mostrar
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
      labels.push(data[index][0].charAt(0).toUpperCase() + data[index][0].slice(1).toLowerCase());
      datos.push(data[index][1]);
    }

    var expenses_of_the_months = new Chart(ctx, {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Gastos - Mensuales",
            data: datos,
            backgroundColor: [
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0",
              "#ff0000b0"
            ],
            borderColor: [
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0",
                "#ff0000b0"
            ],
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
              },
            },
          ],
        },
      },
      
    }); // Chart
  } // Function


}) // Ready