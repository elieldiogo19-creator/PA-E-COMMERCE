<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/admin_flash.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Nova Categoria - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

$errors = [];
$nome   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');

    if ($nome === '') {
        $errors[] = 'O nome da categoria é obrigatório.';
    }

    if (mb_strlen($nome) > 100) {
        $errors[] = 'O nome não pode ter mais de 100 caracteres.';
    }

    // Verificar duplicado
    if (empty($errors)) {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE LOWER(nome) = LOWER(?)");
        $stmtCheck->execute([$nome]);

        if ($stmtCheck->fetchColumn() > 0) {
            $errors[] = 'Já existe uma categoria com esse nome.';
        }
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
        $stmt->execute([$nome]);

        setFlash('sucesso', 'Categoria criada com sucesso.');
        header('Location: listar.php');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho -->
        <section class="section page-header">
            <div>
                <h1>Nova Categoria</h1>
                <p>Adicione uma nova categoria ao catálogo.</p>
            </div>

            <a href="listar.php" class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Voltar
            </a>
        </section>

        <!-- Erros -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    <strong>Ocorreu um erro:</strong>
                    <ul style="margin: 4px 0 0; padding-left: 20px;">
                        <?php foreach ($errors as $erro): ?>
                            <li><?= htmlspecialchars($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulário centrado -->
        <div class="form-narrow">
            <form method="POST" class="admin-form">

                <div class="form-card">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Dados da Categoria
                    </h2>

                    <div class="form-group">
                        <label for="nome">Nome da categoria <span class="required">*</span></label>
                        <input type="text" id="nome" name="nome"
                               value="<?= htmlspecialchars($nome) ?>"
                               placeholder="Ex: Smartphones e Tablets"
                               maxlength="100"
                               required autofocus>
                        <small class="label-hint" style="margin-top: 6px; display: block;">
                            Use nomes claros e curtos. Serão visíveis para os clientes na loja.
                        </small>
                    </div>

                    <div class="form-actions">
                        <a href="listar.php" class="btn-secondary">Cancelar</a>
                        <button type="submit" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Criar Categoria
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>