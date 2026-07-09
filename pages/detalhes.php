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

<!-- CSS específico da página de detalhes (estilo do teu colega) -->
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/detalhes.css">

<main class="detalhes-main">
    <div class="container-producto">

        <!-- Coluna 1: Imagem (Card cinza) -->
        <div class="card">
            <button class="btn-voltar" onclick="history.back()">
                ← Voltar
            </button>

            <div class="bloco-image">
                <?php if (!empty($produto['imagem'])): ?>
                <img id="foto-dinamica" src="<?php echo $baseUrl . htmlspecialchars($produto['imagem']); ?>"
                    alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                <?php else: ?>
                <img id="foto-dinamica" src="<?php echo $baseUrl; ?>assets/img/produto-sem-imagem.png" alt="Sem imagem">
                <?php endif; ?>
            </div>
        </div>

        <!-- Coluna 2: Informações -->
        <div class="bloco-info">
            <h1 id="nome-dinamico"><?php echo htmlspecialchars($produto['nome']); ?></h1>

            <p class="preco" id="preco-dinamico">
                <?php echo number_format($produto['preco'], 2, ',', '.'); ?> Kz
            </p>

            <div class="descricao-container">
                <strong>Descrição:</strong>
                <p id="descricao-dinamica">
                    <?php echo nl2br(htmlspecialchars($produto['descricao'] ?? 'Sem descrição disponível.')); ?>
                </p>
            </div>

            <!-- Estoque -->
            <p class="product-estoque" style="margin-top: 20px; font-size: 14px; color: #666;">
                <?php if ($produto['estoque'] > 0): ?>
                Em estoque (<?php echo (int) $produto['estoque']; ?> disponíveis)
                <?php else: ?>
                <span style="color: #e94f57; font-weight: bold;">Produto esgotado</span>
                <?php endif; ?>
            </p>
        </div>

        <!-- Coluna 3: Botões de Ação -->
        <div class="click">
            <hr class="linha-separadora">

            <?php if ($produto['estoque'] > 0): ?>
            <a href="#" class="btn-carrinho btn-adicionar-detalhes" data-id="<?php echo (int) $produto['id']; ?>"
                data-nome="<?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                <i class="fas fa-shopping-cart"></i> Adicionar ao carrinho
            </a>

            <!-- SEPARADOR "OU" -->
            <div class="ou-separador">
                <span>Ou</span>
            </div>

            <button class="btn-comprar"
                onclick="window.location.href='<?php echo $baseUrl; ?>pages/checkout.php?produto=<?php echo (int) $produto['id']; ?>'">
                Comprar Agora
            </button>
            <?php else: ?>
            <button class="btn-carrinho" disabled style="opacity: 0.5; cursor: not-allowed;">
                <i class="fas fa-times-circle"></i> Indisponível
            </button>
            <?php endif; ?>

            <hr class="linha-separadora">
        </div>

    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>