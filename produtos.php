<?php
ob_start();
session_start();
require __DIR__ . '/config/db.php';

// Detectar se é AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Filtros via GET
$categoria_id = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;
$ordem        = isset($_GET['ordem']) ? trim($_GET['ordem']) : 'recentes';

// Whitelist de ordenações (protege contra SQL injection)
$ordensPermitidas = [
    'recentes'  => 'p.criado_em DESC',
    'antigos'   => 'p.criado_em ASC',
    'preco_asc' => 'p.preco ASC',
    'preco_desc'=> 'p.preco DESC',
    'nome_asc'  => 'p.nome ASC',
    'nome_desc' => 'p.nome DESC',
];

// Se veio ordem inválida, cai no padrão
if (!array_key_exists($ordem, $ordensPermitidas)) {
    $ordem = 'recentes';
}

$orderBy = $ordensPermitidas[$ordem];

try {
    // Buscar todas categorias
    $stmtCat = $pdo->query("
        SELECT id, nome
        FROM categorias
        ORDER BY nome ASC
    ");
    $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    // Campos comuns
    $campos = "
        p.id, p.nome, p.descricao_curta, p.preco, p.imagem, p.estoque,
        c.nome AS categoria_nome
    ";

    // Buscar produtos (com ou sem filtro de categoria)
    if ($categoria_id > 0) {
        $stmt = $pdo->prepare("
            SELECT $campos
            FROM produtos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.categoria_id = ?
            ORDER BY $orderBy
        ");
        $stmt->execute([$categoria_id]);
    } else {
        $stmt = $pdo->query("
            SELECT $campos
            FROM produtos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            ORDER BY $orderBy
        ");
    }

    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $produtos = [];
    $categorias = [];
}

// ============================
// FUNÇÃO PARA RENDERIZAR UM CARD
// ============================
function renderCard(array $produto): string {
    $img = !empty($produto['imagem'])
        ? htmlspecialchars($produto['imagem'])
        : 'assets/img/produto-sem-imagem.png';

    $nome     = htmlspecialchars($produto['nome']);
    $nomeAttr = htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8');
    $desc     = htmlspecialchars($produto['descricao_curta'] ?? 'Sem descrição.');
    $preco    = number_format($produto['preco'], 2, ',', '.');
    $id       = (int) $produto['id'];
    $estoque  = (int) $produto['estoque'];

    $badge = $estoque > 0
        ? '<span class="badge-estoque em-estoque">EM ESTOQUE</span>'
        : '<span class="badge-estoque sem-estoque">ESGOTADO</span>';

    return <<<HTML
    <div class="card">
        {$badge}

        <div class="image-produto">
            <a href="pages/detalhes.php?id={$id}">
                <img src="{$img}" alt="{$nome}">
            </a>
        </div>

        <div class="infor-produto">
            <h3 class="titulo">{$nome}</h3>
            <p class="descri">{$desc}</p>
            <p class="preco">Kz {$preco}</p>
        </div>

        <div class="produtos-accao">
            <a href="#" class="btn-1 btn-adicionar" data-id="{$id}" data-nome="{$nomeAttr}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                Add To Cart
            </a>

            <a href="pages/detalhes.php?id={$id}" class="btn">
                Saiba mais...
            </a>
        </div>
    </div>
    HTML;
}

// ============================
// SE FOR AJAX, RETORNA APENAS OS CARDS
// ============================
if ($isAjax) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: text/html; charset=utf-8');

    if (empty($produtos)) {
        echo '<div class="produtos-vazio"><p>Nenhum produto encontrado.</p></div>';
    } else {
        echo '<div class="vitrine">';
        foreach ($produtos as $produto) {
            echo renderCard($produto);
        }
        echo '</div>';
    }

    echo '<span id="count-produtos" style="display:none">' . count($produtos) . '</span>';
    exit;
}

// ============================
// SE FOR AJAX, RETORNA APENAS OS CARDS
// ============================
if ($isAjax) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: text/html; charset=utf-8');

    if (empty($produtos)) {
        echo '<div class="produtos-vazio"><p>Nenhum produto encontrado nesta categoria.</p></div>';
    } else {
        echo '<div class="vitrine">';
        foreach ($produtos as $produto) {
            echo renderCard($produto);
        }
        echo '</div>';
    }

    echo '<span id="count-produtos" style="display:none">' . count($produtos) . '</span>';
    exit;
}

// ============================
// RENDER NORMAL (não AJAX)
// ============================
$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Produtos - ' . $nomeProjeto;
$navbarMode = 'full';
$baseUrl = '';
$pageCSS = 'produtos';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<main>
    <section class="section">

        <div class="produtos-container">

            <!-- SIDEBAR DE CATEGORIAS -->
            <aside class="produtos-sidebar">
                <h3 class="sidebar-titulo">Categorias</h3>

                <ul class="sidebar-lista">
                    <li>
                        <a href="produtos.php" class="sidebar-item <?= $categoria_id === 0 ? 'active' : '' ?>">
                            <span>Todos os produtos</span>
                        </a>
                    </li>

                    <?php foreach ($categorias as $cat): ?>
                    <li>
                        <a href="produtos.php?categoria=<?= (int) $cat['id'] ?>"
                            class="sidebar-item <?= $categoria_id === (int) $cat['id'] ? 'active' : '' ?>">
                            <span><?= htmlspecialchars($cat['nome']) ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <!-- ÁREA PRINCIPAL -->
            <section class="produtos-content">

                <div class="produtos-cabecalho">
                    <h1 class="produtos-titulo">Shop / Loja</h1>

                    <div class="produtos-cabecalho-direita">
                        <p class="produtos-count">
                            <?= count($produtos) ?>
                            <?= count($produtos) === 1 ? 'produto' : 'produtos' ?>
                        </p>

                        <div class="produtos-ordenar">
                            <label for="ordenar-select">Ordenar:</label>
                            <select id="ordenar-select" class="select-ordem"
                                data-ordem-atual="<?= htmlspecialchars($ordem) ?>">
                                <option value="recentes" <?= $ordem === 'recentes'   ? 'selected' : '' ?>>Mais recentes
                                </option>
                                <option value="antigos" <?= $ordem === 'antigos'    ? 'selected' : '' ?>>Mais antigos
                                </option>
                                <option value="preco_asc" <?= $ordem === 'preco_asc'  ? 'selected' : '' ?>>Preço: menor
                                    para maior</option>
                                <option value="preco_desc" <?= $ordem === 'preco_desc' ? 'selected' : '' ?>>Preço: maior
                                    para menor</option>
                                <option value="nome_asc" <?= $ordem === 'nome_asc'   ? 'selected' : '' ?>>Nome: A-Z
                                </option>
                                <option value="nome_desc" <?= $ordem === 'nome_desc'  ? 'selected' : '' ?>>Nome: Z-A
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if (empty($produtos)): ?>
                <div class="produtos-vazio">
                    <p>Nenhum produto encontrado nesta categoria.</p>
                </div>
                <?php else: ?>
                <div class="vitrine">
                    <?php foreach ($produtos as $produto): ?>
                    <?= renderCard($produto) ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </section>

        </div>

    </section>
</main>

<script src="assets/js/produtos.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>