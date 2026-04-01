<?php
session_start();
require __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA LDA';
$navbarMode = 'simple';
$baseUrl = '../';

// Verificar login
if (empty($_SESSION['usuario_id'])) {
    header('Location: /PA-E-COMMERCE/auth/login.php?from=checkout');
    exit;
}

// Só aceita POST e carrinho não vazio
$carrinho = $_SESSION['carrinho'] ?? [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($carrinho)) {
    header('Location: /PA-E-COMMERCE/pages/carrinho.php');
    exit;
}

// Receber dados do formulário
$nomeCliente  = trim($_POST['nome_cliente']  ?? '');
$emailCliente = trim($_POST['email_cliente'] ?? '');
$endereco     = trim($_POST['endereco']      ?? '');
$observacoes  = trim($_POST['observacoes']   ?? '');

// Validações
$erros = [];

if ($nomeCliente === '' || $emailCliente === '' || $endereco === '') {
    $erros[] = 'Preencha nome, e-mail e endereço.';
}

if (!filter_var($emailCliente, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'E-mail inválido.';
}

// Se houver erros
if (!empty($erros)) {
    $pageTitle = 'Erro no pedido - ' . $nomeProjeto;

    require __DIR__ . '/../includes/header.php';
    require __DIR__ . '/../includes/navbar.php';
    ?>

    <main>
        <section class="section">
            <h1>Erro ao finalizar pedido</h1>

            <div class="alert alert-erro">
                <ul>
                    <?php foreach ($erros as $e): ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <p><a href="/PA-E-COMMERCE/pages/checkout.php">Voltar ao checkout</a></p>
        </section>
    </main>

    <?php
    require __DIR__ . '/../includes/footer.php';
    exit;
}

// Recalcula os itens a partir do carrinho
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
        'produto_id'     => $p['id'],
        'quantidade'     => $quantidade,
        'preco_unitario' => $p['preco'],
    ];
}

// Se não houver itens válidos
if (empty($itensPedido)) {
    header('Location: /PA-E-COMMERCE/produtos.php');
    exit;
}

// Gravar no banco usando transação
try {
    $pdo->beginTransaction();

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
    die('Ocorreu um erro ao salvar o pedido. Tente novamente.');
}

$pageTitle = 'Pedido concluído - ' . $nomeProjeto;

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<main>
    <section class="section">
        <h1>Pedido concluído!</h1>

        <p>O seu pedido foi registado com sucesso.</p>

        <p><strong>Número do pedido:</strong> #<?php echo htmlspecialchars($pedidoId); ?></p>

        <p><strong>Total:</strong> <?php echo number_format($totalGeral, 2, ',', '.'); ?> AOA</p>

        <p><a href="/PA-E-COMMERCE/produtos.php">Voltar para a loja</a></p>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>