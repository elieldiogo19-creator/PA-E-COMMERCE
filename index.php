<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Home - ' . $nomeProjeto;
$navbarMode  = 'full';
$baseUrl     = '';
$pageCSS     = 'home';

$usuarioNome = $_SESSION['usuario_nome'] ?? null;
$qtdCarrinho = 0;

/**
 * 6 CATEGORIAS FIXAS ESCOLHIDAS POR TI
 * Com base no teu banco:
 * 1  = Computadores Portáteis
 * 7  = Redes e Internet
 * 8  = Ratos
 * 9  = Teclados
 * 10 = Smartphones e Tablets
 * 11 = Vídeo Vigilância
 */
$categoriasHome = [1, 7, 2, 9, 10, 11];

/**
 * BUSCA TODOS OS PRODUTOS DAS 6 CATEGORIAS
 * E EMBARALHA DE FORMA ALEATÓRIA
 */
try {
    $placeholders = implode(',', array_fill(0, count($categoriasHome), '?'));

    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.nome,
            p.nome_curto,
            p.imagem,
            p.preco,
            p.estoque,
            p.categoria_id,
            c.nome AS cat_nome
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        WHERE p.categoria_id IN ($placeholders)
        AND p.estoque > 0
        ORDER BY RAND()
    ");
    $stmt->execute($categoriasHome);
    $todosProdutos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $todosProdutos = [];
}

/**
 * DIVIDE OS PRODUTOS SEM REPETIÇÃO
 * Cada seção pega do pool geral em sequência
 * garantindo que nenhum produto se repete
 */
$totalProdutos = count($todosProdutos);
$offset = 0; // controla a posição no array

// Função auxiliar para pegar N produtos do pool sem repetir
function pegarProdutos(array $pool, int &$offset, int $quantidade): array {
    $resultado = [];
    for ($i = 0; $i < $quantidade; $i++) {
        if ($offset < count($pool)) {
            $resultado[] = $pool[$offset];
            $offset++;
        }
    }
    return $resultado;
}

// Hero Slider  → 3 produtos
$produtosSlider  = pegarProdutos($todosProdutos, $offset, 3);

// Grid 6 Cards → 6 produtos
$produtosGrid    = pegarProdutos($todosProdutos, $offset, 6);

// Banner 1     → 1 produto
$produtosBanner1 = pegarProdutos($todosProdutos, $offset, 1);

// Vitrine      → 4 produtos
$produtosVitrine = pegarProdutos($todosProdutos, $offset, 4);

// Banner 2     → 1 produto
$produtosBanner2 = pegarProdutos($todosProdutos, $offset, 1);

/**
 * PREPARA OS DADOS PARA O HERO SLIDER
 */
$coresHero = ['cor1', 'cor2', 'cor3'];
$dbSliders = [];
for ($i = 0; $i < 3; $i++) {
    if (isset($produtosSlider[$i])) {
        $p = $produtosSlider[$i];
        $p['nome_curto'] = $p['nome_curto'] ?: $p['nome'];
        $dbSliders[] = array_merge($p, [
            'cor'      => $coresHero[$i],
            'fallback' => false
        ]);
    } else {
        $dbSliders[] = [
            'id'        => '#',
            'nome'      => 'Novidades',
            'nome_curto'=> 'Novidades',
            'imagem'    => 'assets/img/produto-sem-imagem.png',
            'cor'       => $coresHero[$i],
            'fallback'  => true
        ];
    }
}

/**
 * PREPARA OS DADOS PARA O GRID DE 6 CARDS
 */
$coresGrid = ['card-black', 'card-yellow', 'card-red', 'card-silver', 'card-green', 'card-blue'];
$gridFinal = [];
for ($i = 0; $i < 6; $i++) {
    if (isset($produtosGrid[$i])) {
        $p = $produtosGrid[$i];
        $gridFinal[] = [
            'id'       => $p['id'],
            'titulo'   => $p['cat_nome'] ?? 'Novidade',
            'subtitulo'=> $p['nome_curto'] ?: $p['nome'],
            'imagem'   => $p['imagem'],
            'fallback' => false
        ];
    } else {
        $gridFinal[] = [
            'id'       => '#',
            'titulo'   => 'Canzala',
            'subtitulo'=> 'Novidade',
            'imagem'   => 'assets/img/produto-sem-imagem.png',
            'fallback' => true
        ];
    }
}

/**
 * PREPARA OS DADOS PARA A VITRINE
 */
$vitrineFinal = [];
for ($i = 0; $i < 4; $i++) {
    if (isset($produtosVitrine[$i])) {
        $p = $produtosVitrine[$i];
        $vitrineFinal[] = $p;
    } else {
        $vitrineFinal[] = [
            'id'        => '#',
            'nome'      => 'Produto Canzala',
            'nome_curto'=> 'Produto Canzala',
            'preco'     => '00.00',
            'imagem'    => 'assets/img/produto-sem-imagem.png'
        ];
    }
}

/**
 * PREPARA OS BANNERS
 */
