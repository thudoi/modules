(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.BandScore = {
    attach: function (context, settings) {
      console.log(drupalSettings.list);
      /**Preload**/
      var bandscore_config = {
        type: 'line',
        data: {
          labels: ["01", "02", "03", "04", "05", "06", "07"],
          datasets: [{
            label: "My Band Score Test 1",
            backgroundColor: window.chartColors.red,
            borderColor: window.chartColors.red,
            data: [
              4.5,
              6.5,
              4,
              5,
              5.5,
              6,
              7
            ],
            fill: false,
          }, {
            label: "My Band Score Test 2",
            fill: false,
            backgroundColor: window.chartColors.blue,
            borderColor: window.chartColors.blue,
            data: [
              6.5,
              7.5,
              6,
              8,
              5.5,
              6.5,
              8.5
            ],
          }]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'My Band Score'
          },
          tooltips: {
            mode: 'index',
            intersect: false,
          },
          hover: {
            mode: 'nearest',
            intersect: true
          },
          scales: {
            xAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Test'
              }
            }],
            yAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Value'
              }
            }]
          }
        }
      };

      var accuracy_config = {
        type: 'line',
        data: {
          labels: ["01", "02", "03", "04", "05", "06", "07"],
          datasets: [{
            label: "My Band Score Test 1",
            backgroundColor: window.chartColors.red,
            borderColor: window.chartColors.red,
            data: [
              2.5,
              3.5,
              3,
              5,
              5.5,
              4,
              4.5
            ],
            fill: false,
          }, {
            label: "My Band Score Test 2",
            fill: false,
            backgroundColor: window.chartColors.blue,
            borderColor: window.chartColors.blue,
            data: [
              6.5,
              7.5,
              6,
              8,
              5.5,
              6.5,
              8.5
            ],
          }]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Accuracy'
          },
          tooltips: {
            mode: 'index',
            intersect: false,
          },
          hover: {
            mode: 'nearest',
            intersect: true
          },
          scales: {
            xAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Test'
              }
            }],
            yAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Score'
              }
            }]
          }
        }
      };

      var timespend_config = {
        type: 'line',
        data: {
          labels: ["01", "02", "03", "04", "05", "06", "07"],
          datasets: [{
            label: "My Band Score Test 1",
            backgroundColor: window.chartColors.red,
            borderColor: window.chartColors.red,
            data: [
              10,
              15,
              18,
              30,
              25,
              40,
              60,
            ],
            fill: false,
          }, {
            label: "My Band Score Test 2",
            fill: false,
            backgroundColor: window.chartColors.blue,
            borderColor: window.chartColors.blue,
            data: [
              8,
              15,
              30,
              40,
              45,
              43,
              70,
            ],
          }]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Time Spend'
          },
          tooltips: {
            mode: 'index',
            intersect: false,
          },
          hover: {
            mode: 'nearest',
            intersect: true
          },
          scales: {
            xAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Test'
              }
            }],
            yAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Time(Minutes)'
              }
            }]
          }
        }
      };

      var Pieconfig = {
        type: 'pie',
        data: {
          datasets: [{
            data: [
              70,
              20,
              10,
            ],
            backgroundColor: [
              window.chartColors.red,
              window.chartColors.yellow,
              window.chartColors.blue,
            ],
            label: 'Dataset 1'
          }],
          labels: [
            "Correct questions",
            "Incorrect questions",
            "Unanswered questions"
          ]
        },
        options: {
          responsive: true
        }
      };

      var color = Chart.helpers.color;
      var barChartDataListening = {
        labels: ["M", "MC", "PM", "SC", "SFC"],
        datasets: [{
          label: 'Percent',
          backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
          borderColor: window.chartColors.blue,
          borderWidth: 1,
          data: [
            70,
            80,
            60,
            55,
            33,
          ]
        }]

      };
      var barChartDataReading = {
        labels: ["M", "MC", "PM", "SC", "SFC"],
        datasets: [{
          label: 'Percent',
          backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
          borderColor: window.chartColors.green,
          borderWidth: 1,
          data: [
            90,
            47,
            60,
            80,
            68,
          ]
        }]

      };

      window.onload = function () {
        var bandscore = document.getElementById("band_score").getContext("2d");
        window.bandscore = new Chart(bandscore, bandscore_config);

        var accuracy = document.getElementById("accuracyChart").getContext("2d");
        window.accuracy = new Chart(accuracy, accuracy_config);

        var timespend = document.getElementById("timespend").getContext("2d");
        window.timespend = new Chart(timespend, timespend_config);

        var perform = document.getElementById("pie_perform").getContext("2d");
        window.perform = new Chart(perform, Pieconfig);

        var listening = document.getElementById("listening_perform").getContext("2d");
        window.listening = new Chart(listening, {
          type: 'bar',
          data: barChartDataListening,
          options: {
            responsive: true,
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: 'Listening Performance'
            }
          }
        });

        //window.listening = new Chart(listening, barChartDataListening);

        var reading = document.getElementById("reading_perform").getContext("2d");
        window.reading = new Chart(reading, {
          type: 'bar',
          data: barChartDataReading,
          options: {
            responsive: true,
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: 'Reading Performance'
            }
          }
        });
        //window.reading = new Chart(reading, barChartDataReading);
      };
    }
  }
})(jQuery, Drupal, drupalSettings);
