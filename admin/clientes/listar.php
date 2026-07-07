<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Clientes - ' . $nomeProjeto;
$baseUrl = '../../';

try {
    $stmt = $pdo->query("
        SELECT 
            u.id,
            u.nome,
            u.email,
            u.criado_em,
            COUNT(p.id) AS total_pedidos
        FROM usuarios u
        LEFT JOIN pedidos p ON p.usuario_id = u.id
        GROUP BY u.id
        ORDER BY u.id DESC
    ");

    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clientes = [];
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../admin/includes/admin_sidebar.php';
?>

<main>
    <section class="section">
        <h1>Clientes</h1>

        <?php if (empty($clientes)): ?>
            <p>Nenhum cliente encontrado.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Pedidos</th>
                        <th>Criado em</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= (int) $cliente['id'] ?></td>
                            <td><?= htmlspecialchars($cliente['nome']) ?></td>
                            <td><?= htmlspecialchars($cliente['email']) ?></td>
                            <td><?= (int) $cliente['total_pedidos'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($cliente['criado_em'])) ?></td>
                            <td>
                                <a href="visualizar.php?id=<?= (int) $cliente['id'] ?>">Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../../admin/includes/admin_footer.php'; ?>