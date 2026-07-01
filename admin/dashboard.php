<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Dashboard Admin - ' . $nomeProjeto;
$baseUrl = '../';

// Buscar métricas principais
try {
    $totalProdutos = (int) $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    $totalClientes = (int) $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $totalPedidos  = (int) $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
    $totalFaturado = (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedidos")->fetchColumn();
} catch (PDOException $e) {
    $totalProdutos = 0;
    $totalClientes = 0;
    $totalPedidos  = 0;
    $totalFaturado = 0;
}

// Buscar últimos 5 pedidos
try {
    $stmt = $pdo->query("
        SELECT id, nome_cliente, total, status, criado_em
        FROM pedidos
        ORDER BY id DESC
        LIMIT 5
    ");
    $ultimosPedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $ultimosPedidos = [];
}

// Buscar produtos com estoque baixo (<= 5)
try {
    $stmt = $pdo->query("
        SELECT id, nome, estoque
        FROM produtos
        WHERE estoque <= 5
        ORDER BY estoque ASC
        LIMIT 10
    ");
    $estoqueBaixo = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $estoqueBaixo = [];
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>

<main class="admin-dashboard">

    <section class="section">
        <h1>Painel do Administrador</h1>

        <p>
            Bem-vindo, 
            <strong><?= htmlspecialchars($_SESSION['admin_nome'], ENT_QUOTES, 'UTF-8') ?></strong>.
        </p>

        <?php if (!empty($_SESSION['admin_ultimo_acesso'])): ?>
            <p>
                Último acesso: 
                <?= date('d/m/Y H:i', strtotime($_SESSION['admin_ultimo_acesso'])) ?>
            </p>
        <?php else: ?>
            <p>Primeiro acesso ao painel.</p>
        <?php endif; ?>

    </section>

    <!-- CARDS DE MÉTRICAS -->
    <section class="section dashboard-cards">
        <div class="card">
            <h3>Produtos</h3>
            <p class="card-number"><?= $totalProdutos ?></p>
        </div>

        <div class="card">
            <h3>Clientes</h3>
            <p class="card-number"><?= $totalClientes ?></p>
        </div>

        <div class="card">
            <h3>Pedidos</h3>
            <p class="card-number"><?= $totalPedidos ?></p>
        </div>

        <div class="card">
            <h3>Faturado</h3>
            <p class="card-number"><?= number_format($totalFaturado, 2, ',', '.') ?> Kz</p>
        </div>
    </section>

    <!-- ÚLTIMOS PEDIDOS -->
    <section class="section">
        <h2>Últimos pedidos</h2>

        <?php if (empty($ultimosPedidos)): ?>
            <p>Nenhum pedido registrado ainda.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimosPedidos as $pedido): ?>
                        <tr>
                            <td><?= (int) $pedido['id'] ?></td>
                            <td><?= htmlspecialchars($pedido['nome_cliente']) ?></td>
                            <td><?= number_format($pedido['total'], 2, ',', '.') ?> Kz</td>
                            <td><?= htmlspecialchars($pedido['status']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                            <td>
                                <a href="pedidos/visualizar.php?id=<?= (int) $pedido['id'] ?>">Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <!-- ALERTAS DE ESTOQUE -->
    <section class="section">
        <h2>Alertas de estoque baixo</h2>

        <?php if (empty($estoqueBaixo)): ?>
            <p>Nenhum produto com estoque crítico.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" width="100%">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Estoque atual</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estoqueBaixo as $produto): ?>
                        <tr>
                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                            <td><?= (int) $produto['estoque'] ?></td>
                            <td>
                                <a href="produtos/editar.php?id=<?= (int) $produto['id'] ?>">Repor</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>