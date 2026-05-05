<?php
session_start();
require __DIR__ . '/../config/db.php';

// Se já estiver logado, vai para home
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

    // Validações
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
            // Verifica se email já existe
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $erros[] = 'Este e-mail já está cadastrado.';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nome, email, senha_hash)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$nome, $email, $senhaHash]);

                $sucesso = 'Cadastro realizado com sucesso! Você será redirecionado automaticamente em breve...';
                
                // Redireciona automaticamente após 4 segundos (opcional)
                header('Refresh: 4; URL=index.php');
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro ao cadastrar utilizador. Tente novamente.';
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
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    
    <!-- CSS específico do cadastro -->
    <link rel="stylesheet" href="../assets/css/cadastro.css" />
</head>
<body>
    <div class="card">
        <div class="hero">
            <div class="bg"></div>
        </div>
        
        <form method="POST" action="cadastro.php" novalidate>
            <img src="../assets/img/logo-canzala.png" class="logo" alt="Canzala" />
            
            <h3>Criar conta</h3>

            <!-- Mensagens de erro/sucesso -->
            <?php if (!empty($erros)): ?>
                <div class="alert alert-erro">
                    <?php if (count($erros) === 1): ?>
                        <?php echo htmlspecialchars($erros[0]); ?>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo htmlspecialchars($erro); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="alert alert-sucesso">
                    <?php echo htmlspecialchars($sucesso); ?>
                </div>
            <?php endif; ?>

            <!-- Campos do formulário -->
            <div class="form-group">
                <input 
                    type="text" 
                    name="nome" 
                    placeholder="Nome completo" 
                    value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" 
                    required 
                    autocomplete="name"
                />
            </div>

            <div class="form-group">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="E-mail" 
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                    required 
                    autocomplete="email"
                />
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="senha" 
                    placeholder="Senha (mín. 6 caracteres)" 
                    required 
                    autocomplete="new-password"
                    minlength="6"
                />
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="senha2" 
                    placeholder="Confirmar senha" 
                    required 
                    autocomplete="new-password"
                />
            </div>

            <button type="submit">Cadastrar</button>
            
            <div class="login-link">
                Já tem conta? <a href="login.php">Entrar</a>
            </div>
        </form>
    </div>

    <!-- Opcional: JS para validação visual antes de enviar -->
    <script src="../assets/js/cadastro.js"></script>
        <!-- Three.js para o efeito de background -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
    <script src="../assets/js/cadastro.js"></script>
</body>
</body>
</html>