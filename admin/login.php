<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA LDA';
$pageTitle = 'Login Admin - ' . $nomeProjeto;
$baseUrl = '../';

$errors = [];

// Se já estiver logado, redireciona
if (!empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $errors[] = 'Preencha e-mail e senha.';
    }

    if (empty($errors)) {

        try {
            $stmt = $pdo->prepare("
                SELECT id, nome, email, senha_hash, ultimo_acesso
                FROM admins
                WHERE email = ?
                LIMIT 1
            ");

            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($senha, $admin['senha_hash'])) {

                session_regenerate_id(true);

                $_SESSION['admin_id']            = $admin['id'];
                $_SESSION['admin_nome']          = $admin['nome'];
                $_SESSION['admin_email']         = $admin['email'];
                $_SESSION['admin_ultimo_acesso'] = $admin['ultimo_acesso'];

                $stmtUpdate = $pdo->prepare("
                    UPDATE admins 
                    SET ultimo_acesso = NOW() 
                    WHERE id = ?
                ");
                $stmtUpdate->execute([$admin['id']]);

                header('Location: /PA-E-COMMERCE/admin/dashboard.php');
                exit;

            } else {
                $errors[] = 'Credenciais de administrador inválidas.';
            }

        } catch (PDOException $e) {
            $errors[] = 'Erro ao tentar iniciar sessão no painel.';
            // Debug temporário:
            // $errors[] = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main>
    <section class="section">
        <h1>Login do Administrador</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-erro">
                <ul>
                    <?php foreach ($errors as $erro): ?>
                        <li><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
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

<?php require_once __DIR__ . '/../admin/includes/admin_footer.php'; ?>