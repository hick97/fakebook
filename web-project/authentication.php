<?PHP
	$conecta =  mysqli_connect("localhost","root","","fakebook");
	if (!$conecta) {
	    echo "Error: Unable to connect to MySQL." . PHP_EOL;
	    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	    exit;
	}
	if(isset($_GET["saindo"])){
		//Destrói
		session_destroy();

		//Limpa
		unset ($_SESSION['id_usuario']);
		unset ($_SESSION['senha']);
		
		//Redireciona para a página de autenticação
		header('location:index.php?logout=true');
	}
	if(isset($_POST["form_login"])){

		$id_usuario = $_POST['id_usuario'];
		$senha = $_POST['senha'];

		$selecao = "SELECT * FROM Usuario WHERE id_usuario = '$id_usuario' AND senha = '$senha'";
		$autentica = mysqli_query($conecta, $selecao) or die ("Erro na seleção da tabela.");

		if (mysqli_num_rows ($autentica) > 0) {
			// session_start inicia a sessão
			session_start();
			$_SESSION['id_usuario'] = $id_usuario;
			$_SESSION['senha'] = $senha;
			header('location:profilefak.php?user='.$_SESSION['id_usuario'].'');		
		//Caso contrário redireciona para a página de autenticação
		}else{
			//Destrói
			session_destroy();

			//Limpa
			unset ($_SESSION['id_usuario']);
			unset ($_SESSION['senha']);

			//Redireciona para a página de autenticação
			header('location:index.php?erro=1');
		}
	}else{
		$id_usuario = $_POST['id_usuario'];
		$senha = $_POST['senha'];
		$nome_usuario = $_POST['nome_usuario'];
		$cidade = $_POST['cidade'];
		$visibilidade = $_POST['visibilidade'];
		$img_perfil = $_FILES['imagem'];

		// Pega extensão da imagem
		preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $img_perfil["name"], $ext);

		// Gera um nome único para a imagem
       	$nome_imagem = md5(uniqid(time())) . "." . $ext[1];

		// Caminho onde ficara a imagem
		$caminho_imagem = "statics/img/" . $nome_imagem;
		// Faz o upload da imagem para seu respectivo caminho
		$upload_imagem = move_uploaded_file($img_perfil["tmp_name"], $caminho_imagem);

		if($upload_imagem){
			//Inseredados do usuario
			$insere_usuario = "INSERT INTO Usuario VALUES('$id_usuario','$nome_usuario', '$visibilidade','$cidade', '".$caminho_imagem."','$senha')";
			//Inseredados do mural
			$sql_cria_mural = "INSERT INTO mural VALUE('')";
			$sql_relaciona_mural =	"SELECT id_mural FROM mural ORDER BY id_mural DESC LIMIT 1";
			$conexao_cria_mural = mysqli_query($conecta, $sql_cria_mural) or die ('Não foi possível criar mural!');
			$conexao_relaciona_mural = mysqli_query($conecta, $sql_relaciona_mural) or die ('Não foi possível relacionar mural!');
			$max_indice_mural = mysqli_fetch_assoc($conexao_relaciona_mural);

			$sql_mural_usuario = "INSERT INTO mural_usuario (id_mural, id_usuario) VALUES('".$max_indice_mural['id_mural']."','$id_usuario')";

			//Finaliza o cadastro
			$cadastro = mysqli_query($conecta, $insere_usuario) or die (header('location:index.php?erro=2'));
			$conexao_mural_usuariol = mysqli_query($conecta, $sql_mural_usuario) or die ('deu ruim');			

			session_start();
			$_SESSION['id_usuario'] = $id_usuario;
			$_SESSION['senha'] = $senha;
			header('location:profilefak.php?user='.$_SESSION['id_usuario'].'');
			
		}else{
			echo "Falha ao carregar a imagem!";
		}
		
	}
?>