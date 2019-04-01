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
$registro = mysqli_fetch_assoc($resultado);
$medida_total =$registro["SUM(medicao)"];

if($medida_total=<5){
	$conta = 34.58;
} else if( 5 < $medida_total =< 10 ){
	
}

/*$data = array();
foreach ($resultado as $row) {
  $data[] = $row;
}*/
//print_r($data);
  //print_r($today);

if (!$resultado){
    die("falha no banco");
}

//print_r($data);
 mysqli_close($conecta);

 //print json_encode($data);
 ?>