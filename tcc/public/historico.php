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

$consulta_medicoes="SELECT medicao, timestamp FROM cliente, medicoes2 WHERE cliente.hardware_serial = medicoes2.hardware_serial 
                    AND idcliente =  $user ORDER BY timestamp DESC ";
$resultado = mysqli_query($conecta, $consulta_medicoes);
/*
$data = array();
foreach ($resultado as $row) {
  $data[] = $row;
}
print json_encode($data);*/

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
    <style type="text/css">
        .table td {
            text-align: center;   
        }
    </style>

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
                    <li class="active"><a href="historico.php" style="color: #31708f;">Histórico</a></li>  
                    <li><a href="logout.php" style="color: #31708f;">Logout</a></li>           
                </ul>
            </div>
        </div>
    </nav> 

    <div class="container" style="padding-top:65px;">
        <table class="table table-condensed table-bordered table-hover">  
            <thead>
                <tr class="info">
                    <td><h2 style="color: #31708f;">Medida</h2></td>
                    <td><h2 style="color: #31708f;">Hora</h2></td>  
                </tr>  
            </thead>    
            <?php 
                while ($registro = mysqli_fetch_assoc($resultado))
                {
            ?>
                    <tr>
                        <td><h3 style="color: #31708f;"><?php echo $registro["medicao"]?></h3></td>
                        <td><h3 style="color: #31708f;">
                            <?php 
                                $epoch = $registro["timestamp"];
                                $dt = new DateTime("@$epoch");
                                echo $dt->format('d-m-Y H:i:s');
                           ?>
                           </h3>
                       </td>  
                    </tr> 
            <?php } ?>  
        </table>
    </div>
<br/>
<div>
   
</div>
</body>
</html>
<?php
    // Fechar conexao
    mysqli_close($conecta);
?>