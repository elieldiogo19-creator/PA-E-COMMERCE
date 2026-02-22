<?php
session_start();
require __DIR__ . '/config/db.php';

// Se não estiver logado, obriga a logar antes de finalizar
if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Monta os dados do carrinho (mesma lógica do carrinho.php)
$carrinho = $_SESSION['carrinho'] ?? [];

$produtosCarrinho = [];
$totalGeral = 0;

if (!empty($carrinho)) {
    $ids = array_keys($carrinho);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("
        SELECT id, nome, preco
        FROM produtos
        WHERE id IN ($placeholders)
    ");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $produtosPorId = [];
    foreach ($produtos as $p) {
        $produtosPorId[$p['id']] = $p;
    }

    foreach ($carrinho as $idProduto => $quantidade) {
        if (!isset($produtosPorId[$idProduto])) {
            continue;
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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Checkout</h1>

  <?php if (empty($produtosCarrinho)): ?>
    <p>Seu carrinho está vazio.</p>
    <p><a href="produtos.php">Ver produtos</a></p>
  <?php else: ?>

    <h2>Resumo do pedido</h2>
    <table border="1" cellpadding="8" cellspacing="0">
      <thead>
        <tr>
          <th>Produto</th>
          <th>Preço unitário</th>
          <th>Quantidade</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produtosCarrinho as $item): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['nome']); ?></td>
            <td> <?php echo number_format($item['preco'], 2, ',', '.'); ?> AOA</td>
            <td><?php echo $item['quantidade']; ?></td>
            <td> <?php echo number_format($item['subtotal'], 2, ',', '.'); ?> AOA</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h2>Total: <?php echo number_format($totalGeral, 2, ',', '.'); ?> AOA</h2>

    <h2>Dados do cliente</h2>
    <form method="POST" action="finalizar_pedido.php">
      <label>
        Nome:
        <input type="text" name="nome_cliente"
               value="<?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? ''); ?>"
               required>
      </label>
      <br><br>

      <label>
        E-mail:
        <input type="email" name="email_cliente" required>
      </label>
      <br><br>

      <label>
        Endereço:
        <textarea name="endereco" rows="3" required></textarea>
      </label>
      <br><br>

      <label>
        Observações:
        <textarea name="observacoes" rows="3"></textarea>
      </label>
      <br><br>

      <button type="submit">Confirmar pedido</button>
    </form>

  <?php endif; ?>
</body>
</html>