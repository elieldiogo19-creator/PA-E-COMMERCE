<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Detalhes do Cliente - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

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
        MAX(criado_em) AS ultimo_pedido,
        COALESCE(AVG(total), 0) AS ticket_medio
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
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho -->
        <section class="section page-header">
            <div>
                <h1>Perfil do Cliente</h1>
                <p>Informações e histórico de <strong><?= htmlspecialchars($cliente['nome']) ?></strong>.</p>
            </div>

            <a href="listar.php" class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Voltar aos clientes
            </a>
        </section>

        <?php require __DIR__ . '/../includes/admin_flash_render.php'; ?>

        <!-- Perfil (banner) -->
        <section class="section">
            <div class="cliente-profile">
                <div class="cliente-profile-avatar">
                    <?= strtoupper(substr($cliente['nome'], 0, 1)) ?>
                </div>

                <div class="cliente-profile-info">
                    <h2><?= htmlspecialchars($cliente['nome']) ?></h2>

                    <div class="cliente-meta">
                        <div class="cliente-meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <a href="mailto:<?= htmlspecialchars($cliente['email']) ?>">
                                <?= htmlspecialchars($cliente['email']) ?>
                            </a>
                        </div>

                        <div class="cliente-meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Registado em <?= date('d/m/Y', strtotime($cliente['criado_em'])) ?>
                        </div>

                        <div class="cliente-meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            ID #<?= (int) $cliente['id'] ?>
                        </div>

                        <?php if ($resumo['total_pedidos'] > 0): ?>
                            <span class="cliente-badge active">● Cliente ativo</span>
                        <?php else: ?>
                            <span class="cliente-badge inactive">○ Sem compras</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

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
                <p class="card-number"><?= (int) $resumo['total_pedidos'] ?></p>
                <p class="card-subtitle">Encomendas realizadas</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Total Gasto</h3>
                    <div class="card-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= number_format((float) $resumo['total_gasto'], 2, ',', '.') ?></p>
                <p class="card-subtitle">Kz acumulado</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Ticket Médio</h3>
                    <div class="card-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= number_format((float) $resumo['ticket_medio'], 2, ',', '.') ?></p>
                <p class="card-subtitle">Kz por pedido</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Último Pedido</h3>
                    <div class="card-icon orange">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
                <?php if (!empty($resumo['ultimo_pedido'])): ?>
                    <p class="card-number-sm"><?= date('d/m/Y', strtotime($resumo['ultimo_pedido'])) ?></p>
                    <p class="card-subtitle"><?= date('H:i', strtotime($resumo['ultimo_pedido'])) ?> · há
                        <?php
                            $diff = (new DateTime())->diff(new DateTime($resumo['ultimo_pedido']));
                            if ($diff->days > 0) echo $diff->days . ' dias';
                            else echo $diff->h . 'h';
                        ?>
                    </p>
                <?php else: ?>
                    <p class="card-number-sm">—</p>
                    <p class="card-subtitle">Ainda sem pedidos</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Histórico de pedidos -->
        <section class="section">
            <div class="table-card">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
                    </svg>
                    Histórico de Pedidos
                    <span class="badge-count"><?= count($pedidos) ?></span>
                </h2>

                <?php if (empty($pedidos)): ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11H1l8-8 8 8h-8v10"></path>
                        </svg>
                        <p>Este cliente ainda não realizou nenhum pedido.</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
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
                                        <strong><?= number_format((float) $pedido['total'], 2, ',', '.') ?> Kz</strong>
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
                                            <a href="../pedidos/visualizar.php?id=<?= (int) $pedido['id'] ?>"
                                               class="btn-action" title="Ver pedido">
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