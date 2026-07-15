-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/07/2026 às 05:51
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
(1, 'Admin DaHoodie', 'dahoodiewrld@gmail.com', '$2y$10$68860yDGeEI5GMTMB/yFHOjguwbJ4abaDClBPMJJzKMsFAwdWiP0C', '2026-07-14 20:46:02', '2026-04-06 20:48:00'),
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
(5, 'Componentes', '2026-07-10 19:24:21'),
(6, 'Armazenamento', '2026-07-10 17:39:04'),
(7, 'Redes e Internet', '2026-07-10 17:39:20'),
(8, 'Ratos', '2026-07-10 17:39:36'),
(9, 'Teclados', '2026-07-10 17:39:44'),
(10, 'Smartphones e Tablets', '2026-07-10 19:20:21'),
(11, 'Vídeo Vigilância', '2026-07-10 19:20:34'),
(12, 'Gaming', '2026-07-10 19:23:50');

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

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(12, 9, 80, 1, 25000.90);

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

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `nome_cliente`, `email_cliente`, `endereco`, `total`, `criado_em`, `status`) VALUES
(9, 1, 'DaHoodie', 'dahoodiewrld@gmail.com', 'Viana - Zango 1.', 25000.90, '2026-07-14 03:52:19', 'pendente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `nome_curto` varchar(50) DEFAULT NULL,
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

INSERT INTO `produtos` (`id`, `nome`, `nome_curto`, `descricao`, `descricao_curta`, `preco`, `imagem`, `criado_em`, `estoque`, `categoria_id`) VALUES
(29, 'Teclado Mecânico Ultra-Compacto 60% RGB Anti-Ghosting – Branco', 'Compacto RGB', 'Se procura estética impecável combinada com alta performance de digitação, este teclado mecânico na cor branca é a peça central que faltava no seu ambiente de trabalho ou lazer. O seu layout compacto de 61 teclas reduz o cansaço dos braços ao permitir que os seus ombros fiquem numa posição mais natural e alinhada durante a digitação diária. Por trás do design clean, o teclado esconde um poderoso sistema de iluminação em arco-íris (Rainbow/RGB) que cria um contraste espetacular com a estrutura branca, iluminando as letras com clareza mesmo em divisões totalmente escuras. Conta com tecnologia de engenharia anti-ghosting avançada, permitindo que pressione várias teclas ao mesmo tempo sem que o computador perca nenhum comand.', 'Visual clean e minimalista com switches macios e luzes multicoloridas.', 39500.90, 'assets/img/prods/prod_6a56b32d19d9d3.54531124.png', '2026-07-03 15:47:24', 8, 9),
(30, 'Teclado Mecânico Gamer Compacto 60% RGB Switch Blue – Preto', 'Teclado Gamer', 'Projetado especificamente para entusiastas de jogos e utilizadores que possuem mesas com espaço limitado, este teclado mecânico no formato 60% elimina completamente o teclado numérico lateral e as teclas de função dedicadas para garantir o máximo de área livre para o movimento do rato. Equipado com switches mecânicos do tipo Blue, ele oferece o feedback tátil e o clique audível característicos que os gamers adoram, garantindo que cada comando seja sentido e confirmado com precisão absoluta. O grande destaque visual fica por conta do seu sistema de retroiluminação RGB integrado por baixo das teclas, que conta com múltiplos efeitos dinâmicos de transição de cores e níveis de brilho ajustáveis diretamente através de atalhos no próp', 'Design compacto 60% com switches mecânicos e iluminação RGB viva.', 38000.90, 'assets/img/prods/prod_6a56b1e86f64a9.14728993.png', '2026-07-03 15:48:04', 8, 9),
(31, 'Teclado Mecânico Custom Premium Layout 60% Retro Grey', 'Custom Grey & White', 'Unindo com perfeição a nostalgia do design clássico de computadores antigos com a tecnologia de ponta dos teclados modernos, este modelo customizado de 60% é feito para programadores, redatores e entusiastas que valorizam uma experiência de digitação superior. O seu conjunto de teclas (keycaps) adota uma elegante combinação de tons de cinza industrial, branco gelo e detalhes em fontes coloridas, conferindo um visual vintage sofisticado à sua secretária. As teclas são esculpidas em plástico PBT de alta qualidade, um material muito mais espesso e resistente do que o plástico comum, que oferece uma textura fosca agradável ao toque e impede que as teclas fiquem com aquele aspeto oleoso e brilhante após meses de uso contínuo.', 'Estilo retro exclusivo com teclas PBT premium de escrita macia.', 48000.90, 'assets/img/prods/prod_6a56b136262729.54441988.png', '2026-07-03 15:48:25', 8, 9),
(32, 'Teclado com Fio Padrão Verbatim USB Slim Premium – Preto', 'Teclado de Escritório', 'O teclado com fio da Verbatim é a escolha definitiva para escritórios, recepções, escolas e ambientes de trabalho corporativos que exigem máxima confiabilidade e resistência a longo prazo. Construído com um layout completo de tamanho padrão (100%), ele inclui o teclado numérico lateral dedicado, facilitando consideravelmente a inserção de dados em folhas de cálculo, contabilidade e rotinas administrativas diárias. As suas teclas possuem uma altura otimizada de perfil médio-baixo, proporcionando uma digitação extremamente suave, silenciosa e confortável que ajuda a evitar dores nos dedos após longas jornadas de trabalho. A carcaça plástica de alta densidade conta com um design resistente a pequenos salpicos de líquidos acidentais, possuindo canais interno.', 'Durabilidade e digitação confortável para o trabalho do dia a dia.', 12500.90, 'assets/img/prods/prod_6a56b089766746.88984338.png', '2026-07-03 15:50:14', 8, 9),
(46, 'Kit CFTV CP Plus DVR + 4 Câmeras HD + Disco Rígido e Acessórios', 'CFTV CP Plus DVR', 'Expanda a proteção do seu patrimônio com o Kit Completo de 4 Câmeras da CP Plus. Ideal para comércios de médio porte ou residências familiares, ele permite monitorar quatro pontos estratégicos em simultâneo (como salas, corredores, quintal e entrada principal). O sistema inclui o gravador central DVR, conectores BNC, fontes de alimentação e um disco rígido otimizado para gravação ininterrupta. As câmeras oferecem excelente nitidez e filtros inteligentes para entregar imagens claras tanto no brilho do dia quanto no escuro da noite.', NULL, 210000.90, 'assets/img/prod_6a52781028f1d9.24657552.png', '2026-07-09 23:05:32', 14, 11),
(47, 'Kit CFTV CP Plus DVR + 2 Câmeras HD + Disco Rígido 1TB e Cabos', 'CFTV CP Plus DVR', 'Tenha tudo o que precisa para proteger o seu imóvel com o Kit de Segurança CP Plus. Este combo completo inclui um gravador digital DVR de alta performance, uma câmera bullet para o exterior, uma câmera dome compacta para o interior, além de cabos e fontes de alimentação. O kit vem acompanhado de um disco rígido de 1TB de nível de vigilância, garantindo espaço suficiente para armazenar semanas de gravação contínua com total estabilidade e segurança dos dados.', NULL, 145000.90, 'assets/img/prod_6a5278ddf13a28.77619130.png', '2026-07-11 02:02:25', 11, 11),
(48, 'Câmera Hikvision PanoVu / Multi-Sensor', 'Hikvision', 'A Câmera Hikvision Multi-Sensor redefine o monitoramento de alta segurança. Este dispositivo avançado combina múltiplas lentes fixas para gerar uma visão panorâmica contínua e sem pontos cegos, integrada a uma câmera PTZ inferior de alta velocidade. Enquanto os sensores superiores mantêm os olhos em todo o ambiente simultaneamente, a lente móvel faz aproximações detalhadas e rastreia alvos em movimento automaticamente, oferecendo uma cobertura completa e inteligente para grandes empresas.', NULL, 390000.90, 'assets/img/prod_6a527915509974.73215673.png', '2026-07-11 02:52:00', 14, 11),
(49, 'Câmera Dome PTZ Sunba', 'Sunba', 'Maximize o seu campo de visão com a Câmera Profissional Sunba Dome PTZ. Equipada com motores de alta precisão, ela permite controlar o movimento horizontal, vertical e o zoom diretamente pelo seu smartphone ou central de monitoramento. O seu poderoso zoom ótico consegue aproximar rostos e matrículas de veículos à longa distância sem perder a qualidade da imagem. O design em cúpula facilita a fixação em paredes altas, sendo perfeita para cobrir grandes perímetros com um único equipamento.', NULL, 185000.90, 'assets/img/prod_6a527959d03c60.58754544.png', '2026-07-11 02:53:34', 16, 11),
(50, 'Câmera de Segurança Hikvision Bullet IP Full HD – Branca', 'Hikvision', 'A Câmera Hikvision Bullet é a escolha ideal para garantir a segurança de áreas externas da sua casa ou empresa. Com uma estrutura robusta e proteção contra chuva e poeira, ela monitora fachadas, portões e estacionamentos sob qualquer condição climática. As suas lentes capturam imagens em alta definição com excelente balanço de cores durante o dia e contam com um potente modo de visão noturna por infravermelho para registrar qualquer atividade suspeita na total escuridão.', NULL, 45000.90, 'assets/img/prod_6a5279a7d10178.77124783.png', '2026-07-11 03:02:33', 26, 11),
(51, 'Smartphone Samsung Galaxy S24 Ultra 512GB (Com S Pen) – Preto Titanium', 'Galaxy S24 Ultra', 'Conheça o ápice da tecnologia móvel com o Samsung Galaxy S24 Ultra. Construído com uma estrutura inovadora e ultrarresistente de Titânio, ele eleva o patamar de durabilidade e elegância. Explore o ecossistema Galaxy AI, que permite traduzir chamadas em tempo real, circular objetos na tela para pesquisar instantaneamente e editar fotos profissionalmente com apenas um toque. Sua câmera de 200 megapixels captura texturas e detalhes inacreditáveis, mesmo no escuro absoluto. A icônica S Pen vem embutida no corpo do aparelho, pronta para você assinar documentos, controlar apresentações e criar com precisão cirúrgica.', NULL, 1250000.90, 'assets/img/prod_6a543a2a71efc2.47954642.png', '2026-07-11 03:06:36', 2, 10),
(52, 'Tablet Xiaomi Redmi Pad SE 2 128GB Wi-Fi – Lilás', 'Pad SE 2', 'O Xiaomi Redmi Pad SE 2 é a escolha perfeita para quem procura um tablet versátil e com excelente custo-benefício. O seu ecrã grande com alta taxa de atualização oferece transições suaves e cores vivas, tornando a experiência de assistir vídeos, navegar pelas redes sociais ou ler livros digitais muito mais confortável. O seu corpo fino e elegante na cor lilás garante leveza no transporte, enquanto a bateria de longa duração permite que você aproveite as suas aplicações preferidas durante todo o dia sem a preocupação de correr para a tomada.', NULL, 210000.90, 'assets/img/prods/prod_6a51b3cbb6ae67.80255944.png', '2026-07-11 03:08:59', 8, 10),
(53, 'Tablet Xiaomi Redmi Pad SE 2 128GB Wi-Fi – Lilás', 'Pad SE 2', 'O Xiaomi Redmi Pad SE 2 é a escolha perfeita para quem procura um tablet versátil e com excelente custo-benefício. O seu ecrã grande com alta taxa de atualização oferece transições suaves e cores vivas, tornando a experiência de assistir vídeos, navegar pelas redes sociais ou ler livros digitais muito mais confortável. O seu corpo fino e elegante na cor lilás garante leveza no transporte, enquanto a bateria de longa duração permite que você aproveite as suas aplicações preferidas durante todo o dia sem a preocupação de correr para a tomada.', NULL, 210000.90, 'assets/img/prods/prod_6a51b3f7e69a57.69834463.png', '2026-07-11 03:09:43', 16, 10),
(54, 'Smartphone Samsung Galaxy M56 5G 128GB – Preto Cromo', 'Galaxy M56', 'Entre na era da ultravelocidade com o Samsung Galaxy M56 5G. Este smartphone combina perfeitamente um visual minimalista e elegante em tom escuro com a potência necessária para lidar com as suas tarefas simultâneas sem qualquer lentidão. A sua tela fluida oferece uma óptima experiência de navegação e jogabilidade responsiva. Na parte traseira, o renovado conjunto de câmeras permite registar fotos nítidas e cheias de detalhes em qualquer ambiente, enquanto o sistema inteligente de gestão de energia garante que a bateria dure o dia todo.', NULL, 320000.90, 'assets/img/prods/prod_6a51b420e363c7.59546166.png', '2026-07-11 03:10:24', 8, 10),
(55, 'Tablet Apple iPad Pro 11\" 256GB Wi-Fi – Cinza Espacial', 'iPad Pro', 'O Apple iPad Pro é a ferramenta definitiva para criadores de conteúdo, designers e profissionais exigentes. Equipado com os revolucionários chips de arquitetura própria da Apple, este dispositivo entrega um desempenho gráfico e de processamento que supera a maioria dos computadores portáteis do mercado. O ecrã Liquid Retina oferece uma precisão de cor e brilho incomparáveis para trabalhos profissionais de edição de vídeo e fotografia. O seu design icónico em alumínio cinza espacial conta ainda com o avançado sistema de câmeras e sensores Pro para experiências imersivas em realidade aumentada.', NULL, 1100000.90, 'assets/img/prods/prod_6a51b459eaab45.50370126.png', '2026-07-11 03:11:21', 10, 10),
(56, 'Tablet Xiaomi Redmi Pad 128GB Wi-Fi – Cinza Grafite', 'Pad 128GB', 'Descubra o equilíbrio perfeito entre sofisticação e preço justo com o Xiaomi Redmi Pad. O seu grande destaque é o ecrã com taxa de atualização de 90Hz, que proporciona uma navegação pelos menus e sites extremamente suave e agradável aos olhos. O acabamento premium em metal confere ao tablet uma durabilidade superior e um toque elegante. Conta ainda com um potente sistema de som composto por quatro altifalantes integrados com tecnologia Dolby Atmos, criando uma verdadeira atmosfera de cinema em suas mãos para músicas e filmes.', NULL, 230000.90, 'assets/img/prods/prod_6a51b4806cacb7.39226350.png', '2026-07-11 03:12:00', 8, 10),
(57, 'Tablet Honor Pad X8a 64GB Wi-Fi – Cinza Espacial (Com Capa)', 'Honor Pad', 'O Honor Pad X8a foi desenhado para quem procura praticidade e conforto no dia a dia. Com um ecrã vibrante de alta definição e tecnologia de proteção ocular, ele é ideal para longas sessões de leitura, estudos ou streaming de vídeos. O seu acabamento metálico minimalista na cor cinza garante elegância e durabilidade, enquanto a capa protetora inclusa serve também como suporte ajustável para deixar as suas mãos livres enquanto trabalha ou assiste aos seus conteúdos favoritos.', NULL, 175000.90, 'assets/img/prods/prod_6a51b4b2212f38.62293011.png', '2026-07-11 03:12:50', 20, 10),
(58, 'Tablet Gamer Lenovo Legion Tab 8.8\" 256GB – Grafite', 'Legion Tab', 'Desenvolvido especificamente para os entusiastas de jogos, o Lenovo Legion Tab coloca o desempenho de uma consola na palma das suas mãos. A sua tela compacta de 8.8 polegadas possui uma taxa de atualização ultra-rápida, garantindo que cada movimento em jogos competitivos aconteça com precisão instantânea. Conta com um sistema avançado de arrefecimento interno para manter a performance estável durante maratonas intensas de jogo e um design traseiro robusto com a identidade icónica da linha Legion.', '', 420000.90, 'assets/img/prods/prod_6a51b4f7deb1d6.92133107.png', '2026-07-11 03:13:59', 8, 10),
(59, 'Samsung Galaxy S24+ / S24', 'Galaxy S24+', 'O Samsung Galaxy S24+ abre as portas para o futuro dos smartphones com recursos nativos de inteligência artificial que otimizam desde a tradução instantânea de chamadas até a edição avançada de fotos. O seu design com acabamento acetinado e laterais retas oferece uma pegada firme e extremamente confortável. A tela Dynamic AMOLED 2X entrega cores ultrarrealistas e brilho intenso mesmo sob a luz direta do sol, complementada por uma bateria inteligente de longa duração que acompanha o seu ritmo o dia todo.', 'Inteligência artificial avançada e câmeras de alta resolução.', 790000.90, 'assets/img/prods/prod_6a51b52cdc6362.82423723.png', '2026-07-11 03:14:52', 8, 10),
(60, 'iPhone 15 Pro / 15 Pro Max', '15 Pro Max', 'Conheça o poder e a sofisticação do iPhone 15 Pro. Forjado em titânio aeroespacial de grau premium, este modelo traz uma leveza surpreendente e uma resistência sem precedentes. Equipado com a inovadora Dynamic Island e um ecrã Super Retina XDR incrivelmente brilhante, o aparelho redefine a interação diária. O seu sistema de câmeras profissional captura fotos em altíssima resolução com detalhes impressionantes mesmo em baixa luz, tudo impulsionado por um processador com desempenho gráfico revolucionário para aplicações e jogos pesados.', 'Estrutura robusta em titânio e o chip mais avançado da Apple.', 1250000.90, 'assets/img/prods/prod_6a51b580995099.95157510.png', '2026-07-11 03:16:16', 20, 10),
(61, 'Apple iPad Pro 11\" 128GB Wi-Fi – Cinza Escuro', 'iPad Pro 11', 'O Apple iPad Pro eleva as suas possibilidades a um nível corporativo e artístico avançado. Com o impressionante poder de processamento da arquitectura Apple, ele lida com edição de vídeo em alta resolução, modelagem 3D e multitarefa extrema sem qualquer esforço. O ecrã Liquid Retina oferece uma precisão de cor milimétrica e reflexos mínimos para garantir o melhor conforto visual. Perfeito para designers, engenheiros e criadores que precisam de máxima potência num formato ultrafino e totalmente portátil.', 'Desempenho e velocidade profissional para as suas ideias.', 980000.90, 'assets/img/prods/prod_6a51b5a6682d13.48321258.png', '2026-07-11 03:16:54', 6, 10),
(62, 'Samsung Galaxy S24 Ultra', 'Galaxy S24 Ultra', 'O Samsung Galaxy S24 Ultra representa o auge da tecnologia móvel atual. Protegido por uma armadura de titânio de alta resistência e vidro ultra-resistente contra riscos, este dispositivo foi feito para durar. A renomada caneta S Pen vem integrada ao corpo do aparelho, permitindo tomar notas, desenhar e controlar apresentações com precisão cirúrgica. O seu conjunto fotográfico impressiona com o sensor principal de 200MP e zoom ótico de longo alcance, transformando qualquer registo casual numa obra de arte profissional.', 'O topo de gama com câmera de 200MP e caneta S Pen embutida.', 1150000.90, 'assets/img/prods/prod_6a51b5d39da1c4.40907133.png', '2026-07-11 03:17:39', 10, 10),
(64, 'Samsung Galaxy M36 5G 256GB – Bronze', 'Galaxy M36', 'O Samsung Galaxy M36 5G foi desenhado para quem exige o máximo de autonomia e espaço. Equipado com uma das maiores baterias da categoria, ele foi feito para durar até dois dias longe da tomada. Seus 256GB de memória interna oferecem espaço de sobra para todos os seus aplicativos, fotos e vídeos em alta resolução. O design moderno com acabamento na cor bronze traz sofisticação, enquanto a tela com alta taxa de atualização garante transições suaves e jogabilidade responsiva. Conta ainda com um poderoso sistema de resfriamento interno e câmeras nítidas prontas para as redes sociais.', 'Performance fluida e bateria de longa duração para você nunca ficar na mão.', 340000.90, 'assets/img/prods/prod_6a527a126a60d9.35900101.png', '2026-07-11 17:14:58', 10, 10),
(65, 'Samsung Galaxy Tab S6 Lite 64GB Wi-Fi (Com Caneta S Pen) – Rosa', 'Galaxy Tab S6', 'Transforme a sua rotina de estudos e trabalho com o Samsung Galaxy Tab S6 Lite. Leve, fino e elegante na cor rosa, ele cabe facilmente na bolsa ou mochila. O grande diferencial está na caneta S Pen inclusa: com escrita precisa e baixa latência, ela permite fazer anotações à mão livre, desenhar, editar PDFs e dar asas à sua imaginação como se estivesse usando papel. Sua tela ampla combinada com o sistema de som duplo assinado pela AKG proporciona uma experiência de cinema para vídeos, séries e videoaulas.', 'Acompanha a caneta S Pen na caixa e possui acabamento premium.', 380000.90, 'assets/img/prods/prod_6a527a538447c4.67388877.png', '2026-07-11 17:16:03', 8, 10),
(66, 'Samsung Galaxy S20+ 128GB – Cinza Cósmico', 'Galaxy S20+', 'O Samsung Galaxy S20+ redefine o que um smartphone topo de linha pode fazer por suas fotos e vídeos. Revolucione suas capturas com a gravação de vídeo em resolução 8K, permitindo extrair fotos estáticas de altíssima qualidade direto dos seus vídeos. A tela Dynamic AMOLED 2X de 120Hz oferece uma navegação incrivelmente fluida e cores dignas de cinema. Com o poderoso processador premium e gerenciamento inteligente de energia, ele se adapta aos seus hábitos para economizar bateria e entregar desempenho máximo quando você mais precisa.', 'Tela Dynamic AMOLED 2X super fluida, gravação de vídeos em qualidade profissional 8K e zoom espacial avançado.', 420000.00, 'assets/img/prods/prod_6a527a95b70bc8.68304752.png', '2026-07-11 17:17:09', 10, 10),
(67, 'Samsung Galaxy A26 5G 128GB – Verde Claro', 'Galaxy A26', 'Prepare-se para o futuro com o novo Samsung Galaxy A26 5G. Navegue, jogue e assista aos seus conteúdos favoritos sem interrupções graças à velocidade da rede 5G e ao processador otimizado para o dia a dia. Sua tela vibrante oferece cores realistas e excelente visibilidade mesmo sob a luz do sol. Na traseira, o conjunto triplo de câmeras garante versatilidade: capture desde paisagens amplas até os mínimos detalhes com foco automático e inteligência computacional para aprimorar suas fotos noturnas. Tudo isso sustentado por uma bateria que acompanha o seu ritmo o dia todo.', 'Tela imersiva de alta fluidez e sistema de câmera tripla para fotos perfeitas.', 280000.90, 'assets/img/prods/prod_6a527ae0598880.63051356.png', '2026-07-11 17:18:24', 16, 10),
(68, 'Mini Projetor Portátil Eran Smart com Controle Remoto', 'Eran Smart', 'Transforme qualquer parede ou teto num verdadeiro cinema com o Mini Projetor Portátil Eran. O seu design cilíndrico inovador permite ajustar o ângulo de projeção facilmente sem a necessidade de suportes complexos. Equipado com um sistema inteligente integrado, ele conecta-se diretamente à sua rede Wi-Fi para reproduzir os seus aplicativos de streaming favoritos. Acompanha um controle remoto intuitivo para navegar pelos menus com total conforto, sendo a escolha perfeita para noites de filmes em família ou apresentações rápidas.', 'Cinema em qualquer lugar com rotação flexível e conexão smart.', 75000.90, 'assets/img/prods/prod_6a546ee399d9b0.39112923.png', '2026-07-13 04:51:47', 20, 3),
(69, 'Projetor Digital Home Cinema Full HD USB/HDMI', 'Home Cinema', 'O Projetor Home Cinema Digital entrega uma experiência imersiva de alta qualidade na sua sala de estar ou escritório. Com múltiplas portas de entrada como HDMI e USB, ele conecta-se facilmente a computadores, consolas de videojogos e descodificadores de TV. A sua lâmpada LED de longa duração oferece excelente brilho e contraste, garantindo cores vivas e textos nítidos mesmo em ambientes com luz indireta. Conta ainda com botões de controle rápido na parte superior para ajustes rápidos sem complicações.', 'Projeção nítida e brilhante de alta definição para a sua sala.', 115000.90, 'assets/img/prods/prod_6a546f3410c064.48953118.png', '2026-07-13 04:53:08', 20, 3),
(70, 'Monitor Computador Slim Borderless Full HD IPS 24', 'Slim Borderless', 'Eleve o nível do seu espaço de trabalho ou setup de jogos com este Monitor Slim de 24 polegadas. Graças à tecnologia de painel IPS, ele exibe imagens com cores incrivelmente realistas e ângulos de visão amplos de até 178 graus. O design com bordas ultrafinas minimiza as distrações visuais e o torna ideal para configurações de múltiplos monitores lado a lado. A sua base estável e elegante garante firmeza na mesa, enquanto o filtro de luz azul integrado protege os seus olhos durante longas horas de uso.', 'Cores realistas e bordas ultrafinas para máxima produtividade.', 95000.90, 'assets/img/prods/prod_6a546fb80c2ef7.28166702.png', '2026-07-13 04:55:20', 20, 3),
(71, 'Monitor LED LG Full HD com Base ArcLine e Conexão HDMI', 'LG Multimídia', 'Unindo elegância e desempenho, o Monitor LED LG traz a renomada fidelidade de imagem da marca para as suas tarefas diárias. Destaca-se pela sua sofisticada base curva ArcLine, que acrescenta um toque moderno a qualquer ambiente de trabalho. Perfeito para edição de documentos, navegação web e entretenimento multimídia, ele oferece um gerenciamento de cores inteligente e tempos de resposta rápidos para evitar rastros na tela em cenas de movimento, garantindo uma transição visual extremamente suave.', 'Visualização confortável com a qualidade de imagem da tecnologia LG.', 110000.90, 'assets/img/prods/prod_6a54701326bd19.56961929.png', '2026-07-13 04:56:51', 20, 3),
(72, 'Smart TV Roku LED com Sistema de Streaming Integrado', 'Smart TV', 'Desfrute de uma experiência de entretenimento completa e sem complicações com a Smart TV Roku. A sua tela de alta definição proporciona imagens vibrantes e ótimo contraste para os seus filmes, séries e partidas de futebol.\r\nO grande diferencial está na plataforma Roku integrada: uma interface extremamente limpa, rápida e intuitiva que organiza os seus canais de streaming, dispositivos conectados e TV aberta numa única tela inicial. Ideal para quem procura praticidade e velocidade ao navegar pelos conteúdos favoritos.', 'Todo o entretenimento com o sistema mais rápido e fácil de usar.', 195000.90, 'assets/img/prods/prod_6a54705bbc9391.20993319.png', '2026-07-13 04:58:03', 8, 3),
(73, 'Switch MikroTik Cloud Router CRS309-1G-8S+IN 8 Portas SFP+ 10Gbps', 'Router Switch MikroTik', 'O MikroTik CRS309-1G-8S+IN é um switch de alta performance compacto, ideal para empresas que exigem velocidade extrema e estabilidade na infraestrutura de rede. Ele conta com 8 portas SFP+ que suportam conexões de até 10 Gbps por canal, além de uma porta Gigabit Ethernet para gerenciamento com suporte a PoE. Com sua refrigeração passiva (totalmente silencioso) e carcaça metálica robusta, ele entrega um poder de processamento incrível com opções de boot duplo (RouterOS ou SwitchOS).', 'Switch profissional com 8 portas SFP+ para redes de fibra óptica.', 245000.90, 'assets/img/prods/prod_6a558104794ef0.51070237.png', '2026-07-14 00:21:24', 8, 7),
(74, 'Hub Adaptador Ugreen com 4 Portas USB 3.0 Extensor – Preto', 'Hub USB Ugreen', 'O Hub USB 3.0 da Ugreen é a solução ideal para expandir instantaneamente a conectividade do seu computador ou notebook. Construído com materiais de alta durabilidade e um cabo reforçado, ele transforma uma única porta USB numa estação com quatro entradas adicionais. Perfeito para conectar ratos, teclados, pen drives e discos externos simultaneamente, garantindo taxas de transferência de dados supervelozes de até 5 Gbps sem lentidão.', 'Multiplique as portas USB do seu PC ou portátil com alta velocidade.', 14500.90, 'assets/img/prods/prod_6a5581a5583e37.81910731.png', '2026-07-14 00:24:05', 8, 7),
(75, 'Cabo Adaptador Micro-USB para USB Fêmea OTG Compacto', 'Adaptador Micro-USB', 'Dê uma nova vida e novas funcionalidades aos seus aparelhos com este prático Cabo Adaptador Micro-USB OTG. Extremamente leve e portátil, ele transforma a entrada de carregamento antiga do seu celular ou tablet numa porta USB padrão. Com ele, você pode conectar mouses para navegar na tela, teclados para digitar textos longos de forma confortável ou ler arquivos diretamente de um pen drive sem precisar ligar o aparelho a um computador.', 'Conecte acessórios USB em tablets e celulares com entrada Micro-USB.', 3500.90, 'assets/img/prods/prod_6a5581f994fc77.54668108.png', '2026-07-14 00:25:29', 8, 7),
(76, 'Carregador Portátil Power Bank 20.000mAh com Cabos Embutidos e Display', 'Power Bank Portátil', 'Nunca mais fique sem bateria na rua com este Power Bank de alta capacidade de 20.000mAh. Capaz de dar múltiplas cargas completas no seu smartphone, ele destaca-se pela conveniência de possuir cabos de carregamento embutidos na própria estrutura (incluindo USB-C e Lightning), eliminando a necessidade de andar com fios enrolados na mala. O display digital LED frontal exibe com precisão exata a porcentagem de energia restante na bateria externa, para você saber exatamente quando recarregá-la.', 'Bateria gigante com cabos integrados e indicador digital de carga.', 32000.90, 'assets/img/prods/prod_6a558267df4728.52139861.png', '2026-07-14 00:27:19', 8, 7),
(77, 'Cabo de Rede Ethernet Patch Cord RJ45 Cat6 Homologado – Cinza', 'Ethernet RJ45', 'O Cabo de Rede Ethernet Cat6 garante a velocidade máxima contratada da sua internet sem as oscilações e perdas comuns do sinal Wi-Fi. Equipado com conectores RJ45 blindados e travas resistentes, ele é ideal para interligar computadores, consolas de videojogos, smart TVs e roteadores à rede.\r\nA sua malha interna reduz drasticamente as interferências externas, assegurando uma transmissão de dados limpa, rápida e com a menor latência possível para jogos online e chamadas de vídeo de alta qualidade.', 'Velocidade máxima e estabilidade para computadores.', 4500.90, 'assets/img/prods/prod_6a5582cf3ac5e7.53868103.png', '2026-07-14 00:29:03', 8, 7),
(78, 'Hub Adaptador USB-C Ugreen com 4 Portas USB 3.0 – Alumínio', 'Hub USB-C Ugreen', 'Se o seu notebook moderno tem poucas entradas, o Hub USB-C da Ugreen é o acessório que faltava. Construído com uma elegante carcaça de alumínio que ajuda a dissipar o calor, ele expande uma única porta USB-C em quatro entradas USB 3.0 de alta velocidade. Perfeito para conectar pen drives, discos rígidos externos, mouses e impressoras simultaneamente. Possui também uma entrada de alimentação auxiliar de 5V para garantir energia estável mesmo ao usar múltiplos acessórios pesados ao mesmo tempo.', 'Multiplique as conexões do seu portátil com velocidade e estilo.', 18500.90, 'assets/img/prods/prod_6a5583ae1cd742.03979786.png', '2026-07-14 00:32:46', 8, 7),
(79, 'Modem e Roteador Wi-Fi Motorola Residencial Banda Larga – Preto', 'Router Motorola', 'O Modem Roteador Motorola foi desenvolvido para entregar estabilidade máxima à sua rede de internet residencial ou de pequenos escritórios. Com um design vertical elegante que otimiza o espaço e melhora a dissipação de calor, ele combina as funções de recepção de sinal e distribuição de sinal Wi-Fi num único aparelho. Possui indicadores LED frontais de fácil leitura para acompanhar o status da conexão, além de portas traseiras para ligar dispositivos diretamente via cabo de rede com latência mínima.', 'Conexão estável e gerenciamento inteligente de internet banda larga.', 42000.90, 'assets/img/prods/prod_6a5584061e9807.82019446.png', '2026-07-14 00:34:14', 8, 7),
(80, 'Roteador Wireless Intelbras com Duas Antenas de Alto Ganho – Preto', 'Wi-Fi Intelbras', 'Garanta uma cobertura de internet ampla e sem pontos cegos com o Roteador Wi-Fi Intelbras. Equipado com duas antenas externas de alto ganho, ele propaga o sinal com facilidade através de paredes, garantindo que o seu smartphone, smart TV e computador fiquem conectados mesmo em divisões distantes. A sua configuração é simples e rápida, permitindo gerenciar a rede, criar acessos exclusivos para visitas e controlar os horários de navegação das crianças com total praticidade.', 'Wi-Fi de longo alcance e sinal forte para toda a sua casa.', 25000.90, 'assets/img/prods/prod_6a55844c2fb604.38578917.png', '2026-07-14 00:35:24', 8, 7),
(81, 'Patch Panel de Rede Panduit 48 Portas Cat5e/Cat6 para Rack', 'Patch Panel Panduit', 'O Patch Panel Panduit de 48 portas é a solução indispensável para organizar e identificar o cabeamento estruturado de grandes redes de dados. Projetado para fixação padrão em racks de 19 polegadas, ele centraliza a terminação de até 48 cabos de rede (Cat5e ou Cat6), facilitando manutenções, mudanças de layout e testes de conectividade. A sua estrutura metálica altamente resistente garante durabilidade e firmeza nas conexões, evitando desconexões acidentais no coração do seu CPD.', 'Organização profissional para cabos de rede em racks de servidores.', 85000.90, 'assets/img/prods/prod_6a558521319ed3.26032655.png', '2026-07-14 00:38:57', 8, 7),
(82, 'Switch Gigabit D-Link 24 Portas Easy Smart (DGS-1100-24) – Cinza e Preto', 'Switch D-Link', 'O Switch D-Link de 24 portas é a espinha dorsal perfeita para a infraestrutura de rede da sua empresa ou escritório de grande porte. Oferecendo velocidades de transferência Gigabit em todas as portas, ele permite conectar dezenas de computadores, impressoras de rede, servidores e câmeras IP com estabilidade absoluta e sem gargalos.', 'Expansão de rede estável com 24 portas Gigabit de alta velocidade.', 135000.90, 'assets/img/prods/prod_6a55873d0963b5.99876521.png', '2026-07-14 00:47:57', 8, 7),
(83, 'Cabo Adaptador Trançado USB-C para USB 3.0 Fêmea OTG', 'USB-C To USB-A', 'Elimine a incompatibilidade de conexões com este prático Cabo Adaptador USB-C para USB-A. Com um acabamento premium em nylon trançado de alta resistência e carcaça de alumínio, ele permite ligar pen drives, mouses, teclados ou discos rígidos externos diretamente no seu smartphone, tablet ou portáteis de última geração. Suporta tecnologia OTG (On-The-Go) e transferências de dados em alta velocidade USB 3.0, garantindo o envio rápido de arquivos pesados sem interrupções.', 'Conecte pen drives e acessórios USB antigos em aparelhos modernos.', 7500.90, 'assets/img/prods/prod_6a5587980c25b4.00639202.png', '2026-07-14 00:49:28', 8, 7),
(84, 'Estação de Carregamento de Mesa Cellonic 5x USB-C + 1x USB-A', 'Multi-Portas Cellonic', 'Mantenha todos os seus aparelhos carregados e organize a sua mesa de trabalho com a Estação Multimídia Cellonic. Este carregador potente conta com cinco portas USB-C e uma porta USB-A de carregamento rápido, permitindo alimentar smartphones, tablets, fones de ouvido e relógios inteligentes ao mesmo tempo a partir de um único cabo de tomada. O seu circuito inteligente gerencia a distribuição de energia de forma segura, evitando superaquecimentos e protegendo a vida útil das baterias dos seus dispositivos.', 'Carregue até 6 dispositivos simultaneamente numa única tomada.', 28000.90, 'assets/img/prods/prod_6a558852d0eae9.68807226.png', '2026-07-14 00:52:35', 8, 7),
(85, 'Roteador Wireless Northwest 300M com Duas Antenas – Branco', 'Wi-Fi Northwest', 'O Roteador Wi-Fi Northwest 300M oferece um sinal de internet confiável e de boa qualidade para a sua rotina diária em casa ou no escritório. Equipado com duas antenas externas que melhoram a distribuição do sinal pelas divisões, ele opera na velocidade de 300 Mbps, ideal para navegar em sites, estudar e assistir a vídeos. Possui painel frontal com LEDs indicadores de status (SYS, WLAN, WAN, LAN) e portas traseiras para conexões cabeadas rápidas e seguras.', 'Internet estável com duas antenas para melhor cobertura residencial.', 19000.90, 'assets/img/prods/prod_6a5588d24f37e3.11413174.png', '2026-07-14 00:54:42', 8, 7),
(86, 'Switch MikroTik Cloud Router CRS309-1G-8S+IN 8 Portas SFP+ 10Gbps', 'Router Switch MikroTik', 'O MikroTik CRS309-1G-8S+IN é um switch de alta performance compacto, ideal para empresas que exigem velocidade extrema e estabilidade na infraestrutura de rede. Ele conta com 8 portas SFP+ que suportam conexões de até 10 Gbps por canal, além de uma porta Gigabit Ethernet para gerenciamento com suporte a PoE. Com sua refrigeração passiva (totalmente silencioso) e carcaça metálica robusta, ele entrega um poder de processamento incrível com opções de boot duplo (RouterOS ou SwitchOS).', 'Switch profissional com 8 portas SFP+ para redes de fibra óptica.', 245000.90, 'assets/img/prods/prod_6a56a02fb17f30.17268062.png', '2026-07-14 00:56:09', 8, 7),
(87, 'Rato Gamer Profissional Attack Shark Sem Fios de Alta Precisão', 'Attack Shark Pro', 'Desenvolvido para gamers exigentes e utilizadores que necessitam de máxima velocidade, o Rato Attack Shark Pro oferece uma experiência de rastreamento impecável. Com um sensor ótico topo de gama, ele garante uma precisão cirúrgica pixel por pixel, ideal para jogos de tiro (FPS) ou trabalhos detalhados de design gráfico e modelagem 3D. O seu corpo possui uma engenharia ultra-leve e formato simétrico confortável, permitindo movimentos extremamente rápidos com o mínimo de esforço. Na parte superior, os botões seletores de DPI permitem alternar a sensibilidade do cursor instantaneamente com base na sua necessidade atual, acompanhados por um discreto indicador LED de status. A sua construção robusta conta com switches de alta dur.', 'Alta precisão e ultra-leveza para jogabilidade de nível profissional.', 35000.90, 'assets/img/prods/prod_6a56c1bb5e0779.06950715.png', '2026-07-14 23:09:47', 8, 8),
(88, 'Rato Sem Fios Recarregável 6D-Mouse com Indicador de Bateria', '6D-Mouse', 'Eleve o controle do seu fluxo de trabalho com o Rato Sem Fios 6D-Mouse. Este modelo destaca-se pelo seu design moderno que inclui um indicador de carga em LED azul brilhante no topo, permitindo saber exatamente o nível de energia restante para que nunca seja apanhado de surpresa no meio de uma tarefa importante. Ele possui uma bateria interna de longa duração recarregável via cabo, eliminando de vez a necessidade e o custo constante de comprar pilhas descartáveis. Conta com uma asa lateral texturizada para um encaixe perfeito do polegar, garantindo total firmeza nos movimentos. Além disso, os seus botões de atalho integrados na lateral facilitam o avanço e retrocesso de páginas web, otimizando o seu tempo de navegação na internet ou em sistemas int.', 'Conectividade estável com indicador de bateria LED integrado.', 16500.90, 'assets/img/prods/prod_6a56c21095fb46.61657594.png', '2026-07-14 23:11:12', 8, 8),
(89, 'Rato Sem Fios Ergonómico Sanwa Supply com Scroll Lateral', 'Sanwa Supply', 'O Rato Sem Fios Sanwa Supply foi projetado especificamente para profissionais e estudantes que passam longas horas em frente ao computador e precisam de alívio na tensão do pulso. Com um formato anatómico que apoia a palma da mão de forma natural, ele reduz drasticamente a fadiga muscular. O seu grande diferencial é o segundo scroll texturizado na lateral, ideal para navegar horizontalmente por tabelas extensas do Excel, linhas de código ou edições de vídeo no Premiere. Equipado com cliques extremamente silenciosos, ele permite trabalhar em ambientes calmos sem distrações. A conexão é feita de forma instantânea através do mini recetor USB incluso, operando em uma frequência estável que elimina falhas ou atrasos na resposta.', 'Conforto ergonómico avançado com scroll lateral para produtividade.', 24000.90, 'assets/img/prods/prod_6a56c262c9eab9.99105933.png', '2026-07-14 23:12:34', 8, 8),
(90, 'Portátil Dell XPS Premium – Ecrã Infinito e Acabamento em Fibra de Carbono', 'Dell XPS InfinityEdge', 'O Dell XPS é amplamente reconhecido como um dos melhores portáteis do mundo. O seu ecrã InfinityEdge elimina quase por completo as molduras, oferecendo uma experiência imersiva com cores profundas e excelente brilho para trabalhar mesmo sob luz solar direta. No interior, a zona de descanso dos pulsos é revestida com fibra de carbono inspirada na indústria aeroespacial, o que torna o toque suave, térmico (não aquece a tua mão) e incrivelmente resistente. Compacto no tamanho mas gigante no desempenho, este portátil de elite é perfeito para executivos e utilizadores exigentes que procuram o melhor em tecnologia, design e portabilidade.', 'O topo de gama da Dell com ecrã infinito ultraluminoso e materiais aeroespaciais.', 590000.90, 'assets/img/prods/prod_6a56f9fb8948a0.73224132.png', '2026-07-15 03:09:47', 8, 1),
(91, 'Portátil ASUS ProArt StudioBook – Edição Profissional para Criadores', 'ASUS ProArt', 'Uma verdadeira estação de trabalho portátil construída especificamente para designers gráficos, arquitetos, engenheiros e editores de vídeo. O ASUS ProArt traz um ecrã profissional com calibração de cor de fábrica rigorosa (cobertura total de gama de cores), garantindo que o que vês no ecrã é exatamente o que será impresso ou renderizado. O chassis possui saídas de refrigeração reforçadas nas laterais e traseira, permitindo que os processadores gráficos e de computação rodem na velocidade máxima sem perdas de desempenho. Conta com conexões robustas para múltiplos monitores externos e cartões de memória de alta velocidade, sendo a escolha definitiva para quem não pode comprometer a performance.', 'Potência extrema e ecrã de alta fidelidade de cor para edição de vídeo, imagem e engenharia.', 680000.90, 'assets/img/prods/prod_6a56fa38482bf8.59996064.png', '2026-07-15 03:10:48', 8, 1),
(92, 'Portátil Lenovo IdeaPad Home & Office – Platinum Grey', 'Lenovo IdeaPad Slim', 'A linha IdeaPad da Lenovo foca-se na experiência prática do utilizador. Este modelo destaca-se pelo posicionamento estratégico dos seus altifalantes logo acima do teclado, direcionando o som diretamente para ti para uma clareza cristalina em chamadas do Zoom ou vídeos. O teclado é amplamente elogiado pela sua ergonomia, com um formato de teclas ligeiramente curvado que reduz erros de digitação e o cansaço nos dedos. O seu acabamento premium em cinza platina confere-lhe um aspeto corporativo e limpo, ideal tanto para reuniões de negócios como para a sala de aulas.', 'Desempenho ágil, excelente som frontal e o teclado ergonómico líder de mercado da Lenovo.', 360000.90, 'assets/img/prods/prod_6a56fa87db4037.79192461.png', '2026-07-15 03:12:07', 8, 1),
(93, 'Portátil ASUS VivoBook Daily Task', 'ASUS VivoBook Classic', 'O ASUS VivoBook é o companheiro ideal para as tarefas do dia a dia, equilibrando perfeitamente durabilidade e estilo. Com um acabamento fosco em tom cinza-escuro texturizado, ele resiste melhor a dedadas e riscos do que os modelos convencionais. O seu ecrã vem calibrado com tecnologia de proteção ocular e grande ângulo de visão, tornando as sessões longas de estudo ou trabalho de escritório muito mais confortáveis. A sua dobradiça robusta permite uma abertura suave, enquanto o chassis fino desliza facilmente em qualquer mochila. É uma máquina fiável, fria e extremamente eficiente para navegação web, videoconferências e edição de documentos.', 'A fiabilidade clássica da ASUS num portátil leve com ecrã antirreflexo de grande ângulo.', 345000.90, 'assets/img/prods/prod_6a56fac58b6dd0.55461495.png', '2026-07-15 03:13:09', 8, 1),
(94, 'Portátil Ultrabook Intel Sleek Edition – Prata e Windows 11', 'Ultrabook Slim (Windows 11)', 'Projetado para profissionais em movimento e estudantes modernos, este ultrabook combina elegância e alto rendimento num corpo em liga leve cinza-prateada. O grande destaque é o seu ecrã vibrante de alta definição com margens infinitas (ultra-slim bezels), maximizando o espaço de visualização para PDFs, folhas de cálculo ou séries. Vem equipado de fábrica com o Windows 11, oferecendo uma interface fluida, segura e otimizada para poupança de bateria. O teclado ergonómico de perfil baixo inclui um teclado numérico dedicado à direita, ideal para quem trabalha com contabilidade ou programação, e o trackpad generoso garante gestos precisos sem a necessidade de um rato externo.', 'Perfil ultra fino, ecrã com molduras mínimas e teclado completo para máxima produtividade.', 310000.90, 'assets/img/prods/prod_6a56fb1cc3f169.00708511.png', '2026-07-15 03:14:36', 8, 1),
(95, 'Portátil Produtividade Pro 16\" IPS – Inclui 1 Ano de Office 365', 'Office Pro 16', 'Se precisas de espaço de ecrã para trabalhar sem cansar a vista, este modelo de 16 polegadas com resolução 1920x1200 (FHD+) é a escolha certeira. O painel com tecnologia IPS e fidelidade de cor sRGB assegura cores reais e um contraste excelente para ler relatórios, programar ou gerir bases de dados. Para acrescentar ainda mais valor ao teu negócio ou estudos, este portátil já traz incluída uma licença oficial de um ano do Microsoft Office 365 (Word, Excel, PowerPoint, OneNote, OneDrive, Outlook e Teams), permitindo-te começar a produzir logo no primeiro minuto após abrir a caixa. O design em cinza minimalista com teclado numérico dedicado adapta-se perfeitamente a qualquer ambiente profissional.', 'Ecrã gigante de 16 polegadas FHD+, painel IPS e licença oficial do Office incluída.', 330000.90, 'assets/img/prods/prod_6a56fc04a1a0a3.12425942.png', '2026-07-15 03:18:28', 8, 1),
(96, 'Portátil Produtividade Pro 16\" IPS – Inclui 1 Ano de Office 365', 'Portátil Office Pro', 'Se precisas de espaço de ecrã para trabalhar sem cansar a vista, este modelo de 16 polegadas com resolução 1920x1200 (FHD+) é a escolha certeira. O painel com tecnologia IPS e fidelidade de cor sRGB assegura cores reais e um contraste excelente para ler relatórios, programar ou gerir bases de dados. Para acrescentar ainda mais valor ao teu negócio ou estudos, este portátil já traz incluída uma licença oficial de um ano do Microsoft Office 365 (Word, Excel, PowerPoint, OneNote, OneDrive, Outlook e Teams), permitindo-te começar a produzir logo no primeiro minuto após abrir a caixa. O design em cinza minimalista com teclado numérico dedicado adapta-se perfeitamente a qualquer ambiente profissional.', 'Ecrã gigante de 16 polegadas FHD+, painel IPS e licença oficial do Office incluída.', 330000.90, 'assets/img/prods/prod_6a56fd7564eb37.49773892.png', '2026-07-15 03:24:37', 8, 1),
(97, 'Portátil Gamer Lenovo LOQ Intel Core i5 – NVIDIA RTX 2050', 'Lenovo LOQ', 'Entra no mundo do alto desempenho com o Lenovo LOQ. Equipado com um processador Intel Core i5 de 8 núcleos e uma placa gráfica dedicada NVIDIA GeForce RTX 2050, este portátil está preparado para rodar jogos modernos com física realista (Ray-Tracing) e acelerar programas de renderização em engenharia ou modelação 3D. O seu sistema de ventilação dupla de alta pressão mantém o equipamento frio mesmo durante horas seguidas de uso intenso. O teclado possui resposta tátil ultrarrápida e o ecrã com alta taxa de atualização garante que nunca vais perder nenhum detalhe da ação, eliminando rastos de imagem.', 'Ray-Tracing e refrigeração térmica avançada para jogos e projetos 3D pesados.', 520000.90, 'assets/img/prods/prod_6a56fdb9eee566.46271056.png', '2026-07-15 03:25:45', 8, 1),
(98, 'Computador Desktop Completo Dell OptiPlex Intel Core', 'Dell OptiPlex Desktop', 'A linha OptiPlex da Dell é mundialmente reconhecida pela sua longevidade e engenharia de topo, e este combo completo traz exatamente essa robustez para a sua empresa ou home office. Contando com uma torre slim elegante que apresenta uma grelha frontal prateada para otimizar o fluxo de ar, o computador permanece silencioso e refrigerado mesmo sob uso intenso de várias horas seguidas. O sistema vem equipado com o sistema operativo Windows instalado, proporcionando uma interface familiar, segura e pronta para o trabalho. O monitor que acompanha o kit oferece imagens nítidas com tecnologia antirreflexo, ideal para proteger os olhos durante longas jornadas de leitura, digitação ou estudos.', 'Máxima segurança, durabilidade Dell e alta performance para tarefas diárias e estudos.', 260000.90, 'assets/img/prods/prod_6a5700900acaa3.01912585.png', '2026-07-15 03:37:52', 8, 2),
(99, 'Computador Desktop Completo Acer Aspire Multimedia Tower', 'Acer Aspire Tower', 'Se procura um computador central para toda a família, capaz de gerir desde os trabalhos escolares dos miúdos até ao entretenimento e tarefas financeiras da casa, o Acer Aspire Tower é a escolha ideal. O seu gabinete de tamanho padrão oferece uma excelente circulação de ar interna e vem equipado com um painel frontal completo, incluindo leitor de cartões de memória e conexões de áudio de fácil acesso para auscultadores. O grande diferencial deste conjunto é o seu monitor panorâmico de alta resolução com molduras finas, que entrega uma experiência visual imersiva e de alta qualidade para assistir a vídeos, séries ou navegar em portais de notícias.', 'Computador Desktop Completo Acer Aspire Multimedia Tower', 285000.90, 'assets/img/prods/prod_6a5700e378ac24.07486843.png', '2026-07-15 03:39:15', 8, 2),
(100, 'Desktop HP Pavilion Slim Edition – Acabamento Premium Glossy', 'HP Pavilion Slim', 'O HP Pavilion Slim traz um toque de sofisticação e modernidade para qualquer ambiente graças ao seu painel frontal com acabamento em black piano brilhante (glossy) e detalhes cromados. Este computador foi pensado para utilizadores que apreciam o design sem abdicar de um desempenho ágil para multitarefas. A sua torre compacta esconde um hardware otimizado para reprodução de conteúdos em alta fidelidade, edição leve de fotos e navegação rápida na internet com dezenas de abas abertas. O monitor que acompanha o conjunto possui um design minimalista com uma base elegante que combina perfeitamente com a torre. Para além disso, o teclado e o rato inclusos foram desenhados com um perfil mais baixo e moderno.', 'Design sofisticado com acabamento espelhado e excelente desempenho multimédia.', 295000.90, 'assets/img/prods/prod_6a5701315e07f4.29992563.png', '2026-07-15 03:40:33', 8, 2),
(101, 'Desktop Completo WorkStation Essential', 'Gabinete Torre Preto', 'Uma solução robusta e direta ao ponto, projetada para quem precisa de um computador focado em desempenho administrativo, estudos e rotinas de escritório sem complicações. A sua torre preta de tamanho padrão destaca-se pela excelente acessibilidade, trazendo portas USB de alta velocidade localizadas estrategicamente na parte frontal do painel para facilitar a ligação de pens e discos externos. O monitor LED incluído oferece excelente fidelidade de imagem para a leitura de documentos e navegação na web. Acompanhado por um rato ótico preciso e um teclado confortável de tamanho completo com teclado numérico integrado, este setup garante horas de trabalho contínuo com excelente refrigeração.', 'Sistema prático com portas frontais rápidas e monitor nítido para produtividade diária.', 210000.90, 'assets/img/prods/prod_6a5701853e39d0.68750695.png', '2026-07-15 03:41:57', 8, 2),
(102, 'Desktop HP Pavilion Slim Edition – Acabamento Glossy Premium', 'HP Pavilion Glossy Slim', 'O HP Pavilion Slim eleva o nível estético do ambiente de trabalho graças ao seu painel frontal refinado com acabamento brilhante em estilo black piano e elegantes detalhes cromados. A sua estrutura de perfil fino (slim) ocupa metade do espaço de uma torre convencional, tornando-o perfeito para recepções, consultórios ou secretárias domésticas com espaço limitado. Além do forte apelo visual, o hardware foi calibrado para multitarefas rápidas e exibição de conteúdos multimédia em alta definição. O monitor que acompanha o kit apresenta um design minimalista com uma base ergonómica flutuante, combinando perfeitamente com o teclado e o rato de perfil baixo inclusos.', 'Design elegante com acabamento brilhante e perfil fino ideal para recepções modernas.', 295000.90, 'assets/img/prods/prod_6a5701cb3dc9f3.06664021.png', '2026-07-15 03:43:07', 8, 2),
(103, 'Combo Desktop Design Pro Intel SFF – Acabamento Metálico e Monitor Apple', 'Work Mac-Alu Pro', 'Criado sob medida para profissionais exigentes, criadores de conteúdo e escritórios que priorizam a máxima fidelidade visual, este setup exclusivo combina uma torre de alto desempenho da HP em tom prata metálico com um monitor premium da Apple. O gabinete oferece um fluxo de ar avançado e silencioso, garantindo que o processador funcione na temperatura ideal durante a execução de tarefas pesadas ou multitarefas intensas. O grande destaque é o monitor Apple com base de alumínio minimalista, conhecido pela sua calibração de cor superior e nitidez impressionante, o que reduz drasticamente a fadiga ocular em jornadas longas. O combo vem completo com um teclado estendido e rato erg.', 'Desempenho profissional de escritório com a qualidade de ecrã premium da Apple.', 340000.90, 'assets/img/prods/prod_6a570220daf074.57310068.png', '2026-07-15 03:44:32', 8, 2);
INSERT INTO `produtos` (`id`, `nome`, `nome_curto`, `descricao`, `descricao_curta`, `preco`, `imagem`, `criado_em`, `estoque`, `categoria_id`) VALUES
(104, 'Computador Desktop Completo Dell OptiPlex Intel Core', 'Desktop Dell OptiPlex', 'A linha OptiPlex da Dell é sinónimo mundial de longevidade, segurança e engenharia de precisão. Este conjunto completo traz um gabinete compacto com uma marcante grelha frontal texturizada que otimiza a ventilação e impede a acumulação de poeira nos componentes internos. O sistema vem com o ambiente de trabalho Windows pré-configurado, garantindo total compatibilidade com os principais softwares de gestão e segurança do mercado. O monitor que integra o kit possui painel antirreflexo de alta definição, oferecendo conforto visual excecional para leitura de relatórios e digitação de dados. Completo com rato e teclado originais de alta durabilidade.', 'Engenharia de topo da Dell e segurança avançada para o ambiente corporativo e doméstico.', 260000.90, 'assets/img/prods/prod_6a57026db7ba88.51228364.png', '2026-07-15 03:45:49', 8, 2),
(105, 'Computador Desktop Completo HP Pro SFF Intel Core i5', 'HP Pro Small Form Factor', 'Desenvolvido para atender às exigências do ambiente corporativo e de escritórios modernos, este computador completo da HP oferece o equilíbrio perfeito entre alta produtividade e economia de espaço. O seu gabinete em formato SFF (Small Form Factor) pode ser utilizado tanto na vertical quanto na horizontal sob a mesa, integrando-se discretamente em qualquer espaço de trabalho. Equipado com um processador Intel Core i5, o sistema garante uma navegação fluida em múltiplos sistemas de gestão, folhas de cálculo complexas e softwares administrativos sem apresentar lentidão. O conjunto acompanha um monitor LED de alta definição com cores vivas e excelente ângulo de visão, além de um rato e um teclado erg.', 'Desempenho profissional e fiável num formato compacto que poupa espaço na sua secretária.', 245000.90, 'assets/img/prods/prod_6a5702b9efa778.69420536.png', '2026-07-15 03:47:05', 8, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes_servico`
--

CREATE TABLE `solicitacoes_servico` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `tipo_servico` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Índices de tabela `solicitacoes_servico`
--
ALTER TABLE `solicitacoes_servico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de tabela `solicitacoes_servico`
--
ALTER TABLE `solicitacoes_servico`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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

--
-- Restrições para tabelas `solicitacoes_servico`
--
ALTER TABLE `solicitacoes_servico`
  ADD CONSTRAINT `solicitacoes_servico_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
