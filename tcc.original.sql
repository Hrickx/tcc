-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/01/2026 às 06:41
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
(1, 1, 1, 50, 30.00),
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
(1, 1, 1, 5, 35.90),
(2, 2, 3, 1, 450.00),
(3, 3, 2, 1, 120.00),
(4, 4, 3, 14, 10.00),
(8, 8, 5, 10, 120.00),
(9, 9, 5, 20, 120.00),
(10, 10, 5, 10, 120.00),
(11, 11, 5, 10, 120.00);

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
(1, 1, 'ENTRADA', 50, '2025-01-14'),
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
(12, 1, 'ENTRADA', 222, '2026-01-23'),
(13, 1, 'ENTRADA', 33, '2026-01-23'),
(14, 2, 'ENTRADA', 11, '2026-01-23'),
(15, 2, 'ENTRADA', 1, '2026-01-23'),
(16, 1, 'ENTRADA', 37, '2026-01-23'),
(17, 4, 'ENTRADA', 60, '2026-01-23'),
(18, 5, 'SAIDA', 10, '2026-01-23'),
(19, 5, 'SAIDA', 10, '2026-01-23'),
(20, 3, 'ENTRADA', 32, '2026-01-23'),
(21, 5, 'ENTRADA', 30, '2026-01-23'),
(22, 6, 'ENTRADA', 35, '2026-01-25'),
(24, 8, 'ENTRADA', 23, '2026-01-25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamento`
--

CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `forma_pagamento` enum('DINHEIRO','PIX','CARTAO') NOT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `data_pagamento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pagamento`
--

INSERT INTO `pagamento` (`id_pagamento`, `id_venda`, `forma_pagamento`, `valor_pago`, `data_pagamento`) VALUES
(1, 1, 'PIX', 285.00, '2025-01-20'),
(2, 2, 'CARTAO', 420.00, '2025-01-21'),
(3, 3, 'DINHEIRO', 120.00, '2025-01-22');

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
(1, 'Filtro de Óleo', 'Filtro para motores 1.0 a 2.0', 35.90, 25.13, 1),
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
  `valor_total_compra` decimal(10,2) DEFAULT NULL,
  `data_pedido` date NOT NULL,
  `status` enum('PENDENTE','APROVADO','REJEITADO') NOT NULL,
  `observacao` varchar(200) DEFAULT NULL,
  `justificativa` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_compra`
--

INSERT INTO `pedido_compra` (`id_pedido`, `id_peca`, `id_responsavel_estoque`, `id_fornecedor`, `quantidade`, `valor_total_compra`, `data_pedido`, `status`, `observacao`, `justificativa`) VALUES
(1, NULL, 4, 1, 0, NULL, '2026-01-23', 'REJEITADO', 'Reposição de filtros', 'kkkkk'),
(2, NULL, 5, 2, 0, NULL, '2025-01-12', 'APROVADO', 'Compra de pastilhas', ''),
(3, NULL, 6, 3, 0, NULL, '2025-01-15', 'REJEITADO', 'Preço acima do esperado', ''),
(4, 1, 14, 1, 3, 75.39, '2026-01-23', 'APROVADO', NULL, ''),
(5, 1, 14, 1, 2, 50.26, '2026-01-23', 'APROVADO', NULL, ''),
(6, 2, 14, 1, 4, 336.00, '2026-01-23', 'APROVADO', NULL, ''),
(7, 3, 10, 1, 5, 1575.00, '2026-01-23', 'APROVADO', NULL, ''),
(8, 3, 14, 1, 8, 2520.00, '2026-01-23', 'APROVADO', NULL, ''),
(9, 3, 14, 2, 10, 3150.00, '2026-01-23', 'APROVADO', NULL, ''),
(10, 3, 10, 2, 12, 3780.00, '2026-01-23', 'APROVADO', NULL, ''),
(11, 2, 14, 3, 25, 2100.00, '2026-01-23', 'APROVADO', NULL, ''),
(12, 1, 14, 2, 37, 929.81, '2026-01-23', '', NULL, ''),
(13, 2, 14, 1, 44, 3696.00, '2026-01-23', 'REJEITADO', NULL, 'Não quero'),
(14, 1, 14, 1, 22, 552.86, '2026-01-23', 'APROVADO', NULL, ''),
(15, 1, 14, 2, 22, 552.86, '2026-01-23', 'APROVADO', NULL, ''),
(16, 1, 14, 3, 76, 1909.88, '2026-01-23', 'APROVADO', NULL, ''),
(17, 3, 14, 3, 34, 10710.00, '2026-01-23', 'APROVADO', NULL, ''),
(18, 1, 14, 3, 34, 854.42, '2026-01-23', 'APROVADO', NULL, ''),
(19, 1, 10, 2, 44, 1105.72, '2026-01-23', 'APROVADO', NULL, ''),
(20, 3, 14, 3, 32, 10080.00, '2026-01-23', '', NULL, ''),
(21, 2, 14, 1, 1, 84.00, '2026-01-23', '', NULL, ''),
(22, 2, 14, 2, 11, 924.00, '2026-01-23', '', NULL, ''),
(23, 1, 14, 3, 33, 829.29, '2026-01-23', '', NULL, ''),
(24, 1, 14, 3, 222, 5578.86, '2026-01-23', '', NULL, ''),
(25, 2, 10, 3, 23, 1932.00, '2026-01-23', 'APROVADO', NULL, ''),
(26, 3, 14, 1, 333, 104895.00, '2026-01-23', 'REJEITADO', NULL, 'Não gosto do fornecedor '),
(27, 3, 14, 1, 19, 5985.00, '2026-01-23', 'REJEITADO', NULL, 'Não gosto '),
(28, 2, 14, 2, 56, 4704.00, '2026-01-23', '', NULL, ''),
(29, 2, 14, 1, 4, 336.00, '2026-01-23', '', NULL, ''),
(30, 2, 14, 1, 90, 7560.00, '2026-01-23', '', NULL, ''),
(31, 5, 14, 1, 30, 2700.00, '2026-01-23', '', NULL, ''),
(32, 4, 14, 1, 60, 1800.00, '2026-01-23', '', NULL, ''),
(33, 4, 14, 3, 55, 1650.00, '2026-01-23', 'PENDENTE', NULL, ''),
(34, 4, 14, 3, 10, 300.00, '2026-01-23', 'APROVADO', NULL, ''),
(35, 1, 14, 2, 33, 829.29, '2026-01-23', 'REJEITADO', NULL, 'nao quero'),
(36, 4, 14, 3, 10, 300.00, '2026-01-23', 'PENDENTE', NULL, ''),
(37, 5, 14, 1, 30, 2700.00, '2026-01-23', '', NULL, '');

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
(1, 'Carlos Vendas', '', '$2y$10$Y2k/lhGv3QRKNhH8Ryk1/OEl81jzI5ejla5HhX2Iv0SlkMN71Nl7y', 'VENDEDOR', 'ATIVO', 5),
(2, 'Ana Vendas', '', '$2y$10$rrC9EdOlxzlxYbycDkYnEOkSYhGOvVvA2ekbrugOShjFeL5ZJWQB.', 'VENDEDOR', 'ATIVO', 10),
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
(11, 12, '2026-01-23', 1200.00, 0.00, 'FINALIZADA');

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
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  MODIFY `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pecas`
--
ALTER TABLE `pecas`
  MODIFY `id_peca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `pedido_compra`
--
ALTER TABLE `pedido_compra`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id_venda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
