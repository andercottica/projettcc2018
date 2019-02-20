$(document).ready(function(){
  $.ajax({
    url: "http://localhost/projettcc2018/tcc/public/data.php",
    method: "POST",
    data:{
      cliente:'A0A1A2A3A4A5A6A7'
    },
    success: function(data) {
      console.log(data);
      var player = [];
      var score = [];

      for(var i in data) {
        player.push(data[i].timestamp);
        score.push(data[i].medicao);
      }

console.log(score);
      var chartdata = {
        labels: player,
        datasets : [
          {
            label: 'Medicoes',
            backgroundColor: 'rgba(200, 200, 200, 0.75)',
            borderColor: 'rgba(200, 200, 200, 0.75)',
            hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
            hoverBorderColor: 'rgba(200, 200, 200, 1)',
            data: score
          }
        ]
      };

      var ctx = $("#mycanvas");

      var barGraph = new Chart(ctx, {
        type: 'bar',
        data: chartdata
      });
    },
    error: function(data) {
      console.log(data);
    }
  });
});