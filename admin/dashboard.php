<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Dashboard Admin - ' . $nomeProjeto;
$baseUrl     = '../';
$pageCSS     = 'dashboard'; // carrega assets/css/dashboard.css

// Métricas principais
try {
    $totalProdutos  = (int)   $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    $totalClientes  = (int)   $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $totalPedidos   = (int)   $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
    $totalFaturado  = (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedidos")->fetchColumn();
} catch (PDOException $e) {
    $totalProdutos = $totalClientes = $totalPedidos = $totalFaturado = 0;
}

// Últimos 5 pedidos
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

// Estoque baixo (<=5)
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

// Pedidos por status (para gráfico)
try {
    $stmt = $pdo->query("
        SELECT status, COUNT(*) as total
        FROM pedidos
        GROUP BY status
    ");
    $pedidosPorStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pedidosPorStatus = [];
}

// Vendas dos últimos 7 meses
try {
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(criado_em, '%Y-%m') as mes,
               COALESCE(SUM(total), 0) as total
        FROM pedidos
        WHERE criado_em >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes
        ORDER BY mes ASC
    ");
    $vendasMensais = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $vendasMensais = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho -->
        <section class="section">
            <h1>Painel do Administrador</h1>
            <p>
                Bem-vindo, <strong><?= htmlspecialchars($_SESSION['admin_nome'], ENT_QUOTES, 'UTF-8') ?></strong>.
                <?php if (!empty($_SESSION['admin_ultimo_acesso'])): ?>
                    Último acesso: <?= date('d/m/Y H:i', strtotime($_SESSION['admin_ultimo_acesso'])) ?>
                <?php else: ?>
                    Primeiro acesso ao painel.
                <?php endif; ?>
            </p>
        </section>

        <!-- Cards -->
        <section class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3>Produtos</h3>
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.5 7.27L12 12l-8.5-4.73"></path>
                            <path d="M12 22V12"></path>
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $totalProdutos ?></p>
                <p class="card-subtitle">Produtos cadastrados</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Clientes</h3>
                    <div class="card-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $totalClientes ?></p>
                <p class="card-subtitle">Utilizadores registados</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Pedidos</h3>
                    <div class="card-icon orange">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11H1l8-8 8 8h-8v10"></path>
                            <path d="M14 4h7v7"></path>
                            <path d="M17 3l4 4"></path>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $totalPedidos ?></p>
                <p class="card-subtitle">Encomendas realizadas</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Faturado</h3>
                    <div class="card-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= number_format($totalFaturado, 2, ',', '.') ?></p>
                <p class="card-subtitle">Total em Kz</p>
            </div>
        </section>

        <!-- Gráficos -->
        <section class="charts-grid">
            <div class="chart-card">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    Vendas dos últimos meses
                </h2>
                <div class="chart-container">
                    <canvas id="chartVendas"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                    </svg>
                    Pedidos por Status
                </h2>
                <div class="chart-container">
                    <canvas id="chartStatus"></canvas>
                </div>
            </div>
        </section>

        <!-- Últimos pedidos -->
        <section class="section">
            <div class="table-card">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Últimos pedidos
                </h2>

                <?php if (empty($ultimosPedidos)): ?>
                    <div class="empty-state">
                        <p>Nenhum pedido registado ainda.</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
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
                                    <td>#<?= (int) $pedido['id'] ?></td>
                                    <td><?= htmlspecialchars($pedido['nome_cliente']) ?></td>
                                    <td><strong><?= number_format($pedido['total'], 2, ',', '.') ?> Kz</strong></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower(htmlspecialchars($pedido['status'])) ?>">
                                            <?= htmlspecialchars($pedido['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                                    <td>
                                        <a class="btn-action" href="pedidos/visualizar.php?id=<?= (int) $pedido['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>

        <!-- Estoque baixo -->
        <section class="section">
            <div class="table-card">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Alertas de Estoque Baixo
                </h2>

                <?php if (empty($estoqueBaixo)): ?>
                    <div class="empty-state">
                        <p>Nenhum produto com estoque crítico. 🎉</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
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
                                    <td>
                                        <span class="<?= $produto['estoque'] <= 2 ? 'stock-low' : 'stock-warning' ?>">
                                            <?= (int) $produto['estoque'] ?> unid.
                                        </span>
                                    </td>
                                    <td>
                                        <a class="btn-action warning" href="produtos/editar.php?id=<?= (int) $produto['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                            </svg>
                                            Repor
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>

    </main>
</div>

<!-- Chart.js OFFLINE (baixa e coloca em assets/js/chart.min.js) -->
<script src="<?= $baseUrl ?>assets/js/chart.min.js"></script>
<script>
    // Dados enviados do PHP para o JS
    const vendasMensais = <?= json_encode($vendasMensais) ?>;
    const pedidosStatus = <?= json_encode($pedidosPorStatus) ?>;
</script>
<script src="<?= $baseUrl ?>assets/js/dashboard.js"></script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>