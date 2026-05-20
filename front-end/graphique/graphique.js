const ctx = document.getElementById('myChart');

fetch('data_chart.php')
  .then(response => response.json())
  .then(result => {

    const labels = result.map(item => item.nom_cours);
    const values = result.map(item => item.total);

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Nombre d\'apprenants par cours',
          data: values,
          borderWidth: 1,
          backgroundColor: [
            'rgba(54, 162, 235, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(255, 99, 132, 0.2)',
          ],
          borderColor: [
            'rgba(54, 162, 235, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(255, 99, 132, 1)'
          ]
        }]
      },
      options: {
        plugins: {
            title: {
                display: true,
                text: "Statistiques des cours",
                font: { size: 30 },
                color: '#3493f3',
              },
            legend: {
              labels: {
                font: {
                  size: 18 // 👈 taille du label ici
                }
              }
            }
          },
        scales: {
          y: {
            beginAtZero: true,
            suggestedMin: 0,
            suggestedMax: Math.max(...values) + 20,
            ticks: {
                font: { size: 15, weight: 'bold' },
                color: '#343A40'
            }
            },
          x: {
            beginAtZero: true,
            suggestedMin: 0,
            suggestedMax: Math.max(...values) + 2,
            ticks: {
                font: { size: 15, weight: 'bold' },
                color: '#343A40'
            }
            },
        }
      }
    });

  });


// LINE CHART POUR LE CHIFFRE D'AFFAIRE PAR SESSION
const line_chart_CA = document.getElementById('myChart2');

fetch('data_chart_ca.php')
  .then(res => res.json())
  .then(result => {

    const labels = result.map(item => item.nom_session);
    const values = result.map(item => Number(item.ca_total));

    new Chart(line_chart_CA, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Évolution du CA par session',
          data: values,
          borderColor: 'rgba(54, 162, 235, 1)',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          fill: true,
          tension: 0.3,
          pointRadius: 5,
          pointHoverRadius: 8
        }]
      },
      options: {
        animations: {
            tension: {
              duration: 1000,
              easing: 'linear',
              from: 1,
              to: 0,
              loop: true
            }
          },
        scales: {
          y: {
            beginAtZero: true,
            suggestedMin: 0,
            suggestedMax: Math.max(...values) + 10000,
            ticks: {
                font: { size: 15},
                callback: function(value) {
                    return value.toLocaleString('fr-FR') + " Ar";
                },
                color: '#343A40'
            }
          },
          x: {
            beginAtZero: true,
            ticks: {
                font: { size: 15 },
                color: '#343A40'
            }
          }
        },
        plugins: {
            title: {
              display: true,
              text: "Evolution du chiffre d'affaire par session",
              font: { size: 30 },
              color: '#3493f3',
            },
          legend: {
            labels: {
              font: {
                size: 14
              }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.raw.toLocaleString('fr-FR') + " Ar";
              }
            }
          }
        }
      }
    });

  });