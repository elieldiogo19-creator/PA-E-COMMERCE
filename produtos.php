<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Produtos - ' . $nomeProjeto;
$navbarMode = 'full';
$baseUrl = '';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';

// 🔹 Capturar filtro de categoria via URL
$categoria_id = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;

try {

    // 🔹 Buscar todas categorias (para o menu de filtro)
    $stmtCat = $pdo->query("
        SELECT id, nome 
        FROM categorias 
        ORDER BY nome ASC
    ");
    $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    // 🔹 Buscar produtos (com ou sem filtro)
    if ($categoria_id > 0) {

        $stmt = $pdo->prepare("
            SELECT 
                p.id, p.nome, p.descricao, p.preco, p.imagem,
                c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.categoria_id = ?
            ORDER BY p.criado_em DESC
        ");
        $stmt->execute([$categoria_id]);

    } else {

        $stmt = $pdo->query("
            SELECT 
                p.id, p.nome, p.descricao, p.preco, p.imagem,
                c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            ORDER BY p.criado_em DESC
        ");
    }

    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $produtos = [];
    $categorias = [];
}
?>

<main>
    <section class="section">

        <h1>Shop / Loja</h1>
        <p>Explore todos os produtos disponíveis na nossa loja.</p>

        <!-- 🔹 Filtro de categorias -->
        <nav class="categorias-filtro">
            <a href="produtos.php" 
               class="<?= $categoria_id === 0 ? 'ativo' : '' ?>">
               Todos
            </a>

            <?php foreach ($categorias as $cat): ?>
                <a href="produtos.php?categoria=<?= (int) $cat['id'] ?>"
                   class="<?= $categoria_id === (int) $cat['id'] ? 'ativo' : '' ?>">
                   <?= htmlspecialchars($cat['nome']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if (empty($produtos)): ?>
            <p>Nenhum produto encontrado nesta categoria.</p>
        <?php else: ?>

            <div class="products-grid">

                <?php foreach ($produtos as $produto): ?>

                    <article class="product-card">

                        <?php if (!empty($produto['imagem'])): ?>
                            <img
                                src="<?= htmlspecialchars($produto['imagem']) ?>"
                                alt="<?= htmlspecialchars($produto['nome']) ?>"
                                class="product-image"
                            >
                        <?php endif; ?>

                        <h3><?= htmlspecialchars($produto['nome']) ?></h3>

                        <?php if (!empty($produto['categoria_nome'])): ?>
                            <p class="product-categoria">
                                <?= htmlspecialchars($produto['categoria_nome']) ?>
                            </p>
                        <?php endif; ?>

                        <p class="product-desc">
                            <?= nl2br(htmlspecialchars(mb_strimwidth($produto['descricao'] ?? '', 0, 120, '...'))) ?>
                        </p>

                        <p class="product-price">
                            <?= number_format($produto['preco'], 2, ',', '.') ?> Kz
                        </p>

                        <div class="product-actions">
                            <a href="pages/detalhes.php?id=<?= (int) $produto['id'] ?>" class="btn">
                                Saber mais
                            </a>

                            <a href="actions/adicionar_ao_carrinho.php?id=<?= (int) $produto['id'] ?>" class="btn btn-primary">
                                Adicionar ao carrinho
                            </a>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>