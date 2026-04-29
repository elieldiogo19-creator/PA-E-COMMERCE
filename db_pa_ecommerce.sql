-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/04/2026 às 22:14
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pa_ecommerce`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admins`
--

INSERT INTO `admins` (`id`, `nome`, `email`, `senha_hash`, `criado_em`) VALUES
(1, 'Admin DaHoodie', 'dahoodiewrld@gmail.com', '$2y$10$68860yDGeEI5GMTMB/yFHOjguwbJ4abaDClBPMJJzKMsFAwdWiP0C', '2026-04-06 20:48:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `quantidade` int(10) UNSIGNED NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `nome_cliente` varchar(150) NOT NULL,
  `email_cliente` varchar(150) NOT NULL,
  `endereco` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `imagem`, `criado_em`) VALUES
(10, 'Laptop Work Pro v2', 'Intel i7 12ª Ger, 16GB DDR4, SSD 512GB NVMe.\r\n\r\nPortátil Profissional 15.6\r\n\r\nChassis em magnésio leve e teclado retroiluminado.', 1007249.99, 'assets/img/prod_69f26092b8f8d1.66634706.png', '2026-04-29 19:48:34'),
(11, 'Ultra Monitor 27', 'Monitor Profissional 4K IPS.\r\n\r\nResolução 3840x2160, 99% sRGB, Delta E < 2. Inclinação e rotação 90º (Pivot). Ideal para design.', 168199.99, 'assets/img/prod_69f2611c3d77f1.73563924.png', '2026-04-29 19:50:52'),
(12, 'Desk Mini PC Pro', 'Processador Ryzen 5, 8GB RAM, Gráficos Vega integrados.\r\n\r\nEstação de Trabalho Ultra-Compacta\r\n\r\nSuporte VESA para montar atrás do monitor.', 344099.99, 'assets/img/prod_69f261c341b7b2.70007953.jpg', '2026-04-29 19:53:39'),
(13, 'ErgoBoard Mechanical', 'Teclado Mecânico Silencioso.\r\n\r\nSwitch Brown (tátil e silencioso), layout PT, retroiluminação branca ajustável. Conexão USB-C.', 27499.99, 'assets/img/prod_69f2623c2dba05.65931435.jpg', '2026-04-29 19:55:40'),
(14, 'Precision Mouse MX', 'Rato Wireless de Alta Precisão\r\n\r\nSensor laser de 4000 DPI, funciona em vidro. Scroll de velocidade infinita e botões laterais.', 20249.99, 'assets/img/prod_69f264123e2a11.89753196.jpg', '2026-04-29 20:03:30'),
(15, 'GPU Render X8', 'Placa Gráfica dedicada 8GB.\r\n\r\nArquitetura de última geração para processamento de vídeo 4K, IA e modelação 3D profissional.', 299999.99, 'assets/img/prod_69f264d6b44926.84843469.jpg', '2026-04-29 20:04:46'),
(16, 'RAM Boost 32GB', 'Kit Memória DDR5 (2x16GB)	\r\n\r\nFrequência de 5200MHz com dissipador de calor em alumínio. Otimizado para Intel e AMD.', 90144.99, 'assets/img/prod_69f264cc8aa628.70400217.jpg', '2026-04-29 20:06:36'),
(17, 'Tablet Draw Plus', 'Tablet Digitalizador 12\"\r\n\r\nEcrã laminado anti-reflexo, 8192 níveis de pressão na caneta. Compatível com Windows e MacOS.', 260349.99, 'assets/img/prod_69f2655fb31900.39977753.jpg', '2026-04-29 20:09:03'),
(18, 'Cadeira ErgoTech', 'Cadeira de Escritório Ergonómica	\r\n\r\nSuporte lombar dinâmico, malha respirável (mesh) e apoios de braço 4D. Base em aço.', 78249.99, 'assets/img/prod_69f265a8d84156.35703025.jpg', '2026-04-29 20:10:16'),
(19, 'Power Station UPS', 'Nobreak (UPS) 1500VA	\r\n\r\nProteção contra picos e quedas. Autonomia de 20 min para fecho seguro de projetos.', 68249.99, 'assets/img/prod_69f26645c5d5f6.74229531.png', '2026-04-29 20:12:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `criado_em`) VALUES
(1, 'DaHoodie', 'dahoodiewrld@gmail.com', '$2y$10$68860yDGeEI5GMTMB/yFHOjguwbJ4abaDClBPMJJzKMsFAwdWiP0C', '2026-04-29 19:30:48');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_itens_pedido_pedidos` (`pedido_id`),
  ADD KEY `fk_itens_pedido_produtos` (`produto_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedidos_usuarios` (`usuario_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_nome` (`nome`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `fk_itens_pedido_pedidos` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `fk_itens_pedido_produtos` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
