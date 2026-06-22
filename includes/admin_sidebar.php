<?php
// Define o item ativo com base na URL atual
$currentPath = $_SERVER['REQUEST_URI'] ?? '';

function isActive(string $segment, string $currentPath): string {
    return strpos($currentPath, '/admin/' . $segment) !== false ? 'ativo' : '';
}
?>

<aside class="admin-sidebar">

    <div class="admin-sidebar-header">
        <h2>Painel Admin</h2>
        <p>
            <?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>

    <nav class="admin-sidebar-nav">
        <ul>
            <li>
                <a href="/PA-E-COMMERCE/admin/dashboard.php"
                   class="<?= strpos($currentPath, '/admin/dashboard.php') !== false ? 'ativo' : '' ?>">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="/PA-E-COMMERCE/admin/produtos/"
                   class="<?= isActive('produtos', $currentPath) ?>">
                    Produtos
                </a>
            </li>

            <li>
                <a href="/PA-E-COMMERCE/admin/categorias/"
                   class="<?= isActive('categorias', $currentPath) ?>">
                    Categorias
                </a>
            </li>

            <li>
                <a href="/PA-E-COMMERCE/admin/clientes/"
                   class="<?= isActive('clientes', $currentPath) ?>">
                    Clientes
                </a>
            </li>

            <li>
                <a href="/PA-E-COMMERCE/admin/pedidos/"
                   class="<?= isActive('pedidos', $currentPath) ?>">
                    Pedidos
                </a>
            </li>
        </ul>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="/PA-E-COMMERCE/admin/logout.php">Sair</a>
    </div>

</aside>