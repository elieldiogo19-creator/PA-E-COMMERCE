<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Pedidos - ' . $nomeProjeto;
$baseUrl = '../../';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

// Atualizar status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $novoStatus = $_POST['status'] ?? '';

    $permitidos = ['Pendente', 'Confirmado', 'Enviado', 'Concluído', 'Cancelado'];

    if (in_array($novoStatus, $permitidos)) {

        $stmt = $pdo->prepare("
            UPDATE pedidos
            SET status = ?
            WHERE id = ?
        ");

        $stmt->execute([$novoStatus, $id]);

        header("Location: visualizar.php?id=" . $id);
        exit;
    }
}

// Buscar pedido
$stmt = $pdo->prepare("
    SELECT *
    FROM pedidos
    WHERE id = ?
");
$stmt->execute([$id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header('Location: listar.php');
    exit;
}

// Buscar itens
$stmt = $pdo->prepare("
    SELECT ip.*, p.nome
    FROM itens_pedido ip
    JOIN produtos p ON p.id = ip.produto_id
    WHERE ip.pedido_id = ?
");
$stmt->execute([$id]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../admin/includes/admin_sidebar.php';
?>

<main>
<section class="section">

    <h1>Pedido #<?= $pedido['id'] ?></h1>

    <h3>Dados do Cliente</h3>
    <p><strong>Nome:</strong> <?= htmlspecialchars($pedido['nome_cliente']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($pedido['email_cliente']) ?></p>
    <p><strong>Endereço:</strong> <?= nl2br(htmlspecialchars($pedido['endereco'])) ?></p>

    <hr>

    <h3>Status do Pedido</h3>

<form method="POST">
    <select name="status">

        <?php
        $statusPossiveis = ['Pendente', 'Confirmado', 'Enviado', 'Concluído', 'Cancelado'];

        foreach ($statusPossiveis as $status):
        ?>
            <option value="<?= $status ?>"
                <?= $pedido['status'] === $status ? 'selected' : '' ?>>
                <?= $status ?>
            </option>
        <?php endforeach; ?>

    </select>

    <button type="submit">Atualizar Status</button>
</form>

<hr>

    <h3>Itens do Pedido</h3>

    <table border="1" cellpadding="8" width="100%">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço Unitário</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nome']) ?></td>
                    <td><?= number_format($item['preco_unitario'], 2, ',', '.') ?> Kz</td>
                    <td><?= $item['quantidade'] ?></td>
                    <td>
                        <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?> Kz
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <h3>Total: <?= number_format($pedido['total'], 2, ',', '.') ?> Kz</h3>

    <p><a href="listar.php">← Voltar</a></p>

</section>
</main>

<?php require_once __DIR__ . '/../../admin/includes/admin_footer.php'; ?>