<?php
session_start();
require __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$navbarMode  = 'simple';
$baseUrl     = '../';
$pageTitle   = 'Checkout - ' . $nomeProjeto;
$pageCSS     = 'checkout';

// Redireciona se não estiver logado
if (empty($_SESSION['usuario_id'])) {
    header('Location: /PA-E-COMMERCE/auth/login.php?redirect=checkout.php');
    exit;
}

$carrinho = $_SESSION['carrinho'] ?? [];
$produtosCarrinho = [];
$totalGeral = 0;

if (!empty($carrinho)) {
    $ids = array_keys($carrinho);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, nome, preco FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($produtos as $p) {
        $qtd = (int)$carrinho[$p['id']];
        $subtotal = $p['preco'] * $qtd;
        $totalGeral += $subtotal;

        $produtosCarrinho[] = [
            'nome' => $p['nome'],
            'preco' => $p['preco'],
            'quantidade' => $qtd,
            'subtotal' => $subtotal
        ];
    }
}

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<main class="checkout-main">
    <div class="checkout-container">

        <h1 class="checkout-titulo">Confirmação do Pedido</h1>

        <?php if (empty($produtosCarrinho)): ?>
        <div class="checkout-vazio">
            <h2>O seu carrinho está vazio</h2>
            <a href="../produtos.php" class="btn-voltar">Ver Produtos</a>
        </div>
        <?php else: ?>

        <div class="checkout-layout">

            <!-- FORMULÁRIO -->
            <div class="checkout-form">

                <form method="POST" action="/PA-E-COMMERCE/actions/finalizar_pedido.php" class="form-checkout">

                    <h2 class="form-titulo">Dados do Cliente</h2>

                    <div class="form-grupo">
                        <label>Nome Completo</label>
                        <input type="text" name="nome_cliente"
                            value="<?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? ''); ?>" required>
                    </div>

                    <div class="form-grupo">
                        <label>E-mail</label>
                        <input type="email" name="email_cliente"
                            value="<?php echo htmlspecialchars($_SESSION['usuario_email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-grupo">
                        <label>Endereço Completo</label>
                        <textarea name="endereco" rows="3" required></textarea>
                    </div>

                    <div class="form-grupo">
                        <label>Observações (opcional)</label>
                        <textarea name="observacoes" rows="3"></textarea>
                    </div>


                    <button type="submit" class="btn-finalizar">
                        FINALIZAR PEDIDO
                    </button>

                </form>

            </div>

            <!-- RESUMO -->
            <aside class="checkout-resumo">

                <h2 class="resumo-titulo">Resumo do Pedido</h2>

                <?php foreach ($produtosCarrinho as $item): ?>
                <div class="resumo-item">
                    <span><?php echo htmlspecialchars($item['nome']); ?> x<?php echo $item['quantidade']; ?></span>
                    <strong><?php echo number_format($item['subtotal'], 2, ',', '.'); ?> Kz</strong>
                </div>
                <?php endforeach; ?>

                <hr>

                <div class="resumo-total">
                    <span>Total</span>
                    <span class="total-valor">
                        <?php echo number_format($totalGeral, 2, ',', '.'); ?> Kz
                    </span>
                </div>

            </aside>

        </div>

        <?php endif; ?>

    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>