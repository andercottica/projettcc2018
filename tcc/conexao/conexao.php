<?php
	$servidor = "";
	$usuario = "";
	$senha = "";
	$banco = "";
	$conecta = mysqli_connect($servidor, $usuario, $senha, $banco);

	if ( mysqli_connect_errno() ) {
	    die("Conexao falhou: " . mysqli_connect_errno());
	}
?>
