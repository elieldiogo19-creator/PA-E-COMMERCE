
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php
        $nome = "XAVITA";
        echo "<h1> $nome</h1>";
    ?>
    <?php
        session_start();
    ?>

    <?php if (!empty($_SESSION['usuario_nome'])): ?>
        <p>Logado como: <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></p>
        <nav>
            <a href="index.php">Home</a>
            <a href="produtos.php">Produtos</a>
            <a href="carrinho.php">Carrinho</a>
            <a href="logout.php">Logout</a>
         </nav>
    <?php else: ?>
        <nav>
            <a href="produtos.php">Produtos</a>
            <p><a href="login.php">Entrar</a> | <a href="cadastro.php">Cadastrar</a></p>
         </nav>
    <?php endif; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>