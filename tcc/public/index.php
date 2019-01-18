 <?php require_once("../conexao/conexao.php"); ?>
<?php
	session_start();

	if (isset($_POST["usuario"])) {
		$usuario 	= $_POST["usuario"];
		$senha 		= $_POST["senha"];

		$login = "SELECT * ";
        $login .= "FROM cliente ";
        $login .= "WHERE usuario = '{$usuario}' AND senha = '{$senha}'";

        $acesso = mysqli_query($conecta, $login);
        
        if (!$acesso){
            die("Falha na consulta ao banco de dados");
        }
        
        $informacao = mysqli_fetch_assoc($acesso);
        
        if (empty($informacao)){
            $mensagem = "Login sem sucesso";
        } else{
            $_SESSION["user_portal"]= $informacao["idcliente"];
            header("location:overview.php");
        }
	}
 ?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <title>TCC - Login</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <!-- <link href="_css/login.css" rel="stylesheet"> -->
</head>
<body>
	<header></header>   

	<main>
        
            <div class="container"> 
                <h2>Login</h2>
                <form action="index.php" method="post">
                    <div class="form-group">
                        <input class="form-control" type="text" name="usuario" placeholder="Usuário">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="senha" placeholder="Senha">   
                    </div>
                    <button type="submit" class="btn btn-info btn-block">Login</button>
                    <?php
                                if (isset($mensagem)){
                            ?>
                            <button type="submit" class="btn btn-danger btn-block"><?php echo $mensagem ?></button>
                                
                            <?php }
                            ?>
                </form>
            </div>
       
	</main>

	<footer></footer>

<br/>
<div>
   
</div>
</body>
</html>
<?php
    // Fechar conexao
    mysqli_close($conecta);
?>