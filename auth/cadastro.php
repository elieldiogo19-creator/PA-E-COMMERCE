<?php
session_start();
require __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA LDA';
$navbarMode = 'simple';
$baseUrl = '../';
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
                $erros[] = 'Este e-mail já está em uso.';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nome, email, senha_hash)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$nome, $email, $senhaHash]);

                $sucesso = 'Cadastro realizado com sucesso! Já pode iniciar sessão.';
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro ao cadastrar utilizador.';
        }
    }
}

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<main>
    <section class="section">
        <h1>Cadastro</h1>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-erro">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="alert alert-sucesso">
                <?php echo htmlspecialchars($sucesso); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="cadastro.php">
            <label>
                Nome:
                <input type="text" name="nome" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
            </label>
            <br><br>

            <label>
                E-mail:
                <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </label>
            <br><br>

            <label>
                Senha:
                <input type="password" name="senha" required>
            </label>
            <br><br>

            <label>
                Confirmar senha:
                <input type="password" name="senha2" required>
            </label>
            <br><br>

            <button type="submit">Cadastrar</button>
        </form>

        <p>Já tem conta? <a href="login.php">Entrar</a></p>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>