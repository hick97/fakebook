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
<?PHP
	//Conexão BD
	$conecta =  mysqli_connect("localhost","root","","fakebook");
	if (!$conecta) {
	    echo "Error: Unable to connect to MySQL." . PHP_EOL;
	    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	    exit;
	}
 	//Dados coletados a partir do usuário logado.
 	//ID da página de perfil:
	$grupo_visitado = $_GET['group'];
	//ID do usuário logado:
	$user_logado = $_SESSION['id_usuario'];

	//SQL para consulta no BD
	//Selecionando dados do usuário logado e do usuário visitado:
	$sql_dados_logado = "SELECT * FROM Usuario WHERE id_usuario = '$user_logado'";
	$sql_dados_grupo = "SELECT * FROM grupo WHERE id_grupo = '$grupo_visitado'";
	//Selecionando usuários do sistema
	$sql_usuarios = "SELECT id_usuario,nome_usuario,foto_usuario FROM Usuario";
	//Seleciona Membros do grupo
	$sql_membro ="SELECT * FROM usuario INNER JOIN membro ON membro.id_usuario = usuario.id_usuario AND membro.id_grupo = '$grupo_visitado' AND membro.status_participacao=1";
	//Seleciona amigos do usuário logado
	$sql_amigos1 = "SELECT id_usuario, nome_usuario, foto_usuario FROM Usuario INNER JOIN Amizade ON Amizade.id_usuario2 = usuario.id_usuario AND Amizade.status_amizade=1 AND Amizade.id_usuario1='$user_logado'";
	$sql_amigos2 = "SELECT id_usuario, nome_usuario, foto_usuario FROM Usuario INNER JOIN Amizade ON Amizade.id_usuario1 = usuario.id_usuario AND Amizade.status_amizade=1 AND Amizade.id_usuario2='$user_logado'";

	//Coleta dos pedidos de amizade pendentes		
	$sql_pendentes = "SELECT autor_request, id_usuario1 FROM Amizade WHERE id_usuario2='$user_logado' AND status_amizade=2";
	//Verifica participação no grupo
	$sql_participa_grupo = "SELECT * FROM membro WHERE id_usuario='$user_logado' AND id_grupo='$grupo_visitado'";
    $sql_sou_adm = "SELECT id_grupo FROM membro WHERE id_usuario = '$user_logado' AND status_adm = '1'";
    $sql_sou_adm_visitado = "SELECT * FROM membro WHERE id_usuario = '$user_logado' AND status_adm = '1' AND id_grupo='$grupo_visitado'";
    $sql_block_users = "SELECT * FROM usuario INNER JOIN membro ON membro.id_usuario = usuario.id_usuario AND membro.id_grupo = '$grupo_visitado' AND membro.status_participacao=3";

	//Relacionando a query ao BD
	$conexao_dados_grupo = mysqli_query($conecta, $sql_dados_grupo) or die ("Erro na seleção dos dados do grupo.");
	$conexao_dados_logado = mysqli_query($conecta, $sql_dados_logado) or die ("Erro na seleção dos dados do usuario logado.");
	$conexao_membros = mysqli_query($conecta, $sql_membro) or die ("Erro na seleção dos membros.");
	$conexao_participa_grupo = mysqli_query($conecta, $sql_participa_grupo) or die ("Erro na verificação da participação do grupo.");
	$conexao_usuarios = mysqli_query($conecta, $sql_usuarios) or die ("Erro na seleção dos usuarios do sistema.");
	$conexao_pendentes = mysqli_query($conecta, $sql_pendentes) or die ("Erro na seleção dos pedidos de amizade.");
	$conexao_sou_adm = mysqli_query($conecta, $sql_sou_adm) or die ("Erro na seleção dos grupos em que o user logado é adm.");
	$conexao_block_users = mysqli_query($conecta, $sql_block_users) or die ("Erro na seleção dos usuários do grupo visitado.");
	$conexao_sou_adm_visitado = mysqli_query($conecta, $sql_sou_adm_visitado) or die ("Erro na verificação se o user logado é adm do grupo visitado.");
	$conexao_amigos1 = mysqli_query($conecta, $sql_amigos1) or die ("Erro na seleção dos Amigos.");
	$conexao_amigos2 = mysqli_query($conecta, $sql_amigos2) or die ("Erro na seleção dos Amigos.");
	
 
	//Extração de alguns dados, como array associativo ou como uma matriz
	$dados_grupo = mysqli_fetch_assoc($conexao_dados_grupo);
	$dados_user_logado = mysqli_fetch_assoc($conexao_dados_logado);
	$dados_amg1 = mysqli_fetch_all($conexao_amigos1);
	$dados_amg2 = mysqli_fetch_all($conexao_amigos2);

