<?php
require_once("../conexao/conexao.php");
session_start();
date_default_timezone_set('America/Sao_Paulo');

    if ( !isset($_SESSION["user_portal"] )) {
        header("location:index.php");
    } else{
        $user = $_SESSION["user_portal"];
    }

$today = date('d', $_SERVER['REQUEST_TIME']);
$to_month = date('m', $_SERVER['REQUEST_TIME']);
$to_month = $to_month-1;
$year = date('Y', $_SERVER['REQUEST_TIME']);

$consulta_medicoes="SELECT SUM(medicao) FROM cliente, medida_data WHERE cliente.hardware_serial = medida_data.hardware_serial 
                    and mes = $to_month and ano= $year and idcliente = $user ";

$resultado = mysqli_query($conecta, $consulta_medicoes);
if (!$resultado){
    die("falha no banco");
}

$registro = mysqli_fetch_assoc($resultado);

// Se não tiver medidas, o array estará vazio
 if(!$registro["SUM(medicao)"]){
 	$medida_total=0;
 } else {
 	$medida_total =$registro["SUM(medicao)"];
 }
$medida_total = $medida_total/1000;

if($medida_total < 6){
	$conta_agua = 34.58;
	$conta_esgoto = 29.39;
} else if( 6 <= $medida_total && $medida_total < 11 ){
	$medida_total = $medida_total - 5;
	$conta_agua= 34.58 + ($medida_total * 1.07);
	$conta_esgoto= 29.39 + ($medida_total * 0.91);	
} else if( 11 <= $medida_total && $medida_total < 16 ){
	$medida_total = $medida_total - 10;
	$conta_agua = 34.58 + 5.35 + ($medida_total * 5.96);
	$conta_esgoto = 29.39 + 4.55 + ($medida_total * 5.07);	
} else if( 16 <= $medida_total && $medida_total < 21 ){
	$medida_total = $medida_total - 15;
	$conta_agua = 34.58 + 5.35 + 29.8 + ($medida_total * 5.99);	
	$conta_esgoto = 29.39 + 4.55 + 25.35 + ($medida_total * 5.09);	
} else if( 21 <= $medida_total && $medida_total <= 30 ){
	$medida_total = $medida_total - 20;
	$conta_agua = 34.58 + 5.35 + 29.8 + 29.95 + ($medida_total * 6.04);	
	$conta_esgoto = 29.39 + 4.55 + 25.35 + 25.45 + ($medida_total * 5.13);
} else if($medida_total > 30){
	$medida_total = $medida_total - 30;
	$conta_agua = 34.58 + 5.35 + 29.8 + 29.95 + 60.04 + ($medida_total * 10.22);
	$conta_esgoto = 29.39 + 4.55 + 25.35 + 25.45  + 51.3 + ($medida_total * 8.69);
}

$soma = $conta_agua + $conta_esgoto;
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
                    <li><a href="overview.php" style="color: #31708f;">Visão Geral</a></li>
                    <li><a href="historico.php" style="color: #31708f;">Histórico</a></li>  
                    <li class="active"><a href="conta.php" style="color: #31708f;">Conta</a></li>
                    <li><a href="logout.php" style="color: #31708f;">Logout</a></li>           
                </ul>
            </div>
        </div>
    </nav> 

	<div class="container" style="padding-top:80px;">                 
	    <div class="row"> 
	        <div class="col-sm-4">      
	            <div class="panel panel-info">
	                <div class="panel-heading text-center">
	                    <h2>Água</h2>
	                </div>
	                <div class="panel-body text-center">
	                    <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", $conta_agua));?></h2>
	                </div>
	            </div>
	        </div>
	        <div class="col-sm-4"> 
	            <div class="panel panel-info">
	                <div class="panel-heading text-center">
	                    <h2>Esgoto</h2>
	                </div>
	                <div class="panel-body text-center">
	                    <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", $conta_esgoto));?></h2>
	                </div>
	            </div>
	        </div>
	        <div class="col-sm-4"> 
	            <div class="panel panel-info">
	                <div class="panel-heading text-center">
	                    <h2>Total</h2>
	                </div>
	                <div class="panel-body text-center">
	                    <h2 style="color: #31708f;"><?php print_r(str_replace(".", ",", $soma));?></h2>
	                </div>
	            </div>
	        </div>
	    </div> 
	</div>
</body>
</html>


<?php
 mysqli_close($conecta);

 ?>