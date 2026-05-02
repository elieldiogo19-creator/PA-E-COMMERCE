<?php
session_start();
require __DIR__ . '/../config/db.php';

// Se já estiver logado, redireciona
if (!empty($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Login - ' . $nomeProjeto;

$erros = [];
$mensagem = '';
$from = $_GET['from'] ?? '';

// Mensagem se veio do checkout
if ($from === 'checkout') {
    $mensagem = 'Precisa iniciar sessão para finalizar a compra.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($login === '' || $senha === '') {
        $erros[] = 'Preencha e-mail e senha.';
    } else {
        try {
            $stmt = $pdo->prepare("
                SELECT id, nome, email, senha_hash 
                FROM usuarios 
                WHERE email = ? OR nome = ? 
                LIMIT 1
            ");
            $stmt->execute([$login, $login]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
                session_regenerate_id(true);
                
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                if ($from === 'checkout') {
                    header('Location: ../pages/checkout.php');
                } else {
                    header('Location: ../index.php');
                }
                exit;
            } else {
                $erros[] = 'E-mail ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro ao tentar fazer login.';
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
    
    <!-- Seu CSS renomeado -->
    <link rel="stylesheet" href="../assets/css/login.css" />
</head>
<body>
    <div class="card">
        <div class="hero">
            <div class="bg"></div>
        </div>
        
        <form method="POST" action="login.php<?php echo $from ? '?from=' . urlencode($from) : ''; ?>">
            <!-- Logo (ajuste o nome do arquivo conforme sua imagem) -->
            <img src="../assets/img/logo-canzala.png" class="logo" alt="Logo" />
            
            <h3>Login na sua conta</h3>
            
            <!-- Mensagens de erro/sucesso do PHP -->
            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-info" style="background: rgba(240, 169, 39, 0.1); border: 1px solid #f0a927; color: #f0a927; padding: 12px; border-radius: 8px; margin-bottom: 16px; text-align: center; font-size: 14px;">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($erros)): ?>
                <div class="alert alert-erro" style="background: rgba(255, 0, 0, 0.1); border: 1px solid #ff4444; color: #ff4444; padding: 12px; border-radius: 8px; margin-bottom: 16px; text-align: center; font-size: 14px;">
                    <?php foreach ($erros as $erro): ?>
                        <div><?php echo htmlspecialchars($erro); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="socials">
                <button type="button" class="social-btn" onclick="alert('Login com Google em breve!')" style="cursor: not-allowed; opacity: 0.7;">
                    <img src="../assets/img/google.svg" alt="Google" />
                    <p><span class="extra-text">Login com</span> Google</p>
                </button>
                
                <button type="button" class="social-btn" onclick="alert('Login com Apple em breve!')" style="cursor: not-allowed; opacity: 0.7;">
                    <img src="../assets/img/apple.svg" alt="Apple" />
                    <p><span class="extra-text">Login com</span> Apple</p>
                </button>
            </div>
            
            <span class="or"></span>
            
            <!-- Inputs adaptados para o PHP -->
            <input type="text" name="login" placeholder="E-mail ou nome" value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required />
            <input type="password" name="senha" placeholder="Senha" required />
            
            <button type="submit">Entrar</button>
            
            <p style="text-align: center; margin-top: 16px; font-size: 14px;">
                Ainda não tem conta? <a href="cadastro.php" style="color: #f0a927; text-decoration: none;">Cadastre-se</a>
            </p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
    <!-- Se tiver main.js específico para efeitos, mantenha, senão pode remover -->
    <script src="../assets/js/login.js"></script>
</body>
</html>