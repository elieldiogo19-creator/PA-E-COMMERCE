<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Detalhes do Cliente - ' . $nomeProjeto;
$baseUrl = '../../';

// Buscar cliente
$stmt = $pdo->prepare("
    SELECT id, nome, email, criado_em
    FROM usuarios
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    header('Location: listar.php');
    exit;
}

// Resumo do cliente
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_pedidos,
        COALESCE(SUM(total), 0) AS total_gasto,
        MAX(criado_em) AS ultimo_pedido
    FROM pedidos
    WHERE usuario_id = ?
");
$stmt->execute([$id]);
$resumo = $stmt->fetch(PDO::FETCH_ASSOC);

// Pedidos do cliente
$stmt = $pdo->prepare("
    SELECT id, total, status, criado_em
    FROM pedidos
    WHERE usuario_id = ?
    ORDER BY id DESC
");
$stmt->execute([$id]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../admin/includes/admin_sidebar.php';
?>

<main>
    <section class="section">
        <h1>Cliente #<?= (int) $cliente['id'] ?></h1>

        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['nome']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($cliente['email']) ?></p>
        <p><strong>Registrado em:</strong> <?= date('d/m/Y H:i', strtotime($cliente['criado_em'])) ?></p>

        <hr>

        <h2>Resumo</h2>
        <p><strong>Total de pedidos:</strong> <?= (int) $resumo['total_pedidos'] ?></p>
        <p><strong>Total gasto:</strong> <?= number_format((float) $resumo['total_gasto'], 2, ',', '.') ?> Kz</p>

        <?php if (!empty($resumo['ultimo_pedido'])): ?>
            <p><strong>Último pedido:</strong> <?= date('d/m/Y H:i', strtotime($resumo['ultimo_pedido'])) ?></p>
        <?php endif; ?>

        <hr>

        <h2>Pedidos do cliente</h2>

        <?php if (empty($pedidos)): ?>
            <p>Este cliente ainda não fez pedidos.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" width="100%">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?= (int) $pedido['id'] ?></td>
                            <td><?= number_format((float) $pedido['total'], 2, ',', '.') ?> Kz</td>
                            <td><?= htmlspecialchars($pedido['status']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                            <td>
                                <a href="../pedidos/visualizar.php?id=<?= (int) $pedido['id'] ?>">Ver pedido</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="listar.php">Voltar</a></p>
    </section>
</main>

<?php require_once __DIR__ . '/../../admin/includes/admin_footer.php'; ?>