?>
<?php 
	//Postagem
	//Seleciona postagens do usuário visitado
	$sql_mural_visitado = "SELECT id_mural FROM mural_grupo WHERE id_grupo='$grupo_visitado'";
	$conexao_mural_visitado = mysqli_query($conecta, $sql_mural_visitado) or die (header('Falha ao capturar mural'));
	$index_mural_visitado = mysqli_fetch_assoc($conexao_mural_visitado);
	//Seleciona publicações de acordo com o ID do mural
	$sql_seleciona_post = "SELECT * FROM publicacao WHERE id_mural='".$index_mural_visitado['id_mural']."' ORDER BY id_publicacao DESC";
	$conexao_seleciona_post = mysqli_query($conecta, $sql_seleciona_post) or die (header('Falha ao capturar publicações'));
	$sql_seleciona_mural_logado = "SELECT id_mural FROM mural_usuario WHERE id_usuario='$user_logado'";
	$conexao_seleciona_mural_logado = mysqli_query($conecta, $sql_seleciona_mural_logado) or die (header('Falha ao selecionar mural do usuario logado'));
	$id_mural_user_logado = mysqli_fetch_assoc($conexao_seleciona_mural_logado);
?>
<?php 
	$sql_selecio_allgroups = "SELECT * FROM grupo";
	$sql_seleciona_grupos_logado = "SELECT * FROM grupo INNER JOIN membro ON membro.id_grupo = grupo.id_grupo AND membro.id_usuario = '$user_logado'";

	$conexao_seleciona_grupos_logado  = mysqli_query($conecta, $sql_seleciona_grupos_logado ) or die (header('Falha ao selecionar grupos do usuário logado'));
	$conexao_selecio_allgroups  = mysqli_query($conecta, $sql_selecio_allgroups ) or die (header('Falha ao selecionar todos os grupos do sistema'));
	$grupos_logado = mysqli_fetch_all($conexao_seleciona_grupos_logado);
	$participa_grupo = mysqli_fetch_assoc($conexao_participa_grupo);
	$sou_adm = mysqli_fetch_all($conexao_sou_adm);

	$sou_adm_visitado = false;
	if(mysqli_num_rows($conexao_sou_adm_visitado)>0){
		$sou_adm_visitado=true;
	}
?>
<?php 

	//Verificando se os envolvidos nos bloqueios ainda são administradores dos seus respectivos grupos:
	$sql_block4_membro = "SELECT * FROM membro WHERE status_participacao=4";
	$conexao_block4_membro  = mysqli_query($conecta, $sql_block4_membro ) or die (header('Falha ao selecionar bloqueios'));
	if(mysqli_num_rows($conexao_block4_membro)>0){
		while ($linha_block4 = mysqli_fetch_assoc($conexao_block4_membro)) {
			$id_grupo_block4 = $linha_block4['id_grupo'];
			$id_usuario_block4 = $linha_block4['id_usuario'];
			$id_autor_block4 = $linha_block4['autor_block'];

			$sou_adm_block4 = "SELECT * FROM membro WHERE id_usuario = '$id_autor_block4' AND status_adm = '1' AND id_grupo='$id_grupo_block4'";
			$conexao_adm_block4  = mysqli_query($conecta, $sou_adm_block4 ) or die (header('Falha ao selecionar bloqueios'));
			if(mysqli_num_rows($conexao_adm_block4)>0){
				;
			}else{
				$sql_remove_block4 = "DELETE FROM membro WHERE membro.id_usuario='$id_usuario_block4' AND membro.id_grupo = '$id_grupo_block4' AND status_participacao=4";
				$conexao_remove_block4  = mysqli_query($conecta, $sql_remove_block4 ) or die (header('Falha ao remover bloqueios'));
			}
		}
	}
	
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title> Fakebook | Grupos </title>
	<!-- Tags meta importantes para o site -->
	<!-- width=device-width: largura da minha pagina será igual a largura do dispositivo. initial-scale: Nivel inicial do zoom = 1.-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<meta name="description" content="Clone of facebook.">
	<meta name="keywords" content="Sites, Social, facebook, fakebook">
	<meta name="robots" content="index, follow">
	<meta name="author" content="Henrique Augusto, Lucca peregrino">
	<link rel="stylesheet"  href="statics/css/style.css">
	<!--<link href="css/bootstrap.min.css" rel="stylesheet">-->
	<!-- Link para utilizar pacote de ícones aleatórios (Fonts awesome): -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
	<link rel="stylesheet"  href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/css?family=Antic+Didone" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<!-- Fonte - Google fonts (Lato): -->
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Merienda:400,700" rel="stylesheet"> 
	<!-- Browser Icon -->
	<link rel="icon" href="statics/img/logofb.png">	
