<?php
session_start();
require __DIR__ . '/config/db.php';

/**
 * CONFIGURAÇÕES GERAIS
 */
$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Home - ' . $nomeProjeto;
$navbarMode  = 'full';
$baseUrl     = '';
$pageCSS     = 'home';

$usuarioNome = $_SESSION['usuario_nome'] ?? null;
$qtdCarrinho = 0;

/**
 * LÓGICA DE ROTAÇÃO E CATEGORIAS
 * As categorias mudam a cada 6 minutos (360 segundos)
 */
$categoriasHome = [2, 10, 11, 1, 7, 6, 3, 9];
$seed           = floor(time() / 360);

function seededShuffle(array $array, int $seed): array {
    mt_srand($seed);
    $keys = array_keys($array);
    for ($i = count($keys) - 1; $i > 0; $i--) {
        $j = mt_rand(0, $i);
        $tmp = $keys[$i];
        $keys[$i] = $keys[$j];
        $keys[$j] = $tmp;
    }
    mt_srand();
    $result = [];
    foreach ($keys as $k) { $result[] = $array[$k]; }
    return $result;
}

$cats = seededShuffle($categoriasHome, $seed);

// Distribuição dos IDs de categorias para as seções
$heroCats    = [$cats[0], $cats[1], $cats[2]];
$gridCats    = [$cats[3], $cats[4], $cats[5], $cats[6], $cats[7], $cats[0]];
$bannerCats  = [$cats[1], $cats[2]];
$vitrineCats = [$cats[3], $cats[4], $cats[5], $cats[6]];

/**
 * FUNÇÕES DE BUSCA NO BANCO
 */
function fetchRandomByCategories($pdo, array $catIds, array $extraFields = []) {
    if (empty($catIds)) return [];
    $placeholders = implode(',', array_fill(0, count($catIds), '?'));
    $fields = !empty($extraFields) ? ', ' . implode(', ', $extraFields) : '';

    $sql = "SELECT p.id, p.nome, p.nome_curto, p.imagem, p.categoria_id, c.nome as cat_nome {$fields}
            FROM produtos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.categoria_id IN ($placeholders)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($catIds);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grouped = [];
    foreach ($rows as $row) { $grouped[$row['categoria_id']][] = $row; }

    $result = [];
    foreach ($catIds as $cid) {
        if (!empty($grouped[$cid])) {
            $pick = $grouped[$cid][array_rand($grouped[$cid])];
            $pick['nome_curto'] = $pick['nome_curto'] ?: $pick['nome'];
            $result[] = $pick;
        }
    }
    return $result;
}

function fetchExpensiveByCategories($pdo, array $catIds) {
    if (empty($catIds)) return [];
    $placeholders = implode(',', array_fill(0, count($catIds), '?'));

    $sql = "SELECT p.id, p.nome, p.nome_curto, p.imagem, p.preco, p.categoria_id, c.nome as cat_nome
            FROM produtos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            INNER JOIN (
                SELECT categoria_id, MAX(preco) as max_preco
                FROM produtos
                WHERE categoria_id IN ($placeholders)
                GROUP BY categoria_id
            ) m ON m.categoria_id = p.categoria_id AND m.max_preco = p.preco";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($catIds);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $byCat = [];
    foreach ($rows as $row) {
        $row['nome_curto'] = $row['nome_curto'] ?: $row['nome'];
        if (!isset($byCat[$row['categoria_id']])) { $byCat[$row['categoria_id']] = $row; }
    }

    $result = [];
    foreach ($catIds as $cid) {
        if (isset($byCat[$cid])) { $result[] = $byCat[$cid]; }
    }
    return $result;
}

/**
 * PROCESSAMENTO DE DADOS (DATA FETCHING)
 */
try {
    $rawSliders = fetchRandomByCategories($pdo, $heroCats);
    $dbGrid     = fetchRandomByCategories($pdo, $gridCats);
    $dbBanners  = fetchExpensiveByCategories($pdo, $bannerCats);
    $dbVitrine  = fetchRandomByCategories($pdo, $vitrineCats, ['p.preco']);
} catch (PDOException $e) {
    $rawSliders = $dbGrid = $dbBanners = $dbVitrine = [];
}

/**
 * TRATAMENTO DE FALLBACKS E INTERFACE
 */

