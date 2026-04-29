<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA LDA';
$pageTitle = 'Adicionar Produto - ' . $nomeProjeto;
$baseUrl = '../../';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? '';

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
                INSERT INTO produtos (nome, descricao, preco, imagem)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $nome,
                $descricao,
                $preco,
                $caminhoBanco
            ]);

            header('Location: listar.php');
            exit;

        } catch (Exception $e) {
            $errors[] = 'Erro ao adicionar produto.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<main>
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

            <button type="submit">Salvar Produto</button>
        </form>

        <p>
            <a href="listar.php">← Voltar</a>
        </p>

    </section>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>