<?php
require_once("../conexao/conexao.php");
//setting header to json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
//$today = date('d', $_SERVER['REQUEST_TIME']);

$cliente = $_POST["cliente"];
	if ( mysqli_connect_errno() ) {
	    die("Conexao falhou: " . mysqli_connect_errno());
	}

 $today = date('d', $_SERVER['REQUEST_TIME']);
 $to_month = date('m', $_SERVER['REQUEST_TIME']);
 $to_month = $to_month-1;
 $year = date('Y', $_SERVER['REQUEST_TIME']);
 $week = $today-7;
$consulta_medicoes="SELECT medicao,dia FROM cliente  ,medida_data WHERE cliente.hardware_serial = medida_data.hardware_serial 
                    and mes = $to_month and ano= $year and dia > $week and idcliente = $cliente ";
$resultado = mysqli_query($conecta, $consulta_medicoes);
$registro = mysqli_fetch_assoc($resultado);

$data = array();
foreach ($resultado as $row) {
  $data[] = $row;
}

  

if (!$resultado){
    die("falha no banco");
}

//print_r($data);
 mysqli_close($conecta);

 print json_encode($data);
 ?>