<?php
session_start();
require __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA LDA';
$navbarMode = 'simple';
$baseUrl = '../';
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
        $erros[] = 'Preencha login e senha.';
    }

    if (empty($erros)) {
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
                    header('Location: /PA-E-COMMERCE/pages/checkout.php');
                } else {
                    header('Location: /PA-E-COMMERCE/index.php');
                }
                exit;
            } else {
                $erros[] = 'Senha incorreta ou utilizador inexistente.';
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro ao tentar fazer login.';
        }
    }
}

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<main>
    <section class="section">
        <h1>Login</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-erro">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo $from ? 'login.php?from=' . urlencode($from) : 'login.php'; ?>">
            <label>
                E-mail ou nome:
                <input type="text" name="login" required>
            </label>
            <br><br>

            <label>
                Senha:
                <input type="password" name="senha" required>
            </label>
            <br><br>

            <button type="submit">Entrar</button>
        </form>

        <p>Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>