<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Gerir Categorias - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

$filtroBusca = trim($_GET['busca'] ?? '');

try {
    $sql = "
        SELECT c.*,
               COUNT(p.id) AS total_produtos
        FROM categorias c
        LEFT JOIN produtos p ON p.categoria_id = c.id
        WHERE 1=1
    ";
    $params = [];

    if ($filtroBusca !== '') {
        $sql .= " AND c.nome LIKE ? ";
        $params[] = "%$filtroBusca%";
    }

    $sql .= " GROUP BY c.id ORDER BY c.nome ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estatísticas
    $totalCategorias = (int) $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    $categoriasComProdutos = 0;
    $categoriaTop = null;
    $maiorTotal = 0;

    foreach ($categorias as $cat) {
        if ($cat['total_produtos'] > 0) $categoriasComProdutos++;
        if ($cat['total_produtos'] > $maiorTotal) {
            $maiorTotal = $cat['total_produtos'];
            $categoriaTop = $cat['nome'];
        }
    }

} catch (PDOException $e) {
    $categorias = [];
    $totalCategorias = $categoriasComProdutos = 0;
    $categoriaTop = null;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho -->
        <section class="section page-header">
            <div>
                <h1>Categorias</h1>
                <p>Organize os produtos por categorias.</p>
            </div>

            <a href="adicionar.php" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nova Categoria
            </a>
        </section>

        <?php require __DIR__ . '/../includes/admin_flash_render.php'; ?>

        <!-- Cards de estatísticas -->
        <section class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3>Total de Categorias</h3>
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $totalCategorias ?></p>
                <p class="card-subtitle">Categorias registadas</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Em Utilização</h3>
                    <div class="card-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $categoriasComProdutos ?></p>
                <p class="card-subtitle">Com produtos associados</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Vazias</h3>
                    <div class="card-icon orange">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                </div>
                <p class="card-number"><?= $totalCategorias - $categoriasComProdutos ?></p>
                <p class="card-subtitle">Sem produtos</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Mais Popular</h3>
                    <div class="card-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                </div>
                <?php if ($categoriaTop): ?>
                    <p class="card-number-sm" title="<?= htmlspecialchars($categoriaTop) ?>">
                        <?= htmlspecialchars(mb_strimwidth($categoriaTop, 0, 20, '…')) ?>
                    </p>
                    <p class="card-subtitle"><?= $maiorTotal ?> produtos</p>
                <?php else: ?>
                    <p class="card-number-sm">—</p>
                    <p class="card-subtitle">Sem dados</p>
                <?php endif; ?>
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
                               placeholder="Pesquisar categoria por nome...">
                    </div>
                    <button type="submit" class="btn-primary">Pesquisar</button>
                </form>
            </div>
        </section>

        <!-- Tabela -->
        <section class="section">
            <div class="table-card">

                <?php if (empty($categorias)): ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <p>Nenhuma categoria encontrada
                            <?php if ($filtroBusca): ?>para "<strong><?= htmlspecialchars($filtroBusca) ?></strong>"<?php endif; ?>.
                        </p>
                        <?php if ($filtroBusca): ?>
                            <a href="listar.php" class="btn-secondary" style="margin-top: 16px;">
                                Limpar pesquisa
                            </a>
                        <?php else: ?>
                            <a href="adicionar.php" class="btn-primary" style="margin-top: 16px;">
                                Criar primeira categoria
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Produtos</th>
                                <th>Criada em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $cat): ?>
                                <tr>
                                    <td>
                                        <strong class="pedido-id">#<?= (int) $cat['id'] ?></strong>
                                    </td>

                                    <td>
                                        <div class="cell-categoria">
                                            <div class="categoria-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                                </svg>
                                            </div>
                                            <strong><?= htmlspecialchars($cat['nome']) ?></strong>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if ($cat['total_produtos'] > 0): ?>
                                            <span class="badge-sold">
                                                <?= (int) $cat['total_produtos'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-vazia">Vazia</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-muted">
                                        <?php if (!empty($cat['criado_em'])): ?>
                                            <?= date('d/m/Y', strtotime($cat['criado_em'])) ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="table-actions">
                                            <a href="editar.php?id=<?= (int) $cat['id'] ?>"
                                               class="btn-action" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                                </svg>
                                            </a>
                                            <a href="excluir.php?id=<?= (int) $cat['id'] ?>"
                                               class="btn-action danger" title="Excluir"
                                               onclick="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"></path>
                                                    <path d="M10 11v6"></path>
                                                    <path d="M14 11v6"></path>
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