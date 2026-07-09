-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/07/2026 às 18:13
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
  `ultimo_acesso` timestamp NULL DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admins`
--

INSERT INTO `admins` (`id`, `nome`, `email`, `senha_hash`, `ultimo_acesso`, `criado_em`) VALUES
(1, 'Admin DaHoodie', 'dahoodiewrld@gmail.com', '$2y$10$68860yDGeEI5GMTMB/yFHOjguwbJ4abaDClBPMJJzKMsFAwdWiP0C', '2026-07-07 02:34:58', '2026-04-06 20:48:00'),
(2, 'Eliel Diogo', 'elieldiogo@gmail.com', '$2y$10$cB1gDXDalXmc0NclYcAQzuhzDkLpF1IQ7/.w3eujonRwIiNmwzBbq', NULL, '2026-05-05 13:00:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `criado_em`) VALUES
(1, 'Computação e Hardware', '2026-06-13 18:08:02'),
(2, 'Redes e Infraestrutura', '2026-06-13 18:08:17'),
(3, 'Armazenamento e Periféricos', '2026-06-13 18:08:27'),
(4, 'Segurança e Vigilância', '2026-06-13 18:08:40');

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
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `estoque` int(11) NOT NULL DEFAULT 0,
  `categoria_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `imagem`, `criado_em`, `estoque`, `categoria_id`) VALUES
