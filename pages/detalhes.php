<?php
session_start();
require __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$navbarMode = 'full';
$baseUrl = '../';

// Get ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('Produto inválido.');
}

// Search Prod
try {
    $stmt = $pdo->prepare("
        SELECT id, nome, descricao, preco, imagem
        FROM produtos
        WHERE id = ?
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

// auto Prod title function
$pageTitle = $produto['nome'] . ' - ' . $nomeProjeto;

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<main>
    <section class="section">
        <p>
            <a href="../produtos.php">← Voltar para a loja</a>
        </p>

        <div class="product-detail">
            <div class="product-detail-image">
                <?php if (!empty($produto['imagem'])): ?>
                    <img
                        src="../<?php echo htmlspecialchars($produto['imagem']); ?>"
                        alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                        class="product-image"
                    >
                <?php endif; ?>
            </div>

            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
                
                <div class="product-description">
                    <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
                </div>

                <p class="product-price">
                    <?php echo number_format($produto['preco'], 2, ',', '.'); ?> Kz
                </p>


                <div class="product-actions">
                    <a href="../actions/adicionar_ao_carrinho.php?id=<?php echo (int) $produto['id']; ?>" class="btn btn-primary">
                        Adicionar ao carrinho
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>