<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Adicionar Produto - ' . $nomeProjeto;
$baseUrl = '../../';

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

    // Validação da imagem
    if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Selecione uma imagem válida.';
    }

    if (empty($errors)) {

        try {

            // 🔹 Gerar nome único
            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nomeImagem = uniqid('prod_', true) . '.' . strtolower($extensao);

            // 🔹 Caminho físico
            $caminhoFisico = __DIR__ . '/../../assets/img/' . $nomeImagem;

            // 🔹 Caminho que vai para o banco
            $caminhoBanco = 'assets/img/' . $nomeImagem;

            if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoFisico)) {
                throw new Exception('Erro ao salvar imagem.');
            }

            // 🔹 Inserir no banco
            $stmt = $pdo->prepare("
                INSERT INTO produtos 
                (nome, descricao, preco, imagem, estoque, categoria_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nome,
                $descricao,
                $preco,
                $caminhoBanco,
                $estoque,
                $categoria_id
            ]);

            setFlash('sucesso', 'Produto adicionado com sucesso.');
            header('Location: listar.php');
            exit;

        } catch (Exception $e) {
            $errors[] = 'Erro ao adicionar produto.';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/admin_sidebar.php';
?>

<main>
    <?php require __DIR__ . '/../../includes/flash_render.php'; ?>

    <section class="section">
        <h1>Adicionar Produto</h1>

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
                <input type="text" name="nome" required>
            </label>
            <br><br>

            <label>
                Descrição:
                <textarea name="descricao" rows="4"></textarea>
            </label>
            <br><br>

            <label>
                Preço:
                <input type="number" step="0.01" name="preco" required>
            </label>
            <br><br>

            <label>
                Imagem:
                <input type="file" name="imagem" accept="image/*" required>
            </label>
            <br><br>

            <label>
                Estoque:
            <input type="number" name="estoque" min="0" required>
            </label>
            <br>

            <label>
                Categoria:
            <select name="categoria_id">
                <option value="">Sem categoria</option>

            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['nome']) ?>
                </option>
            <?php endforeach; ?>

            </select>
            </label>
            <br><br>

            

            <button type="submit">Salvar Produto</button>
        </form>

        <p>
            <a href="listar.php">← Voltar</a>
        </p>

    </section>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>