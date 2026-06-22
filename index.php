<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Home - ' . $nomeProjeto;
$navbarMode = 'full';
$baseUrl = '';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';

// Buscar produtos em destaque (mais recentes)
try {
    $stmt = $pdo->query("
        SELECT 
            p.id, p.nome, p.preco, p.imagem,
            c.nome AS categoria_nome
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        ORDER BY p.criado_em DESC
        LIMIT 4
    ");
    $produtosDestaque = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtosDestaque = [];
}

// Buscar categorias para a seção de categorias
try {
    $stmt = $pdo->query("
        SELECT 
            c.id, c.nome,
            COUNT(p.id) AS total_produtos
        FROM categorias c
        LEFT JOIN produtos p ON p.categoria_id = c.id
        GROUP BY c.id
        ORDER BY c.nome ASC
    ");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias = [];
}
?>

<main>

    <!-- HERO / APRESENTAÇÃO -->
    <section class="hero">
        <h1><?= htmlspecialchars($nomeProjeto) ?></h1>
        <p>Plataforma de e-commerce para compra de produtos e solicitação de serviços.</p>
    </section>

    <!-- CATEGORIAS -->
    <section class="section">
        <h2>Categorias</h2>

        <?php if (empty($categorias)): ?>
            <p>Nenhuma categoria disponível.</p>
        <?php else: ?>
            <div class="categorias-grid">
                <?php foreach ($categorias as $cat): ?>
                    <a href="produtos.php?categoria=<?= (int) $cat['id'] ?>" class="categoria-card">
                        <h3><?= htmlspecialchars($cat['nome']) ?></h3>
                        <p><?= (int) $cat['total_produtos'] ?> produto(s)</p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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

        <p>
            <a href="produtos.php">Ver todos os produtos</a>
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