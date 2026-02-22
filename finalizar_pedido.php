<?php
session_start();
require __DIR__ . '/config/db.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$carrinho = $_SESSION['carrinho'] ?? [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($carrinho)) {
    header('Location: carrinho.php');
    exit;
}

$nomeCliente  = trim($_POST['nome_cliente']  ?? '');
$emailCliente = trim($_POST['email_cliente'] ?? '');
$endereco     = trim($_POST['endereco']      ?? '');
$observacoes  = trim($_POST['observacoes']   ?? '');

$erros = [];

if ($nomeCliente === '' || $emailCliente === '' || $endereco === '') {
    $erros[] = 'Preencha nome, e-mail e endereço.';
}

if (!filter_var($emailCliente, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'E-mail inválido.';
}

if (!empty($erros)) {
    // Em um projeto maior, você poderia guardar os erros em sessão e voltar pro checkout
    foreach ($erros as $e) {
        echo "<p>$e</p>";
    }
    echo '<p><a href="checkout.php">Voltar</a></p>';
    exit;
}

// Recalcula os itens e o total a partir do carrinho (igual ao checkout)
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

$itensPedido = [];
$totalGeral = 0;

foreach ($carrinho as $idProduto => $quantidade) {
    if (!isset($produtosPorId[$idProduto])) {
        continue;
    }
    $p = $produtosPorId[$idProduto];
    $subtotal = $p['preco'] * $quantidade;
    $totalGeral += $subtotal;

    $itensPedido[] = [
        'produto_id'    => $p['id'],
        'quantidade'    => $quantidade,
        'preco_unitario'=> $p['preco'],
    ];
}

if (empty($itensPedido)) {
    echo '<p>Não há itens válidos no carrinho.</p>';
    echo '<p><a href="produtos.php">Ver produtos</a></p>';
    exit;
}

// Grava no banco usando transação
try {
    $pdo->beginTransaction();

    // Cria o pedido
    $stmt = $pdo->prepare("
        INSERT INTO pedidos (usuario_id, nome_cliente, email_cliente, endereco, total)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['usuario_id'],
        $nomeCliente,
        $emailCliente,
        $endereco,
        $totalGeral
    ]);

    $pedidoId = $pdo->lastInsertId();

    // Insere os itens
    $stmtItem = $pdo->prepare("
        INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($itensPedido as $item) {
        $stmtItem->execute([
            $pedidoId,
            $item['produto_id'],
            $item['quantidade'],
            $item['preco_unitario']
        ]);
    }

    $pdo->commit();

    // Limpa o carrinho
    unset($_SESSION['carrinho']);

} catch (PDOException $e) {
    $pdo->rollBack();
    die('Erro ao salvar pedido: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Pedido concluído</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Pedido concluído!</h1>
  <p>Seu pedido foi registrado com sucesso.</p>
  <p>Número do pedido: <?php echo htmlspecialchars($pedidoId); ?></p>
  <p>Total: <?php echo number_format($totalGeral, 2, ',', '.'); ?> AOA</p>

  <p><a href="produtos.php">Voltar para os produtos</a></p>
</body>
</html>