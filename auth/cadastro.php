<?php
session_start();
require __DIR__ . '/../config/db.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$nomeProjeto = 'CANZALA LDA,';
$pageTitle = 'Cadastro - ' . $nomeProjeto;

$erros = [];
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['nome'] ?? '');
    $email  = strtolower(trim($_POST['email'] ?? ''));
    $senha  = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    if ($nome === '' || $email === '' || $senha === '' || $senha2 === '') {
        $erros[] = 'Preencha todos os campos.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido.';
    }

    if ($senha !== $senha2) {
        $erros[] = 'As senhas não conferem.';
    }

    if (strlen($senha) < 6) {
        $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
    }

    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $erros[] = 'Este e-mail já está cadastrado.';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $email, $senhaHash]);

                $sucesso = 'Cadastro realizado! Redirecionando...';
                header('Refresh: 2; URL=login.php');
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro ao cadastrar. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    
    <link rel="stylesheet" href="../assets/css/cadastro.css" />
</head>
<body>
    <div class="card">
        <div class="hero">
            <div class="bg"></div>
        </div>
        
        <form method="POST" action="cadastro.php">
            <img src="../assets/img/logo-canzala.png" class="logo" alt="Canzala" />
            
            <h3>Cria uma conta na Canzala, LDA.</h3>

            <?php if (!empty($erros)): ?>
                <div style="background: rgba(255, 0, 0, 0.1); border: 1px solid #ff4444; color: #ff6666; padding: 12px; border-radius: 10px; font-size: 14px; text-align: center;">
                    <?php foreach ($erros as $erro): ?>
                        <div><?php echo htmlspecialchars($erro); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div style="background: rgba(0, 255, 0, 0.1); border: 1px solid #00ff00; color: #66ff66; padding: 12px; border-radius: 10px; font-size: 14px; text-align: center;">
                    <?php echo htmlspecialchars($sucesso); ?>
                </div>
            <?php endif; ?>

            <input type="text" name="nome" placeholder="Nome completo" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required autocomplete="name" />
            
            <input type="email" name="email" placeholder="E-mail" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autocomplete="email" />
            
            <span class="or"></span>

            <input type="password" name="senha" placeholder="Senha (mín. 6 caracteres)" required autocomplete="new-password" minlength="6" />
            
            <input type="password" name="senha2" placeholder="Confirmar senha" required autocomplete="new-password" />

            <button type="submit">Cadastrar</button>

            <p style="text-align: center; margin-top: 16px; font-size: 14px;">
                Já tem conta? <a href="login.php" style="color: #f0a927; text-decoration: none;">Entrar</a>
            </p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
    <script src="../assets/js/cadastro.js"></script>
</body>
</html>