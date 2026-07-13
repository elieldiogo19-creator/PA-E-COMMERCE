<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Gerir Produtos - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

try {
    $stmt = $pdo->query("
        SELECT
            p.*,
            c.nome AS categoria_nome,
            COALESCE(SUM(ip.quantidade), 0) AS total_vendido
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        LEFT JOIN itens_pedido ip ON ip.produto_id = p.id
        GROUP BY p.id
        ORDER BY p.id DESC
    ");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho da página -->
        <section class="section page-header">
            <div>
                <h1>Gerir Produtos</h1>
                <p>Gestão completa do catálogo de produtos.</p>
            </div>

            <a href="adicionar.php" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Adicionar novo produto
            </a>
        </section>

        <!-- Flash messages -->
        <?php require __DIR__ . '/../includes/admin_flash_render.php'; ?>

        <!-- Alerta de erro (não pode excluir) -->
        <?php if (isset($_GET['erro']) && $_GET['erro'] === 'vendido'): ?>
        <div class="alert alert-danger">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            Produto não pode ser excluído porque já possui vendas.
        </div>
        <?php endif; ?>

        <!-- Tabela de produtos -->
        <section class="section">
            <div class="table-card">

                <?php if (empty($produtos)): ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                    </svg>
                    <p>Nenhum produto encontrado.</p>
                </div>
                <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr data-produto-id="<?= (int) $produto['id'] ?>">
                            <th>ID</th>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Vendidos</th>
                            <th>Criado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                        <tr data-produto-id="<?= (int) $produto['id'] ?>">
                            <td>#<?= (int) $produto['id'] ?></td>

                            <td>
                                <?php if (!empty($produto['imagem'])): ?>
                                <img class="table-thumb"
                                    src="/PA-E-COMMERCE/<?= htmlspecialchars($produto['imagem']) ?>"
                                    alt="<?= htmlspecialchars($produto['nome']) ?>">
                                <?php else: ?>
                                <div class="table-thumb no-img">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <strong class="product-name">
                                    <?= htmlspecialchars($produto['nome']) ?>
                                </strong>
                            </td>

                            <td>
                                <?php if (!empty($produto['categoria_nome'])): ?>
                                <span class="tag">
                                    <?= htmlspecialchars($produto['categoria_nome']) ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <strong><?= number_format($produto['preco'], 2, ',', '.') ?> Kz</strong>
                            </td>

                            <td>
                                <?php
                                            $estoque = (int) $produto['estoque'];
                                            if ($estoque <= 2)      $classeEstoque = 'stock-low';
                                            elseif ($estoque <= 5)  $classeEstoque = 'stock-warning';
                                            else                    $classeEstoque = 'stock-ok';
                                        ?>
                                <span class="<?= $classeEstoque ?>">
                                    <?= $estoque ?> unid.
                                </span>
                            </td>

                            <td>
                                <span class="badge badge-sold">
                                    <?= (int) $produto['total_vendido'] ?>
                                </span>
                            </td>

                            <td class="text-muted">
                                <?= date('d/m/Y', strtotime($produto['criado_em'])) ?>
                            </td>

                            <td>
                                <div class="table-actions">
                                    <a href="editar.php?id=<?= (int) $produto['id'] ?>" class="btn-action"
                                        title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </a>
                                    <a href="excluir.php?id=X" data-ajax-action
                                        data-confirm="Tem certeza que deseja excluir este produto?" data-row-id="X">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
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
<script>
// Ao excluir com sucesso, remove a linha da tabela com animação
window.onAjaxActionSuccess = function(data, link) {
    if (data.id) {
        const row = document.querySelector(`tr[data-produto-id="${data.id}"]`);
        if (row) {
            row.style.transition = 'all 0.4s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => row.remove(), 400);
        }
    }
};
</script>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>