$b1 = $produtosBanner1[0] ?? null;
$b2 = $produtosBanner2[0] ?? null;

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">

    <!-- 1. HERO SLIDER -->
    <section class="hero revelar">
        <?php foreach ($dbSliders as $prod): 
            $img = !empty($prod['imagem']) ? $prod['imagem'] : 'assets/img/produto-sem-imagem.png';
        ?>
        <div class="carrosel <?= $prod['cor']; ?>">
            <div class="content revelar">
                <span class="brand-name"><?= $prod['fallback'] ? 'Canzala' : 'Canzala Series'; ?></span>
                <h1><?= htmlspecialchars($prod['nome_curto']); ?></h1>
                <h2 class="bg-text"><?= $prod['fallback'] ? 'E-COMMERCE' : 'WIRELESS'; ?></h2>

                <?php if ($prod['fallback']): ?>
                <button class="btn-shop">Ver Catálogo</button>
                <?php else: ?>
                <button class="btn-shop" onclick="irParaDetalhes(<?= $prod['id']; ?>)">Shop Now</button>
                <?php endif; ?>
            </div>
            <div class="hero-images revelar">
                <img src="<?= htmlspecialchars($img); ?>" alt="Hero Image" class="image"
                    onerror="this.src='assets/img/produto-sem-imagem.png'">
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <!-- 2. PRODUCTS GRID (6 Cards) -->
    <section class="products-grid revelar">
        <?php foreach ($gridFinal as $index => $item): 
            $link = $item['id'] !== '#' ? "pages/detalhes.php?id=" . $item['id'] : "#";
        ?>
        <div class="card <?= $coresGrid[$index]; ?> revelar">
            <div class="text">
                <span><?= htmlspecialchars($item['titulo']); ?></span>
                <h3><?= htmlspecialchars($item['subtitulo']); ?></h3>
                <a href="<?= $link; ?>" class="btn">Saiba Mais</a>
            </div>
            <img src="<?= htmlspecialchars($item['imagem']); ?>" alt="Grid Image"
                onerror="this.src='assets/img/produto-sem-imagem.png'">
        </div>
        <?php endforeach; ?>
    </section>

    <!-- 3. BANNER 1 -->
    <?php
    $b1_nome  = $b1 ? ($b1['nome_curto'] ?: $b1['nome']) : 'Fine Smile';
    $b1_preco = $b1 ? number_format($b1['preco'], 2, ',', '.') . ' Kz' : '$129';
    $b1_img   = $b1['imagem'] ?? 'assets/img/produto-sem-imagem.png';
    $b1_link  = $b1 ? "irParaDetalhes({$b1['id']})" : "";
?>
    <div class="banner revelar">
        <div class="left-content revelar">
            <span class="txt">10% OFF</span>
            <h1>FINE<br>SMILE</h1>
            <span class="txt2">Campanha de Inverno</span>
        </div>
        <div class="phones2 revelar">
            <img src="<?= htmlspecialchars($b1_img); ?>" alt="Banner 1" class="headphone"
                onerror="this.src='assets/img/produto-sem-imagem.png'">
        </div>
        <div class="right-content revelar">
            <p class="p">Air Sale Now</p>
            <h2><?= $b1_preco; ?></h2>
            <span class="dure"><?= htmlspecialchars($b1_nome); ?></span>
            <a href="#" class="btn-shop1" onclick="<?= $b1_link; ?>">Shop</a>
        </div>
    </div>

    <!-- 4. VITRINE (Best Sellers) -->
    <section class="vitrine revelar">
        <h2 class="osmay">Best Seller Products</h2>
        <div class="produtos-grid">
            <?php foreach ($vitrineFinal as $prod): 
                $img = !empty($prod['imagem']) ? $prod['imagem'] : 'assets/img/produto-sem-imagem.png';
                $link = $prod['id'] !== '#' ? "irParaDetalhes({$prod['id']})" : "#";
            ?>
            <div class="produto-card revelar">
                <div class="image-container">
                    <img src="<?= htmlspecialchars($img); ?>" alt="Produto"
                        onerror="this.src='assets/img/produto-sem-imagem.png'">
                    <button class="add-to-cart" onclick="<?= $link; ?>">Saiba Mais</button>
                </div>
                <h3 class="vamos"><?= htmlspecialchars($prod['nome_curto'] ?: $prod['nome']); ?></h3>
                <p class="preco">
                    <?= is_numeric($prod['preco']) ? number_format($prod['preco'], 2, ',', '.') . ' Kz' : $prod['preco']; ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 5. BANNER 2 -->
    <?php
    $b2_nome  = $b2 ? ($b2['nome_curto'] ?: $b2['nome']) : 'Smart Solo';
    $b2_preco = $b2 ? number_format($b2['preco'], 2, ',', '.') . ' Kz' : '$129';
    $b2_img   = $b2['imagem'] ?? 'assets/img/produto-sem-imagem.png';
    $b2_link  = $b2 ? "irParaDetalhes({$b2['id']})" : "";
?>
    <div class="banner2 revelar">
        <div class="left-content revelar">
            <span class="txt">30% OFF</span>
            <h1>WINTER<br>SALE</h1>
            <span class="txt2">Tempo Limitado</span>
        </div>
        <div class="phones2 revelar">
            <img src="<?= htmlspecialchars($b2_img); ?>" alt="Banner 2" class="headphone"
                onerror="this.src='assets/img/produto-sem-imagem.png'">
        </div>
        <div class="right-content revelar">
            <p class="p">Smart Design</p>
            <h2><?= $b2_preco; ?></h2>
            <span class="dure"><?= htmlspecialchars($b2_nome); ?></span>
            <a href="#" class="btn-shop2" onclick="<?= $b2_link; ?>">Shop</a>
        </div>
    </div>

</main>

<script src="<?= $baseUrl; ?>assets/js/home.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>