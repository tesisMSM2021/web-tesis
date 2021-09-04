$.ajax({
    url: '../../php/graficos/controlador_grafico.php',
    type: 'POST'
  }).done(function(resp){
      var temp = [];
      var hum = [];
      var humSoil = [];
      var date = [];
      var data = JSON.parse(resp);
      for(var i = 0; i < data.length; i++) {
          date.push(data[i][1]);
          temp.push(data[i][2]);
          hum.push(data[i][3])
          humSoil.push(data[i][4])
      }

    //grafico temperatura

    var ctx = document.getElementById('chartTemp');
    var chartTemp = new Chart(ctx, {
        type: 'line',
        data: {
            labels: date,
            datasets: [{
                label: 'Temperatura',
                data: temp,
                fill: false,
                borderColor: '#0cc2aa',
                tension: 0.3,
                fill: true,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false
                }
            },
        }
    });

    //grafico humedad
    var ctx = document.getElementById('chartHum');
    var chartHum = new Chart(ctx, {
        type: 'line',
        data: {
            labels: date,
            datasets: [{
                label: 'Humedad',
                data: hum,
                fill: false,
                borderColor: '#a88add',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    //grafico humedad suelo

    var ctx = document.getElementById('chartHumSoil');
    var chartHumSoil = new Chart(ctx, {
        type: 'line',
        data: {
            labels: date,
            datasets: [{
                label: 'Humedad de suelo',
                data: humSoil,
                fill: false,
                borderColor: '#fcc100',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
})
