<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'XAVITA';

// Buscar produtos em destaque (últimos 8)
try {
    $stmt = $pdo->query("
        SELECT id, nome, descricao, preco, imagem
        FROM produtos
        ORDER BY criado_em DESC
        LIMIT 8
    ");
    $produtosDestaque = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtosDestaque = [];
}

// Dados para o header
$usuarioNome  = $_SESSION['usuario_nome'] ?? null;
$qtdCarrinho  = !empty($_SESSION['carrinho'])
    ? array_sum($_SESSION['carrinho'])
    : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nomeProjeto); ?> - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- HEADER / NAV -->
    <header class="site-header">
        <div class="header-inner">
            <!-- logo -->
            <div class="header-left">
                <a href="index.php" class="logo">
                    <!-- just logo -->
                    <span><?php echo htmlspecialchars($nomeProjeto); ?></span>
                </a>
            </div>

            <!-- navbar -->
            <nav class="navbar">
                <a href="index.php">Home</a>
                <a href="produtos.php">Shop</a>
                <a href="sobre.php">About</a>
                <a href="contato.php">Blogs</a>
                <a href="servicos.php">Services</a>
            </nav>

            <div class="header-right">
                <!-- Busca simples (futura integração com produtos.php?q=...) -->
                <form class="search-box" method="GET" action="produtos.php">
                    <input type="text" name="q" placeholder="Search..." />
                    <button type="submit">🔍</button>
                </form>

                <!-- Carrinho -->
                <a href="carrinho.php" class="icon-button" title="Carrinho">
                    🛒
                    <?php if ($qtdCarrinho > 0): ?>
                        <span class="icon-badge"><?php echo $qtdCarrinho; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Usuário -->
                <?php if ($usuarioNome): ?>
                    <span class="user-name">
                        Olá, <?php echo htmlspecialchars($usuarioNome); ?>
                    </span>
                    <a href="logout.php" class="icon-button">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="icon-button" title="Login">
                        Entrar
                    </a>
                    <a href="cadastro.php" class="icon-button" title="Cadastrar">
                        Cadastrar.
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <!-- HERO / APRESENTAÇÃO (layout) -->
        <section class="hero">
            <!-- banner fones, etc. -->
            <h1><?php echo htmlspecialchars($nomeProjeto); ?></h1>
            <p>Texto de apresentação da plataforma de e-commerce.</p>
        </section>

        <!-- PRODUTOS EM DESTAQUE -->
        <section class="section">
            <h2>Produtos em destaque</h2>

            <?php if (empty($produtosDestaque)): ?>
                <p>Nenhum produto em destaque no momento.</p>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($produtosDestaque as $produto): ?>
                        <article class="product-card">
                            <?php if (!empty($produto['imagem'])): ?>
                                <img
                                    src="<?php echo htmlspecialchars($produto['imagem']); ?>"
                                    alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                    class="product-image">
                            <?php endif; ?>

                            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

                            <p class="product-price">
                                <?php echo number_format($produto['preco'], 2, ',', '.'); ?> AOA
                            </p>

                            <a href="adicionar_ao_carrinho.php?id=<?php echo $produto['id']; ?>"
                                class="btn btn-primary">
                                Adicionar ao carrinho
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <p>
                <a href="produtos.php">Ver todos os produtos →</a>
            </p>
        </section>

        <!-- AQUI depois entram:
       - seção de serviços
       - sobre a empresa
       - contato
       (tudo front do teu mano) -->
    </main>

    <script src="assets/js/main.js"></script>
</body>

</html>