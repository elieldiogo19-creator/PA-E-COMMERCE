<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Acesso Restrito - ' . $nomeProjeto;
$baseUrl     = '../';

$errors = [];

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

                $stmtUpdate = $pdo->prepare("UPDATE admins SET ultimo_acesso = NOW() WHERE id = ?");
                $stmtUpdate->execute([$admin['id']]);

                header('Location: /PA-E-COMMERCE/admin/dashboard.php');
                exit;
            } else {
                $errors[] = 'Credenciais inválidas.';
            }

        } catch (PDOException $e) {
            $errors[] = 'Não foi possível processar o pedido. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/global.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/dashboard.css">
</head>
<body class="login-page">

    <div class="login-container">

        <!-- Painel Esquerdo (informativo / sério) -->
        <aside class="login-visual">
            <div class="login-visual-content">

                <div class="login-brand">
                    <img src="<?= $baseUrl ?>assets/img/logo-canzala-2.png" alt="Canzala">
                    <span class="brand-divider"></span>
                    <span class="brand-label">Área Administrativa</span>
                </div>

                <div class="login-notice">
                    <h1>Sistema de Gestão Interna</h1>
                    <p>
                        Este ambiente é de uso <strong>exclusivo</strong> de administradores autorizados
                        pela Canzala, LDA. O acesso e as operações realizadas neste painel são
                        monitorizados e ficam registados para fins de auditoria e segurança.
                    </p>
                </div>

                <div class="login-security-list">
                    <div class="security-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <div>
                            <strong>Acesso restrito</strong>
                            <span>Somente utilizadores autenticados.</span>
                        </div>
                    </div>

                    <div class="security-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <div>
                            <strong>Sessões registadas</strong>
                            <span>Data, hora e endereço IP são armazenados.</span>
                        </div>
                    </div>

                    <div class="security-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <div>
                            <strong>Uso indevido</strong>
                            <span>Sujeito às disposições legais aplicáveis.</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="login-visual-footer">
                <span>&copy; <?= date('Y') ?> Canzala, LDA.</span>
                <span>Versão 1.0</span>
            </div>
        </aside>

        <!-- Painel Direito (formulário) -->
        <main class="login-form-wrapper">

            <a href="<?= $baseUrl ?>index.php" class="login-back">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Voltar ao site
            </a>

            <div class="login-form-content">

                <div class="login-header">
                    <h2>Autenticação</h2>
                    <p>Introduza as suas credenciais para continuar.</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            <?php foreach ($errors as $erro): ?>
                                <span><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="login-form" autocomplete="off">

                    <div class="form-group">
                        <label for="email">Endereço de e-mail</label>
                        <div class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <input type="email" id="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <div class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" id="senha" name="senha" required>
                            <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar senha">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Entrar
                    </button>
                </form>

                <div class="login-footer-note">
                    Em caso de problemas com o acesso, contacte o administrador do sistema.
                </div>

            </div>
        </main>
    </div>

    <script>
        const toggle = document.getElementById('togglePassword');
        const senhaInput = document.getElementById('senha');

        toggle.addEventListener('click', () => {
            const isPassword = senhaInput.type === 'password';
            senhaInput.type = isPassword ? 'text' : 'password';

            toggle.innerHTML = isPassword
                ? `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                     <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                     <line x1="1" y1="1" x2="23" y2="23"></line>
                   </svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                     <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                     <circle cx="12" cy="12" r="3"></circle>
                   </svg>`;
        });
    </script>

</body>
</html>