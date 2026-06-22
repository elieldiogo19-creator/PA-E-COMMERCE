<?php
session_start();
require __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$navbarMode = 'full';
$baseUrl = '../';

// Capturar ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('Produto inválido.');
}

// Buscar produto + categoria
try {
    $stmt = $pdo->prepare("
        SELECT 
            p.id, p.nome, p.descricao, p.preco, p.imagem, p.estoque,
            c.id AS categoria_id,
            c.nome AS categoria_nome
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        WHERE p.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        die('Produto não encontrado.');
    }

} catch (PDOException $e) {
    die('Erro ao carregar o produto.');
}

// Título dinâmico
$pageTitle = $produto['nome'] . ' - ' . $nomeProjeto;

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<main>
    <section class="section">

        <p>
            <a href="../produtos.php">Voltar para a loja</a>
        </p>

        <div class="product-detail">

            <div class="product-detail-image">
                <?php if (!empty($produto['imagem'])): ?>
                    <img
                        src="../<?= htmlspecialchars($produto['imagem']) ?>"
                        alt="<?= htmlspecialchars($produto['nome']) ?>"
                        class="product-image"
                    >
                <?php endif; ?>
            </div>

            <div class="product-detail-info">

                <h1><?= htmlspecialchars($produto['nome']) ?></h1>

                <?php if (!empty($produto['categoria_nome'])): ?>
                    <p class="product-categoria">
                        Categoria:
                        <a href="../produtos.php?categoria=<?= (int) $produto['categoria_id'] ?>">
                            <?= htmlspecialchars($produto['categoria_nome']) ?>
                        </a>
                    </p>
                <?php endif; ?>

                <div class="product-description">
                    <p>
                        <?= nl2br(htmlspecialchars($produto['descricao'] ?? '')) ?>
                    </p>
                </div>

                <p class="product-price">
                    <?= number_format($produto['preco'], 2, ',', '.') ?> Kz
                </p>

                <p class="product-estoque">
                    <?php if ($produto['estoque'] > 0): ?>
                        Em estoque (<?= (int) $produto['estoque'] ?> disponíveis)
                    <?php else: ?>
                        Produto esgotado
                    <?php endif; ?>
                </p>

                <div class="product-actions">

                    <?php if ($produto['estoque'] > 0): ?>
                        <a href="../actions/adicionar_ao_carrinho.php?id=<?= (int) $produto['id'] ?>"
                           class="btn btn-primary">
                            Adicionar ao carrinho
                        </a>
                    <?php else: ?>
                        <button class="btn btn-disabled" disabled>
                            Indisponível
                        </button>
                    <?php endif; ?>

                </div>

            </div>

        </div>

    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>