<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Gerir Pedidos - ' . $nomeProjeto;
$baseUrl = '../../';

try {
    $stmt = $pdo->query("
        SELECT id, nome_cliente, email_cliente, total, status, criado_em 
        FROM pedidos
        ORDER BY id DESC
    ");

    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $pedidos = [];
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../admin/includes/admin_sidebar.php';
?>

<main>
    <section class="section">
        <?php require __DIR__ . '/../includes/admin_flash_render.php'; ?>

        <h1>Pedidos</h1>

        <?php if (empty($pedidos)): ?>
        <p>Nenhum pedido encontrado.</p>
        <?php else: ?>

        <table border="1" cellpadding="8" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?= $pedido['id'] ?></td>
                    <td><?= htmlspecialchars($pedido['nome_cliente']) ?></td>
                    <td><?= htmlspecialchars($pedido['email_cliente']) ?></td>
                    <td><?= number_format($pedido['total'], 2, ',', '.') ?> Kz</td>
                    <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                    <td><?= htmlspecialchars($pedido['status']) ?></td>
                    <td>
                        <a href="visualizar.php?id=<?= $pedido['id'] ?>">
                            Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>

        <?php endif; ?>

    </section>
</main>

<?php require_once __DIR__ . '/../../admin/includes/admin_footer.php'; ?>