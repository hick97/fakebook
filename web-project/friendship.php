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
	if(isset($_GET["amizade_requisitada"])){
		$user_recebe_pedido = $_GET['amizade_requisitada'];
		$sql_autor_request = "SELECT nome_usuario FROM Usuario WHERE id_usuario='$user_logado'";
		$conexao_dados_autor = mysqli_query($conecta, $sql_autor_request) or die ("Erro na seleção do autor da requisição.");
		$array_autor = mysqli_fetch_assoc($conexao_dados_autor);
		$nome_autor = $array_autor['nome_usuario'];
		$sql_recebe_pedido = "INSERT INTO Amizade VALUES('$user_logado', '$user_recebe_pedido', '2', '$nome_autor')";
		$conexao_insere_pedido = mysqli_query($conecta, $sql_recebe_pedido) or die ("Erro na inserção da amizade.");
		header('location:profilefak.php?user='.$user_recebe_pedido.'');
	}
	if(isset($_GET["aceitou"])){
		$user_aceito = $_GET['aceitou'];
		$sql_aceita = "UPDATE Amizade SET status_amizade = 1 WHERE id_usuario1='$user_aceito' AND id_usuario2 = '$user_logado' AND status_amizade=2";
		$conexao_aceita = mysqli_query($conecta, $sql_aceita) or die ("Erro na aprovação do pedido de amizade");
		header('location:profilefak.php?user='.$user_logado.'');
	}
	if(isset($_GET["recusou"])){
		$user_recusado = $_GET['recusou'];
		$sql_recusa = "DELETE FROM Amizade WHERE Amizade.id_usuario1='$user_recusado' AND Amizade.id_usuario2 = '$user_logado' AND status_amizade=2";
		$conexao_recusa = mysqli_query($conecta, $sql_recusa) or die ("Erro ao recusar o pedido de amizade");
		header('location:profilefak.php?user='.$user_logado.'');
	}
	if(isset($_GET["bloqueio_requisitado"])){
		$user_bloqueado = $_GET['bloqueio_requisitado'];
		//Checa amizade e pendencia:
		$sql_check1 = "SELECT * FROM Amizade WHERE id_usuario1='$user_logado' AND id_usuario2='$user_bloqueado' AND (status_amizade=1 OR status_amizade=2)";
		$sql_check2 = "SELECT * FROM Amizade WHERE id_usuario2='$user_logado' AND id_usuario1='$user_bloqueado' AND (status_amizade=1 OR status_amizade=2)";
		$conexao_check1 = mysqli_query($conecta, $sql_check1) or die ("Erro ao checar amizade/pendencia");
		$conexao_check2 = mysqli_query($conecta, $sql_check2) or die ("Erro ao checar amizade/pendencia");
		//Caso n tenha amizade ou pedencia:
		if(!(mysqli_num_rows ($conexao_check1) > 0 or mysqli_num_rows ($conexao_check2)>0)){
			$sql_insere_block = "INSERT INTO Amizade VALUES('$user_logado', '$user_bloqueado', '3', '$user_logado')";
			$conexao_insere_block = mysqli_query($conecta, $sql_insere_block) or die ("Erro ao inserir bloqueio");

		}else{
			//Caso já sejam amigos ou tenha um pedido de amizade pendente:
			$sql_desfazamz1 = "UPDATE Amizade SET status_amizade = 3, autor_request = '$user_logado' WHERE id_usuario1='$user_bloqueado' AND id_usuario2 = '$user_logado' AND (status_amizade=1 OR status_amizade=2)";
			$sql_desfazamz2 = "UPDATE Amizade SET status_amizade = 3, autor_request = '$user_logado' WHERE id_usuario2='$user_bloqueado' AND id_usuario1 = '$user_logado' AND (status_amizade=1 OR status_amizade=2)";
			$conexao_desfazamz1 = mysqli_query($conecta, $sql_desfazamz1) or die ("Erro ao desfazer amizade");
			$conexao_desfazamz2 = mysqli_query($conecta, $sql_desfazamz2) or die ("Erro ao desfazer amizade");	
		}
		//Limpando mural.
		$id_mural_logado = $_GET['id_mural_logado'];
		$sql_seleciona_pub = "SELECT id_publicacao FROM publicacao WHERE id_mural='$id_mural_logado' AND id_autor = '$user_bloqueado'";
		$conexao_seleciona_pub = mysqli_query($conecta, $sql_seleciona_pub) or die (header('Falha ao selecionar publicações'));

		//Limpa post do que bloqueou.
		if(mysqli_num_rows($conexao_seleciona_pub) > 0){
			while ($linha_pub = mysqli_fetch_assoc($conexao_seleciona_pub)) {
				$sql_seleciona_pub_coment = "SELECT id_comentario FROM comentario WHERE id_publicacao='".$linha_pub['id_publicacao']."'";
				$conexao_seleciona_pub_coment = mysqli_query($conecta, $sql_seleciona_pub_coment) or die (header('Falha ao selecionar comentario da publicação'));
				if(mysqli_num_rows($conexao_seleciona_pub_coment)>0){
					while ($linha_pub_coment = mysqli_fetch_assoc($conexao_seleciona_pub_coment)) {
						$sql_delete_resp = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha_pub_coment['id_comentario']."'";
						$conexao_delete_resp = mysqli_query($conecta, $sql_delete_resp) or die (header('Falha ao deletar respostas'));
						$sql_delete_coment = "DELETE FROM comentario WHERE comentario.id_comentario='".$linha_pub_coment['id_comentario']."'";
						$conexao_delete_coment = mysqli_query($conecta, $sql_delete_coment) or die (header('Falha ao deletar comentario'));
					}
				}
				$sql_delete_pub = "DELETE FROM publicacao WHERE publicacao.id_publicacao='".$linha_pub['id_publicacao']."'";
				$conexao_delete_pub = mysqli_query($conecta, $sql_delete_pub) or die (header('Falha ao deletar publicacao'));
			}
		}
		$sql_seleciona_coment = "SELECT id_comentario FROM comentario INNER JOIN publicacao ON comentario.id_publicacao = publicacao.id_publicacao AND publicacao.id_mural = '$id_mural_logado' AND comentario.id_autor = '$user_bloqueado'";
		$conexao_seleciona_coment = mysqli_query($conecta, $sql_seleciona_coment) or die (header('Falha ao selecionar comentários'));
		//Limpa comentario do que bloqueou.
		if(mysqli_num_rows($conexao_seleciona_coment) > 0){
			while ($linha_coment = mysqli_fetch_assoc($conexao_seleciona_coment)) {
				$sql_delete_coment_resp = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha_coment['id_comentario']."'";
				$conexao_delete_coment_resp = mysqli_query($conecta, $sql_delete_coment_resp) or die (header('Falha ao deletar respostas'));
				$sql_delete_coment = "DELETE FROM comentario WHERE comentario.id_comentario='".$linha_coment['id_comentario']."'";
				$conexao_delete_coment = mysqli_query($conecta, $sql_delete_coment) or die (header('Falha ao deletar comentario'));	
			}	
		}
		$sql_seleciona_resposta = "SELECT id_resposta FROM resposta INNER JOIN comentario ON resposta.id_comentario =comentario.id_comentario AND resposta.id_autor='$user_bloqueado'INNER JOIN publicacao ON comentario.id_publicacao = publicacao.id_publicacao	AND publicacao.id_mural = '$id_mural_logado'";
		$conexao_seleciona_resposta = mysqli_query($conecta, $sql_seleciona_resposta) or die (header('Falha ao selecionar resposta'));
		//Limpar respostas do que bloqueou
		if(mysqli_num_rows($conexao_seleciona_resposta) > 0){
			while ($linha_resposta = mysqli_fetch_assoc($conexao_seleciona_resposta)) {
				$sql_delete_resposta = "DELETE FROM resposta WHERE resposta.id_resposta='".$linha_resposta['id_resposta']."'";
				$conexao_delete_resposta = mysqli_query($conecta, $sql_delete_resposta) or die (header('Falha ao deletar respostas'));	
			}	
		}

		$sql_seleciona_mural_bloq = "SELECT id_mural FROM mural_usuario WHERE id_usuario='$user_bloqueado'";
		$conexao_seleciona_mural_bloq = mysqli_query($conecta, $sql_seleciona_mural_bloq) or die (header('Falha ao mural'));
		$id_mural_bloq = mysqli_fetch_assoc($conexao_seleciona_mural_bloq);
		$sql_seleciona_pub_bloq = "SELECT id_publicacao FROM publicacao WHERE id_mural='".$id_mural_bloq['id_mural']."' AND id_autor = '$user_logado'";
		$conexao_seleciona_pub_bloq = mysqli_query($conecta, $sql_seleciona_pub_bloq) or die (header('Falha ao selecionar publicações'));
		//Limpa post do usuário bloqueado.
		if(mysqli_num_rows($conexao_seleciona_pub_bloq) > 0){
			while ($linha_pub_bloq = mysqli_fetch_assoc($conexao_seleciona_pub_bloq)) {
				echo "Selecionei corretamente minhas publicações <br>";
				$sql_seleciona_pub_coment_bloq = "SELECT id_comentario FROM comentario WHERE id_publicacao='".$linha_pub_bloq['id_publicacao']."'";
				$conexao_seleciona_pub_coment_bloq = mysqli_query($conecta, $sql_seleciona_pub_coment_bloq) or die (header('Falha ao selecionar comentario da publicação'));
				if(mysqli_num_rows($conexao_seleciona_pub_coment_bloq)>0){
					while ($linha_pub_coment_bloq = mysqli_fetch_assoc($conexao_seleciona_pub_coment_bloq)) {
						$sql_delete_resp_bloq = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha_pub_coment_bloq['id_comentario']."'";
						$conexao_delete_resp_bloq = mysqli_query($conecta, $sql_delete_resp_bloq) or die (header('Falha ao deletar respostas'));
						$sql_delete_coment_bloq = "DELETE FROM comentario WHERE comentario.id_comentario='".$linha_pub_coment_bloq['id_comentario']."'";
						$conexao_delete_coment_bloq = mysqli_query($conecta, $sql_delete_coment_bloq) or die (header('Falha ao deletar comentario'));
					}
				}
				$sql_delete_pub_bloq = "DELETE FROM publicacao WHERE publicacao.id_publicacao='".$linha_pub_bloq['id_publicacao']."'";
				$conexao_delete_coment_bloq = mysqli_query($conecta, $sql_delete_pub_bloq) or die (header('Falha ao deletar publicacao'));
			}

		}
		$sql_seleciona_coment_bloq = "SELECT id_comentario FROM comentario INNER JOIN publicacao ON comentario.id_publicacao = publicacao.id_publicacao AND publicacao.id_mural = '".$id_mural_bloq['id_mural']."' AND comentario.id_autor = '$user_logado'";
		$conexao_seleciona_coment_bloq = mysqli_query($conecta, $sql_seleciona_coment_bloq) or die (header('Falha ao selecionar comentários'));
		//Limpa comentario do usuário bloquedo.
		if(mysqli_num_rows($conexao_seleciona_coment_bloq) > 0){
			while ($linha_coment_bloq = mysqli_fetch_assoc($conexao_seleciona_coment_bloq)) {
				$sql_delete_coment_resp_bloq = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha_coment_bloq['id_comentario']."'";
				$conexao_delete_coment_resp_bloq = mysqli_query($conecta, $sql_delete_coment_resp_bloq) or die (header('Falha ao deletar respostas'));
				$sql_delete_coment_bloq = "DELETE FROM comentario WHERE comentario.id_comentario='".$linha_coment_bloq['id_comentario']."'";
				$conexao_delete_coment_bloq = mysqli_query($conecta, $sql_delete_coment_bloq) or die (header('Falha ao deletar comentario'));	
			}	
		}
		//Limpa respostas do usuário bloqueado
		$sql_seleciona_resposta_bloq = "SELECT id_resposta FROM resposta INNER JOIN comentario ON resposta.id_comentario =comentario.id_comentario AND resposta.id_autor='$user_logado'INNER JOIN publicacao ON comentario.id_publicacao = publicacao.id_publicacao	AND publicacao.id_mural = '".$id_mural_bloq['id_mural']."'";
		$conexao_seleciona_resposta_bloq = mysqli_query($conecta, $sql_seleciona_resposta_bloq) or die (header('Falha ao selecionar resposta'));
		if(mysqli_num_rows($conexao_seleciona_resposta_bloq) > 0){
			while ($linha_resposta_bloq = mysqli_fetch_assoc($conexao_seleciona_resposta_bloq)) {
				$sql_delete_resposta_bloq = "DELETE FROM resposta WHERE resposta.id_resposta='".$linha_resposta_bloq['id_resposta']."'";
				$conexao_delete_resposta_bloq = mysqli_query($conecta, $sql_delete_resposta_bloq) or die (header('Falha ao deletar respostas'));	
			}	
		}

		//NO QUE DIZ RESPEITO A LIMPEZA DO MURAL DO GRUPO EM QUE O OUTRO USUÁRIO É ADM
		//LIMPANDO PUBLICAÇÕES DO USER LOGADO NO MURAL DO GRUPO Q O USUARIO BLOQUEADO EH ADM
		$sql_grupos_logado = "SELECT * FROM grupo INNER JOIN membro ON membro.id_grupo = grupo.id_grupo AND membro.id_usuario = '$user_logado' AND membro.status_participacao=1";
		$conexao_grupos_logado = mysqli_query($conecta, $sql_grupos_logado) or die (header('Falha ao selecionar grupos do usuário logado'));
		while ($linha_grupos_logado = mysqli_fetch_assoc($conexao_grupos_logado)) {
			$sql_verificaadm_bloqueado = "SELECT id_mural FROM mural_grupo INNER JOIN membro ON membro.id_grupo = mural_grupo.id_grupo AND membro.id_usuario='$user_bloqueado' AND membro.status_adm = 1 AND membro.id_grupo='".$linha_grupos_logado['id_grupo']."'";

			$conexao_verificaadm_bloqueado = mysqli_query($conecta, $sql_verificaadm_bloqueado) or die (header('Falha ao verificar se o user bloqueado é adm de algum grupo do usuário logado'));
			if(mysqli_num_rows($conexao_verificaadm_bloqueado)>0){
				while($linha_mural_grupo = mysqli_fetch_assoc($conexao_verificaadm_bloqueado)){
					echo "To verificando o bloqueio com o grupo: ".$linha_grupos_logado['id_grupo']."<br>";
					$id_grupo = $linha_grupos_logado['id_grupo'];
					//Efetua bloqueio
					$sql_block_user = "UPDATE membro SET status_participacao = 4, autor_block='".$user_bloqueado."' WHERE id_usuario='$user_logado' AND status_participacao=1 AND id_grupo = '$id_grupo'";
					$conexao_block_user = mysqli_query($conecta, $sql_block_user) or die ("Erro ao bloquear usuário do grupo");
					//Limpeza mural do grupo
					$id_mural_grupo = $linha_mural_grupo['id_mural'];
					echo "Mural selecionado: ".$id_mural_grupo."<br>";
					$sql_seleciona_pub_bloq = "SELECT id_publicacao FROM publicacao WHERE id_mural='$id_mural_grupo' AND id_autor = '$user_logado'";
					$conexao_seleciona_pub_bloq = mysqli_query($conecta, $sql_seleciona_pub_bloq) or die (header('Falha ao selecionar publicações'));
					//Limpa post do usuário bloqueado.
					if(mysqli_num_rows($conexao_seleciona_pub_bloq) > 0){
						while ($linha_pub_bloq = mysqli_fetch_assoc($conexao_seleciona_pub_bloq)) {
							echo "Selecionei corretamente minhas publicações <br>";
							$sql_seleciona_pub_coment_bloq = "SELECT id_comentario FROM comentario WHERE id_publicacao='".$linha_pub_bloq['id_publicacao']."'";
							$conexao_seleciona_pub_coment_bloq = mysqli_query($conecta, $sql_seleciona_pub_coment_bloq) or die (header('Falha ao selecionar comentario da publicação'));
							if(mysqli_num_rows($conexao_seleciona_pub_coment_bloq)>0){
								while ($linha_pub_coment_bloq = mysqli_fetch_assoc($conexao_seleciona_pub_coment_bloq)) {
									$sql_delete_resp_bloq = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha_pub_coment_bloq['id_comentario']."'";
									$conexao_delete_resp_bloq = mysqli_query($conecta, $sql_delete_resp_bloq) or die (header('Falha ao deletar respostas'));
									$sql_delete_coment_bloq = "DELETE FROM comentario WHERE comentario.id_comentario='".$linha_pub_coment_bloq['id_comentario']."'";
									$conexao_delete_coment_bloq = mysqli_query($conecta, $sql_delete_coment_bloq) or die (header('Falha ao deletar comentario'));
								}
							}
							$sql_delete_pub_bloq = "DELETE FROM publicacao WHERE publicacao.id_publicacao='".$linha_pub_bloq['id_publicacao']."'";
							$conexao_delete_coment_bloq = mysqli_query($conecta, $sql_delete_pub_bloq) or die (header('Falha ao deletar publicacao'));
						}

					}
					$sql_seleciona_coment_bloq = "SELECT id_comentario FROM comentario INNER JOIN publicacao ON comentario.id_publicacao = publicacao.id_publicacao AND publicacao.id_mural = '$id_mural_grupo' AND comentario.id_autor = '$user_logado'";
					$conexao_seleciona_coment_bloq = mysqli_query($conecta, $sql_seleciona_coment_bloq) or die (header('Falha ao selecionar comentários'));
					//Limpa comentario do usuário bloquedo.
					if(mysqli_num_rows($conexao_seleciona_coment_bloq) > 0){
						while ($linha_coment_bloq = mysqli_fetch_assoc($conexao_seleciona_coment_bloq)) {
							$sql_delete_coment_resp_bloq = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha_coment_bloq['id_comentario']."'";
							$conexao_delete_coment_resp_bloq = mysqli_query($conecta, $sql_delete_coment_resp_bloq) or die (header('Falha ao deletar respostas'));
							$sql_delete_coment_bloq = "DELETE FROM comentario WHERE comentario.id_comentario='".$linha_coment_bloq['id_comentario']."'";
							$conexao_delete_coment_bloq = mysqli_query($conecta, $sql_delete_coment_bloq) or die (header('Falha ao deletar comentario'));	
						}	
					}
					//Limpa respostas do usuário bloqueado
					$sql_seleciona_resposta_bloq = "SELECT id_resposta FROM resposta INNER JOIN comentario ON resposta.id_comentario =comentario.id_comentario AND resposta.id_autor='$user_logado'INNER JOIN publicacao ON comentario.id_publicacao = publicacao.id_publicacao	AND publicacao.id_mural = '$id_mural_grupo'";
					$conexao_seleciona_resposta_bloq = mysqli_query($conecta, $sql_seleciona_resposta_bloq) or die (header('Falha ao selecionar resposta'));
					if(mysqli_num_rows($conexao_seleciona_resposta_bloq) > 0){
						while ($linha_resposta_bloq = mysqli_fetch_assoc($conexao_seleciona_resposta_bloq)) {
							$sql_delete_resposta_bloq = "DELETE FROM resposta WHERE resposta.id_resposta='".$linha_resposta_bloq['id_resposta']."'";
							$conexao_delete_resposta_bloq = mysqli_query($conecta, $sql_delete_resposta_bloq) or die (header('Falha ao deletar respostas'));	
						}	
					}

				}
			}
		}
		//LIMPANDO PUBLICAÇÕES DO USER BLOQUEADO NO MURAL DO GRUPO Q O USUARIO LOGADO EH ADM
		$sql_grupos_block = "SELECT * FROM grupo INNER JOIN membro ON membro.id_grupo = grupo.id_grupo AND membro.id_usuario = '$user_bloqueado' AND membro.status_participacao=1";
		$conexao_grupos_block = mysqli_query($conecta, $sql_grupos_block) or die (header('Falha ao selecionar grupos do usuário block'));
		while ($linha_grupos_block = mysqli_fetch_assoc($conexao_grupos_block)) {
			$sql_verificaadm_logado = "SELECT id_mural FROM mural_grupo INNER JOIN membro ON membro.id_grupo = mural_grupo.id_grupo AND membro.id_usuario='$user_logado' AND membro.status_adm = 1 AND membro.id_grupo='".$linha_grupos_block['id_grupo']."'";

			$conexao_verificaadm_logado = mysqli_query($conecta, $sql_verificaadm_logado) or die (header('Falha ao verificar se o user logado é adm de algum grupo do usuário bloqueado'));
			if(mysqli_num_rows($conexao_verificaadm_logado)>0){
				while($linha_mural_grupo = mysqli_fetch_assoc($conexao_verificaadm_logado)){
					echo "To verificando o bloqueio com o grupo: ".$linha_grupos_block['id_grupo']."<br>";
					$id_grupo = $linha_grupos_block['id_grupo'];
					//Efetua bloqueio
					$sql_block_user = "UPDATE membro SET status_participacao = 4, autor_block='".$user_logado."' WHERE id_usuario='$user_bloqueado' AND status_participacao=1 AND id_grupo = '$id_grupo'";
					$conexao_block_user = mysqli_query($conecta, $sql_block_user) or die ("Erro ao bloquear usuário do grupo");
					//Limpeza mural do grupo
					$id_mural_grupo = $linha_mural_grupo['id_mural'];
					echo "Mural selecionado: ".$id_mural_grupo."<br>";
					//Limpando mural.
					$sql_seleciona_pub = "SELECT id_publicacao FROM publicacao WHERE id_mural='$id_mural_grupo' AND id_autor = '$user_bloqueado'";
					$conexao_seleciona_pub = mysqli_query($conecta, $sql_seleciona_pub) or die (header('Falha ao selecionar publicações'));

					//Limpa post do que bloqueou.
					if(mysqli_num_rows($conexao_seleciona_pub) > 0){
						while ($linha_pub = mysqli_fetch_assoc($conexao_seleciona_pub)) {
							$sql_seleciona_pub_coment = "SELECT id_comentario FROM comentario WHERE id_publicacao='".$linha_pub['id_publicacao']."'";
							$conexao_seleciona_pub_coment = mysqli_query($conecta, $sql_seleciona_pub_coment) or die (header('Falha ao selecionar comentario da publicação'));
							if(mysqli_num_rows($conexao_seleciona_pub_coment)>0){
								while ($linha_pub_coment = mysqli_fetch_assoc($conexao_seleciona_pub_coment)) {
									$sql_delete_resp = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha_pub_coment['id_comentario']."'";
									$conexao_delete_resp = mysqli_query($conecta, $sql_delete_resp) or die (header('Falha ao deletar respostas'));
									$sql_delete_coment = "DELETE FROM comentario WHERE comentario.id_comentario='".$linha_pub_coment['id_comentario']."'";
									$conexao_delete_coment = mysqli_query($conecta, $sql_delete_coment) or die (header('Falha ao deletar comentario'));
								}
							}
							$sql_delete_pub = "DELETE FROM publicacao WHERE publicacao.id_publicacao='".$linha_pub['id_publicacao']."'";
							$conexao_delete_pub = mysqli_query($conecta, $sql_delete_pub) or die (header('Falha ao deletar publicacao'));
						}
					}
					$sql_seleciona_coment = "SELECT id_comentario FROM comentario INNER JOIN publicacao ON comentario.id_publicacao = publicacao.id_publicacao AND publicacao.id_mural = '$id_mural_grupo' AND comentario.id_autor = '$user_bloqueado'";
					$conexao_seleciona_coment = mysqli_query($conecta, $sql_seleciona_coment) or die (header('Falha ao selecionar comentários'));
					//Limpa comentario do que bloqueou.
					if(mysqli_num_rows($conexao_seleciona_coment) > 0){
						while ($linha_coment = mysqli_fetch_assoc($conexao_seleciona_coment)) {
							$sql_delete_coment_resp = "DELETE FROM resposta WHERE resposta.id_comentario='".$linha_coment['id_comentario']."'";
							$conexao_delete_coment_resp = mysqli_query($conecta, $sql_delete_coment_resp) or die (header('Falha ao deletar respostas'));
							$sql_delete_coment = "DELETE FROM comentario WHERE comentario.id_comentario='".$linha_coment['id_comentario']."'";
							$conexao_delete_coment = mysqli_query($conecta, $sql_delete_coment) or die (header('Falha ao deletar comentario'));	
						}	
					}
					$sql_seleciona_resposta = "SELECT id_resposta FROM resposta INNER JOIN comentario ON resposta.id_comentario =comentario.id_comentario AND resposta.id_autor='$user_bloqueado'INNER JOIN publicacao ON comentario.id_publicacao = publicacao.id_publicacao	AND publicacao.id_mural = '$id_mural_grupo'";
					$conexao_seleciona_resposta = mysqli_query($conecta, $sql_seleciona_resposta) or die (header('Falha ao selecionar resposta'));
					//Limpar respostas do que bloqueou
					if(mysqli_num_rows($conexao_seleciona_resposta) > 0){
						while ($linha_resposta = mysqli_fetch_assoc($conexao_seleciona_resposta)) {
							$sql_delete_resposta = "DELETE FROM resposta WHERE resposta.id_resposta='".$linha_resposta['id_resposta']."'";
							$conexao_delete_resposta = mysqli_query($conecta, $sql_delete_resposta) or die (header('Falha ao deletar respostas'));	
						}	
					}
					

				}
			}
		}


		header('location:profilefak.php?user='.$user_logado.'');
	}
	if(isset($_GET["unblock"])){
		$user_desbloq = $_GET['unblock'];
		$sql_desbloq1 = "DELETE FROM Amizade WHERE Amizade.id_usuario1='$user_desbloq' AND Amizade.id_usuario2 = '$user_logado' AND status_amizade=3";
		$sql_desbloq2 = "DELETE FROM Amizade WHERE Amizade.id_usuario2='$user_desbloq' AND Amizade.id_usuario1 = '$user_logado' AND status_amizade=3";
		$conexao_desbloq1 = mysqli_query($conecta, $sql_desbloq1) or die ("Erro ao desbloquear usuario");
		$conexao_desbloq2 = mysqli_query($conecta, $sql_desbloq2) or die ("Erro ao desbloquear usuario");

		$sql_remove1_block4 = "DELETE FROM membro WHERE membro.id_usuario='$user_desbloq' AND membro.autor_block = '$user_logado' AND status_participacao=4";
		$conexao_remove1_block4  = mysqli_query($conecta, $sql_remove1_block4 ) or die (header('Falha ao remover bloqueios'));
		$sql_remove2_block4 = "DELETE FROM membro WHERE membro.autor_block='$user_desbloq' AND membro.id_usuario = '$user_logado' AND status_participacao=4";
		$conexao_remove2_block4  = mysqli_query($conecta, $sql_remove2_block4 ) or die (header('Falha ao remover bloqueios'));

		header('location:profilefak.php?user='.$user_logado.'');
	}
	if(isset($_GET['id_mural_user'])){

	}
	/*
	if(isset($_GET["user_avaliado"])){
		$user_avaliado = $_GET['user_avaliado'];
		$sql_verifica_bloqueio1 = "SELECT id_usuario1 FROM Amizade WHERE status_amizade=3 AND id_usuario2='$user_logado'";
		$sql_verifica_bloqueio2 = "SELECT id_usuario2 FROM Amizade WHERE status_amizade=3 AND id_usuario1='$user_logado'";
		$conexao_verifica_bloqueio1 = mysqli_query($conecta, $sql_verifica_bloqueio1) or die ("Erro na verificação do bloqueio.");
		$conexao_verifica_bloqueio2 = mysqli_query($conecta, $sql_verifica_bloqueio2) or die ("Erro na verificação do bloqueio");

		$aval_bloqueio = false;
		if(mysqli_num_rows ($conexao_verifica_bloqueio1) > 0){
			while ($linha = mysqli_fetch_assoc($conexao_verifica_bloqueio1)) {
				if($linha['id_usuario1'] == $user_avaliado){
					$aval_bloqueio = true;
				}
			}
		}
		if(mysqli_num_rows ($conexao_verifica_bloqueio2) > 0){
			while ($linha = mysqli_fetch_assoc($conexao_verifica_bloqueio2)) {
				if($linha['id_usuario2'] == $user_avaliado){
					$aval_bloqueio = true;
				}
			}
		}
		if($aval_bloqueio){
			header('location:profilefak.php?user='.$user_logado.'&block=true');
		}else{
			header('location:profilefak.php?user='.$user_avaliado.'&block=false');			
		}


	}
	*/
?>