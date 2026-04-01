<?php
session_start();
require __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA LDA,';
$navbarMode = 'simple';
$baseUrl = '../';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';

// Limpar carrinho inteiro
if (isset($_GET['limpar'])) {
    unset($_SESSION['carrinho']);
    header('Location: carrinho.php');
    exit;
}

// Remover item
if (isset($_GET['remover'])) {
    $idRemover = (int) $_GET['remover'];

    if (isset($_SESSION['carrinho'][$idRemover])) {
        unset($_SESSION['carrinho'][$idRemover]);
    }

    header('Location: carrinho.php');
    exit;
}

// Aumentar quantidade
if (isset($_GET['aumentar'])) {
    $idAumentar = (int) $_GET['aumentar'];

    if (isset($_SESSION['carrinho'][$idAumentar])) {
        $_SESSION['carrinho'][$idAumentar]++;
    }

    header('Location: carrinho.php');
    exit;
}

// Diminuir quantidade
if (isset($_GET['diminuir'])) {
    $idDiminuir = (int) $_GET['diminuir'];

    if (isset($_SESSION['carrinho'][$idDiminuir])) {
        $_SESSION['carrinho'][$idDiminuir]--;

        if ($_SESSION['carrinho'][$idDiminuir] <= 0) {
            unset($_SESSION['carrinho'][$idDiminuir]);
        }
    }

    header('Location: carrinho.php');
    exit;
}

// Processar carrinho
$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    $produtosCarrinho = [];
    $totalGeral = 0;
} else {
    $ids = array_keys($carrinho);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, nome, preco FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $produtosPorId = [];
    foreach ($produtos as $p) {
        $produtosPorId[$p['id']] = $p;
    }

    $produtosCarrinho = [];
    $totalGeral = 0;

    foreach ($carrinho as $idProduto => $quantidade) {
        if (!isset($produtosPorId[$idProduto])) {
            continue;
        }

        $p = $produtosPorId[$idProduto];
        $subtotal = $p['preco'] * $quantidade;
        $totalGeral += $subtotal;

        $produtosCarrinho[] = [
            'id' => $p['id'],
            'nome' => $p['nome'],
            'preco' => $p['preco'],
            'quantidade' => $quantidade,
            'subtotal' => $subtotal,
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - <?php echo htmlspecialchars($nomeProjeto); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main>
    <section class="section">
        <h1>Carrinho de compras</h1>

        <?php if (empty($produtosCarrinho)): ?>
            <p>O seu carrinho está vazio.</p>
            <p>
                <a href="../produtos.php">Ver produtos</a> |
                <a href="../index.php">Página Inicial</a>
            </p>

        <?php else: ?>

            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço unitário</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtosCarrinho as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nome']); ?></td>
                            <td><?php echo number_format($item['preco'], 2, ',', '.'); ?> AOA</td>
                            <td>
                                <a href="carrinho.php?diminuir=<?php echo (int) $item['id']; ?>">➖</a>
                                <?php echo (int) $item['quantidade']; ?>
                                <a href="carrinho.php?aumentar=<?php echo (int) $item['id']; ?>">➕</a>
                            </td>
                            <td><?php echo number_format($item['subtotal'], 2, ',', '.'); ?> AOA</td>
                            <td>
                                <a href="carrinho.php?remover=<?php echo (int) $item['id']; ?>">🗑️ Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Total: <?php echo number_format($totalGeral, 2, ',', '.'); ?> AOA</h2>

            <p>
                <a href="carrinho.php?limpar=1"
                   onclick="return confirm('Tem certeza que deseja limpar o carrinho?')">
                    🗑️ Limpar carrinho
                </a>
            </p>

            <p><a href="../produtos.php">Continuar comprando</a></p>

            <?php if (!empty($_SESSION['usuario_id'])): ?>
                <p><a href="checkout.php">Finalizar compra</a></p>
            <?php else: ?>
                <p>
                    <a href="/PA-E-COMMERCE/auth/login.php?from=checkout"
   onclick="return confirm('Precisa iniciar sessão para finalizar a compra. Deseja continuar?');">
    Finalizar compra
</a>
                </p>
            <?php endif; ?>

        <?php endif; ?>
    </section>
</main>

<script src="../assets/js/main.js"></script>
</body>
</html>

<?php require __DIR__ . '/../includes/footer.php'; ?>