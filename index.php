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
// CONFIGURAÇÃO: 8 CATEGORIAS DA HOME
// ============================================
$categoriasHome = [4, 10, 11, 1, 7, 6, 3, 8];

// ============================================
// ROTAÇÃO AUTOMÁTICA (A cada 6 minutos)
// Para testar: mude 360 para 10 (10 segundos)
// ============================================
$seed = floor(time() / 360);

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
    foreach ($keys as $k) {
        $result[] = $array[$k];
    }
    return $result;
}

// Embaralha as 8 categorias com base no tempo
$cats = seededShuffle($categoriasHome, $seed);

// Distribui pelas seções (8 cats para 15 slots)
$heroCats    = [$cats[0], $cats[1], $cats[2]];
$gridCats    = [$cats[3], $cats[4], $cats[5], $cats[6], $cats[7], $cats[0]];
$bannerCats  = [$cats[1], $cats[2]];
$vitrineCats = [$cats[3], $cats[4], $cats[5], $cats[6]];

// ============================================
// FUNÇÃO: Busca 1 produto aleatório por categoria
// ============================================
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

    // Agrupa por categoria
    $grouped = [];
    foreach ($rows as $row) {
        $grouped[$row['categoria_id']][] = $row;
    }

    // Pega 1 aleatório de cada categoria, respeitando a ordem do sorteio
    $result = [];
    foreach ($catIds as $cid) {
        if (!empty($grouped[$cid])) {
            $pick = $grouped[$cid][array_rand($grouped[$cid])];
            // Garante fallback se nome_curto estiver vazio
            if (empty($pick['nome_curto'])) {
                $pick['nome_curto'] = $pick['nome'];
            }
            $result[] = $pick;
        }
    }
    return $result;
}

// ============================================
// FUNÇÃO: Busca o produto mais caro por categoria
// ============================================
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

    // Garante 1 só por categoria (se houver empate no preço, pega o primeiro)
    $byCat = [];
    foreach ($rows as $row) {
        if (empty($row['nome_curto'])) {
            $row['nome_curto'] = $row['nome'];
        }
        if (!isset($byCat[$row['categoria_id']])) {
            $byCat[$row['categoria_id']] = $row;
        }
    }

    // Mantém a ordem das categorias sorteadas
    $result = [];
    foreach ($catIds as $cid) {
        if (isset($byCat[$cid])) {
            $result[] = $byCat[$cid];
        }
    }
    return $result;
}

// ============================================
// 1. HERO SLIDER (3 produtos aleatórios)
// ============================================
try {
    $rawSliders = fetchRandomByCategories($pdo, $heroCats);
} catch (PDOException $e) {
    $rawSliders = [];
}

// GARANTE EXATAMENTE 3 SLIDES (preenche com fallback se faltar)
$coresHero = ['cor1', 'cor2', 'cor3'];
$dbSliders = [];

for ($i = 0; $i < 3; $i++) {
    if (isset($rawSliders[$i])) {
        $dbSliders[] = [
            'id' => $rawSliders[$i]['id'],
            'nome' => $rawSliders[$i]['nome'],
            'nome_curto' => $rawSliders[$i]['nome_curto'] ?? $rawSliders[$i]['nome'],
            'imagem' => $rawSliders[$i]['imagem'],
            'cor' => $coresHero[$i],
            'fallback' => false
        ];
    } else {
        $dbSliders[] = [
            'id' => '#',
            'nome' => 'Novidades',
            'nome_curto' => 'Novidades',
            'imagem' => 'assets/img/produto-sem-imagem.png',
            'cor' => $coresHero[$i],
            'fallback' => true
        ];
    }
}

// ============================================
// 2. GRID DE 6 CARDS (1 produto de cada categoria)
// ============================================
try {
    $dbGrid = fetchRandomByCategories($pdo, $gridCats);
} catch (PDOException $e) {
    $dbGrid = [];
}

// ============================================
// 3. BANNERS (Produto mais caro de 2 categorias)
// ============================================
try {
    $dbBanners = fetchExpensiveByCategories($pdo, $bannerCats);
} catch (PDOException $e) {
    $dbBanners = [];
}

// ============================================
// 4. VITRINE (4 produtos aleatórios)
// ============================================
try {
    $dbVitrine = fetchRandomByCategories($pdo, $vitrineCats, ['p.preco']);
} catch (PDOException $e) {
    $dbVitrine = [];
}

