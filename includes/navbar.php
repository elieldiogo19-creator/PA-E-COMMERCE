<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = $baseUrl ?? '';
$nomeProjeto = $nomeProjeto ?? 'CANZALA LDA,';
$navbarMode = $navbarMode ?? 'full'; // padrão é completo

$usuarioNome = $_SESSION['usuario_nome'] ?? null;
$qtdCarrinho = !empty($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0;
?>

<header class="site-header">
    <div class="header-inner">

        <!-- Logo / Nome do projeto -->
        <div class="header-left">
            <a href="<?php echo $baseUrl; ?>index.php" class="logo">
                <span><?php echo htmlspecialchars($nomeProjeto); ?></span>
            </a>
        </div>

        <!-- Navegação principal (só aparece no modo full) -->
        <?php if ($navbarMode === 'full'): ?>
        <nav class="navbar">
            <a href="<?php echo $baseUrl; ?>index.php">Home</a>
            <a href="<?php echo $baseUrl; ?>produtos.php">Shop</a>
            <a href="<?php echo $baseUrl; ?>services.php">Serviços</a>
            <a href="<?php echo $baseUrl; ?>sobre.php">Sobre</a>
            <a href="<?php echo $baseUrl; ?>contato.php">Contacto</a>
        </nav>
        <?php endif; ?>

        <!-- Ações do lado direito -->
        <div class="header-right">

            <!-- Busca (só aparece no modo full) -->
            <?php if ($navbarMode === 'full'): ?>
            <form class="search-box" method="GET" action="<?php echo $baseUrl; ?>produtos.php">
                <input type="text" name="q" placeholder="Pesquisar produtos...">
                <button type="submit">🔍</button>
            </form>
            <?php endif; ?>

            <!-- Carrinho (só aparece no modo full) -->
            <?php if ($navbarMode === 'full'): ?>
            <a href="<?php echo $baseUrl; ?>pages/carrinho.php" class="icon-button" title="Carrinho">
                🛒
                <?php if ($qtdCarrinho > 0): ?>
                    <span class="icon-badge"><?php echo $qtdCarrinho; ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <!-- Utilizador (só aparece no modo full) -->
            <?php if ($navbarMode === 'full'): ?>
                <?php if ($usuarioNome): ?>
                    <span class="user-name">
                        Olá, <?php echo htmlspecialchars($usuarioNome); ?>
                    </span>
                    <a href="<?php echo $baseUrl; ?>actions/logout.php" class="icon-button">Sair</a>
                <?php else: ?>
                    <a href="<?php echo $baseUrl; ?>auth/login.php" class="icon-button" title="Login">Entrar</a>
                    <a href="<?php echo $baseUrl; ?>auth/cadastro.php" class="icon-button" title="Cadastrar">Cadastrar</a>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</header>