<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}
require_once __DIR__ . '/../includes/admin_flash.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Editar Produto - ' . $nomeProjeto;
$baseUrl = '../../';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

// Buscar produto
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

//Buscar categorias 
$stmtCat = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: listar.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? '';
    $estoque = (int) ($_POST['estoque'] ?? 0);
    $categoria_id = !empty($_POST['categoria_id'])
    ? (int) $_POST['categoria_id']
    : null;

    if ($nome === '' || $preco === '') {
        $errors[] = 'Nome e preço são obrigatórios.';
    }

    $imagemBanco = $produto['imagem']; // padrão: mantém a atual

    // Se enviou nova imagem
    if (!empty($_FILES['imagem']['name'])) {

        if ($_FILES['imagem']['error'] === UPLOAD_ERR_OK) {

            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novoNome = uniqid('prod_', true) . '.' . strtolower($extensao);

            $caminhoFisico = __DIR__ . '/../../assets/img/' . $novoNome;
            $caminhoBanco = 'assets/img/' . $novoNome;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoFisico)) {

                // Apagar imagem antiga
                $imagemAntiga = __DIR__ . '/../../' . $produto['imagem'];
                if (file_exists($imagemAntiga)) {
                    unlink($imagemAntiga);
                }

                $imagemBanco = $caminhoBanco;

            } else {
                $errors[] = 'Erro ao enviar nova imagem.';
            }
        }
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
    UPDATE produtos
    SET nome=?, descricao=?, preco=?, imagem=?, estoque=?, categoria_id=?
    WHERE id=?
");

        $stmt->execute([
    $nome,
    $descricao,
    $preco,
    $imagemBanco,
    $estoque,
    $categoria_id,
    $id
]);
        setFlash('sucesso', 'Produto atualizado com sucesso.');
        header('Location: listar.php');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../admin/includes/admin_sidebar.php';

?>

<main>
    <section class="section">
        <h1>Editar Produto</h1>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-erro">
            <ul>
                <?php foreach ($errors as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>
                Nome:
                <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
            </label>
            <br><br>

            <label>
                Descrição:
                <textarea name="descricao" rows="4"><?= htmlspecialchars($produto['descricao']) ?></textarea>
            </label>
            <br><br>

            <label>
                Preço:
                <input type="number" step="0.01" name="preco" value="<?= $produto['preco'] ?>" required>
            </label>
            <br><br>

            <p>Imagem atual:</p>
            <img src="/PA-E-COMMERCE/<?= htmlspecialchars($produto['imagem']) ?>" width="120">
            <br><br>

            <label>
                Trocar imagem:
                <input type="file" name="imagem" accept="image/*">
            </label>
            <br><br>
            <label>
                Estoque:
                <input type="number" name="estoque" value="<?= $produto['estoque'] ?>" min="0" required>
            </label>
            <br><br>
            <label>
                Categoria:
                <select name="categoria_id">
                    <option value="">Sem categoria</option>

                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $produto['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nome']) ?>
                    </option>
                    <?php endforeach; ?>

                </select>
            </label>
            <br><br>

            <button type="submit">Atualizar Produto</button>
        </form>

        <p><a href="listar.php">← Voltar</a></p>

    </section>
</main>

<?php require_once __DIR__ . '/../../admin/includes/admin_footer.php'; ?>