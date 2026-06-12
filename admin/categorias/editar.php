<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$id]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');

    $stmt = $pdo->prepare("
        UPDATE categorias
        SET nome = ?
        WHERE id = ?
    ");

    $stmt->execute([$nome, $id]);

    header('Location: listar.php');
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<main>
<section class="section">

<h1>Editar Categoria</h1>

<form method="POST">
    <label>
        Nome:
        <input type="text" name="nome"
               value="<?= htmlspecialchars($categoria['nome']) ?>" required>
    </label>

    <br><br>

    <button type="submit">Atualizar</button>
</form>

<p><a href="listar.php">← Voltar</a></p>

</section>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>