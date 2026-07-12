<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '';

function isActive(string $segment, string $currentPath): string {
    return strpos($currentPath, '/admin/' . $segment) !== false ? 'ativo' : '';
}
?>

<aside class="admin-sidebar">
    <div class="admin-sidebar-header">
        <h2>Painel Admin</h2>
        <p><?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <nav class="admin-sidebar-nav">
        <ul>
            <li>
                <a href="/PA-E-COMMERCE/admin/dashboard.php"
                   class="<?= strpos($currentPath, '/admin/dashboard.php') !== false ? 'ativo' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="/PA-E-COMMERCE/admin/produtos/"
                   class="<?= isActive('produtos', $currentPath) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.5 7.27L12 12l-8.5-4.73"></path>
                        <path d="M12 22V12"></path>
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                    Produtos
                </a>
            </li>

            <li>
                <a href="/PA-E-COMMERCE/admin/categorias/"
                   class="<?= isActive('categorias', $currentPath) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                    Categorias
                </a>
            </li>

            <li>
                <a href="/PA-E-COMMERCE/admin/clientes/"
                   class="<?= isActive('clientes', $currentPath) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Clientes
                </a>
            </li>

            <li>
                <a href="/PA-E-COMMERCE/admin/pedidos/"
                   class="<?= isActive('pedidos', $currentPath) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11H1l8-8 8 8h-8v10"></path>
                        <path d="M14 4h7v7"></path>
                        <path d="M17 3l4 4"></path>
                    </svg>
                    Pedidos
                </a>
            </li>
        </ul>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="/PA-E-COMMERCE/admin/logout.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Sair
        </a>
    </div>
</aside>