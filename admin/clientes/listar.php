<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Clientes - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

$filtroBusca = trim($_GET['busca'] ?? '');

try {
    $sql = "
        SELECT
            u.id,
            u.nome,
            u.email,
            u.criado_em,
            COUNT(p.id) AS total_pedidos,
            COALESCE(SUM(p.total), 0) AS total_gasto
        FROM usuarios u
        LEFT JOIN pedidos p ON p.usuario_id = u.id
        WHERE 1=1
    ";
    $params = [];

    if ($filtroBusca !== '') {
        $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR u.id = ?) ";
        $params[] = "%$filtroBusca%";
        $params[] = "%$filtroBusca%";
        $params[] = (int) $filtroBusca;
    }

    $sql .= " GROUP BY u.id ORDER BY u.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estatísticas gerais
    $totalClientes  = (int)   $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $clientesAtivos = (int)   $pdo->query("SELECT COUNT(DISTINCT usuario_id) FROM pedidos WHERE usuario_id IS NOT NULL")->fetchColumn();
    $novosMes       = (int)   $pdo->query("SELECT COUNT(*) FROM usuarios WHERE criado_em >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
    $totalGasto     = (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedidos")->fetchColumn();

} catch (PDOException $e) {
    $clientes = [];
    $totalClientes = $clientesAtivos = $novosMes = 0;
    $totalGasto = 0;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho -->
        <section class="section page-header">
            <div>
                <h1>Clientes</h1>
                <p>Base de clientes registados no sistema.</p>
            </div>
        </section>

        <?php require __DIR__ . '/../includes/admin_flash_render.php'; ?>

        <!-- Cards de estatísticas -->
        <section class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3>Total de Clientes</h3>
                    <div class="card-icon">
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
                <p class="card-subtitle">Registados no sistema</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Clientes Ativos</h3>
                    <div class="card-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $clientesAtivos ?></p>
                <p class="card-subtitle">Já realizaram pedidos</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Novos (30 dias)</h3>
                    <div class="card-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $novosMes ?></p>
                <p class="card-subtitle">Últimos 30 dias</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Faturado</h3>
                    <div class="card-icon orange">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= number_format($totalGasto, 2, ',', '.') ?></p>
                <p class="card-subtitle">Total em Kz</p>
            </div>
        </section>

        <!-- Filtros -->
        <section class="section">
            <div class="filters-bar">
                <form method="GET" class="filters-form">
                    <div class="search-input">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" name="busca"
                               value="<?= htmlspecialchars($filtroBusca) ?>"
                               placeholder="Pesquisar por nome, email ou ID do cliente...">
                    </div>
                    <button type="submit" class="btn-primary">Pesquisar</button>
                </form>
            </div>
        </section>

        <!-- Tabela de clientes -->
        <section class="section">
            <div class="table-card">

                <?php if (empty($clientes)): ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                        <p>Nenhum cliente encontrado
                            <?php if ($filtroBusca): ?>para "<strong><?= htmlspecialchars($filtroBusca) ?></strong>"<?php endif; ?>.
                        </p>
                        <?php if ($filtroBusca): ?>
                            <a href="listar.php" class="btn-secondary" style="margin-top: 16px;">
                                Limpar pesquisa
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>E-mail</th>
                                <th>Pedidos</th>
                                <th>Registado</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td>
                                        <strong class="pedido-id">#<?= (int) $cliente['id'] ?></strong>
                                    </td>

                                    <td>
                                        <div class="cell-cliente">
                                            <div class="cliente-avatar">
                                                <?= strtoupper(substr($cliente['nome'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($cliente['nome']) ?></strong>
                                                <?php if ($cliente['total_pedidos'] > 0): ?>
                                                    <small class="cliente-status active">● Cliente ativo</small>
                                                <?php else: ?>
                                                    <small class="cliente-status inactive">○ Sem pedidos</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-muted">
                                        <a href="mailto:<?= htmlspecialchars($cliente['email']) ?>" class="email-link">
                                            <?= htmlspecialchars($cliente['email']) ?>
                                        </a>
                                    </td>

                                    <td>
                                        <?php if ($cliente['total_pedidos'] > 0): ?>
                                            <span class="badge-sold">
                                                <?= (int) $cliente['total_pedidos'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-muted">
                                        <?= date('d/m/Y', strtotime($cliente['criado_em'])) ?>
                                        <br>
                                        <small><?= date('H:i', strtotime($cliente['criado_em'])) ?></small>
                                    </td>

                                    <td>
                                        <div class="table-actions">
                                            <a href="visualizar.php?id=<?= (int) $cliente['id'] ?>"
                                               class="btn-action" title="Visualizar">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </a>
                                        </div>
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

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>