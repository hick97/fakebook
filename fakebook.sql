-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 01-Nov-2018 às 16:08
-- Versão do servidor: 10.1.35-MariaDB
-- versão do PHP: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fakebook`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `amizade`
--

CREATE TABLE `amizade` (
  `id_usuario1` varchar(255) NOT NULL,
  `id_usuario2` varchar(255) NOT NULL,
  `status_amizade` int(11) NOT NULL,
  `autor_request` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `amizade`
--

INSERT INTO `amizade` (`id_usuario1`, `id_usuario2`, `status_amizade`, `autor_request`) VALUES
('bu@hotmail.com', 'hick_97@hotmail.com', 1, 'Bruna Paiva'),
('hick_97@hotmail.com', 'marcelo@hotmail.com', 1, 'Henrique Augusto'),
('jorge@hotmail.com', 'hick_97@hotmail.com', 1, 'Jorge Peixoto'),
('lucca@hotmail.com', 'bu@hotmail.com', 1, 'Lucca Peregrino'),
('lucca@hotmail.com', 'hick_97@hotmail.com', 3, 'lucca@hotmail.com'),
('lucca@hotmail.com', 'jorge@hotmail.com', 1, 'Lucca Peregrino');

-- --------------------------------------------------------

--
-- Estrutura da tabela `comentario`
--

CREATE TABLE `comentario` (
  `id_comentario` int(11) NOT NULL,
  `id_publicacao` int(11) NOT NULL,
  `id_autor` varchar(255) NOT NULL,
  `conteudo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `comentario`
--

INSERT INTO `comentario` (`id_comentario`, `id_publicacao`, `id_autor`, `conteudo`) VALUES
(40, 39, 'hick_97@hotmail.com', 'ApareÃ§a irmÃ£o hehe'),
(42, 20, 'bu@hotmail.com', 'Todo fitness!'),
(43, 7, 'hick_97@hotmail.com', 'Que dia!');

-- --------------------------------------------------------

--
-- Estrutura da tabela `grupo`
--