// 1. Sliders (Hero)
$coresHero = ['cor1', 'cor2', 'cor3'];
$dbSliders = [];
for ($i = 0; $i < 3; $i++) {
    if (isset($rawSliders[$i])) {
        $dbSliders[] = array_merge($rawSliders[$i], ['cor' => $coresHero[$i], 'fallback' => false]);
    } else {
        $dbSliders[] = [
            'id' => '#', 'nome' => 'Novidades', 'nome_curto' => 'Novidades',
            'imagem' => 'assets/img/produto-sem-imagem.png', 'cor' => $coresHero[$i], 'fallback' => true
        ];
    }
}

// 2. Grid de 6 Cards
$coresGrid = ['card-black', 'card-yellow', 'card-red', 'card-silver', 'card-green', 'card-blue'];
$titulosFicticios = ['Earphone', 'Gadget', 'Laptop', 'Console', 'Oculus', 'Speakers'];
$gridFinal = [];
for ($i = 0; $i < 6; $i++) {
    if (isset($dbGrid[$i])) {
        $gridFinal[] = [
            'id' => $dbGrid[$i]['id'],
            'titulo' => $dbGrid[$i]['cat_nome'] ?? 'Novidade',
            'subtitulo' => $dbGrid[$i]['nome_curto'],
            'imagem' => $dbGrid[$i]['imagem'],
            'fallback' => false
        ];
    } else {
        $gridFinal[] = [
            'id' => '#', 'titulo' => 'Enjoy With', 'subtitulo' => $titulosFicticios[$i],
            'imagem' => 'assets/img/produto-sem-imagem.png', 'fallback' => true
        ];
    }
}

// 3. Vitrine
$vitrineFinal = [];
for ($i = 0; $i < 4; $i++) {
    $vitrineFinal[] = $dbVitrine[$i] ?? [
        'id' => '#', 'nome' => 'Produto Canzala', 'nome_curto' => 'Produto Canzala',
        'preco' => '00.00', 'imagem' => 'assets/img/produto-sem-imagem.png'
    ];
}

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
                    <img src="<?= htmlspecialchars($img); ?>" alt="Hero Image" class="image" onerror="this.src='assets/img/produto-sem-imagem.png'">
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
                <img src="<?= htmlspecialchars($item['imagem']); ?>" alt="Grid Image" onerror="this.src='assets/img/produto-sem-imagem.png'">
            </div>
        <?php endforeach; ?>
    </section>

    <!-- 3. BANNER 1 -->
    <?php 
        $b1 = $dbBanners[0] ?? null;
        $b1_nome = $b1 ? ($b1['nome_curto'] ?: $b1['nome']) : 'Fine Smile';
        $b1_preco = $b1 ? number_format($b1['preco'], 2, ',', '.') . ' Kz' : '$129';
        $b1_img = $b1['imagem'] ?? 'assets/img/produto-sem-imagem.png';
        $b1_link = $b1 ? "irParaDetalhes({$b1['id']})" : "";
    ?>
    <div class="banner revelar">
        <div class="left-content revelar">
            <span class="txt">10% OFF</span>
            <h1>FINE<br>SMILE</h1>
            <span class="txt2">Campanha de Inverno</span>
        </div>
        <div class="phones2 revelar">
            <img src="<?= htmlspecialchars($b1_img); ?>" alt="Banner 1" class="headphone" onerror="this.src='assets/img/produto-sem-imagem.png'">
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
                        <img src="<?= htmlspecialchars($img); ?>" alt="Produto" onerror="this.src='assets/img/produto-sem-imagem.png'">
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
        $b2 = $dbBanners[1] ?? null;
        $b2_nome = $b2 ? ($b2['nome_curto'] ?: $b2['nome']) : 'Smart Solo';
        $b2_preco = $b2 ? number_format($b2['preco'], 2, ',', '.') . ' Kz' : '$129';
        $b2_img = $b2['imagem'] ?? 'assets/img/produto-sem-imagem.png';
        $b2_link = $b2 ? "irParaDetalhes({$b2['id']})" : "";
    ?>
    <div class="banner2 revelar">
        <div class="left-content revelar">
            <span class="txt">30% OFF</span>
            <h1>WINTER<br>SALE</h1>
            <span class="txt2">Tempo Limitado</span>
        </div>
        <div class="phones2 revelar">
            <img src="<?= htmlspecialchars($b2_img); ?>" alt="Banner 2" class="headphone" onerror="this.src='assets/img/produto-sem-imagem.png'">
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