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

if($medida_total<6){
	$conta = 34.58;
} else if( 6 <= $medida_total && $medida_total < 11 ){
	$medida_total = $medida_total - 5;
	$conta= 34.58 + ($medida_total * 1.07);	
} else if( 11 <= $medida_total && $medida_total < 16 ){
	$medida_total = $medida_total - 5;
	$conta = 34.58 + 5.35 + ($medida_total * 5.96);	
}

print_r($conta);

if (!$resultado){
    die("falha no banco");
}


 mysqli_close($conecta);

 ?>