(29, 'Beats Solo HEADPHONES', '', 9499.90, 'assets/img/prods/prod_6a47d98c325f96.28105985.png', '2026-07-03 15:47:24', 29, 3),
(30, 'Beats Solo HEADPHONES', '', 9499.90, 'assets/img/prods/prod_6a47d9b457cd55.07107259.png', '2026-07-03 15:48:04', 29, 3),
(31, 'Beats Solo HEADPHONES', '', 9499.90, 'assets/img/prods/prod_6a47d9c96667d8.97566723.png', '2026-07-03 15:48:25', 29, 3),
(32, 'Beats Solo HEADPHONES', '', 9499.90, 'assets/img/prods/prod_6a47da368328d7.48591629.png', '2026-07-03 15:50:14', 29, 3),
(33, 'FINE SMILE', '', 649099.90, 'assets/img/prods/prod_6a47da8a4fed13.91772308.png', '2026-07-03 15:51:38', 3, 3),
(34, 'MacBook Pro Max M3 Ultra', '', 443099.90, 'assets/img/prods/prod_6a47dacbda9078.00225852.png', '2026-07-03 15:52:43', 12, 1),
(36, 'Apple Black Air Pods', 'Sinta a liberdade de um som sem fios com tecnologia de ponta. Este earphone foi projetado para quem busca discrição, conforto e uma qualidade de áudio cristalina em qualquer lugar.\r\n\r\n*Áudio Espacial 360: Uma experiência de som surround que coloca você no centro da música.\r\n*Cancelamento de Ruído Ativo: Bloqueie o barulho externo e foque apenas no que importa.\r\n*Design In-Ear Ergonômico: Encaixe seguro e leve, ideal para treinos e longas jornadas de trabalho.\r\n*Resistência à Água IPX4: Proteção contra suor e respingos para te acompanhar em qualquer clima.\r\n*Conexão Instantânea: Pareamento automático e estável com Bluetooth 5.3 de baixa latência.', 49199.90, 'assets/img/prods/prod_6a47dc88a143a3.09405951.png', '2026-07-03 16:00:08', 6, 3),
(37, 'Oculus Vision Pro', '', 70099.90, 'assets/img/prods/prod_6a47dcb4bfea61.04626106.png', '2026-07-03 16:00:52', 9, 1),
(38, 'AirPods ErgoTech', '', 8599.90, 'assets/img/prods/prod_6a47dcf80b9f59.15860939.png', '2026-07-03 16:02:00', 12, 3),
(39, 'Z-Pulse Intelligence Watch', 'Experimente a fusão perfeita entre alta tecnologia e design minimalista. Este smartwatch foi desenvolvido para quem não abre mão de performance.\r\n\r\n*Tela AMOLED Retina: Cores vibrantes e nitidez absoluta em qualquer condição de luz.\r\n*Monitoramento Biométrico: Sensor de oxigênio no sangue e ritmo cardíaco em tempo real.\r\n*Performance Esportiva: Mais de 50 modos de treino com GPS integrado de alta precisão.\r\n*Resistência 5ATM: Totalmente à prova d\'água, ideal para natação e atividades intensas.\r\n*Conectividade Inteligente: Sincronização instantânea de notificações, músicas e chamadas.', 13000.90, 'assets/img/prods/prod_6a47dde93d8733.30473846.png', '2026-07-03 16:06:01', 17, 3),
(40, 'CPU Rack PC Gamer', '', 345099.90, 'assets/img/prods/prod_6a47de1934a3d5.71814791.png', '2026-07-03 16:06:49', 10, 2),
(41, 'Sonic Boom Pulse G2', 'Leve a festa para qualquer lugar com uma explosão sonora de alta fidelidade e graves profundos. Esta caixa de som foi projetada para quem vive em movimento, combinando uma estrutura ultra resistente com um sistema de áudio que preenche qualquer ambiente, seja ao ar livre ou em espaços fechados.\r\n\r\n*Som Imersivo 360°: Drivers de alta performance que distribuem o áudio uniformemente.\r\n*Tecnologia BassBoost: Radiadores passivos que entregam graves potentes e sem distorção.\r\n*Proteção IPX7: Totalmente à prova d\'água, ideal para festas na piscina ou na praia.\r\n*Bateria de Longa Duração: Até 15 horas de reprodução contínua para sua trilha sonora não parar.\r\n*Show de Luzes LED: Anéis luminosos que sincronizam com a batida da música para um visual dinâmico.', 12599.90, 'assets/img/prods/prod_6a47de5202c9e9.26432931.png', '2026-07-03 16:07:46', 14, 3),
(42, 'iPhone 14 Pro Max', '', 400099.90, 'assets/img/prods/prod_6a47deedcf3618.92900242.png', '2026-07-03 16:10:21', 20, 1),
(43, 'PlayStation 5 Pro Edition', 'Mergulhe em uma nova era de entretenimento com gráficos de tirar o fôlego e carregamentos quase instantâneos. Este console foi desenhado para oferecer a imersão definitiva, combinando potência bruta com uma biblioteca de jogos exclusivos e inovadores.\r\n\r\n*Arquitetura RDNA 2: Gráficos em 4K nativo com tecnologia Ray Tracing para reflexos reais.\r\n*SSD de Ultra Velocidade: Esqueça as telas de carregamento e entre na ação em segundos.\r\n*Áudio 3D Tempest: Uma experiência sonora envolvente que coloca você dentro do jogo.\r\n*Controle DualSense: Sinta cada impacto e movimento com o feedback tátil e gatilhos adaptáveis.\r\n*Taxa de Quadros de 120fps: Fluidez absoluta para competições e jogos de alta performance.', 750499.90, 'assets/img/prods/prod_6a47df3e0f0903.34071948.png', '2026-07-03 16:11:42', 15, 1),
(44, 'Oculus Vision Pro Quest', 'Transcenda os limites da realidade física e entre em mundos digitais com uma clareza impressionante. Este headset de realidade virtual foi projetado para oferecer total liberdade de movimento e uma imersão sensorial profunda, sem a necessidade de cabos ou computadores externos.\r\n\r\n*Resolução Ultra-HD: Painéis de alta densidade para imagens nítidas e sem efeito de grade.\r\n*Rastreamento de Movimento 6DOF: Sensores integrados que traduzem seus movimentos com precisão milimétrica.\r\n*Áudio Espacial Integrado: Som posicional 3D que permite ouvir tudo ao seu redor como na vida real.\r\n*Controles Touch Ergonômicos: Interaja com objetos virtuais com naturalidade e resposta tátil imediata.\r\n*Biblioteca Imersiva: Acesso a centenas de jogos, experiências sociais e ferramentas de produtividade.', 68499.90, 'assets/img/prods/prod_6a47df57e35393.05130640.png', '2026-07-03 16:12:07', 12, 1),
(45, 'Beats Solo HEADPHONES', '**Uma experiência sonora imersiva!** Este headphone combina um design moderno com alta fidelidade de áudio.\r\n\r\n*Som Hi-Fi Estéreo: Drivers de alta qualidade que entregam graves potentes.\r\n*Design Ergonômico: Conchas almofadadas que garantem conforto.\r\n*Conectividade Dual: Bluetooth ou via cabo auxiliar.\r\n*Bateria de Longa Duração: Criado para acompanhar seu dia a dia.', 10500.90, 'assets/img/prods/prod_6a47df746148c5.73906130.png', '2026-07-03 16:12:36', 19, 3);

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
(1, 'DaHoodie', 'dahoodiewrld@gmail.com', '$2y$10$68860yDGeEI5GMTMB/yFHOjguwbJ4abaDClBPMJJzKMsFAwdWiP0C', '2026-04-29 19:30:48'),
(12, 'Eliel Manuel Mucanza Diogo', 'elieldiogo@gmail.com', '$2y$10$cB1gDXDalXmc0NclYcAQzuhzDkLpF1IQ7/.w3eujonRwIiNmwzBbq', '2026-05-05 13:00:02'),
(18, 'Hoodie', 'hoodie@gmail.com', '$2y$10$e6lrsw3ZKQlvq9iPuPvDmekSpMf1QAmm.Qr9L0Gd9M8Ea8qwnrk5a', '2026-07-03 16:43:59');

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
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria` (`categoria_id`);

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
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
