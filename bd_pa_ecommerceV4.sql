-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13/07/2026 às 03:07
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
(1, 'Admin DaHoodie', 'dahoodiewrld@gmail.com', '$2y$10$68860yDGeEI5GMTMB/yFHOjguwbJ4abaDClBPMJJzKMsFAwdWiP0C', '2026-07-12 02:26:39', '2026-04-06 20:48:00'),
(2, 'Eliel Diogo', 'elieldiogo@gmail.com', '$2y$10$cB1gDXDalXmc0NclYcAQzuhzDkLpF1IQ7/.w3eujonRwIiNmwzBbq', NULL, '2026-05-05 13:00:02'),
(3, 'vou le aleja', 'josediogo2344@gmail.com', '$2y$10$j8uv2oEWa.HGncleqx0b3.5/oTjAlZU87aKQpS8dwlm0aNUsdfJaG', '2026-07-07 01:34:58', '2026-07-10 15:16:02');

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
(1, 'Computadores Portáteis', '2026-06-13 18:08:02'),
(2, 'Computadores de Secretária', '2026-06-13 18:08:17'),
(3, 'Monitores e Projetores', '2026-06-13 18:08:27'),
(4, 'Acessórios e Periféricos', '2026-06-13 18:08:40'),
(6, 'Armazenamento', '2026-07-10 17:39:04'),
(7, 'Redes e Internet', '2026-07-10 17:39:20'),
(8, 'Ratos', '2026-07-10 17:39:36'),
(9, 'Teclados', '2026-07-10 17:39:44'),
(10, 'Smartphones e Tablets', '2026-07-10 19:20:21'),
(11, 'Vídeo Vigilância', '2026-07-10 19:20:34'),
(12, 'Gaming', '2026-07-10 19:23:50'),
(13, 'Componentes', '2026-07-10 19:24:21');

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
  `descricao_curta` varchar(200) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `estoque` int(11) NOT NULL DEFAULT 0,
  `categoria_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `descricao_curta`, `preco`, `imagem`, `criado_em`, `estoque`, `categoria_id`) VALUES
(29, 'Beats Solo HEADPHONES', '', '', 9499.90, 'assets/img/prods/prod_6a47d98c325f96.28105985.png', '2026-07-03 15:47:24', 29, 4),
(30, 'Beats Solo HEADPHONES', '', '', 9499.90, 'assets/img/prods/prod_6a47d9b457cd55.07107259.png', '2026-07-03 15:48:04', 29, 4),
(31, 'Beats Solo HEADPHONES', '', '', 9499.90, 'assets/img/prods/prod_6a47d9c96667d8.97566723.png', '2026-07-03 15:48:25', 29, 4),
(32, 'Beats Solo HEADPHONES', '', '', 9499.90, 'assets/img/prods/prod_6a47da368328d7.48591629.png', '2026-07-03 15:50:14', 29, 4),
(46, 'Kit CFTV CP Plus DVR + 4 Câmeras HD + Disco Rígido e Acessórios', 'Expanda a proteção do seu patrimônio com o Kit Completo de 4 Câmeras da CP Plus. Ideal para comércios de médio porte ou residências familiares, ele permite monitorar quatro pontos estratégicos em simultâneo (como salas, corredores, quintal e entrada principal). O sistema inclui o gravador central DVR, conectores BNC, fontes de alimentação e um disco rígido otimizado para gravação ininterrupta. As câmeras oferecem excelente nitidez e filtros inteligentes para entregar imagens claras tanto no brilho do dia quanto no escuro da noite.', 'Cobertura total para a sua residência com 4 pontos de visão.', 210000.90, 'assets/img/prod_6a52781028f1d9.24657552.png', '2026-07-09 23:05:32', 14, 11),
(47, 'Kit CFTV CP Plus DVR + 2 Câmeras HD + Disco Rígido 1TB e Cabos', 'Tenha tudo o que precisa para proteger o seu imóvel com o Kit de Segurança CP Plus. Este combo completo inclui um gravador digital DVR de alta performance, uma câmera bullet para o exterior, uma câmera dome compacta para o interior, além de cabos e fontes de alimentação. O kit vem acompanhado de um disco rígido de 1TB de nível de vigilância, garantindo espaço suficiente para armazenar semanas de gravação contínua com total estabilidade e segurança dos dados.', 'Sistema pronto a instalar com gravação contínua em HD.', 145000.90, 'assets/img/prod_6a5278ddf13a28.77619130.png', '2026-07-11 02:02:25', 11, 11),
(48, 'Câmera Hikvision PanoVu / Multi-Sensor', 'A Câmera Hikvision Multi-Sensor redefine o monitoramento de alta segurança. Este dispositivo avançado combina múltiplas lentes fixas para gerar uma visão panorâmica contínua e sem pontos cegos, integrada a uma câmera PTZ inferior de alta velocidade. Enquanto os sensores superiores mantêm os olhos em todo o ambiente simultaneamente, a lente móvel faz aproximações detalhadas e rastreia alvos em movimento automaticamente, oferecendo uma cobertura completa e inteligente para grandes empresas.', 'Visão panorâmica de 360 graus combinada com rastreamento PTZ.', 390000.90, 'assets/img/prod_6a527915509974.73215673.png', '2026-07-11 02:52:00', 14, 11),
(49, 'Câmera Dome PTZ Sunba', 'Maximize o seu campo de visão com a Câmera Profissional Sunba Dome PTZ. Equipada com motores de alta precisão, ela permite controlar o movimento horizontal, vertical e o zoom diretamente pelo seu smartphone ou central de monitoramento. O seu poderoso zoom ótico consegue aproximar rostos e matrículas de veículos à longa distância sem perder a qualidade da imagem. O design em cúpula facilita a fixação em paredes altas, sendo perfeita para cobrir grandes perímetros com um único equipamento.', 'Movimentação completa PTZ e zoom potente para grandes áreas.', 185000.90, 'assets/img/prod_6a527959d03c60.58754544.png', '2026-07-11 02:53:34', 16, 11),
(50, 'Câmera de Segurança Hikvision Bullet IP Full HD – Branca', 'A Câmera Hikvision Bullet é a escolha ideal para garantir a segurança de áreas externas da sua casa ou empresa. Com uma estrutura robusta e proteção contra chuva e poeira, ela monitora fachadas, portões e estacionamentos sob qualquer condição climática. As suas lentes capturam imagens em alta definição com excelente balanço de cores durante o dia e contam com um potente modo de visão noturna por infravermelho para registrar qualquer atividade suspeita na total escuridão.', 'Proteção resistente à água e monitoramento nítido para o exterior.', 45000.90, 'assets/img/prod_6a5279a7d10178.77124783.png', '2026-07-11 03:02:33', 26, 11),
(51, 'Smartphone Samsung Galaxy S24 Ultra 512GB (Com S Pen) – Preto Titanium', 'Conheça o ápice da tecnologia móvel com o Samsung Galaxy S24 Ultra. Construído com uma estrutura inovadora e ultrarresistente de Titânio, ele eleva o patamar de durabilidade e elegância. Explore o ecossistema Galaxy AI, que permite traduzir chamadas em tempo real, circular objetos na tela para pesquisar instantaneamente e editar fotos profissionalmente com apenas um toque. Sua câmera de 200 megapixels captura texturas e detalhes inacreditáveis, mesmo no escuro absoluto. A icônica S Pen vem embutida no corpo do aparelho, pronta para você assinar documentos, controlar apresentações e criar com precisão cirúrgica.', 'Conheça o ápice da tecnologia móvel com o Samsung Galaxy S24 Ultra. Construído com uma estrutura ino', 1250000.90, 'assets/img/prod_6a543a2a71efc2.47954642.png', '2026-07-11 03:06:36', 2, 10),
(52, 'Tablet Xiaomi Redmi Pad SE 2 128GB Wi-Fi – Lilás', 'O Xiaomi Redmi Pad SE 2 é a escolha perfeita para quem procura um tablet versátil e com excelente custo-benefício. O seu ecrã grande com alta taxa de atualização oferece transições suaves e cores vivas, tornando a experiência de assistir vídeos, navegar pelas redes sociais ou ler livros digitais muito mais confortável. O seu corpo fino e elegante na cor lilás garante leveza no transporte, enquanto a bateria de longa duração permite que você aproveite as suas aplicações preferidas durante todo o dia sem a preocupação de correr para a tomada.', 'Tela fluida e design moderno ideal para o entretenimento diário.', 210000.90, 'assets/img/prods/prod_6a51b3cbb6ae67.80255944.png', '2026-07-11 03:08:59', 8, NULL),
(53, 'Tablet Xiaomi Redmi Pad SE 2 128GB Wi-Fi – Lilás', 'O Xiaomi Redmi Pad SE 2 é a escolha perfeita para quem procura um tablet versátil e com excelente custo-benefício. O seu ecrã grande com alta taxa de atualização oferece transições suaves e cores vivas, tornando a experiência de assistir vídeos, navegar pelas redes sociais ou ler livros digitais muito mais confortável. O seu corpo fino e elegante na cor lilás garante leveza no transporte, enquanto a bateria de longa duração permite que você aproveite as suas aplicações preferidas durante todo o dia sem a preocupação de correr para a tomada.', 'O Xiaomi Redmi Pad SE 2 é a escolha perfeita para quem procura um tablet versátil e com excelente cu', 210000.90, 'assets/img/prods/prod_6a51b3f7e69a57.69834463.png', '2026-07-11 03:09:43', 16, 10),
(54, 'Smartphone Samsung Galaxy M56 5G 128GB – Preto Cromo', 'Entre na era da ultravelocidade com o Samsung Galaxy M56 5G. Este smartphone combina perfeitamente um visual minimalista e elegante em tom escuro com a potência necessária para lidar com as suas tarefas simultâneas sem qualquer lentidão. A sua tela fluida oferece uma óptima experiência de navegação e jogabilidade responsiva. Na parte traseira, o renovado conjunto de câmeras permite registar fotos nítidas e cheias de detalhes em qualquer ambiente, enquanto o sistema inteligente de gestão de energia garante que a bateria dure o dia todo.', 'Conexão 5G rápida, ótimo desempenho e câmeras avançadas.', 320000.90, 'assets/img/prods/prod_6a51b420e363c7.59546166.png', '2026-07-11 03:10:24', 8, 10),
(55, 'Tablet Apple iPad Pro 11\" 256GB Wi-Fi – Cinza Espacial', 'O Apple iPad Pro é a ferramenta definitiva para criadores de conteúdo, designers e profissionais exigentes. Equipado com os revolucionários chips de arquitetura própria da Apple, este dispositivo entrega um desempenho gráfico e de processamento que supera a maioria dos computadores portáteis do mercado. O ecrã Liquid Retina oferece uma precisão de cor e brilho incomparáveis para trabalhos profissionais de edição de vídeo e fotografia. O seu design icónico em alumínio cinza espacial conta ainda com o avançado sistema de câmeras e sensores Pro para experiências imersivas em realidade aumentada.', 'Desempenho profissional extremo com o poder do chip Apple.', 1100000.90, 'assets/img/prods/prod_6a51b459eaab45.50370126.png', '2026-07-11 03:11:21', 10, 10),
(56, 'Tablet Xiaomi Redmi Pad 128GB Wi-Fi – Cinza Grafite', 'Descubra o equilíbrio perfeito entre sofisticação e preço justo com o Xiaomi Redmi Pad. O seu grande destaque é o ecrã com taxa de atualização de 90Hz, que proporciona uma navegação pelos menus e sites extremamente suave e agradável aos olhos. O acabamento premium em metal confere ao tablet uma durabilidade superior e um toque elegante. Conta ainda com um potente sistema de som composto por quatro altifalantes integrados com tecnologia Dolby Atmos, criando uma verdadeira atmosfera de cinema em suas mãos para músicas e filmes.', 'Tela de 90Hz super fluida e acabamento premium em metal.', 230000.90, 'assets/img/prods/prod_6a51b4806cacb7.39226350.png', '2026-07-11 03:12:00', 8, 10),
(57, 'Tablet Honor Pad X8a 64GB Wi-Fi – Cinza Espacial (Com Capa)', 'O Honor Pad X8a foi desenhado para quem procura praticidade e conforto no dia a dia. Com um ecrã vibrante de alta definição e tecnologia de proteção ocular, ele é ideal para longas sessões de leitura, estudos ou streaming de vídeos. O seu acabamento metálico minimalista na cor cinza garante elegância e durabilidade, enquanto a capa protetora inclusa serve também como suporte ajustável para deixar as suas mãos livres enquanto trabalha ou assiste aos seus conteúdos favoritos.', 'Tela confortável e capa de suporte ideal para estudos e vídeos.', 175000.90, 'assets/img/prods/prod_6a51b4b2212f38.62293011.png', '2026-07-11 03:12:50', 20, 10),
(58, 'Tablet Gamer Lenovo Legion Tab 8.8\" 256GB – Grafite', 'Desenvolvido especificamente para os entusiastas de jogos, o Lenovo Legion Tab coloca o desempenho de uma consola na palma das suas mãos. A sua tela compacta de 8.8 polegadas possui uma taxa de atualização ultra-rápida, garantindo que cada movimento em jogos competitivos aconteça com precisão instantânea. Conta com um sistema avançado de arrefecimento interno para manter a performance estável durante maratonas intensas de jogo e um design traseiro robusto com a identidade icónica da linha Legion.', 'Poder gamer portátil com ecrã ultra-responsivo de alta taxa.', 420000.90, 'assets/img/prods/prod_6a51b4f7deb1d6.92133107.png', '2026-07-11 03:13:59', 8, 10),
(59, 'Samsung Galaxy S24+ / S24', 'O Samsung Galaxy S24+ abre as portas para o futuro dos smartphones com recursos nativos de inteligência artificial que otimizam desde a tradução instantânea de chamadas até a edição avançada de fotos. O seu design com acabamento acetinado e laterais retas oferece uma pegada firme e extremamente confortável. A tela Dynamic AMOLED 2X entrega cores ultrarrealistas e brilho intenso mesmo sob a luz direta do sol, complementada por uma bateria inteligente de longa duração que acompanha o seu ritmo o dia todo.', 'Inteligência artificial avançada e câmeras de alta resolução.', 790000.90, 'assets/img/prods/prod_6a51b52cdc6362.82423723.png', '2026-07-11 03:14:52', 8, 10),
(60, 'iPhone 15 Pro / 15 Pro Max', 'Conheça o poder e a sofisticação do iPhone 15 Pro. Forjado em titânio aeroespacial de grau premium, este modelo traz uma leveza surpreendente e uma resistência sem precedentes. Equipado com a inovadora Dynamic Island e um ecrã Super Retina XDR incrivelmente brilhante, o aparelho redefine a interação diária. O seu sistema de câmeras profissional captura fotos em altíssima resolução com detalhes impressionantes mesmo em baixa luz, tudo impulsionado por um processador com desempenho gráfico revolucionário para aplicações e jogos pesados.', 'Estrutura robusta em titânio e o chip mais avançado da Apple.', 1250000.90, 'assets/img/prods/prod_6a51b580995099.95157510.png', '2026-07-11 03:16:16', 20, 10),
(61, 'Apple iPad Pro 11\" 128GB Wi-Fi – Cinza Escuro', 'O Apple iPad Pro eleva as suas possibilidades a um nível corporativo e artístico avançado. Com o impressionante poder de processamento da arquitectura Apple, ele lida com edição de vídeo em alta resolução, modelagem 3D e multitarefa extrema sem qualquer esforço. O ecrã Liquid Retina oferece uma precisão de cor milimétrica e reflexos mínimos para garantir o melhor conforto visual. Perfeito para designers, engenheiros e criadores que precisam de máxima potência num formato ultrafino e totalmente portátil.', 'Desempenho e velocidade profissional para as suas ideias.', 980000.90, 'assets/img/prods/prod_6a51b5a6682d13.48321258.png', '2026-07-11 03:16:54', 6, 10),
(62, 'Samsung Galaxy S24 Ultra', 'O Samsung Galaxy S24 Ultra representa o auge da tecnologia móvel atual. Protegido por uma armadura de titânio de alta resistência e vidro ultra-resistente contra riscos, este dispositivo foi feito para durar. A renomada caneta S Pen vem integrada ao corpo do aparelho, permitindo tomar notas, desenhar e controlar apresentações com precisão cirúrgica. O seu conjunto fotográfico impressiona com o sensor principal de 200MP e zoom ótico de longo alcance, transformando qualquer registo casual numa obra de arte profissional.', 'O topo de gama com câmera de 200MP e caneta S Pen embutida.', 1150000.90, 'assets/img/prods/prod_6a51b5d39da1c4.40907133.png', '2026-07-11 03:17:39', 10, 10),
(64, 'Samsung Galaxy M36 5G 256GB – Bronze', 'O Samsung Galaxy M36 5G foi desenhado para quem exige o máximo de autonomia e espaço. Equipado com uma das maiores baterias da categoria, ele foi feito para durar até dois dias longe da tomada. Seus 256GB de memória interna oferecem espaço de sobra para todos os seus aplicativos, fotos e vídeos em alta resolução. O design moderno com acabamento na cor bronze traz sofisticação, enquanto a tela com alta taxa de atualização garante transições suaves e jogabilidade responsiva. Conta ainda com um poderoso sistema de resfriamento interno e câmeras nítidas prontas para as redes sociais.', 'Performance fluida e bateria de longa duração para você nunca ficar na mão.', 340000.90, 'assets/img/prods/prod_6a527a126a60d9.35900101.png', '2026-07-11 17:14:58', 10, 10),
(65, 'Samsung Galaxy Tab S6 Lite 64GB Wi-Fi (Com Caneta S Pen) – Rosa', 'Transforme a sua rotina de estudos e trabalho com o Samsung Galaxy Tab S6 Lite. Leve, fino e elegante na cor rosa, ele cabe facilmente na bolsa ou mochila. O grande diferencial está na caneta S Pen inclusa: com escrita precisa e baixa latência, ela permite fazer anotações à mão livre, desenhar, editar PDFs e dar asas à sua imaginação como se estivesse usando papel. Sua tela ampla combinada com o sistema de som duplo assinado pela AKG proporciona uma experiência de cinema para vídeos, séries e videoaulas.', 'Acompanha a caneta S Pen na caixa e possui acabamento premium.', 380000.90, 'assets/img/prods/prod_6a527a538447c4.67388877.png', '2026-07-11 17:16:03', 8, 10),
(66, 'Samsung Galaxy S20+ 128GB – Cinza Cósmico', 'O Samsung Galaxy S20+ redefine o que um smartphone topo de linha pode fazer por suas fotos e vídeos. Revolucione suas capturas com a gravação de vídeo em resolução 8K, permitindo extrair fotos estáticas de altíssima qualidade direto dos seus vídeos. A tela Dynamic AMOLED 2X de 120Hz oferece uma navegação incrivelmente fluida e cores dignas de cinema. Com o poderoso processador premium e gerenciamento inteligente de energia, ele se adapta aos seus hábitos para economizar bateria e entregar desempenho máximo quando você mais precisa.', 'Tela Dynamic AMOLED 2X super fluida, gravação de vídeos em qualidade profissional 8K e zoom espacial avançado.', 420000.00, 'assets/img/prods/prod_6a527a95b70bc8.68304752.png', '2026-07-11 17:17:09', 10, 10),
(67, 'Samsung Galaxy A26 5G 128GB – Verde Claro', 'Prepare-se para o futuro com o novo Samsung Galaxy A26 5G. Navegue, jogue e assista aos seus conteúdos favoritos sem interrupções graças à velocidade da rede 5G e ao processador otimizado para o dia a dia. Sua tela vibrante oferece cores realistas e excelente visibilidade mesmo sob a luz do sol. Na traseira, o conjunto triplo de câmeras garante versatilidade: capture desde paisagens amplas até os mínimos detalhes com foco automático e inteligência computacional para aprimorar suas fotos noturnas. Tudo isso sustentado por uma bateria que acompanha o seu ritmo o dia todo.', 'Tela imersiva de alta fluidez e sistema de câmera tripla para fotos perfeitas.', 280000.90, 'assets/img/prods/prod_6a527ae0598880.63051356.png', '2026-07-11 17:18:24', 16, 10);

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
(12, 'Eliel Manuel Mucanza Diogo', 'elieldiogo19@gmail.com', '$2y$10$cB1gDXDalXmc0NclYcAQzuhzDkLpF1IQ7/.w3eujonRwIiNmwzBbq', '2026-05-05 13:00:02'),
(20, 'Vou lhe aleja', 'josediogo2344@gmail.com', '$2y$10$j8uv2oEWa.HGncleqx0b3.5/oTjAlZU87aKQpS8dwlm0aNUsdfJaG', '2026-07-13 00:55:51');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
