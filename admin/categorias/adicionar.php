<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');

    if ($nome === '') {
        $errors[] = "Nome é obrigatório.";
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
        $stmt->execute([$nome]);

        header('Location: listar.php');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/admin_sidebar.php';
?>

<main>
<section class="section">

<h1>Nova Categoria</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-erro">
        <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <label>
        Nome:
        <input type="text" name="nome" required>
    </label>

    <br><br>

    <button type="submit">Salvar</button>
</form>

<p><a href="listar.php">← Voltar</a></p>

</section>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>