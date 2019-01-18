<?php 
    session_start();

?> 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <title>TCC - LoRa</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="script.js"></script>
</head>
<body>
      <?php 
    unset($_SESSION["usuario"]);
    header("location:index.php");
    ?>       
<br/>
<div>
   
</div>
</body>
</html>