// ============================================
// LÓGICA DE FALLBACK (Mocks se faltar produtos)
// ============================================
$coresGrid = ['card-black', 'card-yellow', 'card-red', 'card-silver', 'card-green', 'card-blue'];
$titulosFicticios = ['Earphone', 'Gadget', 'Laptop', 'Console', 'Oculus', 'Speakers'];
$gridFinal = [];

for ($i = 0; $i < 6; $i++) {
    if (isset($dbGrid[$i])) {
        $gridFinal[] = [
            'id' => $dbGrid[$i]['id'],
            'titulo' => $dbGrid[$i]['cat_nome'] ?? 'Novidade',
            'subtitulo' => $dbGrid[$i]['nome_curto'] ?? $dbGrid[$i]['nome'],
            'imagem' => $dbGrid[$i]['imagem'],
            'fallback' => false
        ];
    } else {
        $gridFinal[] = [
            'id' => '#',
            'titulo' => 'Enjoy With',
            'subtitulo' => $titulosFicticios[$i],
            'imagem' => 'assets/img/produto-sem-imagem.png',
            'fallback' => true
        ];
    }
}

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


<?php require __DIR__ . '/includes/navbar.php'; ?>

<main class="main-content">

    <!-- ============================================
         1. HERO SLIDER (Infinite Carrousel)
         ============================================ -->
    <section class="hero revelar">
        <?php foreach ($dbSliders as $index => $prod): 
        $classeCor = $prod['cor'];
        $img = !empty($prod['imagem']) ? $prod['imagem'] : 'assets/img/produto-sem-imagem.png';
    ?>
        <div class="carrosel <?php echo $classeCor; ?>">
            <div class="content revelar">
                <span class="brand-name"><?php echo $prod['fallback'] ? 'Canzala' : 'Canzala Series'; ?></span>
                <h1><?php echo htmlspecialchars($prod['nome_curto'] ?: $prod['nome']); ?></h1>
                <h2 class="bg-text"><?php echo $prod['fallback'] ? 'E-COMMERCE' : 'WIRELESS'; ?></h2>
                <?php if ($prod['fallback']): ?>
                <button class="btn-shop">Ver Catálogo</button>
                <?php else: ?>
                <button class="btn-shop" onclick="irParaDetalhes(<?php echo $prod['id']; ?>)">Shop Now</button>
                <?php endif; ?>
            </div>
            <div class="hero-images revelar">
                <img src="<?php echo htmlspecialchars($img); ?>"
                    alt="<?php echo $prod['fallback'] ? 'Fallback' : 'Hero'; ?>" class="image"
                    onerror="this.src='assets/img/produto-sem-imagem.png'">
            </div>
        </div>
        <?php endforeach; ?>
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
            <img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="Card Image"
                onerror="this.src='assets/img/produto-sem-imagem.png'">
        </div>
        <?php endforeach; ?>
    </section>

    <!-- ============================================
         3. BANNER 1 (Produto mais caro / destaque do banco)
         ============================================ -->
    <?php 
        $b1_nome = isset($dbBanners[0]) ? ($dbBanners[0]['nome_curto'] ?: $dbBanners[0]['nome']) : 'Fine Smile';
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
            <img src="<?php echo htmlspecialchars($b1_img); ?>" alt="Banner Image" class="headphone"
                onerror="this.src='assets/img/produto-sem-imagem.png'">
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
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Produto"
                        onerror="this.src='assets/img/produto-sem-imagem.png'">
                    <button class="add-to-cart" onclick="<?php echo $link; ?>">Saiba Mais</button>
                </div>
                <h3 class="vamos"><?php echo htmlspecialchars($prod['nome_curto'] ?: $prod['nome']); ?></h3>
                <p class="preco">
                    <?php echo is_numeric($prod['preco']) ? number_format($prod['preco'], 2, ',', '.') . ' Kz' : $prod['preco']; ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ============================================
         5. BANNER 2 (Segundo produto em destaque)
         ============================================ -->
    <?php 
        $b2_nome = isset($dbBanners[1]) ? ($dbBanners[1]['nome_curto'] ?: $dbBanners[1]['nome']) : 'Smart Solo';
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
            <img src="<?php echo htmlspecialchars($b2_img); ?>" alt="Banner Image" class="headphone"
                onerror="this.src='assets/img/produto-sem-imagem.png'">
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