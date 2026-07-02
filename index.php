<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Home - ' . $nomeProjeto;
$navbarMode = 'full';
$baseUrl = '';

// Variáveis para o navbar
$usuarioNome = $_SESSION['usuario_nome'] ?? null;
$qtdCarrinho = 0;

// ============================================
// Queries ao Banco de Dados
// ============================================

// 1. Slider (Carrega os 3 mais recentes do banco)
try {
    $stmt = $pdo->query("SELECT id, nome, preco, imagem FROM produtos ORDER BY criado_em DESC LIMIT 3");
    $dbSliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dbSliders = [];
}

// 2. Grid de 6 Cards (Carrega produtos do banco para preencher os cards)
try {
    $stmt = $pdo->query("
        SELECT p.id, p.nome, p.imagem, c.nome as cat_nome 
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        ORDER BY p.criado_em DESC LIMIT 6
    ");
    $dbGrid = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dbGrid = [];
}

// 3. Banners (Banner 1 e Banner 2)
try {
    $stmt = $pdo->query("SELECT id, nome, preco, imagem FROM produtos ORDER BY preco DESC LIMIT 2");
    $dbBanners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dbBanners = [];
}

// 4. Vitrine (Best Sellers - 4 produtos do banco)
try {
    $stmt = $pdo->query("SELECT id, nome, preco, imagem FROM produtos ORDER BY criado_em ASC LIMIT 4");
    $dbVitrine = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dbVitrine = [];
}

// ============================================
// Lógica de Fallback (Placeholders se o banco estiver com poucos registros)
// ============================================

// Preenchimento dos 6 cards coloridos caso não existam 6 cadastrados
$coresGrid = ['card-black', 'card-yellow', 'card-red', 'card-silver', 'card-green', 'card-blue'];
$titulosFicticios = ['Earphone', 'Gadget', 'Laptop', 'Console', 'Oculus', 'Speakers'];
$gridFinal = [];

for ($i = 0; $i < 6; $i++) {
    if (isset($dbGrid[$i])) {
        $gridFinal[] = [
            'id' => $dbGrid[$i]['id'],
            'titulo' => $dbGrid[$i]['cat_nome'] ?? 'Novidade',
            'subtitulo' => $dbGrid[$i]['nome'],
            'imagem' => $dbGrid[$i]['imagem'],
            'fallback' => false
        ];
    } else {
        // Mock se não houver produto suficiente no banco para preencher os 6 cards
        $gridFinal[] = [
            'id' => '#',
            'titulo' => 'Enjoy With',
            'subtitulo' => $titulosFicticios[$i],
            'imagem' => 'assets/img/produto-sem-imagem.png',
            'fallback' => true
        ];
    }
}

// Preenchimento dos 4 produtos da Vitrine
$vitrineFinal = [];
for ($i = 0; $i < 4; $i++) {
    if (isset($dbVitrine[$i])) {
        $vitrineFinal[] = $dbVitrine[$i];
    } else {
        $vitrineFinal[] = [
            'id' => '#',
            'nome' => 'Produto Canzala',
            'preco' => '00.00',
            'imagem' => 'assets/img/produto-sem-imagem.png'
        ];
    }
}
$pageCSS = 'home'; // Carrega home.css depois do global
require __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/home.css">

<?php require __DIR__ . '/includes/navbar.php'; ?>

<main class="main-content">

    <!-- ============================================
         1. HERO SLIDER (Infinite Carrousel)
         ============================================ -->
    <section class="hero revelar">
        <?php if (!empty($dbSliders)): ?>
            <?php foreach ($dbSliders as $index => $prod): 
                $cores = ['cor1', 'cor2', 'cor3'];
                $classeCor = $cores[$index % count($cores)];
                $img = !empty($prod['imagem']) ? $prod['imagem'] : 'assets/img/produto-sem-imagem.png';
            ?>
            <div class="carrosel <?php echo $classeCor; ?>">
                <div class="content revelar">
                    <span class="brand-name">Canzala Series</span>
                    <h1><?php echo htmlspecialchars($prod['nome']); ?></h1>
                    <h2 class="bg-text">WIRELESS</h2>
                    <button class="btn-shop" onclick="irParaDetalhes(<?php echo $prod['id']; ?>)">Shop Now</button>
                </div>
                <div class="hero-images revelar">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Hero" class="image" onerror="this.src='assets/img/produto-sem-imagem.png'">
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Slide de Fallback caso banco esteja zerado -->
            <div class="carrosel cor1">
                <div class="content revelar">
                    <span class="brand-name">Canzala</span>
                    <h1>Novidades</h1>
                    <h2 class="bg-text">E-COMMERCE</h2>
                    <button class="btn-shop">Ver Catálogo</button>
                </div>
                <div class="hero-images revelar">
                    <img src="assets/img/produto-sem-imagem.png" alt="Fallback" class="image">
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- ============================================
         2. PRODUCTS GRID (Exatamente 6 Cards Coloridos)
         ============================================ -->
    <section class="products-grid revelar">
        <?php foreach ($gridFinal as $index => $item): 
            $cor = $coresGrid[$index];
            $link = $item['id'] !== '#' ? "pages/detalhes.php?id=" . $item['id'] : "#";
        ?>
            <div class="card <?php echo $cor; ?> revelar">
                <div class="text">
                    <span><?php echo htmlspecialchars($item['titulo']); ?></span>
                    <h3><?php echo htmlspecialchars($item['subtitulo']); ?></h3>
                    <a href="<?php echo $link; ?>" class="btn">Saiba Mais</a>
                </div>
                <img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="Card Image" onerror="this.src='assets/img/produto-sem-imagem.png'">
            </div>
        <?php endforeach; ?>
    </section>

    <!-- ============================================
         3. BANNER 1 (Produto mais caro / destaque do banco)
         ============================================ -->
    <?php 
        $b1_nome = isset($dbBanners[0]) ? $dbBanners[0]['nome'] : 'Fine Smile';
        $b1_preco = isset($dbBanners[0]) ? number_format($dbBanners[0]['preco'], 2, ',', '.') . ' Kz' : '$129';
        $b1_img = isset($dbBanners[0]['imagem']) ? $dbBanners[0]['imagem'] : 'assets/img/produto-sem-imagem.png';
        $b1_link = isset($dbBanners[0]) ? "irParaDetalhes(".$dbBanners[0]['id'].")" : "";
    ?>
    <div class="banner revelar">
        <div class="left-content revelar">
            <span class="txt">10% OFF</span>
            <h1>FINE<br>SMILE</h1>
            <span class="txt2">Campanha de Inverno</span>
        </div>
        <div class="phones2 revelar">
            <img src="<?php echo htmlspecialchars($b1_img); ?>" alt="Banner Image" class="headphone" onerror="this.src='assets/img/produto-sem-imagem.png'">
        </div>
        <div class="right-content revelar">
            <p class="p">Air Sale Now</p>
            <h2><?php echo $b1_preco; ?></h2>
            <span class="dure"><?php echo htmlspecialchars($b1_nome); ?></span>
            <a href="#" class="btn-shop1" onclick="<?php echo $b1_link; ?>">Shop</a>
        </div>
    </div>

    <!-- ============================================
         4. VITRINE (Best Sellers - Exatamente 4 Cards)
         ============================================ -->
    <section class="vitrine revelar">
        <h2 class="osmay">Best Seller Products</h2>
        <div class="produtos-grid">
            <?php foreach ($vitrineFinal as $prod): 
                $img = !empty($prod['imagem']) ? $prod['imagem'] : 'assets/img/produto-sem-imagem.png';
                $link = $prod['id'] !== '#' ? "irParaDetalhes(".$prod['id'].")" : "#";
            ?>
            <div class="produto-card revelar">
                <div class="image-container">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Produto" onerror="this.src='assets/img/produto-sem-imagem.png'">
                    <button class="add-to-cart" onclick="<?php echo $link; ?>">Saiba Mais</button>
                </div>
                <h3 class="vamos"><?php echo htmlspecialchars($prod['nome']); ?></h3>
                <p class="preco"><?php echo is_numeric($prod['preco']) ? number_format($prod['preco'], 2, ',', '.') . ' Kz' : $prod['preco']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ============================================
         5. BANNER 2 (Segundo produto em destaque)
         ============================================ -->
    <?php 
        $b2_nome = isset($dbBanners[1]) ? $dbBanners[1]['nome'] : 'Smart Solo';
        $b2_preco = isset($dbBanners[1]) ? number_format($dbBanners[1]['preco'], 2, ',', '.') . ' Kz' : '$129';
        $b2_img = isset($dbBanners[1]['imagem']) ? $dbBanners[1]['imagem'] : 'assets/img/produto-sem-imagem.png';
        $b2_link = isset($dbBanners[1]) ? "irParaDetalhes(".$dbBanners[1]['id'].")" : "";
    ?>
    <div class="banner2 revelar">
        <div class="left-content revelar">
            <span class="txt">30% OFF</span>
            <h1>WINTER<br>SALE</h1>
            <span class="txt2">Tempo Limitado</span>
        </div>
        <div class="phones2 revelar">
            <img src="<?php echo htmlspecialchars($b2_img); ?>" alt="Banner Image" class="headphone" onerror="this.src='assets/img/produto-sem-imagem.png'">
        </div>
        <div class="right-content revelar">
            <p class="p">Smart Design</p>
            <h2><?php echo $b2_preco; ?></h2>
            <span class="dure"><?php echo htmlspecialchars($b2_nome); ?></span>
            <a href="#" class="btn-shop2" onclick="<?php echo $b2_link; ?>">Shop</a>
        </div>
    </div>

</main>

<script src="<?php echo $baseUrl; ?>assets/js/home.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>