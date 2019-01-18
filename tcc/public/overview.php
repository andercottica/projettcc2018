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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
    <link href="_css/login.css" rel="stylesheet">
    

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
                <a href="historico.php" class="navbar-brand">TCC - LoRa</a>
            </div>
            <div class="navbar-collapse collapse" id="movelmenu">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="overview.php">Visão Geral</a></li>
                    <li><a href="historico.php">Histórico</a></li>  
                    <li><a href="logout.php">Logout</a></li>           
                </ul>
            </div>
        </div>
    </nav> 

    <div class="container" style="padding-top:80px;">
        <?php 
            date_default_timezone_set('Etc/GMT+3');
            $today = date('d', $_SERVER['REQUEST_TIME']);
            $to_month = date('m', $_SERVER['REQUEST_TIME']);
            $soma = 0;
            $soma_semana = 0;
            $soma_mes = 0;
            //print_r($today);
            while($registro = mysqli_fetch_assoc($resultado))
            {
                $day = date('d', $registro["timestamp"]);
                $month = date('m', $registro["timestamp"]);
                if ($today == $day) {
                    $soma = $soma + $registro["medicao"];  
                }
                if ($_SERVER['REQUEST_TIME'] - $registro["timestamp"] <604800) {
                    $soma_semana = $soma_semana + $registro["medicao"]; 
                }
                if ($to_month == $month) {
                    $soma_mes = $soma_mes + $registro["medicao"];
                }
            }
            $media_brasil = (($soma*100)/10000);// valor de media alterado para uso com sensor de temperatura
            $media_brasil = number_format($media_brasil, 2, '.','');
            //print_r($media_brasil);
            //$media_brasil = str_replace(".", ",", $media_brasil);
        ?>                     
        <div class="row"> 
            <div class="col-sm-4">      
                <div class="panel panel-info">
                    <div class="panel-heading text-center">
                        <h2>Hoje</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2><?php print_r(str_replace(".", ",", $soma));?></h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-4"> 
                <div class="panel panel-info">
                    <div class="panel-heading text-center">
                        <h2>Semana</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2><?php print_r(str_replace(".", ",", $soma_semana));?></h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-4"> 
                <div class="panel panel-info">
                    <div class="panel-heading text-center">
                        <h2>Mês</h2>
                    </div>
                    <div class="panel-body text-center">
                        <h2><?php print_r(str_replace(".", ",", $soma_mes));?></h2>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div class="container">
        <h2>Média Brasil</h2>
        <div class="progress">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $media_brasil ?>" aria-valuemin="0" aria-valuemax="100" 
                style="width: <?php echo $media_brasil ?>%">
          <?php echo (str_replace(".", ",", $media_brasil))?>%
            </div>
        </div>
    </div>

</body>
</html>
<?php
    // Fechar conexao
    mysqli_close($conecta);
?>