</head>
<body>
	<header>
	<!-- Cabeçalho: -->	
	<nav class="nav-profile">
		<div class="search-section">
			<img src="statics/img/logofb.png">
			<div class="search">
				<input type="text" name="search" placeholder="Pesquisar"><button class="btn btn-info"><i class="fas fa-search"></i></button>
			</div>
		</div>
		<div class="session-user">
			<?php
				echo "<img src=".$dados_user_logado['foto_usuario']."><span>".$dados_user_logado['nome_usuario']."<span>";
			?>
		</div>
		<div class="initial-page">
			<a <?php echo "href='profilefak.php?user=".$user_logado."'" ?>><span>Pagina inicial</span></a>
		</div>
		<div class="friend-request">
			<span class="dropdown-toggle" type="button" data-toggle="dropdown"><i class="fas fa-user-friends fa-lg"></i>
			<?php
				if(mysqli_num_rows ($conexao_pendentes) > 0){
			 		echo "<div class='notification-count'><i class='fas fa-plus'></i></div>"; 
				}
			 ?></span>
			<ul class="option-request dropdown-menu">
				<div class="header-request">
					<span>Solicitações</span>
				</div>
			   	<?php
			   		if(mysqli_num_rows ($conexao_pendentes) > 0){
				   		while($linha = mysqli_fetch_assoc($conexao_pendentes)){ 
				   			echo "<li><span>".$linha['autor_request']."</span><a href='friendship.php?aceitou=".$linha['id_usuario1']."'><button class='btn btn-primary btn-sm'>Confirmar</button></a><a href='friendship.php?recusou=".$linha['id_usuario1']."'><button class='btn btn-default btn-sm'>Excluir</button></a></li>";
				   		}
				   	}
			   		if(mysqli_num_rows ($conexao_sou_adm) > 0){
						foreach ($sou_adm as $value) {
							$id_grupo = $value[0];
							$sql_grupo_adm = "SELECT nome_grupo FROM grupo WHERE id_grupo = $id_grupo";
							$conexao_grupo_adm  = mysqli_query($conecta, $sql_grupo_adm ) or die (header('Falha ao selecionar nome dos grupos que sou adm'));
							$nome_grupo_adm = mysqli_fetch_assoc($conexao_grupo_adm);
							$sql_user_pendente_grupo = "SELECT * FROM usuario INNER JOIN membro ON membro.status_participacao = 2 AND membro.id_usuario = usuario.id_usuario AND membro.id_grupo= '$id_grupo'";
							$conexao_user_pendente_grupo  = mysqli_query($conecta, $sql_user_pendente_grupo ) or die (header('Falha ao selecionar usuários pedentes'));
					   		while($linha = mysqli_fetch_assoc($conexao_user_pendente_grupo)){ 
					   			echo "<li><span>".$linha['nome_usuario']." <br><small>Deseja entrar em</small><br> ".$nome_grupo_adm['nome_grupo']."</span><a href='groups.php?aceitou=".$linha['id_usuario']."&group=".$id_grupo."'><button class='btn btn-primary btn-sm'>Confirmar</button></a><a href='groups.php?recusou=".$linha['id_usuario']."&group=".$id_grupo."'><button class='btn btn-default btn-sm'>Excluir</button></a></li>";
					   		}
						}
					}
			   	?>
			</ul>
		</div>
		<div class="log-out">
			<a href="authentication.php?saindo=true"><span>Sair</span></a>
		</div>
	</nav>
	</header>
			
	<!-- Main and Article: -->
	<main class="content-wrapper">
		  <div class="modal fade" id="myModal" role="dialog">
		    <div class="modal-dialog">
		      <!-- Modal content-->
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">&times;</button>
		          <h4 class="modal-title">Insira as informações abaixo:</h4>
		        </div>
				<form enctype="multipart/form-data" action="groups.php" method="POST" id="cadastro_usuario">
			        <div class="modal-body">
			        		<label for="visibilidade_grupo">Nome do grupo</label><br>
							<input type="text" name="nome_grupo" id="nome_grupo" placeholder="Ex.: CI-UFPB" required><br>	
							<label for="visibilidade_grupo">Visibilidade</label><br>
							<select name="visibilidade_grupo" id="visibilidade" required>
								<option value="1">Público</option>
								<option value="2">Privado</option>
							</select><br>
							<label>Imagem do grupo:</label><br><input type="file" name="imagem_grupo" id="imagem_grupo" required><br>
							<label for="visibilidade_grupo">Descrição do grupo:</label><br>
							<textarea name="description-group"></textarea>
			        </div>
			        <div class="modal-footer">
			          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			          <input class="btn pull-left" type="submit" name="form_grupo" value="Criar grupo">
			        </div>
		    	</form>
		      </div>
		      
		    </div>
		  </div>
		<div class="data-user">
			<label><i class="far fa-id-card fa-lg"></i>Apresentação</label><br>
			<?php
				echo "<img src=".$dados_grupo['foto_grupo']."><br><label><i class='fas fa-user-circle'></i>".$dados_grupo['nome_grupo']."</label><br><label>";
				if($dados_grupo['privacidade'] == 2 or $participa_grupo['status_participacao']==1){
					echo "Sobre:<br></label><h4>".$dados_grupo['descricao']."</h4><br>";
				}else{
					echo "Sobre:<br></label><h4>".$dados_grupo['descricao']."</h4><br>";
					echo "<label><i class='fas fa-user-lock'></i></label><a href=''><h4>Privado</h4></a><br>";
				}
			?>
			<button class="btn btn-primary" onclick="window.location= 
			<?php echo "'groups.php?requisita_grupo=".$grupo_visitado."'" ?>" 
			<?php 
				if($participa_grupo['status_participacao']==1 or $participa_grupo['status_participacao']==2 ){
					echo "disabled";
				} 
			?> 
			><i class="fas fa-user-plus"></i>Participar</button><br>			
		</div>
		<?php
		if($dados_grupo['privacidade'] == 2 or $participa_grupo['status_participacao']==1){
		?>
		<form class="input-mural" enctype="multipart/form-data"  action="posts.php" method="POST" id="post_grupo">
			<div class="content-input">
				<?php
					echo "<img src=".$dados_user_logado['foto_usuario'].">";
				?>
				<textarea name="content-post-input" placeholder="O que você está pensando?"></textarea>
				<input type="hidden" name="grupo_visitado" <?php echo "value='$grupo_visitado'"?>>
			</div>
			<div class="footer-input">
				<input type="file" name="imagem_post_grupo" id="imagem_post_grupo">
				<input class="btn btn-primary" type="submit" name="btn-newpost-group" 
				<?php 
					if($participa_grupo['status_participacao']!=1){
						echo "disabled=true";
					}
				?>
				 value="Publicar">
			</div>
		</form>
		<div class="mural-posts">
		<?php 
			$count_id_post = mysqli_num_rows($conexao_seleciona_post);
			if(mysqli_num_rows($conexao_seleciona_post)>0){
				while ($linha = mysqli_fetch_assoc($conexao_seleciona_post)) {
				//Selecionando informações do autor:
					$sql_info_autor = "SELECT * FROM Usuario WHERE id_usuario='".$linha['id_autor']."'";
					$conexao_info_autor = mysqli_query($conecta, $sql_info_autor) or die (header('Falha ao capturar informações do autor da publicação'));
					$dados_info_autor = mysqli_fetch_assoc($conexao_info_autor);
					//verifica bloqueio
					$aval_block_post = false;
					$id_autor_post = $linha['id_autor'];
					$sql_verifica_bloqueio1_post = "SELECT id_usuario1 FROM Amizade WHERE status_amizade=3 AND id_usuario2='$id_autor_post'";
					$sql_verifica_bloqueio2_post = "SELECT id_usuario2 FROM Amizade WHERE status_amizade=3 AND id_usuario1='$id_autor_post'";
					$conexao_verifica_bloqueio1_post = mysqli_query($conecta, $sql_verifica_bloqueio1_post) or die ("Erro na verificação do bloqueio.");
					$conexao_verifica_bloqueio2_post = mysqli_query($conecta, $sql_verifica_bloqueio2_post) or die ("Erro na verificação do bloqueio");
					$block_list1_post = mysqli_fetch_all($conexao_verifica_bloqueio1_post);
					$block_list2_post = mysqli_fetch_all($conexao_verifica_bloqueio2_post);
					if(mysqli_num_rows($conexao_verifica_bloqueio1_post) > 0 or mysqli_num_rows($conexao_verifica_bloqueio2_post) > 0){
						foreach ($block_list1_post as $value) {
							$id_user_block1_post = $value[0];
							if($id_user_block1_post == $dados_user_logado['id_usuario']){
								$aval_block_post = true;
							}
						}
						unset($value);
						foreach ($block_list2_post as $value) {
							$id_user_block2_post = $value[0];
							if($id_user_block2_post == $dados_user_logado['id_usuario']){
								$aval_block_post = true;
							}
						}
						unset($value);
					}
					if (!$aval_block_post) {

				?>
				<div class="myposts">
					<div class="item-post">
						<img class="img-autor-pub" <?php echo "src='".$dados_info_autor['foto_usuario']."'"; ?>>
						<div class="content-post">
						<?php 
							if($dados_info_autor['id_usuario'] == $dados_user_logado['id_usuario'] || $sou_adm_visitado){
						?>
						<a href= <?php echo "'posts.php?count_id_publicacao_grupo=".$count_id_post."&id_mural=".$linha['id_mural']."&grupo_visitado=".$dados_grupo['id_grupo']."'"; ?>>
							<i class="far fa-trash-alt pull-right"></i>
						</a>
						<?php 
							}
						?>
						<?php

							if(!empty($linha['conteudo'])){
								echo "<p>".$linha['conteudo']."</p>";
							}
							if(!empty($linha['arquivo'])){
								echo "<img src='".$linha['arquivo']."''><br>";
							}
						?>
						</div>
					</div>
					<form class="coment-post" action="posts.php" method="POST">
						<input type="text" name="text-coment" placeholder="Insira seu comentário" required autocomplete="off">
						<input type="hidden" name="id_publicacao" <?php echo "value=".$linha['id_publicacao'].""?>>
						<input type="hidden" name="grupo_visitado" <?php echo "value=".$dados_grupo['id_grupo'].""?>>
						<button class="btn btn-primary" type="submit" 
						<?php 
							if($participa_grupo['status_participacao']!=1){
								echo "disabled=true";
							}
						?>
						name="btn-comenta-group"><i class="far fa-comment fa-lg"></i>Comentar</button>
					</form>
					<?php 
						//Seleciona comentario de acordo com o ID da publicação
						$sql_seleciona_coment = "SELECT * FROM comentario WHERE id_publicacao='".$linha['id_publicacao']."'";
						$conexao_seleciona_coment = mysqli_query($conecta, $sql_seleciona_coment) or die (header('Falha ao capturar comentários'));
						$count_id_comentario = 0;
						if(mysqli_num_rows($conexao_seleciona_coment)>0){
							while ($linha_coment = mysqli_fetch_assoc($conexao_seleciona_coment)) {
							//Selecionando informações do autor-comentario:
								$sql_coment_autor = "SELECT * FROM Usuario WHERE id_usuario='".$linha_coment['id_autor']."'";
								$conexao_coment_autor = mysqli_query($conecta, $sql_coment_autor) or die (header('Falha ao capturar informações do autor do comentario'));
								$dados_coment_autor = mysqli_fetch_assoc($conexao_coment_autor);
								//verifica bloqueio
								$aval_block_coment = false;
								$id_autor_coment = $linha_coment['id_autor'];
								$sql_verifica_bloqueio1_coment = "SELECT id_usuario1 FROM Amizade WHERE status_amizade=3 AND id_usuario2='$id_autor_coment'";
								$sql_verifica_bloqueio2_coment = "SELECT id_usuario2 FROM Amizade WHERE status_amizade=3 AND id_usuario1='$id_autor_coment'";
								$conexao_verifica_bloqueio1_coment = mysqli_query($conecta, $sql_verifica_bloqueio1_coment) or die ("Erro na verificação do bloqueio.");
								$conexao_verifica_bloqueio2_coment = mysqli_query($conecta, $sql_verifica_bloqueio2_coment) or die ("Erro na verificação do bloqueio");
								$block_list1_coment = mysqli_fetch_all($conexao_verifica_bloqueio1_coment);
								$block_list2_coment = mysqli_fetch_all($conexao_verifica_bloqueio2_coment);

								foreach ($block_list1_coment as $value) {
									$id_user_block1_coment = $value[0];
									if($id_user_block1_coment == $dados_user_logado['id_usuario']){
										$aval_block_coment = true;
									}
								}
								unset($value);
								foreach ($block_list2_coment as $value) {
									$id_user_block2_coment = $value[0];
									if($id_user_block2_coment == $dados_user_logado['id_usuario']){
										$aval_block_coment = true;
									}
								}
								unset($value);
								if (!$aval_block_coment) {
								
					?>
						<div class="mycoments">
							<div class="item-coment">
								<div class="margin-coment">
									<span><?php echo "".$dados_coment_autor['nome_usuario'].""; ?><small>(Comentário)</small></span>
								</div>
								
								<div class="content-coment">
									<?php 
										if($dados_coment_autor['id_usuario'] == $dados_user_logado['id_usuario'] || $sou_adm_visitado){
									?>
									<a href= <?php echo "'posts.php?count_id_comentario_grupo=".$count_id_comentario."&id_publicacao=".$linha['id_publicacao']."&grupo_visitado=".$dados_grupo['id_grupo']."'"; ?>>
										<i class="far fa-trash-alt pull-right"></i>
									</a>
									<?php 
										}
									?>
									<p><?php echo "".$linha_coment['conteudo'].""; ?></p>
								</div>
								<form class="coment-post" action="posts.php" method="POST">
									<input type="text" name="text-resposta" placeholder="Insira sua resposta" autocomplete="off" required>
									<input type="hidden" name="id_comentario" <?php echo "value=".$linha_coment['id_comentario'].""?>>
									<input type="hidden" name="grupo_visitado" <?php echo "value=".$dados_grupo['id_grupo'].""?>>
									<button class="btn btn-primary" type="submit" 
									<?php 
										if($participa_grupo['status_participacao']!=1){
											echo "disabled=true";
										}
									?>
									name="btn-resposta-group"><i class="far fa-comment fa-lg"></i>Resposta</button>
								</form>
								<?php 
									//Seleciona respostas de acordo com o ID do comentario
									$sql_seleciona_resposta = "SELECT * FROM resposta WHERE id_comentario='".$linha_coment['id_comentario']."'";
									$conexao_seleciona_resposta = mysqli_query($conecta, $sql_seleciona_resposta) or die (header('Falha ao capturar respostas'));
									$count_id_resposta = 0;
									
									if(mysqli_num_rows($conexao_seleciona_resposta)>0){
										while ($linha_resposta = mysqli_fetch_assoc($conexao_seleciona_resposta)) {
										//Selecionando informações do autor-comentario:
											$sql_resposta_autor = "SELECT * FROM Usuario WHERE id_usuario='".$linha_resposta['id_autor']."'";
											$conexao_resposta_autor = mysqli_query($conecta, $sql_resposta_autor) or die (header('Falha ao capturar informações do autor da resposta'));
											$dados_resposta_autor = mysqli_fetch_assoc($conexao_resposta_autor);
											//Verifica bloqueio
											$aval_block_resp = false;
											$id_autor_resp = $linha_resposta['id_autor'];
											$sql_verifica_bloqueio1_resp = "SELECT id_usuario1 FROM Amizade WHERE status_amizade=3 AND id_usuario2='$id_autor_resp'";
											$sql_verifica_bloqueio2_resp = "SELECT id_usuario2 FROM Amizade WHERE status_amizade=3 AND id_usuario1='$id_autor_resp'";
											$conexao_verifica_bloqueio1_resp = mysqli_query($conecta, $sql_verifica_bloqueio1_resp) or die ("Erro na verificação do bloqueio.");
											$conexao_verifica_bloqueio2_resp = mysqli_query($conecta, $sql_verifica_bloqueio2_resp) or die ("Erro na verificação do bloqueio");
											$block_list1_resp = mysqli_fetch_all($conexao_verifica_bloqueio1_resp);
											$block_list2_resp = mysqli_fetch_all($conexao_verifica_bloqueio2_resp);

											foreach ($block_list1_resp as $value) {
												$id_user_block1_resp = $value[0];
												if($id_user_block1_resp == $dados_user_logado['id_usuario']){
													$aval_block_resp = true;
												}
											}
											unset($value);
											foreach ($block_list2_resp as $value) {
												$id_user_block2_resp = $value[0];
												if($id_user_block2_resp == $dados_user_logado['id_usuario']){
													$aval_block_resp = true;
												}
											}
											unset($value);
											if (!$aval_block_resp) {
								?>
									<div class="resposta-comentario">
										<div class="margin-resposta">
											<span><?php echo "".$dados_resposta_autor['nome_usuario'].""; ?><small>(Resposta)</small></span>
										</div>									
											<div class="content-resposta">
												<?php 
													if($dados_resposta_autor['id_usuario'] == $dados_user_logado['id_usuario'] || $sou_adm_visitado){
												?>
												<a href= <?php echo "'posts.php?count_id_resposta_grupo=".$count_id_resposta."&id_comentario=".$linha_coment['id_comentario']."&grupo_visitado=".$dados_grupo['id_grupo']."'"; ?>>
													<i class="far fa-trash-alt pull-right"></i>
												</a>
												<?php 
													}
												?>
												<p><?php echo "".$linha_resposta['conteudo'].""; ?></p>
											</div>
									</div>
								<?php 
											$count_id_resposta += 1;
											  }
										}
									}
								?>
							</div>
						</div>
					<?php
							$count_id_comentario += 1;
								} 
							}
						}
					?>
				</div> <!-- Fim mypost-->				
				<?php
					$count_id_post -= 1;
						}
				}
			}
		?>
		</div>
		<?php 
			}
		?>
		<div class="panel-fixed-r">
			<h3><i class="fas fa-search"></i>SUGESTÕES</h3>
			<div class="users-system">
				<h4><i class="fas fa-user-friends fa-lg"></i>USUÁRIOS<i class="fas fa-chevron-circle-up pull-right"></i></h4>
				<ul class="users-list" id='toggle-user'>
					<?php
					//Verifica se o usuário a ser listado está bloqueado pelo usuário logado.
						while($linha = mysqli_fetch_assoc($conexao_usuarios)){
							$id_user_logado = $dados_user_logado['id_usuario'];
							$id_user_verficado = $linha['id_usuario'];
							$sql_tablock1 = "SELECT * FROM Amizade WHERE status_amizade=3 AND id_usuario1='$id_user_logado' AND id_usuario2='$id_user_verficado' AND autor_request!='$id_user_logado'";
							$sql_tablock2 = "SELECT * FROM Amizade WHERE status_amizade=3 AND id_usuario2='$id_user_logado' AND id_usuario1='$id_user_verficado' AND autor_request!='$id_user_logado'";
							$sql_tablock1_autor = "SELECT * FROM Amizade WHERE status_amizade=3 AND id_usuario1='$id_user_logado' AND id_usuario2='$id_user_verficado' AND autor_request='$id_user_logado'";
							$sql_tablock2_autor = "SELECT * FROM Amizade WHERE status_amizade=3 AND id_usuario2='$id_user_logado' AND id_usuario1='$id_user_verficado' AND autor_request='$id_user_logado'";
							$conexao_tablock1 = mysqli_query($conecta, $sql_tablock1) or die ("Erro na verificação do bloqueio.");
							$conexao_tablock2 = mysqli_query($conecta, $sql_tablock2) or die ("Erro na verificação do bloqueio");
							$conexao_tablock1_autor = mysqli_query($conecta, $sql_tablock1_autor) or die ("Erro na verificação do bloqueio como autor.");
							$conexao_tablock2_autor = mysqli_query($conecta, $sql_tablock2_autor) or die ("Erro na verificação do bloqueio como autor");	
							if(mysqli_num_rows ($conexao_tablock1) > 0 or mysqli_num_rows ($conexao_tablock2) > 0){
								;
							}else
							if(mysqli_num_rows ($conexao_tablock1_autor) > 0 or mysqli_num_rows ($conexao_tablock2_autor) > 0){
								echo "<li><a><img src='".$linha['foto_usuario']."'><h4>".$linha['nome_usuario']."</h4></a><a href=".'friendship.php?unblock='.$linha['id_usuario']."><i class='fas fa-ban'></i></a></li>";	
							}else{
								echo "<li><a href=".'profilefak.php?user='.$linha['id_usuario']."><img src='".$linha['foto_usuario']."'><h4>".$linha['nome_usuario']."</h4></a></li>";	
							}

						}
					?>
				</ul>
			</div>
			<div class="groups-system">
				<h4><i class="fas fa-users fa-lg"></i>GRUPOS FAKE</h4>
				<ul class="groups-list">
					<?php 
						while($linha = mysqli_fetch_assoc($conexao_selecio_allgroups)){
							$grupo_verificado = $linha['id_grupo'];
							$sql_ver_block = "SELECT * FROM membro WHERE id_grupo='$grupo_verificado' AND id_usuario='$user_logado' AND (status_participacao=3 OR status_participacao=4)";
							$conexao_ver_block = mysqli_query($conecta, $sql_ver_block) or die ("Erro na verificação do bloqueio com o grupo");
							if(mysqli_num_rows($conexao_ver_block)>0){
								;
							}else{
								echo "<li><a href=".'groupsfak.php?group='.$linha['id_grupo']."><img src='".$linha['foto_grupo']."'><h4>".$linha['nome_grupo']."</h4></a></li>";
							}
						}

					?>
				</ul>
			</div>
		</div>

		<div class="panel-fixed-l">
			<div class="users-system">
				<div class="create-group">
					<button class="btn btn-default" data-toggle="modal" data-target="#myModal"><i class="fas fa-plus-circle"></i>Criar Grupo</button>
				</div>
				<h4><i class="fas fa-user-friends fa-lg"></i>MEMBROS</h4>
				<?php
				if($dados_grupo['privacidade'] == 2 or $participa_grupo['status_participacao']==1){
				?>
				<ul class="users-list">
					<?php
						//Lista mural
						while ($linha_membros = mysqli_fetch_assoc($conexao_membros)) {
							//Seleciona amigos do usuário logado
							$id_membro_amg = $linha_membros['id_usuario'];
							$sql_ehamg1 = "SELECT * FROM Usuario INNER JOIN Amizade ON Amizade.id_usuario2 = '$id_membro_amg' AND Amizade.status_amizade=1 AND Amizade.id_usuario1='$user_logado'";
							$sql_ehamg2 = "SELECT id_usuario, nome_usuario, foto_usuario FROM Usuario INNER JOIN Amizade ON Amizade.id_usuario1 = '$id_membro_amg' AND Amizade.status_amizade=1 AND Amizade.id_usuario2='$user_logado'";
							$conexao_ehamg1 = mysqli_query($conecta, $sql_ehamg1) or die ("Erro na seleção dos Amigos.");
							$conexao_ehamg2 = mysqli_query($conecta, $sql_ehamg2) or die ("Erro na seleção dos Amigos.");
							//Verifica se membro é adm do grupo visitado
							$sql_membro_ehadm = "SELECT * FROM membro WHERE id_usuario = '$id_membro_amg' AND status_adm = '1' AND id_grupo='$grupo_visitado'";
							$conexao_membro_ehadm = mysqli_query($conecta, $sql_membro_ehadm) or die ("Erro na verficação do adm.");
							if(mysqli_num_rows($conexao_ehamg1) > 0 or mysqli_num_rows($conexao_ehamg2)>0){
								echo "<li><a><img src='".$linha_membros['foto_usuario']."'><i class='fas fa-user-check pull-right fa-sm'></i><h4>".$linha_membros['nome_usuario']."</h4></a><a href=".'profilefak.php?user='.$linha_membros['id_usuario']."></a>";
								if($sou_adm_visitado){
									if(mysqli_num_rows($conexao_membro_ehadm) > 0){
										echo "<br><a class='tornar_adm' href=".'groups.php?remove_adm='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><small>Remover administração</small></a><a href=".'groups.php?remove_user='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-user-minus pull-right fa-sm'></i></a><a href=".'groups.php?block_user='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-user-slash pull-right fa-sm'></i></a></li>";
									}else{
										echo "<br><a class='tornar_adm' href=".'groups.php?user_adm='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><small>Torná-lo administrador</small></a><a href=".'groups.php?remove_user='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-user-minus pull-right fa-sm'></i></a><a href=".'groups.php?block_user='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-user-slash pull-right fa-sm'></i></a></li>";
									}
								}else{
									echo "</li>";
								}
							}else{
								echo "<li><a><img src='".$linha_membros['foto_usuario']."'><h4>".$linha_membros['nome_usuario']."</h4></a><a href=".'profilefak.php?user='.$linha_membros['id_usuario']."></a>";
								if($sou_adm_visitado && $user_logado != $linha_membros['id_usuario']){
									if(mysqli_num_rows($conexao_membro_ehadm) > 0){
										echo "<br><a class='tornar_adm' href=".'groups.php?remove_adm='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><small>Remover administração</small></a><a href=".'groups.php?remove_user='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-user-minus pull-right fa-sm'></i></a><a href=".'groups.php?block_user='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-user-slash pull-right fa-sm'></i></a></li>";
									}else{
										echo "<br><a class='tornar_adm' href=".'groups.php?user_adm='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><small>Torná-lo administrador</small></a><a href=".'groups.php?remove_user='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-user-minus pull-right fa-sm'></i></a><a href=".'groups.php?block_user='.$linha_membros['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-user-slash pull-right fa-sm'></i></a></li>";
									}
								}else{
									echo "</li>";
								}
							}
						}	
					?>
				</ul>
				<?php
					}
				?>
			</div>
			<?php
			if($participa_grupo['status_participacao']==1 && $participa_grupo['status_adm']==1){
			?>
			<div class="users-system">
				<h4></i>BLOQUEADOS</h4>
				<ul class="users-list">
					<?php
						while ($linha_block_users = mysqli_fetch_assoc($conexao_block_users)) {
							echo "<li><a><img src='".$linha_block_users['foto_usuario']."'><a href=".'groups.php?unblock_group='.$linha_block_users['id_usuario']."&group=".$grupo_visitado."><i class='fas fa-redo-alt'></i></a><h4 class='pull-left'>".$linha_block_users['nome_usuario']."</h4></a><a href=".'profilefak.php?user='.$linha_block_users['id_usuario']."></a></li>";
						}	
					?>
				</ul>
			</div>
			<?php
				}
			?>		
		</div>	
	</main>
	<!-- Contacts: -->
	
	<footer class="#">
		
	</footer>
	
	<!-- Events - jQuery: -->
	<script type="text/javascript" src="statics/js/jquery-3.2.0.min.js"></script>
	<script type="text/javascript" src="statics/js/script.js"></script>
</body>
</html>