	<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title> Fakebook - Login </title>
	<!-- Tags meta importantes para o site -->
	<!-- width=device-width: largura da minha pagina será igual a largura do dispositivo. initial-scale: Nivel inicial do zoom = 1.-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<meta name="description" content="Clone of facebook.">
	<meta name="keywords" content="Sites, Social, facebook, fakebook">
	<meta name="robots" content="index, follow">
	<meta name="author" content="Henrique Augusto, Lucca peregrino">
	<link rel="stylesheet"  href="statics/css/style.css">
	<!-- Link para utilizar pacote de ícones aleatórios (Fonts awesome): -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
	<link rel="stylesheet"  href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/css?family=Antic+Didone" rel="stylesheet">
	
	<!-- Fonte - Google fonts (Lato): -->
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Merienda:400,700" rel="stylesheet"> 
	<!-- Browser Icon -->
	<link rel="icon" href="statics/img/logofb.png">	
	<?php
		if(isset($_GET["erro"])){
			if($_GET["erro"]=='1'){
				echo "<style> #id_usuario, #senha {border: 1px solid red;} .erro_login{display: block !important;} </style>";
			}else{
				echo "<style> #nome_usuario, #novo_usuario {border: 1px solid red;} .erro_cadastro{display: block !important;} </style>";
			}
			
		}

	?>
</head>
<body>
	<header class="header-info">
	<!-- Cabeçalho: -->	
	<nav class="logo-nav">
		<img src="statics/img/facebook-logos.jpg">
		<div class="login_fakebook">
			<form action="authentication.php" method="POST" id="login_form">
				<label for="id_usuario">Email</label><label for="id_usuario">Senha</label><br>
				<input type="email" name="id_usuario" id="id_usuario" autofocus required>	
				<input type="password" name="senha" id="senha" required>
				<input type="submit" name="form_login" value="Entrar">
			</form>
			<p class="erro_login" style="display: none;  color: #fff; margin-top: 5px; font-weight: 400;">Os dados inseridos estão incorretos!</p>
		</div>	
	</nav>
	</header>
			
	<!-- Main and Article: -->
	<main class="content-wrapper">
		<div class="cadastro_fakebook">
			<h1>Criar uma nova conta</h1>
			<h3>Não é o facebook, mas quebra o galho!</h3>
			<form enctype="multipart/form-data" action="authentication.php" method="POST" id="cadastro_usuario">
				<input type="text" name="nome_usuario" id="nome_usuario" placeholder="Nome" required>
				<input type="email" name="id_usuario" id="novo_usuario" placeholder="Email" required>	
				<input type="password" name="senha" id="nova_senha" placeholder="Senha" required><br>
				<label for="visibilidade">Visibilidade</label><br>
				<select name="visibilidade" id="visibilidade" required>
					<option value="1">Amigos</option>
					<option value="2">Amigos e amigos de amigos</option>
					<option value="3">Pública</option>
				</select><input type="text" name="cidade" id="cidade" placeholder="Cidade" required><br>
				<label>Imagem Perfil:</label><br><input type="file" name="imagem" id="imagem" required><br>
				<input type="submit" name="form_cadastro" value="Cadastrar">
			</form>
			<h3 class="erro_cadastro" style=" display: none; color: red; margin-top: 15px; font-weight: 400;">Usuário já existe!</h3>
		</div>
		<div class=""></div>	
	</main>
	<!-- Contacts: -->
	
	<footer class="#">
		
	</footer>
	
	<!-- Events - jQuery: -->
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="statics/js/script.js"></script>
	<script src="js/handlebars-v4.0.11.js"></script>
</body>
</html>