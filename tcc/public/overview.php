<?php 
//-------- BANCO DE DADOS TTN --------
/*$url = "https://tccvictorteste1.data.thethingsnetwork.org/api/v2/query?last=60m";
$data = array('Accept: application/json', 'Authorization: key ttn-account-v2.BDwfYudPjqbuBpBpGRZ6n9uq9pFJKrH7Cqv78wYEEKg');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $data); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result=curl_exec($ch);
curl_close($ch);

$retorno = json_decode($result); // json para php object

foreach($retorno as $valor){
   $time=$valor->time;
   $temperatura=$valor->temperatura;
   $response[] = array(
    "device" => "$valor->device_id",
    "temperatura"=>"$valor->temperatura",
    "time" => "$valor->time"
   );
}
print_r(json_encode($response));*/
//-----------------------------------------------
require_once("../conexao/conexao.php");
session_start();

    if ( !isset($_SESSION["user_portal"] )) {
        header("location:index.php");
    } else{
        $user = $_SESSION["user_portal"];
    }

$consulta_medicoes="SELECT medicao, timestamp FROM cliente, medicoes2 where cliente.hardware_serial = medicoes2.hardware_serial 
                    and idcliente =  $user ";
$resultado = mysqli_query($conecta, $consulta_medicoes);

if (!$resultado){
    die("falha no banco");
}

?> 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta name="author" content="Anderson Cottica & Victor Hugo Laynez">
    <meta name="generator" content="sublime text">
    <meta name="description" content="App para TCC">
    <meta name="application-name" content="LoRa">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TCC - LoRa</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <link href="_css/login.css" rel="stylesheet">
     <!-- <style type="text/css">
      #chart-container {
        width: 640px;
        height: auto;
      }
    </style> -->

</head>
<body>
     <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button"
                    class="navbar-toggle"
                    data-toggle="collapse"
                    data-target="#movelmenu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="historico.php" class="navbar-brand" style="color: #31708f;">TCC - LoRa</a>
            </div>
            <div class="navbar-collapse collapse" id="movelmenu">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="overview.php" style="color: #31708f;">Visão Geral</a></li>
                    <li><a href="historico.php" style="color: #31708f;">Histórico</a></li>  
                    <li><a href="logout.php" style="color: #31708f;">Logout</a></li>           
                </ul>
            </div>
        </div>
    </nav> 

    <div class="container" style="padding-top:80px;">
        <?php 
            date_default_timezone_set('America/Sao_Paulo');
            $today = date('d', $_SERVER['REQUEST_TIME']);
            $to_month = date('m', $_SERVER['REQUEST_TIME']);
            $soma = 0;
            $yesterday=$today-1;
            $ontem = 0;
            $diferenca = 0;
            //print_r($today);
            while($registro = mysqli_fetch_assoc($resultado))
            {
                $day = date('d', $registro["timestamp"]);
                $month = date('m', $registro["timestamp"]);
                if ($today == $day) {
                    $soma = $soma + $registro["medicao"];  
                }
                if ($yesterday == $day && $month ==$to_month) {
                    $ontem = $ontem + $registro["medicao"]; 
                }
                /*if ($_SERVER['REQUEST_TIME'] - $registro["timestamp"] <604800) {
                    $soma_semana = $soma_semana + $registro["medicao"]; 
                }*/
               /* if ($to_month == $month) {
                    $soma_mes = $soma_mes + $registro["medicao"];
                }*/
            }
        ?>                     
        <div class="row"> 
            <div class="col-sm-4">      
                <div class="panel panel-info">
                    <div class="panel-heading text-center">
                        <h2>Ontem</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", $ontem));?></h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-4"> 
                <div class="panel panel-info">
                    <div class="panel-heading text-center">
                        <h2>Atual</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", $soma));?></h2>
                    </div>
                   <!--  <div class="panel-heading text-center">
                        <h2>Semana</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", $soma_semana));?></h2>
                    </div> -->
                </div>
            </div>
            <?php if (($ontem-$soma)>0) { ?>
                <div class="col-sm-4"> 
                <div class="panel panel-info">
                    <div class="panel-heading text-center">
                        <h2>Saldo</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", ($ontem-$soma)));?> litros a menos</h2>
                    </div>
                    <!-- <div class="panel-heading text-center">
                        <h2>Mês</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", $soma_mes));?></h2>
                    </div> -->
                </div>
            </div>
          <?php  } else { ?>
            <div class="col-sm-4"> 
                <div class="panel panel-danger">
                    <div class="panel-heading text-center">
                        <h2>Saldo</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", abs($ontem-$soma)));?> litros a mais</h2>
                    </div>
                    <!-- <div class="panel-heading text-center">
                        <h2>Mês</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", $soma_mes));?></h2>
                    </div> -->
                </div>
            </div>
            <?php } ?>
        </div> 
    </div>

    <div class="container" style="padding-top: 50px;">
        <div id="chart-container">
          <canvas id="mycanvas"></canvas>
        </div>
    </div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
   <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/locale/pt-br.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
          $.ajax({
            url: "data.php",
            method: "POST",
            data:{
              cliente:"<?php echo $user ?>"
          },
          success: function(data) {
              console.log(data);
              var player = [];
              var score = [];
              
              for(var i in data) {
                player.push(data[i].dia);
                score.push(data[i].medicao);
            }
            var ctx = $("#mycanvas");
            var myLine = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: player,
                    datasets: [{
                        label: 'Sua Medida',
                        backgroundColor: '#31708f',
                        borderColor: '#31708f',
                        fill: false,
                        data: score,
                        lineTension: 0
                    },
                    {
                        label: 'Média Brasil',
                        backgroundColor: '#D9EDF7',
                        borderColor: '#D9EDF7',
                        fill: true,
                        data: [400,400,400,400,400,400,400],
                        lineTension: 0
                    }
                    ]
                },
                options: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: "Consumo nos últimos 7 dias",
                        fontColor:'#31708f',
                    },
                    scales: {
                        xAxes: [{
                            scaleLabel:{
                                display:true,
                                labelString:'Dias'
                            }
                        }],
                        yAxes: [{
                            scaleLabel:{
                                display:true,
                                labelString:'Litros'
                            }
                        }]
                    }
                }
            });
        },
        error: function(data) {
          console.log(data);
        }
  });
      });
    </script>

</body>
</html>
<?php
    // Fechar conexao
    mysqli_close($conecta);
?>