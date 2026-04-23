<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Se já estiver logado como admin, vai direto para o dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');;
    exit;
}

$nomeProjeto = 'CANZALA LDA';
$pageTitle = 'Login Admin - ' . $nomeProjeto;
$baseUrl = '../';
// require_once __DIR__ . '/../includes/header.php';
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erros[] = 'Preencha e-mail e senha.';
    }

    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare("
                SELECT id, nome, email, senha_hash
                FROM admins
                WHERE email = ?
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($senha, $admin['senha_hash'])) {
                session_regenerate_id(true);

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nome'] = $admin['nome'];
                $_SESSION['admin_email'] = $admin['email'];

                header('Location: /PA-E-COMMERCE/admin/dashboard.php');
                exit;
            } else {
                $erros[] = 'Credenciais de administrador inválidas.';
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro ao tentar iniciar sessão no painel.';
        }
    }
}
?>

<main>
    <section class="section">
        <h1>Login do Administrador</h1>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-erro">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>
                E-mail:
                <input type="email" name="email" required>
            </label>
            <br><br>

            <label>
                Senha:
                <input type="password" name="senha" required>
            </label>
            <br><br>

            <button type="submit">Entrar no painel</button>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>