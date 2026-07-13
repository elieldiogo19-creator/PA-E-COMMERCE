<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Gerir Pedidos - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

// Filtro por status (opcional)
$filtroStatus = $_GET['status'] ?? '';
$filtroBusca  = trim($_GET['busca'] ?? '');

try {
    $sql = "
        SELECT id, nome_cliente, email_cliente, total, status, criado_em
        FROM pedidos
        WHERE 1=1
    ";
    $params = [];

    if ($filtroStatus !== '') {
        $sql .= " AND LOWER(status) = LOWER(?) ";
        $params[] = $filtroStatus;
    }

    if ($filtroBusca !== '') {
        $sql .= " AND (nome_cliente LIKE ? OR email_cliente LIKE ? OR id = ?) ";
        $params[] = "%$filtroBusca%";
        $params[] = "%$filtroBusca%";
        $params[] = (int) $filtroBusca;
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estatísticas por status
    $stmtStats = $pdo->query("
        SELECT status, COUNT(*) as total, COALESCE(SUM(total), 0) as valor
        FROM pedidos
        GROUP BY status
    ");
    $stats = $stmtStats->fetchAll(PDO::FETCH_ASSOC);

    $totalPedidos    = 0;
    $totalFaturado   = 0;
    $totalPendentes  = 0;
    $totalConfirmados = 0;

    foreach ($stats as $s) {
        $totalPedidos  += (int) $s['total'];
        $totalFaturado += (float) $s['valor'];
        if (strtolower($s['status']) === 'pendente')    $totalPendentes   = (int) $s['total'];
        if (strtolower($s['status']) === 'confirmado')  $totalConfirmados = (int) $s['total'];
    }

} catch (PDOException $e) {
    $pedidos = [];
    $totalPedidos = $totalFaturado = $totalPendentes = $totalConfirmados = 0;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho -->
        <section class="section page-header">
            <div>
                <h1>Gerir Pedidos</h1>
                <p>Acompanhe e gira todas as encomendas realizadas no sistema.</p>
            </div>
        </section>

        <?php require __DIR__ . '/../includes/admin_flash_render.php'; ?>

        <!-- Cards de estatísticas -->
        <section class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3>Total de Pedidos</h3>
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11H1l8-8 8 8h-8v10"></path>
                            <path d="M14 4h7v7"></path>
                            <path d="M17 3l4 4"></path>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $totalPedidos ?></p>
                <p class="card-subtitle">Encomendas registadas</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Pendentes</h3>
                    <div class="card-icon orange">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $totalPendentes ?></p>
                <p class="card-subtitle">Aguardam confirmação</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Confirmados</h3>
                    <div class="card-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $totalConfirmados ?></p>
                <p class="card-subtitle">Processados com sucesso</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Faturamento</h3>
                    <div class="card-icon blue">
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
                               placeholder="Pesquisar por nome, email ou ID do pedido...">
                    </div>

                    <?php if ($filtroStatus): ?>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($filtroStatus) ?>">
                    <?php endif; ?>

                    <button type="submit" class="btn-primary">Pesquisar</button>
                </form>

                <div class="filter-tabs">
                    <a href="?" class="filter-tab <?= $filtroStatus === '' ? 'active' : '' ?>">
                        Todos
                        <span><?= $totalPedidos ?></span>
                    </a>
                    <a href="?status=pendente" class="filter-tab <?= strtolower($filtroStatus) === 'pendente' ? 'active' : '' ?>">
                        Pendentes
                        <span><?= $totalPendentes ?></span>
                    </a>
                    <a href="?status=confirmado" class="filter-tab <?= strtolower($filtroStatus) === 'confirmado' ? 'active' : '' ?>">
                        Confirmados
                        <span><?= $totalConfirmados ?></span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Tabela de pedidos -->
        <section class="section">
            <div class="table-card">

                <?php if (empty($pedidos)): ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11H1l8-8 8 8h-8v10"></path>
                        </svg>
                        <p>Nenhum pedido encontrado
                            <?php if ($filtroBusca || $filtroStatus): ?>
                                para os filtros aplicados
                            <?php endif; ?>.
                        </p>
                        <?php if ($filtroBusca || $filtroStatus): ?>
                            <a href="listar.php" class="btn-secondary" style="margin-top: 16px;">
                                Limpar filtros
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>E-mail</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td>
                                        <strong class="pedido-id">#<?= (int) $pedido['id'] ?></strong>
                                    </td>

                                    <td>
                                        <div class="cell-cliente">
                                            <div class="cliente-avatar">
                                                <?= strtoupper(substr($pedido['nome_cliente'], 0, 1)) ?>
                                            </div>
                                            <span><?= htmlspecialchars($pedido['nome_cliente']) ?></span>
                                        </div>
                                    </td>

                                    <td class="text-muted">
                                        <?= htmlspecialchars($pedido['email_cliente']) ?>
                                    </td>

                                    <td>
                                        <strong><?= number_format($pedido['total'], 2, ',', '.') ?> Kz</strong>
                                    </td>

                                    <td>
                                        <span class="badge badge-<?= strtolower(htmlspecialchars($pedido['status'])) ?>">
                                            <?= htmlspecialchars(ucfirst($pedido['status'])) ?>
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        <?= date('d/m/Y', strtotime($pedido['criado_em'])) ?>
                                        <br>
                                        <small><?= date('H:i', strtotime($pedido['criado_em'])) ?></small>
                                    </td>

                                    <td>
                                        <div class="table-actions">
                                            <a href="visualizar.php?id=<?= (int) $pedido['id'] ?>"
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