<?php
ob_start();
session_start();
require __DIR__ . '/config/db.php';

// Detectar se é AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Filtros via GET
$categoria_id = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;
$ordem        = isset($_GET['ordem'])     ? trim($_GET['ordem'])     : 'recentes';

// 🆕 PAGINAÇÃO
$porPagina    = 8;
$paginaAtual  = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
$offset       = ($paginaAtual - 1) * $porPagina;

// Whitelist de ordenações
$ordensPermitidas = [
    'recentes'   => 'p.criado_em DESC',
    'antigos'    => 'p.criado_em ASC',
    'preco_asc'  => 'p.preco ASC',
    'preco_desc' => 'p.preco DESC',
    'nome_asc'   => 'p.nome ASC',
    'nome_desc'  => 'p.nome DESC',
];

if (!array_key_exists($ordem, $ordensPermitidas)) {
    $ordem = 'recentes';
}

$orderBy = $ordensPermitidas[$ordem];

try {
    $stmtCat = $pdo->query("
        SELECT id, nome
        FROM categorias
        ORDER BY nome ASC
    ");
    $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    $campos = "
        p.id, p.nome, p.descricao_curta, p.preco, p.imagem, p.estoque,
        c.nome AS categoria_nome
    ";

    // 🆕 Contar total de produtos (com filtro)
    if ($categoria_id > 0) {
        $stmtCount = $pdo->prepare("
            SELECT COUNT(*)
            FROM produtos p
            WHERE p.categoria_id = ?
        ");
        $stmtCount->execute([$categoria_id]);
    } else {
        $stmtCount = $pdo->query("SELECT COUNT(*) FROM produtos");
    }
    $totalProdutos = (int) $stmtCount->fetchColumn();

    // 🆕 Calcular total de páginas
    $totalPaginas = max(1, (int) ceil($totalProdutos / $porPagina));

    // Se pedir página que não existe, volta pra última
    if ($paginaAtual > $totalPaginas) {
        $paginaAtual = $totalPaginas;
        $offset      = ($paginaAtual - 1) * $porPagina;
    }

    // Buscar produtos (com paginação)
    if ($categoria_id > 0) {
        $stmt = $pdo->prepare("
            SELECT $campos
            FROM produtos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.categoria_id = ?
            ORDER BY $orderBy
            LIMIT $porPagina OFFSET $offset
        ");
        $stmt->execute([$categoria_id]);
    } else {
        $stmt = $pdo->query("
            SELECT $campos
            FROM produtos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            ORDER BY $orderBy
            LIMIT $porPagina OFFSET $offset
        ");
    }

    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $produtos      = [];
    $categorias    = [];
    $totalProdutos = 0;
    $totalPaginas  = 1;
}

// ==========================
// FUNÇÃO PARA RENDERIZAR UM CARD
// ==========================
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

// 🆕 ==========================
// FUNÇÃO PARA RENDERIZAR A PAGINAÇÃO
// ==========================
function renderPaginacao(int $paginaAtual, int $totalPaginas, int $categoria_id, string $ordem): string {
    if ($totalPaginas <= 1) return '';

    // Base para os links (mantém filtros)
    $qs = [];
    if ($categoria_id > 0) $qs['categoria'] = $categoria_id;
    if ($ordem !== 'recentes') $qs['ordem'] = $ordem;

    $buildUrl = function(int $pagina) use ($qs) {
        $qs['pagina'] = $pagina;
        return 'produtos.php?' . http_build_query($qs);
    };

    $html = '<nav class="paginacao" aria-label="Navegação de páginas">';

    // ---------- Anterior ----------
    if ($paginaAtual > 1) {
        $html .= '<a href="' . $buildUrl($paginaAtual - 1) . '" class="pag-btn pag-nav" data-pagina="' . ($paginaAtual - 1) . '" aria-label="Página anterior">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    Anterior
                  </a>';
    } else {
        $html .= '<span class="pag-btn pag-nav disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    Anterior
                  </span>';
    }

    // ---------- Números ----------
    // Estratégia: 1 ... (atual-1) (atual) (atual+1) ... (última)
    $numeros = [];

    if ($totalPaginas <= 7) {
        // Mostra todas
        for ($i = 1; $i <= $totalPaginas; $i++) $numeros[] = $i;
    } else {
        $numeros[] = 1;

        if ($paginaAtual > 3)  $numeros[] = '...';

        $inicio = max(2, $paginaAtual - 1);
        $fim    = min($totalPaginas - 1, $paginaAtual + 1);

        for ($i = $inicio; $i <= $fim; $i++) $numeros[] = $i;

        if ($paginaAtual < $totalPaginas - 2) $numeros[] = '...';

        $numeros[] = $totalPaginas;
    }

    foreach ($numeros as $num) {
        if ($num === '...') {
            $html .= '<span class="pag-btn pag-dots">…</span>';
        } elseif ($num === $paginaAtual) {
            $html .= '<span class="pag-btn pag-num active">' . $num . '</span>';
        } else {
            $html .= '<a href="' . $buildUrl($num) . '" class="pag-btn pag-num" data-pagina="' . $num . '">' . $num . '</a>';
        }
    }

    // ---------- Próximo ----------
    if ($paginaAtual < $totalPaginas) {
        $html .= '<a href="' . $buildUrl($paginaAtual + 1) . '" class="pag-btn pag-nav" data-pagina="' . ($paginaAtual + 1) . '" aria-label="Próxima página">
                    Próximo
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                  </a>';
    } else {
        $html .= '<span class="pag-btn pag-nav disabled">
                    Próximo
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                  </span>';
    }

    $html .= '</nav>';
    return $html;
}

// ==========================
// SE FOR AJAX, RETORNA APENAS OS CARDS + PAGINAÇÃO
// ==========================
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

        // 🆕 Paginação junto com os cards
        echo renderPaginacao($paginaAtual, $totalPaginas, $categoria_id, $ordem);
    }

    echo '<span id="count-produtos" style="display:none">' . count($produtos) . '</span>';
    echo '<span id="total-produtos" style="display:none">' . $totalProdutos . '</span>';
    echo '<span id="pagina-atual" style="display:none">' . $paginaAtual . '</span>';
    echo '<span id="total-paginas" style="display:none">' . $totalPaginas . '</span>';
    exit;
}

// ==========================
// RENDER NORMAL (não AJAX)
// ==========================
$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Produtos - ' . $nomeProjeto;
$navbarMode  = 'full';
$baseUrl     = '';
$pageCSS     = 'produtos';

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

            <section class="produtos-content">

                <div class="produtos-cabecalho">
                    <h1 class="produtos-titulo">Shop / Loja</h1>

                    <div class="produtos-cabecalho-direita">
                        <!-- 🆕 Info detalhada de paginação -->
                        <p class="produtos-count">
                            <?php
                            $inicioItem = $totalProdutos > 0 ? ($offset + 1) : 0;
                            $fimItem    = min($offset + $porPagina, $totalProdutos);
                            ?><?= $totalProdutos ?></strong>
                            <?= $totalProdutos === 1 ? 'produto' : 'produtos' ?>
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

                <!-- 🆕 Container que será atualizado por AJAX (vitrine + paginação juntos) -->
                <div id="produtos-wrapper">
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

                    <?= renderPaginacao($paginaAtual, $totalPaginas, $categoria_id, $ordem) ?>
                    <?php endif; ?>
                </div>

            </section>

        </div>
    </section>
</main>

<script src="assets/js/produtos.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>