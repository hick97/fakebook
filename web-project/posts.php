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
	if(isset($_POST['user_visitado'])){
		$user_visitado = $_POST['user_visitado'];
		$sql_mural_visitado = "SELECT id_mural FROM mural_usuario WHERE id_usuario='$user_visitado'";
		$conexao_mural_visitado = mysqli_query($conecta, $sql_mural_visitado) or die (header('Falha ao capturar mural'));
		$index_mural_visitado = mysqli_fetch_assoc($conexao_mural_visitado);	
	}
	if(isset($_GET['user_visitado'])){
		$user_visitado = $_GET['user_visitado'];
		$sql_mural_visitado = "SELECT id_mural FROM mural_usuario WHERE id_usuario='$user_visitado'";
		$conexao_mural_visitado = mysqli_query($conecta, $sql_mural_visitado) or die (header('Falha ao capturar mural'));
		$index_mural_visitado = mysqli_fetch_assoc($conexao_mural_visitado);
	}
	//Selecionando index do mural do GRUPO visitado:
	if(isset($_POST['grupo_visitado'])){
		$grupo_visitado = $_POST['grupo_visitado'];
		$sql_mural_visitado = "SELECT id_mural FROM mural_grupo WHERE id_grupo='$grupo_visitado'";
		$conexao_mural_visitado = mysqli_query($conecta, $sql_mural_visitado) or die (header('Falha ao capturar mural'));
		$index_mural_visitado = mysqli_fetch_assoc($conexao_mural_visitado);	
	}
	if(isset($_GET['grupo_visitado'])){
		$grupo_visitado = $_GET['grupo_visitado'];
		$sql_mural_visitado = "SELECT id_mural FROM mural_grupo WHERE id_grupo='$grupo_visitado'";
		$conexao_mural_visitado = mysqli_query($conecta, $sql_mural_visitado) or die (header('Falha ao capturar mural'));
		$index_mural_visitado = mysqli_fetch_assoc($conexao_mural_visitado);
	}



	if(isset($_POST['btn-newpost'])){
		//Se recebe texto e imagem:
		if((!empty($_POST['content-post-input'])) && (!empty($_FILES['imagem_post_usuario']['name']))){
			echo "entrei1";
			//Texto
			$texto_post_usuario = $_POST['content-post-input'];
			//Imagem
			$img_post_usuario = $_FILES['imagem_post_usuario'];
			// Pega extensão da imagem
			preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $img_post_usuario["name"], $ext);

			// Gera um nome único para a imagem
	       	$nome_imagem = md5(uniqid(time())) . "." . $ext[1];

			// Caminho onde ficara a imagem
			$caminho_imagem = "statics/img/" . $nome_imagem;
			// Faz o upload da imagem para seu respectivo caminho
			$upload_imagem = move_uploaded_file($img_post_usuario["tmp_name"], $caminho_imagem);
			if($upload_imagem){
				$insere_text_img = "INSERT INTO publicacao VALUES('','".$index_mural_visitado['id_mural']."', '".$caminho_imagem."','".$texto_post_usuario."','$user_logado')";

				$conexao_post_user = mysqli_query($conecta, $insere_text_img) or die (header('Falha ao inserir post'));
				
			}else{
				echo "Falha ao carregar a imagem!";
			}	
		}else{
			//Se recebe texto:
			if(!empty($_POST['content-post-input'])){
				$texto_post_usuario = $_POST['content-post-input'];
				$sql_insere_text = "INSERT INTO publicacao VALUES('','".$index_mural_visitado['id_mural']."', '','".$texto_post_usuario."','$user_logado')";

				$conexao_insere_text = mysqli_query($conecta, $sql_insere_text) or die (header('Falha ao inserir post'));
			//Se recebe foto:
			}
			if(!empty($_FILES['imagem_post_usuario']['name'])){
				$img_post_usuario = $_FILES['imagem_post_usuario'];
				// Pega extensão da imagem
				preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $img_post_usuario["name"], $ext);

				// Gera um nome único para a imagem
		       	$nome_imagem = md5(uniqid(time())) . "." . $ext[1];

				// Caminho onde ficara a imagem
				$caminho_imagem = "statics/img/" . $nome_imagem;
				// Faz o upload da imagem para seu respectivo caminho
				$upload_imagem = move_uploaded_file($img_post_usuario["tmp_name"], $caminho_imagem);
				if($upload_imagem){
					$insere_post_user = "INSERT INTO publicacao VALUES('','".$index_mural_visitado['id_mural']."', '".$caminho_imagem."','','$user_logado')";

					$conexao_post_user = mysqli_query($conecta, $insere_post_user) or die (header('Falha ao inserir post'));
					
				}else{
					echo "Falha ao carregar a imagem!";
				}
			}	
		}
		header('location:profilefak.php?user='.$user_visitado.'');
	}
	if(isset($_POST['btn-comenta'])){
		$texto_coment = $_POST['text-coment'];
		$id_publicacao = $_POST['id_publicacao'];
		$insere_coment_user = "INSERT INTO comentario VALUES('','$id_publicacao', '$user_logado','".$texto_coment."')";
		$conexao_coment_user = mysqli_query($conecta, $insere_coment_user) or die (header('Falha ao inserir comentario'));
		header('location:profilefak.php?user='.$user_visitado.'');

	}
	if(isset($_POST['btn-resposta'])){
		$texto_resposta = $_POST['text-resposta'];
		$id_comentario = $_POST['id_comentario'];
		$insere_resposta_user = "INSERT INTO resposta VALUES('','$id_comentario', '$user_logado','".$texto_resposta."')";
		$conexao_resposta_user = mysqli_query($conecta, $insere_resposta_user) or die (header('Falha ao inserir resposta'));
		header('location:profilefak.php?user='.$user_visitado.'');

	}
	if(isset($_GET['count_id_resposta'])){
		$index_resposta = $_GET['count_id_resposta'];
		$id_comentario = $_GET['id_comentario'];
		$sql_seleciona_coment = "SELECT id_resposta FROM resposta WHERE id_comentario='$id_comentario'";
		$conexao_seleciona_coment = mysqli_query($conecta, $sql_seleciona_coment) or die (header('Falha ao selecionar comentario'));
		$dados_seleciona_coment = mysqli_fetch_all($conexao_seleciona_coment);
		$id_resposta_apagada = $dados_seleciona_coment[$index_resposta][0];

		$sql_remove_respota = "DELETE FROM resposta WHERE resposta.id_resposta='$id_resposta_apagada'";
		$conexao_remove_respota = mysqli_query($conecta, $sql_remove_respota) or die (header('Falha ao remover resposta'));

		header('location:profilefak.php?user='.$user_visitado.'');

	}
	if(isset($_GET['count_id_comentario'])){
		$index_comentario = $_GET['count_id_comentario'];
		$id_publicacao = $_GET['id_publicacao'];
		$sql_seleciona_publicacao = "SELECT id_comentario FROM comentario WHERE id_publicacao='$id_publicacao'";
		$conexao_seleciona_publicacao = mysqli_query($conecta, $sql_seleciona_publicacao) or die (header('Falha ao selecionar publicacao'));
		$dados_seleciona_publicacao = mysqli_fetch_all($conexao_seleciona_publicacao);
		$id_comentario_apagada = $dados_seleciona_publicacao[$index_comentario][0];
		//Removendo as respostas daquele comentario
		$sql_remove_respostas = "DELETE FROM resposta WHERE resposta.id_comentario='$id_comentario_apagada'";
		//Remove comentario propriamente dito
		$sql_remove_comentario = "DELETE FROM comentario WHERE comentario.id_comentario='$id_comentario_apagada'";
		$conexao_remove_respostas = mysqli_query($conecta, $sql_remove_respostas) or die (header('Falha ao remover respostas do comentario a ser excluido'));
		$conexao_remove_comentario = mysqli_query($conecta, $sql_remove_comentario) or die (header('Falha ao remover comentario'));


		header('location:profilefak.php?user='.$user_visitado.'');
	}
	if(isset($_GET['count_id_publicacao'])){
		$index_publicacao = $_GET['count_id_publicacao'];
		$id_mural = $_GET['id_mural'];
		//echo "index publicacao = ".$index_publicacao."<br>";
		//echo "id mural = ".$id_mural."<br>";
		
		$sql_seleciona_mural = "SELECT id_publicacao FROM publicacao WHERE id_mural='$id_mural'";
		$conexao_seleciona_mural = mysqli_query($conecta, $sql_seleciona_mural) or die (header('Falha ao selecionar mural'));
		$dados_seleciona_mural = mysqli_fetch_all($conexao_seleciona_mural);
		$id_publicacao_apagada = $dados_seleciona_mural[$index_publicacao-1][0];
		//Seleciona comentários da publicação a ser apagada.
		$sql_seleciona_coment_apagados = "SELECT id_comentario FROM comentario WHERE id_publicacao='$id_publicacao_apagada'";
		$conexao_seleciona_coment_apagados = mysqli_query($conecta, $sql_seleciona_coment_apagados) or die (header('Falha ao selecionar comentários da publicacao a ser apagada'));
		while($linha = mysqli_fetch_assoc($conexao_seleciona_coment_apagados)){
			$sql_remove_respostas = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha['id_comentario']."'";
			$conexao_remove_respostas = mysqli_query($conecta, $sql_remove_respostas) or die (header('Falha ao remover respostas do comentario a ser excluido'));
		}
		//Remove comentario daquela publicação
		$sql_remove_comentario = "DELETE FROM comentario WHERE comentario.id_publicacao='$id_publicacao_apagada'";
		$conexao_remove_comentario = mysqli_query($conecta, $sql_remove_comentario) or die (header('Falha ao remover comentario'));
		//Remove publicacao propriamente dita
		$sql_remove_publicacao = "DELETE FROM publicacao WHERE publicacao.id_publicacao='$id_publicacao_apagada'";
		$conexao_remove_publicacao = mysqli_query($conecta, $sql_remove_publicacao) or die (header('Falha ao remover publicacao'));
		header('location:profilefak.php?user='.$user_visitado.'');
	}

	//Tudo abaixo diz respeito aos POSTS no MURAL do GRUPO

	if(isset($_POST['btn-newpost-group'])){
		//Se recebe texto e imagem:
		if((!empty($_POST['content-post-input'])) && (!empty($_FILES['imagem_post_grupo']['name']))){
			echo "entrei1";
			//Texto
			$texto_post_usuario = $_POST['content-post-input'];
			//Imagem
			$img_post_usuario = $_FILES['imagem_post_grupo'];
			// Pega extensão da imagem
			preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $img_post_usuario["name"], $ext);

			// Gera um nome único para a imagem
	       	$nome_imagem = md5(uniqid(time())) . "." . $ext[1];

			// Caminho onde ficara a imagem
			$caminho_imagem = "statics/img/" . $nome_imagem;
			// Faz o upload da imagem para seu respectivo caminho
			$upload_imagem = move_uploaded_file($img_post_usuario["tmp_name"], $caminho_imagem);
			if($upload_imagem){
				$insere_text_img = "INSERT INTO publicacao VALUES('','".$index_mural_visitado['id_mural']."', '".$caminho_imagem."','".$texto_post_usuario."','$user_logado')";

				$conexao_post_user = mysqli_query($conecta, $insere_text_img) or die (header('Falha ao inserir post'));
				
			}else{
				echo "Falha ao carregar a imagem!";
			}	
		}else{
			//Se recebe texto:
			if(!empty($_POST['content-post-input'])){
				$texto_post_usuario = $_POST['content-post-input'];
				$sql_insere_text = "INSERT INTO publicacao VALUES('','".$index_mural_visitado['id_mural']."', '','".$texto_post_usuario."','$user_logado')";

				$conexao_insere_text = mysqli_query($conecta, $sql_insere_text) or die (header('Falha ao inserir post'));
			//Se recebe foto:
			}
			if(!empty($_FILES['imagem_post_usuario']['name'])){
				$img_post_usuario = $_FILES['imagem_post_usuario'];
				// Pega extensão da imagem
				preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $img_post_usuario["name"], $ext);

				// Gera um nome único para a imagem
		       	$nome_imagem = md5(uniqid(time())) . "." . $ext[1];

				// Caminho onde ficara a imagem
				$caminho_imagem = "statics/img/" . $nome_imagem;
				// Faz o upload da imagem para seu respectivo caminho
				$upload_imagem = move_uploaded_file($img_post_usuario["tmp_name"], $caminho_imagem);
				if($upload_imagem){
					$insere_post_user = "INSERT INTO publicacao VALUES('','".$index_mural_visitado['id_mural']."', '".$caminho_imagem."','','$user_logado')";

					$conexao_post_user = mysqli_query($conecta, $insere_post_user) or die (header('Falha ao inserir post'));
					
				}else{
					echo "Falha ao carregar a imagem!";
				}
			}	
		}
		header('location:groupsfak.php?group='.$grupo_visitado.'');
	}

	if(isset($_POST['btn-comenta-group'])){
		$texto_coment = $_POST['text-coment'];
		$id_publicacao = $_POST['id_publicacao'];
		$insere_coment_user = "INSERT INTO comentario VALUES('','$id_publicacao', '$user_logado','".$texto_coment."')";
		$conexao_coment_user = mysqli_query($conecta, $insere_coment_user) or die (header('Falha ao inserir comentario'));
		echo $grupo_visitado;
		header('location:groupsfak.php?group='.$grupo_visitado.'');

	}

	if(isset($_POST['btn-resposta-group'])){
		$texto_resposta = $_POST['text-resposta'];
		$id_comentario = $_POST['id_comentario'];
		$insere_resposta_user = "INSERT INTO resposta VALUES('','$id_comentario', '$user_logado','".$texto_resposta."')";
		$conexao_resposta_user = mysqli_query($conecta, $insere_resposta_user) or die (header('Falha ao inserir resposta'));
		header('location:groupsfak.php?group='.$grupo_visitado.'');

	}

	if(isset($_GET['count_id_resposta_grupo'])){
		$index_resposta = $_GET['count_id_resposta_grupo'];
		$id_comentario = $_GET['id_comentario'];
		$sql_seleciona_coment = "SELECT id_resposta FROM resposta WHERE id_comentario='$id_comentario'";
		$conexao_seleciona_coment = mysqli_query($conecta, $sql_seleciona_coment) or die (header('Falha ao selecionar comentario'));
		$dados_seleciona_coment = mysqli_fetch_all($conexao_seleciona_coment);
		$id_resposta_apagada = $dados_seleciona_coment[$index_resposta][0];

		$sql_remove_respota = "DELETE FROM resposta WHERE resposta.id_resposta='$id_resposta_apagada'";
		$conexao_remove_respota = mysqli_query($conecta, $sql_remove_respota) or die (header('Falha ao remover resposta'));

		header('location:groupsfak.php?group='.$grupo_visitado.'');

	}

	if(isset($_GET['count_id_comentario_grupo'])){
		$index_comentario = $_GET['count_id_comentario_grupo'];
		$id_publicacao = $_GET['id_publicacao'];
		$sql_seleciona_publicacao = "SELECT id_comentario FROM comentario WHERE id_publicacao='$id_publicacao'";
		$conexao_seleciona_publicacao = mysqli_query($conecta, $sql_seleciona_publicacao) or die (header('Falha ao selecionar publicacao'));
		$dados_seleciona_publicacao = mysqli_fetch_all($conexao_seleciona_publicacao);
		$id_comentario_apagada = $dados_seleciona_publicacao[$index_comentario][0];
		//Removendo as respostas daquele comentario
		$sql_remove_respostas = "DELETE FROM resposta WHERE resposta.id_comentario='$id_comentario_apagada'";
		//Remove comentario propriamente dito
		$sql_remove_comentario = "DELETE FROM comentario WHERE comentario.id_comentario='$id_comentario_apagada'";
		$conexao_remove_respostas = mysqli_query($conecta, $sql_remove_respostas) or die (header('Falha ao remover respostas do comentario a ser excluido'));
		$conexao_remove_comentario = mysqli_query($conecta, $sql_remove_comentario) or die (header('Falha ao remover comentario'));


		header('location:groupsfak.php?group='.$grupo_visitado.'');
	}
	
	if(isset($_GET['count_id_publicacao_grupo'])){
		$index_publicacao = $_GET['count_id_publicacao_grupo'];
		$id_mural = $_GET['id_mural'];
		//echo "index publicacao = ".$index_publicacao."<br>";
		//echo "id mural = ".$id_mural."<br>";
		
		$sql_seleciona_mural = "SELECT id_publicacao FROM publicacao WHERE id_mural='$id_mural'";
		$conexao_seleciona_mural = mysqli_query($conecta, $sql_seleciona_mural) or die (header('Falha ao selecionar mural'));
		$dados_seleciona_mural = mysqli_fetch_all($conexao_seleciona_mural);
		$id_publicacao_apagada = $dados_seleciona_mural[$index_publicacao-1][0];
		//Seleciona comentários da publicação a ser apagada.
		$sql_seleciona_coment_apagados = "SELECT id_comentario FROM comentario WHERE id_publicacao='$id_publicacao_apagada'";
		$conexao_seleciona_coment_apagados = mysqli_query($conecta, $sql_seleciona_coment_apagados) or die (header('Falha ao selecionar comentários da publicacao a ser apagada'));
		while($linha = mysqli_fetch_assoc($conexao_seleciona_coment_apagados)){
			$sql_remove_respostas = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha['id_comentario']."'";
			$conexao_remove_respostas = mysqli_query($conecta, $sql_remove_respostas) or die (header('Falha ao remover respostas do comentario a ser excluido'));
		}
		//Remove comentario daquela publicação
		$sql_remove_comentario = "DELETE FROM comentario WHERE comentario.id_publicacao='$id_publicacao_apagada'";
		$conexao_remove_comentario = mysqli_query($conecta, $sql_remove_comentario) or die (header('Falha ao remover comentario'));
		//Remove publicacao propriamente dita
		$sql_remove_publicacao = "DELETE FROM publicacao WHERE publicacao.id_publicacao='$id_publicacao_apagada'";
		$conexao_remove_publicacao = mysqli_query($conecta, $sql_remove_publicacao) or die (header('Falha ao remover publicacao'));
		header('location:groupsfak.php?group='.$grupo_visitado.'');
	}
	
?>