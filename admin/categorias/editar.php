<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/admin_flash.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Editar Categoria - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

// Buscar categoria
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$id]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    header('Location: listar.php');
    exit;
}

// Contar produtos associados
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = ?");
$stmtCount->execute([$id]);
$totalProdutos = (int) $stmtCount->fetchColumn();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');

    if ($nome === '') {
        $errors[] = 'O nome da categoria é obrigatório.';
    }

    if (mb_strlen($nome) > 100) {
        $errors[] = 'O nome não pode ter mais de 100 caracteres.';
    }

    // Verificar duplicado (excluindo a própria)
    if (empty($errors)) {
        $stmtCheck = $pdo->prepare("
            SELECT COUNT(*) FROM categorias
            WHERE LOWER(nome) = LOWER(?) AND id != ?
        ");
        $stmtCheck->execute([$nome, $id]);

        if ($stmtCheck->fetchColumn() > 0) {
            $errors[] = 'Já existe outra categoria com esse nome.';
        }
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            UPDATE categorias
            SET nome = ?
            WHERE id = ?
        ");

        $stmt->execute([$nome, $id]);

        setFlash('sucesso', 'Categoria atualizada com sucesso.');
        header('Location: listar.php');
        exit;
    }

    // Atualiza valor para reexibir no form em caso de erro
    $categoria['nome'] = $nome;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho -->
        <section class="section page-header">
            <div>
                <h1>Editar Categoria</h1>
                <p>
                    A editar: <strong><?= htmlspecialchars($categoria['nome']) ?></strong>
                    · ID #<?= $id ?>
                </p>
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

        <!-- Aviso se tem produtos -->
        <?php if ($totalProdutos > 0): ?>
            <div class="form-narrow">
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div>
                        Esta categoria possui <strong><?= $totalProdutos ?> produto(s)</strong> associado(s).
                        Alterar o nome afetará como estes produtos são exibidos na loja.
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulário -->
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
                               value="<?= htmlspecialchars($categoria['nome']) ?>"
                               placeholder="Ex: Smartphones e Tablets"
                               maxlength="100"
                               required autofocus>
                        <small class="label-hint" style="margin-top: 6px; display: block;">
                            Use nomes claros e curtos. Serão visíveis para os clientes na loja.
                        </small>
                    </div>

                    <!-- Info extra -->
                    <div class="info-list" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--admin-border);">
                        <div class="info-item">
                            <div class="info-label">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.5 7.27L12 12l-8.5-4.73"></path>
                                    <path d="M12 22V12"></path>
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                </svg>
                                Produtos associados
                            </div>
                            <div class="info-value">
                                <?= $totalProdutos ?> <?= $totalProdutos === 1 ? 'produto' : 'produtos' ?>
                            </div>
                        </div>

                        <?php if (!empty($categoria['criado_em'])): ?>
                            <div class="info-item">
                                <div class="info-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    Criada em
                                </div>
                                <div class="info-value">
                                    <?= date('d/m/Y \à\s H:i', strtotime($categoria['criado_em'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <a href="listar.php" class="btn-secondary">Cancelar</a>
                        <button type="submit" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                            Atualizar Categoria
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>