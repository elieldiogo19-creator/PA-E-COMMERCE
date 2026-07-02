<?php
// Garante que sessão está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variáveis padrão
$qtdCarrinho = 0;
$usuarioNome = $_SESSION['usuario_nome'] ?? null;
$usuarioId = $_SESSION['usuario_id'] ?? null;

// Só busca do banco se $pdo existir (evita erro em páginas sem conexão)
if (isset($pdo) && !empty($usuarioId)) {
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantidade) as total FROM carrinho WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        $result = $stmt->fetch();
        $qtdCarrinho = $result['total'] ?? 0;
    } catch (PDOException $e) {
        $qtdCarrinho = 0;
    }
}

// Função auxiliar para buscar categorias só se tiver conexão
function getNavbarCategorias($pdo) {
    if (!isset($pdo)) return [];
    try {
        $stmt = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome LIMIT 6");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

$categoriasNav = getNavbarCategorias($pdo ?? null);
?>

<header class="main-header">
    <div class="header-left">
        <a href="<?php echo $baseUrl; ?>index.php" class="logo-link">
            <img src="<?php echo $baseUrl; ?>assets/img/logo-canzala-2.png" alt="Canzala" class="logo-nav">
        </a>

        <nav class="main-nav">
            <ul class="nav-links">
                <li>
                    <a href="<?php echo $baseUrl; ?>index.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        Home
                    </a>
                </li>

                <li class="dropdown">
                    <a href="<?php echo $baseUrl; ?>produtos.php" class="dropdown-toggle">
                        Shop <span class="arrow">▼</span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (!empty($categoriasNav)): ?>
                        <?php foreach ($categoriasNav as $cat): ?>
                        <li>
                            <a href="<?php echo $baseUrl; ?>produtos.php?categoria=<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <li><a href="#">Categorias em breve</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li><a href="<?php echo $baseUrl; ?>servicos.php">Services</a></li>
                <li><a href="<?php echo $baseUrl; ?>sobre.php">About</a></li>
                <li><a href="<?php echo $baseUrl; ?>blog.php">Blogs</a></li>
            </ul>
        </nav>
    </div>

    <div class="header-right">
        <form class="search-box" method="GET" action="<?php echo $baseUrl; ?>produtos.php">
            <input type="text" name="q" placeholder="Search..." autocomplete="off">
            <button type="submit" class="search-btn" aria-label="Buscar">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </form>

        <a href="<?php echo $baseUrl; ?>pages/carrinho.php" class="icon-btn cart-icon" title="Carrinho">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                class="icon icon-tabler icons-tabler-filled icon-tabler-shopping-cart">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M6 2a1 1 0 0 1 .993 .883l.007 .117v1.068l13.071 .935a1 1 0 0 1 .929 1.024l-.01 .114l-1 7a1 1 0 0 1 -.877 .853l-.113 .006h-12v2h10a3 3 0 1 1 -2.995 3.176l-.005 -.176l.005 -.176c.017 -.288 .074 -.564 .166 -.824h-5.342a3 3 0 1 1 -5.824 1.176l-.005 -.176l.005 -.176a3.002 3.002 0 0 1 1.995 -2.654v-12.17h-1a1 1 0 0 1 -.993 -.883l-.007 -.117a1 1 0 0 1 .883 -.993l.117 -.007h2zm0 16a1 1 0 1 0 0 2a1 1 0 0 0 0 -2m11 0a1 1 0 1 0 0 2a1 1 0 0 0 0 -2" />
            </svg>
            <?php if ($qtdCarrinho > 0): ?>
            <span class="cart-count"><?php echo $qtdCarrinho; ?></span>
            <?php endif; ?>
        </a>

        <?php if (!empty($usuarioId)): ?>
        <div class="user-menu dropdown">
            <a href="#" class="icon-btn user-icon" title="<?php echo htmlspecialchars($usuarioNome); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                    class="icon icon-tabler icons-tabler-filled icon-tabler-user">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 2a5 5 0 1 1 -5 5l.005 -.217a5 5 0 0 1 4.995 -4.783z" />
                    <path d="M14 14a5 5 0 0 1 5 5v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a5 5 0 0 1 5 -5h4z" />
                </svg>
                <span class="user-name-short"><?php echo substr($usuarioNome, 0, 1); ?></span>
            </a>
            <ul class="dropdown-menu user-dropdown">
                <li class="user-info">
                    <span>Olá, <strong><?php echo htmlspecialchars($usuarioNome); ?></strong></span>
                </li>
                <li><a href="<?php echo $baseUrl; ?>pages/perfil.php">Meu Perfil</a></li>
                <li><a href="<?php echo $baseUrl; ?>pages/pedidos.php">Meus Pedidos</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo $baseUrl; ?>actions/logout.php" class="logout-link">Sair</a></li>
            </ul>
        </div>
        <?php else: ?>
        <a href="<?php echo $baseUrl; ?>auth/login.php" class="icon-btn user-icon" title="Entrar">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
            </svg>
        </a>
        <?php endif; ?>
    </div>

    <!-- BOTÃO HAMBÚRGUER (Mobile) -->
    <button class="hamburger" id="hamburger-btn" aria-label="Menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>

    <!-- OVERLAY MOBILE -->
    <div class="mobile-overlay" id="mobile-overlay"></div>

    <!-- MENU MOBILE -->
    <nav class="mobile-nav" id="mobile-menu">
        <button class="close-mobile" id="close-mobile" aria-label="Fechar">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <?php if (!empty($usuarioId)): ?>
        <div class="mobile-user-info">
            <p>Olá, <strong><?php echo htmlspecialchars($usuarioNome); ?></strong></p>
            <a href="<?php echo $baseUrl; ?>actions/logout.php" class="logout-mobile">Sair</a>
        </div>
        <?php else: ?>
        <a href="<?php echo $baseUrl; ?>auth/login.php" class="login-mobile-btn">Entrar</a>
        <a href="<?php echo $baseUrl; ?>auth/cadastro.php" class="login-mobile-btn">Cadastrar</a>
        <?php endif; ?>

        <ul class="mobile-links">
            <li><a href="<?php echo $baseUrl; ?>index.php">Home</a></li>

            <li class="mobile-dropdown">
                <a href="#" class="dropdown-toggle">Shop ▼</a>
                <ul class="mobile-submenu">
                    <?php if (!empty($categoriasNav)): ?>
                    <?php foreach ($categoriasNav as $cat): ?>
                    <li>
                        <a href="<?php echo $baseUrl; ?>produtos.php?categoria=<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <li><a href="#">Categorias em breve</a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <li><a href="<?php echo $baseUrl; ?>servicos.php">Services</a></li>
            <li><a href="<?php echo $baseUrl; ?>sobre.php">About</a></li>
            <li><a href="<?php echo $baseUrl; ?>blog.php">Blogs</a></li>
        </ul>
    </nav>

    <script>
    // Menu Hamburguer
    const hamburger = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMobile = document.getElementById('close-mobile');
    const overlay = document.getElementById('mobile-overlay');

    function openMobileMenu() {
        mobileMenu.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', openMobileMenu);
    closeMobile.addEventListener('click', closeMobileMenu);
    overlay.addEventListener('click', closeMobileMenu);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Dropdown Shop no Mobile
    const mobileShopToggle = document.querySelector('.mobile-dropdown .dropdown-toggle');
    const mobileSubmenu = document.querySelector('.mobile-submenu');

    if (mobileShopToggle && mobileSubmenu) {
        mobileShopToggle.addEventListener('click', (e) => {
            e.preventDefault();
            mobileSubmenu.classList.toggle('open');

            const text = mobileShopToggle.textContent;
            if (text.includes('▼')) {
                mobileShopToggle.textContent = text.replace('▼', '▲');
            } else {
                mobileShopToggle.textContent = text.replace('▲', '▼');
            }
        });
    }
    </script>
</header>

<div class="header-spacer"></div>