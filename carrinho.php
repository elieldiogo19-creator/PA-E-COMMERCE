<?php
session_start();
require __DIR__ . '/config/db.php';

$carrinho = $_SESSION['carrinho'] ?? [];

// Se o carrinho estiver vazio
if (empty($carrinho)) {
    $produtosCarrinho = [];
    $totalGeral = 0;
} else {
    // Pega todos os IDs de produtos no carrinho
    $ids = array_keys($carrinho);

    // Monta placeholders para o IN (?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, nome, preco FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Reorganiza por ID pra facilitar
    $produtosPorId = [];
    foreach ($produtos as $p) {
        $produtosPorId[$p['id']] = $p;
    }

    $produtosCarrinho = [];
    $totalGeral = 0;

    foreach ($carrinho as $idProduto => $quantidade) {
        if (!isset($produtosPorId[$idProduto])) {
            continue; // produto apagado do banco, ignora
        }
        $p = $produtosPorId[$idProduto];

        $subtotal = $p['preco'] * $quantidade;
        $totalGeral += $subtotal;

        $produtosCarrinho[] = [
            'id'         => $p['id'],
            'nome'       => $p['nome'],
            'preco'      => $p['preco'],
            'quantidade' => $quantidade,
            'subtotal'   => $subtotal,
        ];
    }
}

// Remover item (por GET: carrinho.php?remover=ID)
if (isset($_GET['remover'])) {
    $idRemover = (int) $_GET['remover'];
    if (isset($_SESSION['carrinho'][$idRemover])) {
        unset($_SESSION['carrinho'][$idRemover]);
    }
    header('Location: carrinho.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Carrinho</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Carrinho de compras</h1>

  <?php if (empty($produtosCarrinho)): ?>
    <p>Seu carrinho está vazio.</p>
    <p><a href="produtos.php">Ver produtos</a> | <a href="index.php">Página Inicial</a></p>
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
          <td><?php echo $item['quantidade']; ?></td>
          <td><?php echo number_format($item['subtotal'], 2, ',', '.'); ?> AOA</td>
          <td>
            <a href="carrinho.php?remover=<?php echo $item['id']; ?>">Remover</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <h2>Total: <?php echo number_format($totalGeral, 2, ',', '.'); ?>  AOA</h2>

    <p><a href="produtos.php">Continuar comprando</a></p>
    <p><a href="checkout.php">Finalizar compra</a> <!-- vamos criar depois --></p>
  <?php endif; ?>
</body>
</html>