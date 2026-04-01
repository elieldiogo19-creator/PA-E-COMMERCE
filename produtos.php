<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA LDA,';
$pageTitle = 'Produtos - ' . $nomeProjeto;
$navbarMode = 'full';
$baseUrl = '';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';

try {
    $stmt = $pdo->query("
        SELECT id, nome, descricao, preco, imagem
        FROM produtos
        ORDER BY criado_em DESC
    ");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
}
?>

<main>
    <section class="section">
        <h1>Shop / Produtos</h1>
        <p>Explore todos os produtos disponíveis na nossa loja.</p>

        <?php if (empty($produtos)): ?>
            <p>Nenhum produto cadastrado.</p>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($produtos as $produto): ?>
                    <article class="product-card">
                        <?php if (!empty($produto['imagem'])): ?>
                            <img
                                src="<?php echo htmlspecialchars($produto['imagem']); ?>"
                                alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                class="product-image"
                            >
                        <?php endif; ?>

                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

                        <p class="product-desc">
                            <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
                        </p>

                        <p class="product-price">
                            <?php echo number_format($produto['preco'], 2, ',', '.'); ?> AOA
                        </p>

                        <div class="product-actions">
                            <a href="pages/detalhes.php?id=<?php echo (int) $produto['id']; ?>" class="btn">
                                Saber mais
                            </a>

                            <a href="actions/adicionar_ao_carrinho.php?id=<?php echo (int) $produto['id']; ?>" class="btn btn-primary">
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