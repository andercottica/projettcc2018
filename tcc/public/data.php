<?php
require_once("../conexao/conexao.php");
//setting header to json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$cliente = $_POST["cliente"];
	if ( mysqli_connect_errno() ) {
	    die("Conexao falhou: " . mysqli_connect_errno());
	}

$consulta_medicoes="SELECT medicao, timestamp FROM cliente, medicoes2 WHERE cliente.hardware_serial = medicoes2.hardware_serial 
                    and idcliente = '$cliente' AND timestamp>'1548594767'  ORDER BY timestamp DESC ";
$resultado = mysqli_query($conecta, $consulta_medicoes);
$registro = mysqli_fetch_assoc($resultado);
$data   = array();
$teste1 = array();

foreach ($resultado as $row) {
  $data[] = $row;
}

foreach($data as $key=>$value){
	$teste = $data[$key]['timestamp']; 
	$data[$key]['timestamp'] = strftime ( '%B', $teste );
}

$response[] = array(
   );
$response[0]['medicao'] = 0;
$response[0]['timestamp']= 0;
$response[1]['medicao']= 0;
$response[1]['timestamp'] = 0;

foreach ($data as $key=>$value) {
	if ($data[$key]['timestamp']== 'janeiro') {
		$response[0]['medicao']=$response[0]['medicao']+$data[$key]['medicao'];
		$response[0]['timestamp']='janeiro';
	}
	if ($data[$key]['timestamp']=="fevereiro") {
		$response[1]['medicao']=$response[1]['medicao'] + $data[$key]['medicao'];
		$response[1]['timestamp']='fevereiro';
	}
}

if (!$resultado){
    die("falha no banco");
}

//print_r($response);
 mysqli_close($conecta);

 print json_encode($response);
 ?>