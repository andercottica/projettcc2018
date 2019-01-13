<?php
	$servidor = "gzp0u91edhmxszwf.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
	$usuario = "snam60v5qd60olbe";
	$senha = "eo3tcicft75wtkyd";
	$banco = "lhpf7436mnacfhpn";
	$conecta = mysqli_connect($servidor, $usuario, $senha, $banco);

	if ( mysqli_connect_errno() ) {
	    die("Conexao falhou: " . mysqli_connect_errno());
	}
?>