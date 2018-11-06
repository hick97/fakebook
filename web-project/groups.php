<?PHP
session_start();

//Caso o usuário não esteja autenticado, limpa os dados e redireciona
if ( !isset($_SESSION['id_usuario']) and !isset($_SESSION['senha']) ) {
	//Destrói
	session_destroy();

	//Limpa
	unset ($_SESSION['id_usuario']);
	unset ($_SESSION['senha']);
	
	//Redireciona para a página de autenticação
	header('location:index.php?erro=5');
}
?>
<?php
	$conecta =  mysqli_connect("localhost","root","","fakebook");
	if (!$conecta) {
	    echo "Error: Unable to connect to MySQL." . PHP_EOL;
	    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	    exit;
	}
	$user_logado = $_SESSION['id_usuario'];


	if(isset($_POST['form_grupo'])){
		echo "Entrei no cadastro do grupo!";
		$nome_grupo = $_POST['nome_grupo'];
		$privacidade = $_POST['visibilidade_grupo'];
		$img_grupo = $_FILES['imagem_grupo'];
		$description_group = $_POST['description-group'];

		// Pega extensão da imagem
		preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $img_grupo["name"], $ext);

		// Gera um nome único para a imagem
       	$nome_imagem = md5(uniqid(time())) . "." . $ext[1];

		// Caminho onde ficara a imagem
		$caminho_imagem = "statics/img/" . $nome_imagem;
		// Faz o upload da imagem para seu respectivo caminho
		$upload_imagem = move_uploaded_file($img_grupo["tmp_name"], $caminho_imagem);

		if($upload_imagem){
			//Inseredados do usuario
			$insere_grupo = "INSERT INTO Grupo VALUES('','$nome_grupo', '$description_group','".$caminho_imagem."','$privacidade')";
			$cadastro = mysqli_query($conecta, $insere_grupo) or die ('Falha ao inserir grupo');
			//Insere dados do mural
			$sql_cria_mural = "INSERT INTO mural VALUE('')";
			$sql_relaciona_mural =	"SELECT id_mural FROM mural ORDER BY id_mural DESC LIMIT 1";
			$sql_seleciona_idgrupo ="SELECT id_grupo FROM grupo ORDER BY id_grupo DESC LIMIT 1";
			$conexao_cria_mural = mysqli_query($conecta, $sql_cria_mural) or die ('Não foi possível criar mural!');
			$conexao_relaciona_mural = mysqli_query($conecta, $sql_relaciona_mural) or die ('Não foi possível relacionar mural!');
			$conexao_seleciona_idgrupo = mysqli_query($conecta, $sql_seleciona_idgrupo) or die ('Não foi possível selecionar id do grupo!');
			$max_indice_mural = mysqli_fetch_assoc($conexao_relaciona_mural);
			$id_grupo = mysqli_fetch_assoc($conexao_seleciona_idgrupo);

			$sql_mural_grupo = "INSERT INTO mural_grupo (id_mural, id_grupo) VALUES('".$max_indice_mural['id_mural']."','".$id_grupo['id_grupo']."')";

			//Finaliza o cadastro do grupo
			$conexao_mural_grupo = mysqli_query($conecta, $sql_mural_grupo) or die ('erro ao inserir relacionamento entre mural e grupo');
			// 1 = Membro| 2= Bloqueado / 1 = é ADM| 0 = n é adm
			$sql_insere_membro = "INSERT INTO membro VALUES('".$id_grupo['id_grupo']."','$user_logado', '1', '1')";
			$conexao_insere_membro = mysqli_query($conecta, $sql_insere_membro) or die ('erro ao inserir membro');			
			header('location:groupsfak.php?group='.$id_grupo['id_grupo'].'');
			
		}else{
			echo "Falha ao carregar a imagem!";
		}
	}
	if(isset($_GET['requisita_grupo'])){
		$id_grupo = $_GET['requisita_grupo'];

		$sql_insere_membro = "INSERT INTO membro VALUES('".$id_grupo."','$user_logado', '2', '0', '')";
		$conexao_insere_membro = mysqli_query($conecta, $sql_insere_membro) or die ('erro ao inserir membro');

		header('location:groupsfak.php?group='.$id_grupo.'');
	}
	if(isset($_GET["aceitou"])){
		$user_aceito = $_GET['aceitou'];
		$id_grupo = $_GET['group'];
		$sql_aceita = "UPDATE membro SET status_participacao = 1 WHERE id_usuario='$user_aceito' AND status_participacao=2 AND id_grupo = '$id_grupo'";
		$conexao_aceita = mysqli_query($conecta, $sql_aceita) or die ("Erro na aprovação do pedido de amizade");
		header('location:profilefak.php?user='.$user_logado.'');
	}
	if(isset($_GET["recusou"])){
		$user_recusado = $_GET['recusou'];
		$id_grupo = $_GET['group'];
		$sql_recusa = "DELETE FROM membro WHERE membro.id_usuario='$user_recusado' AND membro.status_participacao=2 AND membro.id_grupo = '$id_grupo'";
		$conexao_recusa = mysqli_query($conecta, $sql_recusa) or die ("Erro ao recusar o pedido de amizade");
		header('location:profilefak.php?user='.$user_logado.'');
	}
	if(isset($_GET["user_adm"])){
		$user_adm = $_GET['user_adm'];
		$id_grupo = $_GET['group'];
		$sql_torna_adm = "UPDATE membro SET status_adm = 1 WHERE id_usuario='$user_adm' AND status_adm=0 AND id_grupo = '$id_grupo'";
		$conexao_torna_adm = mysqli_query($conecta, $sql_torna_adm) or die ("Erro ao tornar usuário administrador");
		header('location:groupsfak.php?group='.$id_grupo.'');
	}
	if(isset($_GET["remove_adm"])){
		$user_adm = $_GET['remove_adm'];
		$id_grupo = $_GET['group'];
		$sql_remove_adm = "UPDATE membro SET status_adm = 0 WHERE id_usuario='$user_adm' AND status_adm=1 AND id_grupo = '$id_grupo'";
		$conexao_remove_adm = mysqli_query($conecta, $sql_remove_adm) or die ("Erro ao remover usuário da administração");
		header('location:groupsfak.php?group='.$id_grupo.'');
	}
	if(isset($_GET["remove_user"])){
		$user_removido = $_GET['remove_user'];
		$id_grupo = $_GET['group'];
		$sql_remove_user = "DELETE FROM membro WHERE membro.id_usuario='$user_removido' AND membro.status_participacao=1 AND membro.id_grupo = '$id_grupo'";
		$conexao_remove_user = mysqli_query($conecta, $sql_remove_user) or die ("Erro ao remover usuário do grupo");
		header('location:groupsfak.php?group='.$id_grupo.'');
	}
	if(isset($_GET["block_user"])){
		$user_bloqueado = $_GET['block_user'];
		$id_grupo = $_GET['group'];
		$sql_remove_user = "UPDATE membro SET status_participacao = 3 WHERE id_usuario='$user_bloqueado' AND status_participacao=1 AND id_grupo = '$id_grupo'";
		$conexao_remove_user = mysqli_query($conecta, $sql_remove_user) or die ("Erro ao bloquear usuário do grupo");
		header('location:groupsfak.php?group='.$id_grupo.'');
	}
	if(isset($_GET["unblock_group"])){
		$user_desbloqueado = $_GET['unblock_group'];
		$id_grupo = $_GET['group'];
		$sql_remove_user = "DELETE FROM membro WHERE membro.id_usuario='$user_desbloqueado' AND membro.status_participacao=3 AND membro.id_grupo = '$id_grupo'";
		$conexao_remove_user = mysqli_query($conecta, $sql_remove_user) or die ("Erro ao desbloquear usuário do grupo");
		header('location:groupsfak.php?group='.$id_grupo.'');
	}
?>