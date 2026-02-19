
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
        $nome = "PA-E-COMMERCE";
        echo "<h1>Bem-vindo! $nome</h1>";
    ?>
    <?php
        session_start();
    ?>

    <?php if (!empty($_SESSION['usuario_nome'])): ?>
        <p>Logado como: <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></p>
        <p><a href="logout.php">Sair</a></p>
    <?php else: ?>
        <p><a href="login.php">Entrar</a> | <a href="cadastro.php">Cadastrar</a></p>
    <?php endif; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>