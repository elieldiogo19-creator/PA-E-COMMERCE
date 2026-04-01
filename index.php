<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA LDA,';
$pageTitle = 'Home - ' . $nomeProjeto;
$navbarMode = 'full';
$baseUrl = '';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';

// Buscar produtos em destaque
try {
    $stmt = $pdo->query("
        SELECT id, nome, descricao, preco, imagem
        FROM produtos
        ORDER BY criado_em DESC
        LIMIT 4
    ");
    $produtosDestaque = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtosDestaque = [];
}
?>

<main>
    <!-- HERO / APRESENTAÇÃO -->
    <section class="hero">
        <h1><?php echo htmlspecialchars($nomeProjeto); ?></h1>
        <p>Plataforma de e-commerce para compra de produtos e solicitação de serviços.</p>
    </section>

    <!-- PRODUTOS EM DESTAQUE -->
    <section class="section">
        <h2>Produtos em destaque</h2>

        <?php if (empty($produtosDestaque)): ?>
            <p>Nenhum produto em destaque no momento.</p>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($produtosDestaque as $produto): ?>
                    <article class="product-card">
                        <?php if (!empty($produto['imagem'])): ?>
                            <img
                                src="<?php echo htmlspecialchars($produto['imagem']); ?>"
                                alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                class="product-image"
                            >
                        <?php endif; ?>

                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

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

        <p>
            <a href="produtos.php">Ver todos os produtos →</a>
        </p>
    </section>

    <!-- SECÇÕES FUTURAS -->
    <!--
    Aqui podem entrar depois:
    - serviços
    - sobre a empresa
    - contacto
    -->
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>