CREATE TABLE `grupo` (
  `id_grupo` int(11) NOT NULL,
  `nome_grupo` varchar(255) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `foto_grupo` varchar(255) NOT NULL,
  `privacidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `grupo`
--

INSERT INTO `grupo` (`id_grupo`, `nome_grupo`, `descricao`, `foto_grupo`, `privacidade`) VALUES
(3, 'CI-UFPB', 'Grupo do Centro de InformÃ¡tica da Universidade Federal da ParaÃ­ba.', 'statics/img/586234c28fb91c232b8041fc58b03b92.jpg', 1),
(5, 'Banco de Dados', 'Grupo criado com o intuito de propor discussÃµes Ã  respeito da disciplina de Banco de Dados.', 'statics/img/cb8ebf0a13055cd9595103a11c7c3d43.jpg', 1),
(6, 'Teoria do Universo', 'Grupo criado para pessoas que curtem o universo, planetas e astrofÃ­sica.', 'statics/img/86ed24de1312e0542b2f8a811cf094a9.jpg', 2),
(7, 'DBZ - Brasil', 'Grupo destinado Ã queles que sÃ£o fÃ£s de verdade de Dragon Ball Z!', 'statics/img/6831db20bf1d746d9875cfbe51e26686.jpg', 1),
(8, 'Smart Fit', 'Um grupo feito para toda comunidade Smartfiteira!', 'statics/img/3261c8a7abd33a56ec1c3554b3296f1c.jpg', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `membro`
--

CREATE TABLE `membro` (
  `id_grupo` int(11) NOT NULL,
  `id_usuario` varchar(255) NOT NULL,
  `status_participacao` int(11) NOT NULL,
  `status_adm` int(11) NOT NULL,
  `autor_block` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `membro`
--

INSERT INTO `membro` (`id_grupo`, `id_usuario`, `status_participacao`, `status_adm`, `autor_block`) VALUES
(3, 'hick_97@hotmail.com', 1, 1, ''),
(5, 'hick_97@hotmail.com', 4, 0, 'lucca@hotmail.com'),
(5, 'jorge@hotmail.com', 1, 0, ''),
(5, 'lucca@hotmail.com', 1, 1, ''),
(6, 'hick_97@hotmail.com', 1, 1, ''),
(6, 'lucca@hotmail.com', 4, 0, 'hick_97@hotmail.com'),
(6, 'marcelo@hotmail.com', 1, 0, ''),
(7, 'pati@hotmail.com', 1, 0, ''),
(8, 'bu@hotmail.com', 1, 0, ''),
(8, 'hick_97@hotmail.com', 1, 1, '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mural`
--

CREATE TABLE `mural` (
  `id_mural` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `mural`
--

INSERT INTO `mural` (`id_mural`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12),
(13),
(14),
(15);

-- --------------------------------------------------------

--
-- Estrutura da tabela `mural_grupo`
--

CREATE TABLE `mural_grupo` (
  `id_mural` int(11) NOT NULL,
  `id_grupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `mural_grupo`
--

INSERT INTO `mural_grupo` (`id_mural`, `id_grupo`) VALUES
(10, 3),
(12, 5),
(13, 6),
(14, 7),
(15, 8);

-- --------------------------------------------------------

--
-- Estrutura da tabela `mural_usuario`
--

CREATE TABLE `mural_usuario` (
  `id_mural` int(11) NOT NULL,
  `id_usuario` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `mural_usuario`
--

INSERT INTO `mural_usuario` (`id_mural`, `id_usuario`) VALUES
(1, 'hick_97@hotmail.com'),
(2, 'ab@hotmail.com'),
(3, 'jorge@hotmail.com'),
(4, 'pati@hotmail.com'),
(5, 'lucca@hotmail.com'),
(6, 'bu@hotmail.com'),
(7, 'marcelo@hotmail.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `publicacao`
--

CREATE TABLE `publicacao` (
  `id_publicacao` int(11) NOT NULL,
  `id_mural` int(11) NOT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `conteudo` varchar(255) DEFAULT NULL,
  `id_autor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `publicacao`
--

INSERT INTO `publicacao` (`id_publicacao`, `id_mural`, `arquivo`, `conteudo`, `id_autor`) VALUES
(7, 6, 'statics/img/a91d2449b05994e3f0c7df9a0722f51e.jpg', 'Que dia maravilhoso!', 'bu@hotmail.com'),
(18, 10, '', 'As fÃ©rias se aproximam !!', 'hick_97@hotmail.com'),
(20, 15, 'statics/img/e68206201a331cf6a082517a910f1db0.jpg', 'Corridinha de todo dia :D', 'hick_97@hotmail.com'),
(36, 2, '', 'Bom dia !', 'ab@hotmail.com'),
(38, 1, 'statics/img/2d5b7e56e874b28889c7e375cf1ff5c0.jpg', 'Eu adoro essa paisagem...', 'hick_97@hotmail.com'),
(39, 3, 'statics/img/2075a639eb3077b72ddb9b1c0c615eff.jpg', 'Muito grato pelo dia de hoje!', 'jorge@hotmail.com'),
(42, 13, 'statics/img/17797254d618f608f1b42ab0f4492697.jpg', 'Um dos planetas mais misteriosos que eu conheÃ§o!', 'hick_97@hotmail.com'),
(43, 6, '', 'Oi bruna!', 'hick_97@hotmail.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `resposta`
--

CREATE TABLE `resposta` (
  `id_resposta` int(11) NOT NULL,
  `id_comentario` int(11) NOT NULL,
  `id_autor` varchar(255) NOT NULL,
  `conteudo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` varchar(255) NOT NULL,
  `nome_usuario` varchar(255) NOT NULL,
  `visibilidade` int(11) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `foto_usuario` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `visibilidade`, `cidade`, `foto_usuario`, `senha`) VALUES
('ab@hotmail.com', 'Abigobaldo Farias', 3, 'JoÃ£o Pessoa', 'statics/img/ccc0b4b9ccddbff4105d7277c7047926.jpg', '1234'),
('bu@hotmail.com', 'Bruna Paiva', 2, 'JoÃ£o Pessoa', 'statics/img/d30ac16065f7b0243b23e55b720cd6ef.jpg', '1234'),
('hick_97@hotmail.com', 'Henrique Augusto', 2, 'JoÃ£o Pessoa', 'statics/img/e850c8acaffc547f753c9f021026991b.jpg', '1234'),
('jorge@hotmail.com', 'Jorge Peixoto', 1, 'JoÃ£o Pessoa', 'statics/img/ecfd4e8ce0d67e166a4453faca81df41.jpg', '1234'),
('lucca@hotmail.com', 'Lucca Peregrino', 2, 'JoÃ£o Pessoa', 'statics/img/6ef55c85c54abe82fcdf1bd8fa339585.jpg', '1234'),
('marcelo@hotmail.com', 'Marcelo Marques', 1, 'SÃ£o Paulo', 'statics/img/d5dca45b39d8e32aaa0448a2f8441c7d.jpg', '1234'),
('pati@hotmail.com', 'Patricia Silva', 1, 'JoÃ£o Pessoa', 'statics/img/758b8904d3439f14055900c09793084e.jpg', '1234');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amizade`
--
ALTER TABLE `amizade`
  ADD PRIMARY KEY (`id_usuario1`,`id_usuario2`),
  ADD KEY `id_usuario2` (`id_usuario2`);

--
-- Indexes for table `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_publicacao` (`id_publicacao`),
  ADD KEY `id_autor` (`id_autor`);

--
-- Indexes for table `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`id_grupo`);

--
-- Indexes for table `membro`
--
ALTER TABLE `membro`
  ADD PRIMARY KEY (`id_grupo`,`id_usuario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `mural`
--
ALTER TABLE `mural`
  ADD PRIMARY KEY (`id_mural`);

--
-- Indexes for table `mural_grupo`
--
ALTER TABLE `mural_grupo`
  ADD PRIMARY KEY (`id_mural`,`id_grupo`),
  ADD KEY `id_grupo` (`id_grupo`);

--
-- Indexes for table `mural_usuario`
--
ALTER TABLE `mural_usuario`
  ADD PRIMARY KEY (`id_mural`,`id_usuario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `publicacao`
--
ALTER TABLE `publicacao`
  ADD PRIMARY KEY (`id_publicacao`),
  ADD KEY `id_mural` (`id_mural`),
  ADD KEY `id_autor` (`id_autor`);

--
-- Indexes for table `resposta`
--
ALTER TABLE `resposta`
  ADD PRIMARY KEY (`id_resposta`),
  ADD KEY `id_comentario` (`id_comentario`),
  ADD KEY `id_autor` (`id_autor`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `grupo`
--
ALTER TABLE `grupo`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mural`
--
ALTER TABLE `mural`
  MODIFY `id_mural` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `publicacao`
--
ALTER TABLE `publicacao`
  MODIFY `id_publicacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `resposta`
--
ALTER TABLE `resposta`
  MODIFY `id_resposta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `amizade`
--
ALTER TABLE `amizade`
  ADD CONSTRAINT `amizade_ibfk_1` FOREIGN KEY (`id_usuario1`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `amizade_ibfk_2` FOREIGN KEY (`id_usuario2`) REFERENCES `usuario` (`id_usuario`);

--
-- Limitadores para a tabela `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `comentario_ibfk_1` FOREIGN KEY (`id_publicacao`) REFERENCES `publicacao` (`id_publicacao`),
  ADD CONSTRAINT `comentario_ibfk_2` FOREIGN KEY (`id_autor`) REFERENCES `usuario` (`id_usuario`);

--
-- Limitadores para a tabela `membro`
--
ALTER TABLE `membro`
  ADD CONSTRAINT `membro_ibfk_1` FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`),
  ADD CONSTRAINT `membro_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Limitadores para a tabela `mural_grupo`
--
ALTER TABLE `mural_grupo`
  ADD CONSTRAINT `mural_grupo_ibfk_1` FOREIGN KEY (`id_mural`) REFERENCES `mural` (`id_mural`),
  ADD CONSTRAINT `mural_grupo_ibfk_2` FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`);

--
-- Limitadores para a tabela `mural_usuario`
--
ALTER TABLE `mural_usuario`
  ADD CONSTRAINT `mural_usuario_ibfk_1` FOREIGN KEY (`id_mural`) REFERENCES `mural` (`id_mural`),
  ADD CONSTRAINT `mural_usuario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Limitadores para a tabela `publicacao`
--
ALTER TABLE `publicacao`
  ADD CONSTRAINT `publicacao_ibfk_1` FOREIGN KEY (`id_mural`) REFERENCES `mural` (`id_mural`),
  ADD CONSTRAINT `publicacao_ibfk_2` FOREIGN KEY (`id_autor`) REFERENCES `usuario` (`id_usuario`);

--
-- Limitadores para a tabela `resposta`
--
ALTER TABLE `resposta`
  ADD CONSTRAINT `resposta_ibfk_1` FOREIGN KEY (`id_comentario`) REFERENCES `comentario` (`id_comentario`),
  ADD CONSTRAINT `resposta_ibfk_2` FOREIGN KEY (`id_autor`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
