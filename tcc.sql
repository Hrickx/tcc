-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/01/2026 às 02:17
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tcc`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `aprovacao_pedido`
--

CREATE TABLE `aprovacao_pedido` (
  `id_aprovacao` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_gerente` int(11) NOT NULL,
  `data_aprovacao` date NOT NULL,
  `decisao` enum('APROVADO','REJEITADO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `aprovacao_pedido`
--

INSERT INTO `aprovacao_pedido` (`id_aprovacao`, `id_pedido`, `id_gerente`, `data_aprovacao`, `decisao`) VALUES
(1, 2, 8, '2025-01-13', 'APROVADO'),
(2, 3, 9, '2025-01-16', 'REJEITADO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedor`
--

CREATE TABLE `fornecedor` (
  `id_fornecedor` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedor`
--

INSERT INTO `fornecedor` (`id_fornecedor`, `nome`, `cnpj`, `telefone`, `email`, `endereco`) VALUES
(1, 'Auto Peças Brasil', '11.111.111/0001-11', '11999990001', 'contato@autobrasil.com', 'Rua A, 100'),
(2, 'Distribuidora Motor Forte', '22.222.222/0001-22', '11999990002', 'vendas@motorforte.com', 'Rua B, 200'),
(3, 'Peças Premium', '33.333.333/0001-33', '11999990003', 'comercial@pecaspremium.com', 'Rua C, 300');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido_compra`
--

CREATE TABLE `itens_pedido_compra` (
  `id_item` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_peca` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_estimado` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_pedido_compra`
--

INSERT INTO `itens_pedido_compra` (`id_item`, `id_pedido`, `id_peca`, `quantidade`, `preco_estimado`) VALUES
(2, 2, 2, 40, 110.00),
(3, 3, 3, 20, 420.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_venda`
--

CREATE TABLE `itens_venda` (
  `id_item` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `id_peca` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_venda`
--

INSERT INTO `itens_venda` (`id_item`, `id_venda`, `id_peca`, `quantidade`, `preco_unitario`) VALUES
(2, 2, 3, 1, 450.00),
(3, 3, 2, 1, 120.00),
(4, 4, 3, 14, 10.00),
(8, 8, 5, 10, 120.00),
(9, 9, 5, 20, 120.00),
(10, 10, 5, 10, 120.00),
(11, 11, 5, 10, 120.00),
(13, 13, 3, 4, 30.00),
(14, 14, 2, 1, 50.00),
(15, 15, 5, 3, 35.00),
(16, 16, 4, 3, 35.00),
(17, 17, 5, 3, 35.00),
(18, 18, 3, 1, 10.00),
(19, 19, 3, 10, 20.00),
(20, 20, 3, 1, 20.00),
(21, 21, 3, 4, 35.00),
(22, 22, 3, 4, 35.00),
(23, 23, 3, 5, 30.00),
(24, 24, 3, 1, 20.00),
(25, 25, 3, 5, 35.00),
(26, 26, 2, 3, 45.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacao_estoque`
--

CREATE TABLE `movimentacao_estoque` (
  `id_movimentacao` int(11) NOT NULL,
  `id_peca` int(11) NOT NULL,
  `tipo` enum('ENTRADA','SAIDA') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_movimentacao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movimentacao_estoque`
--

INSERT INTO `movimentacao_estoque` (`id_movimentacao`, `id_peca`, `tipo`, `quantidade`, `data_movimentacao`) VALUES
(2, 2, 'ENTRADA', 40, '2025-01-15'),
(3, 3, 'ENTRADA', 20, '2025-01-16'),
(4, 3, 'SAIDA', 14, '2026-01-23'),
(5, 5, 'SAIDA', 10, '2026-01-23'),
(6, 5, 'SAIDA', 20, '2026-01-23'),
(7, 5, 'ENTRADA', 10, '2026-01-23'),
(8, 5, 'ENTRADA', 30, '2026-01-23'),
(9, 2, 'ENTRADA', 90, '2026-01-23'),
(10, 2, 'ENTRADA', 56, '2026-01-23'),
(11, 2, 'ENTRADA', 4, '2026-01-23'),
(14, 2, 'ENTRADA', 11, '2026-01-23'),
(15, 2, 'ENTRADA', 1, '2026-01-23'),
(17, 4, 'ENTRADA', 60, '2026-01-23'),
(18, 5, 'SAIDA', 10, '2026-01-23'),
(19, 5, 'SAIDA', 10, '2026-01-23'),
(20, 3, 'ENTRADA', 32, '2026-01-23'),
(21, 5, 'ENTRADA', 30, '2026-01-23'),
(22, 6, 'ENTRADA', 35, '2026-01-25'),
(24, 8, 'ENTRADA', 23, '2026-01-25'),
(26, 3, 'SAIDA', 4, '2026-01-27'),
(27, 2, 'SAIDA', 1, '2026-01-27'),
(28, 5, 'SAIDA', 3, '2026-01-27'),
(29, 4, 'SAIDA', 3, '2026-01-27'),
(30, 5, 'SAIDA', 3, '2026-01-27'),
(31, 3, 'SAIDA', 1, '2026-01-27'),
(32, 3, 'SAIDA', 10, '2026-01-27'),
(33, 3, 'SAIDA', 1, '2026-01-27'),
(34, 3, 'SAIDA', 4, '2026-01-27'),
(35, 3, 'SAIDA', 4, '2026-01-27'),
(36, 3, 'SAIDA', 5, '2026-01-27'),
(37, 3, 'SAIDA', 1, '2026-01-27'),
(38, 3, 'SAIDA', 5, '2026-01-27'),
(39, 2, 'SAIDA', 3, '2026-01-27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamento`
--

CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `forma_pagamento` enum('DINHEIRO','PIX','CARTAO') NOT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `data_pagamento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pagamento`
--

INSERT INTO `pagamento` (`id_pagamento`, `id_venda`, `forma_pagamento`, `valor_pago`, `data_pagamento`) VALUES
(1, 1, 'PIX', 285.00, '2025-01-20 00:00:00'),
(2, 2, 'CARTAO', 420.00, '2025-01-21 00:00:00'),
(3, 3, 'DINHEIRO', 120.00, '2025-01-22 00:00:00'),
(4, 13, 'DINHEIRO', 0.00, '2026-01-27 00:00:00'),
(5, 14, 'DINHEIRO', 0.00, '2026-01-27 00:00:00'),
(6, 15, 'DINHEIRO', 0.00, '2026-01-27 00:00:00'),
(7, 16, '', 0.00, '2026-01-27 00:00:00'),
(8, 17, '', 0.00, '2026-01-27 00:00:00'),
(9, 18, '', 0.00, '2026-01-27 14:49:08'),
(10, 19, '', 0.00, '2026-01-27 14:49:34'),
(11, 20, '', 0.00, '2026-01-27 14:50:37'),
(12, 21, '', 0.00, '2026-01-27 15:07:28'),
(13, 22, 'DINHEIRO', 0.00, '2026-01-27 15:08:36'),
(14, 23, 'DINHEIRO', 0.00, '2026-01-27 16:00:18'),
(15, 24, 'DINHEIRO', 0.00, '2026-01-27 16:00:50'),
(16, 25, '', 0.00, '2026-01-27 16:07:03'),
(17, 26, 'DINHEIRO', 0.00, '2026-01-27 16:14:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pecas`
--

CREATE TABLE `pecas` (
  `id_peca` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(150) DEFAULT NULL,
  `preco_venda` decimal(10,2) DEFAULT NULL,
  `preco_custo` decimal(10,2) DEFAULT NULL,
  `id_fornecedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pecas`
--

INSERT INTO `pecas` (`id_peca`, `nome`, `descricao`, `preco_venda`, `preco_custo`, `id_fornecedor`) VALUES
(2, 'Pastilha de Freio', 'Pastilha dianteira', 120.00, 84.00, 2),
(3, 'Bateria 60Ah', 'Bateria automotiva 60Ah', 450.00, 315.00, 3),
(4, 'Vela de ignição', NULL, 55.00, 30.00, 3),
(5, 'Radiador', 'Radiador bruto', 120.00, 90.00, 2),
(6, 'Carburador', NULL, NULL, 200.00, 2),
(8, 'Radio China', NULL, NULL, 200.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_compra`
--

CREATE TABLE `pedido_compra` (
  `id_pedido` int(11) NOT NULL,
  `id_peca` int(11) DEFAULT NULL,
  `id_responsavel_estoque` int(11) NOT NULL,
  `id_fornecedor` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) DEFAULT NULL,
  `valor_total_compra` decimal(10,2) DEFAULT NULL,
  `data_pedido` date NOT NULL,
  `status` enum('PENDENTE','APROVADO','REJEITADO') NOT NULL,
  `observacao` varchar(200) DEFAULT NULL,
  `justificativa` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_compra`
--

INSERT INTO `pedido_compra` (`id_pedido`, `id_peca`, `id_responsavel_estoque`, `id_fornecedor`, `quantidade`, `valor_unitario`, `valor_total_compra`, `data_pedido`, `status`, `observacao`, `justificativa`) VALUES
(1, NULL, 4, 1, 0, NULL, NULL, '2026-01-23', 'REJEITADO', 'Reposição de filtros', 'kkkkk'),
(2, NULL, 5, 2, 0, NULL, NULL, '2025-01-12', 'APROVADO', 'Compra de pastilhas', ''),
(3, NULL, 6, 3, 0, NULL, NULL, '2025-01-15', 'REJEITADO', 'Preço acima do esperado', ''),
(4, 1, 14, 1, 3, NULL, 75.39, '2026-01-23', 'APROVADO', NULL, ''),
(5, 1, 14, 1, 2, NULL, 50.26, '2026-01-23', 'APROVADO', NULL, ''),
(6, 2, 14, 1, 4, NULL, 336.00, '2026-01-23', 'APROVADO', NULL, ''),
(7, 3, 10, 1, 5, NULL, 1575.00, '2026-01-23', 'APROVADO', NULL, ''),
(8, 3, 14, 1, 8, NULL, 2520.00, '2026-01-23', 'APROVADO', NULL, ''),
(9, 3, 14, 2, 10, NULL, 3150.00, '2026-01-23', 'APROVADO', NULL, ''),
(10, 3, 10, 2, 12, NULL, 3780.00, '2026-01-23', 'APROVADO', NULL, ''),
(11, 2, 14, 3, 25, NULL, 2100.00, '2026-01-23', 'APROVADO', NULL, ''),
(12, 1, 14, 2, 37, NULL, 929.81, '2026-01-23', '', NULL, ''),
(13, 2, 14, 1, 44, NULL, 3696.00, '2026-01-23', 'REJEITADO', NULL, 'Não quero'),
(14, 1, 14, 1, 22, NULL, 552.86, '2026-01-23', 'APROVADO', NULL, ''),
(15, 1, 14, 2, 22, NULL, 552.86, '2026-01-23', 'APROVADO', NULL, ''),
(16, 1, 14, 3, 76, NULL, 1909.88, '2026-01-23', 'APROVADO', NULL, ''),
(17, 3, 14, 3, 34, NULL, 10710.00, '2026-01-23', 'APROVADO', NULL, ''),
(18, 1, 14, 3, 34, NULL, 854.42, '2026-01-23', 'APROVADO', NULL, ''),
(19, 1, 10, 2, 44, NULL, 1105.72, '2026-01-23', 'APROVADO', NULL, ''),
(20, 3, 14, 3, 32, NULL, 10080.00, '2026-01-23', '', NULL, ''),
(21, 2, 14, 1, 1, NULL, 84.00, '2026-01-23', '', NULL, ''),
(22, 2, 14, 2, 11, NULL, 924.00, '2026-01-23', '', NULL, ''),
(23, 1, 14, 3, 33, NULL, 829.29, '2026-01-23', '', NULL, ''),
(24, 1, 14, 3, 222, NULL, 5578.86, '2026-01-23', '', NULL, ''),
(25, 2, 10, 3, 23, NULL, 1932.00, '2026-01-23', 'APROVADO', NULL, ''),
(26, 3, 14, 1, 333, NULL, 104895.00, '2026-01-23', 'REJEITADO', NULL, 'Não gosto do fornecedor '),
(27, 3, 14, 1, 19, NULL, 5985.00, '2026-01-23', 'REJEITADO', NULL, 'Não gosto '),
(28, 2, 14, 2, 56, NULL, 4704.00, '2026-01-23', '', NULL, ''),
(29, 2, 14, 1, 4, NULL, 336.00, '2026-01-23', '', NULL, ''),
(30, 2, 14, 1, 90, NULL, 7560.00, '2026-01-23', '', NULL, ''),
(31, 5, 14, 1, 30, NULL, 2700.00, '2026-01-23', '', NULL, ''),
(32, 4, 14, 1, 60, NULL, 1800.00, '2026-01-23', '', NULL, ''),
(33, 4, 14, 3, 55, NULL, 1650.00, '2026-01-27', 'APROVADO', NULL, ''),
(34, 4, 14, 3, 10, NULL, 300.00, '2026-01-23', 'APROVADO', NULL, ''),
(35, 1, 14, 2, 33, NULL, 829.29, '2026-01-23', 'REJEITADO', NULL, 'nao quero'),
(36, 4, 14, 3, 10, NULL, 300.00, '2026-01-27', 'APROVADO', NULL, ''),
(37, 5, 14, 1, 30, NULL, 2700.00, '2026-01-23', '', NULL, ''),
(38, 8, 14, 1, 31, NULL, 6200.00, '2026-01-27', 'APROVADO', NULL, ''),
(39, 6, 14, 1, 12, NULL, 2400.00, '2026-01-27', 'APROVADO', NULL, ''),
(40, 3, 10, 3, 2, NULL, 630.00, '2026-01-27', 'APROVADO', NULL, ''),
(41, 1, 14, 1, 7, NULL, 175.91, '2026-01-27', 'APROVADO', NULL, ''),
(42, 2, 14, 2, 1, NULL, 84.00, '2026-01-27', 'APROVADO', NULL, ''),
(43, 5, 14, 1, 5, NULL, 450.00, '2026-01-27', 'APROVADO', NULL, ''),
(44, 8, 14, 2, 4, NULL, 800.00, '2026-01-27', 'APROVADO', NULL, ''),
(45, 5, 10, 3, 99, NULL, 8910.00, '2026-01-27', 'APROVADO', NULL, ''),
(46, 2, 14, 1, 88, NULL, 7392.00, '2026-01-27', 'APROVADO', NULL, ''),
(47, 5, 14, 3, 1, NULL, 90.00, '2026-01-27', 'APROVADO', NULL, ''),
(48, 4, 10, 1, 4, NULL, 120.00, '2026-01-27', 'APROVADO', NULL, ''),
(49, 4, 14, 1, 3, NULL, 90.00, '2026-01-27', 'APROVADO', NULL, ''),
(50, 1, 10, 3, 44, NULL, 1105.72, '2026-01-27', 'APROVADO', NULL, ''),
(51, 5, 10, 2, 7, NULL, 630.00, '2026-01-27', 'APROVADO', NULL, ''),
(52, 8, 14, 3, 1, NULL, 200.00, '2026-01-27', 'APROVADO', NULL, ''),
(53, 1, 14, 1, 1, NULL, 25.13, '2026-01-27', 'APROVADO', NULL, ''),
(54, 2, 14, 1, 4, NULL, 336.00, '2026-01-27', 'APROVADO', NULL, ''),
(55, 6, 10, 1, 2, NULL, 400.00, '2026-01-27', 'APROVADO', NULL, ''),
(56, 5, 14, 1, 1, NULL, 90.00, '2026-01-27', 'APROVADO', NULL, ''),
(58, 3, 14, 2, 2, 315.00, 630.00, '2026-01-27', 'APROVADO', NULL, ''),
(59, 5, 14, 2, 2, 90.00, 180.00, '2026-01-27', 'APROVADO', NULL, ''),
(60, 4, 10, 2, 5, 30.00, 150.00, '2026-01-27', 'APROVADO', NULL, ''),
(61, 8, 14, 1, 4, 200.00, 800.00, '2026-01-27', 'APROVADO', NULL, ''),
(62, 5, 10, 2, 5, 90.00, 450.00, '2026-01-27', 'APROVADO', NULL, ''),
(63, 4, 14, 3, 4, 30.00, 120.00, '2026-01-27', 'PENDENTE', NULL, '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expiracao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `recuperacao_senha`
--

INSERT INTO `recuperacao_senha` (`id`, `email`, `token`, `expiracao`) VALUES
(1, 'henrique@auto.com', '002ab329455ed41b2dd90f106f5085', '2026-01-27 18:00:09'),
(2, 'henrique@auto.com', '2bb42a7ae6f87ef353fad16a61400b', '2026-01-27 18:05:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('GERENTE','VENDEDOR','ESTOQUISTA') DEFAULT NULL,
  `status_usuario` enum('ATIVO','INATIVO') DEFAULT 'ATIVO',
  `percentual_desconto_max` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `senha`, `perfil`, `status_usuario`, `percentual_desconto_max`) VALUES
(1, 'Genebri ', 'daniel@auto.com', '$2y$10$Y2k/lhGv3QRKNhH8Ryk1/OEl81jzI5ejla5HhX2Iv0SlkMN71Nl7y', 'VENDEDOR', 'ATIVO', 5),
(2, 'Daniel Douglas', 'daniel@auto.com', '$2y$10$rrC9EdOlxzlxYbycDkYnEOkSYhGOvVvA2ekbrugOShjFeL5ZJWQB.', 'VENDEDOR', 'INATIVO', 10),
(3, 'Marcos Vendas', '', '$2y$10$5zhgvQ6hz2xgiq7PKUwG/ulnDjdFm4yfEbkX7vBFGWH6Qq2moBy/y', 'VENDEDOR', 'ATIVO', 8),
(4, 'Paulo Estoque', '', '$2y$10$Mi2dtjQKm6hW.OWbNbr4TedF9Dx6C8PtFCQnCP/ciWyINZ7C/PV2C', '', 'ATIVO', 0),
(5, 'Fernanda Estoque', '', '$2y$10$r7QyCzOzrCHC7DtRddCcv.UV5.M9SA0NV.emUpEuG3j/B8cmxW2hi', '', 'ATIVO', 0),
(6, 'João Estoque', '', '$2y$10$lG.mmLVu6QAvDiEjAI/RoOX8e2axRaZ9hKZWVxoz9Rj3KpyGgPYoG', '', 'ATIVO', 0),
(7, 'Ricardo Gerente', '', '$2y$10$NhW1IwYXNBWH5IvtIvdNJO2Hpaojs1NXvGYu1rl8GwnE6AindLFcO', 'GERENTE', 'ATIVO', 0),
(8, 'Luciana Gerente', '', '$2y$10$wxXH1Ny92zybF5q1ax9o8egnz4Eh3vWYL5Xc9EE88x/8db7Hlny..', 'GERENTE', 'ATIVO', 0),
(9, 'Eduardo Gerente', '', '$2y$10$MH0NSM5KNK5ylcDm0m46DugCyU1bjGLcjqHxdgKzF4EA8mqQ/G3QW', 'GERENTE', 'ATIVO', 0),
(10, 'Henrique', 'henrique@auto.com', '$2y$10$CWaCyKrVk8xCu1ACNT314uVxnqw4lPNhw78sgCWgckijA9ji/EHau', 'GERENTE', 'ATIVO', 0),
(11, 'Eduarda', 'Eduarda.venda@auto.com', '$2y$10$O4AtCYgPg2DdfQCWspk81OOI3PSa0H0F8DpWM75QD0meVVTyjOLvq', 'VENDEDOR', 'ATIVO', 10),
(12, 'eduarda', 'eduarda@auto.com', '$2y$10$4m99rqDaRJwnuuowWCcuPeoo3yb6aXKgQqisMw2Ir/33o6k/4KWUS', 'VENDEDOR', 'ATIVO', 0),
(13, 'Dede', 'dede@auto.com', '$2y$10$rbbqn6a2cf7w.5l214vW5eufoaDw4Qd0q8TRCLlw5l2xXoWaXDYZq', '', 'ATIVO', 0),
(14, 'Dedo', 'dedo@auto.com', '$2y$10$ipgraOf7jbWq2Gkujei55e9wZhSJUb5N89l0DrztVbWW78t5G/wca', 'ESTOQUISTA', 'ATIVO', 0),
(15, 'Pamela', 'pamela@auto.com', '$2y$10$0/6JyrkMml2Gcw0o9S8JOeG5GjPSsI1Ma01lTn/sSe5UP3M0mKHNW', 'GERENTE', 'ATIVO', 0),
(16, 'Elton', 'elton@auto.com', '$2y$10$ENNGzDm0ITdeE8Qd0..bLepaKTzUcI1hzpp2nzFMVNuDHnlRCMOcS', 'GERENTE', 'ATIVO', 0),
(17, 'ffff', 'gg@auto.com', '$2y$10$tvd12HZXJ7KtK07eZ2PeXOzt8zx5s5FEpnEbicQSFae8erPxrvJj.', 'GERENTE', 'ATIVO', 0),
(18, 'Emilio', 'emilio@auto.com', '$2y$10$7HCPj/CT1tKSoGJ5P8SpNu0.wsd3BYCDX1M3UeN9NhnEr1v26.Qqa', 'ESTOQUISTA', 'ATIVO', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id_venda` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `data_venda` date NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `desconto_aplicado` decimal(10,2) DEFAULT 0.00,
  `status` enum('ABERTA','FINALIZADA','CANCELADA') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id_venda`, `id_vendedor`, `data_venda`, `valor_total`, `desconto_aplicado`, `status`) VALUES
(1, 1, '2025-01-20', 300.00, 15.00, 'FINALIZADA'),
(2, 2, '2025-01-21', 450.00, 30.00, 'FINALIZADA'),
(3, 3, '2025-01-22', 120.00, 0.00, 'FINALIZADA'),
(4, 12, '2026-01-23', 133.00, 7.00, 'FINALIZADA'),
(8, 12, '2026-01-23', 1200.00, 0.00, 'FINALIZADA'),
(9, 10, '2026-01-23', 2400.00, 0.00, 'FINALIZADA'),
(10, 12, '2026-01-23', 1200.00, 0.00, 'FINALIZADA'),
(11, 12, '2026-01-23', 1200.00, 0.00, 'FINALIZADA'),
(13, 12, '2026-01-27', 120.00, 0.00, 'FINALIZADA'),
(14, 10, '2026-01-27', 50.00, 0.00, 'FINALIZADA'),
(15, 12, '2026-01-27', 105.00, 0.00, 'FINALIZADA'),
(16, 10, '2026-01-27', 99.75, 5.25, 'FINALIZADA'),
(17, 12, '2026-01-27', 99.75, 5.25, 'FINALIZADA'),
(18, 10, '2026-01-27', 9.50, 0.50, 'FINALIZADA'),
(19, 12, '2026-01-27', 190.00, 10.00, 'FINALIZADA'),
(20, 10, '2026-01-27', 20.00, 0.00, 'FINALIZADA'),
(21, 12, '2026-01-27', 133.00, 7.00, 'FINALIZADA'),
(22, 10, '2026-01-27', 133.00, 7.00, 'FINALIZADA'),
(23, 10, '2026-01-27', 142.50, 7.50, 'FINALIZADA'),
(24, 12, '2026-01-27', 19.00, 1.00, 'FINALIZADA'),
(25, 12, '2026-01-27', 166.25, 8.75, 'FINALIZADA'),
(26, 12, '2026-01-27', 128.25, 6.75, 'FINALIZADA');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `aprovacao_pedido`
--
ALTER TABLE `aprovacao_pedido`
  ADD PRIMARY KEY (`id_aprovacao`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_gerente` (`id_gerente`);

--
-- Índices de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  ADD PRIMARY KEY (`id_fornecedor`);

--
-- Índices de tabela `itens_pedido_compra`
--
ALTER TABLE `itens_pedido_compra`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_peca` (`id_peca`);

--
-- Índices de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_venda` (`id_venda`),
  ADD KEY `id_peca` (`id_peca`);

--
-- Índices de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  ADD PRIMARY KEY (`id_movimentacao`),
  ADD KEY `id_peca` (`id_peca`);

--
-- Índices de tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `id_venda` (`id_venda`);

--
-- Índices de tabela `pecas`
--
ALTER TABLE `pecas`
  ADD PRIMARY KEY (`id_peca`),
  ADD KEY `id_fornecedor` (`id_fornecedor`);

--
-- Índices de tabela `pedido_compra`
--
ALTER TABLE `pedido_compra`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_responsavel_estoque` (`id_responsavel_estoque`),
  ADD KEY `id_fornecedor` (`id_fornecedor`);

--
-- Índices de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id_venda`),
  ADD KEY `id_vendedor` (`id_vendedor`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `aprovacao_pedido`
--
ALTER TABLE `aprovacao_pedido`
  MODIFY `id_aprovacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  MODIFY `id_fornecedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `itens_pedido_compra`
--
ALTER TABLE `itens_pedido_compra`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  MODIFY `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de tabela `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `pecas`
--
ALTER TABLE `pecas`
  MODIFY `id_peca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `pedido_compra`
--
ALTER TABLE `pedido_compra`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id_venda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `aprovacao_pedido`
--
ALTER TABLE `aprovacao_pedido`
  ADD CONSTRAINT `aprovacao_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido_compra` (`id_pedido`),
  ADD CONSTRAINT `aprovacao_pedido_ibfk_2` FOREIGN KEY (`id_gerente`) REFERENCES `usuarios` (`id_usuario`);

--
-- Restrições para tabelas `itens_pedido_compra`
--
ALTER TABLE `itens_pedido_compra`
  ADD CONSTRAINT `itens_pedido_compra_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido_compra` (`id_pedido`),
  ADD CONSTRAINT `itens_pedido_compra_ibfk_2` FOREIGN KEY (`id_peca`) REFERENCES `pecas` (`id_peca`);

--
-- Restrições para tabelas `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD CONSTRAINT `itens_venda_ibfk_1` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id_venda`),
  ADD CONSTRAINT `itens_venda_ibfk_2` FOREIGN KEY (`id_peca`) REFERENCES `pecas` (`id_peca`);

--
-- Restrições para tabelas `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  ADD CONSTRAINT `movimentacao_estoque_ibfk_1` FOREIGN KEY (`id_peca`) REFERENCES `pecas` (`id_peca`);

--
-- Restrições para tabelas `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id_venda`);

--
-- Restrições para tabelas `pecas`
--
ALTER TABLE `pecas`
  ADD CONSTRAINT `pecas_ibfk_1` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedor` (`id_fornecedor`);

--
-- Restrições para tabelas `pedido_compra`
--
ALTER TABLE `pedido_compra`
  ADD CONSTRAINT `pedido_compra_ibfk_1` FOREIGN KEY (`id_responsavel_estoque`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `pedido_compra_ibfk_2` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedor` (`id_fornecedor`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`id_vendedor`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
