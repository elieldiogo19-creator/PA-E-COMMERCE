<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/admin_flash.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Editar Produto - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

// Buscar produto
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar categorias
$stmtCat = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: listar.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome            = trim($_POST['nome'] ?? '');
    $descricao       = trim($_POST['descricao'] ?? '');
    $descricao_curta = trim($_POST['descricao_curta'] ?? '');
    $preco           = $_POST['preco'] ?? '';
    $estoque         = (int) ($_POST['estoque'] ?? 0);
    $categoria_id    = !empty($_POST['categoria_id'])
                        ? (int) $_POST['categoria_id']
                        : null;

    if ($nome === '' || $preco === '') {
        $errors[] = 'Nome e preço são obrigatórios.';
    }

    $imagemBanco = $produto['imagem']; // padrão: mantém a atual

    // Se enviou nova imagem
    if (!empty($_FILES['imagem']['name'])) {

        if ($_FILES['imagem']['error'] === UPLOAD_ERR_OK) {

            $extensao  = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novoNome  = uniqid('prod_', true) . '.' . strtolower($extensao);

            $caminhoFisico = __DIR__ . '/../../assets/img/' . $novoNome;
            $caminhoBanco  = 'assets/img/' . $novoNome;

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
            SET nome=?, descricao=?, descricao_curta=?, preco=?, imagem=?, estoque=?, categoria_id=?
            WHERE id=?
        ");

        $stmt->execute([
            $nome,
            $descricao,
            $descricao_curta,
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
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho da página -->
        <section class="section page-header">
            <div>
                <h1>Editar Produto</h1>
                <p>A editar: <strong><?= htmlspecialchars($produto['nome']) ?></strong> · ID #<?= $id ?></p>
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

        <!-- Formulário -->
        <form method="POST" enctype="multipart/form-data" class="admin-form">

            <div class="form-grid">

                <!-- Coluna esquerda -->
                <div class="form-column">

                    <div class="form-card">
                        <h2>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Informações do Produto
                        </h2>

                        <div class="form-group">
                            <label for="nome">Nome do produto <span class="required">*</span></label>
                            <input type="text" id="nome" name="nome"
                                   value="<?= htmlspecialchars($produto['nome']) ?>"
                                   placeholder="Ex: Samsung Galaxy S24 Ultra"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="descricao_curta">
                                Descrição curta
                                <span class="label-hint">(aparece nos cards, máx. 200 caracteres)</span>
                            </label>
                            <textarea id="descricao_curta" name="descricao_curta"
                                      rows="2" maxlength="200"
                                      placeholder="Uma breve descrição atraente..."><?= htmlspecialchars($produto['descricao_curta'] ?? '') ?></textarea>
                            <small class="char-counter"><span id="counter">0</span>/200</small>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição completa</label>
                            <textarea id="descricao" name="descricao" rows="6"
                                      placeholder="Detalhes técnicos, benefícios, características..."><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-card">
                        <h2>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            Preço e Estoque
                        </h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="preco">Preço (Kz) <span class="required">*</span></label>
                                <input type="number" id="preco" name="preco"
                                       step="0.01" min="0"
                                       value="<?= htmlspecialchars($produto['preco']) ?>"
                                       placeholder="0.00" required>
                            </div>

                            <div class="form-group">
                                <label for="estoque">Estoque atual <span class="required">*</span></label>
                                <input type="number" id="estoque" name="estoque"
                                       min="0"
                                       value="<?= (int) $produto['estoque'] ?>"
                                       placeholder="0" required>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Coluna direita -->
                <div class="form-column">

                    <div class="form-card">
                        <h2>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            Imagem do Produto
                        </h2>

                        <!-- Imagem Atual -->
                        <?php if (!empty($produto['imagem'])): ?>
                            <div class="current-image">
                                <p class="current-image-label">Imagem atual:</p>
                                <img src="/PA-E-COMMERCE/<?= htmlspecialchars($produto['imagem']) ?>"
                                     alt="<?= htmlspecialchars($produto['nome']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="upload-area">
                            <input type="file" id="imagem" name="imagem"
                                   accept="image/*"
                                   class="upload-input">
                            <label for="imagem" class="upload-label">
                                <div class="upload-preview" id="uploadPreview">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <p><strong>Clique para trocar</strong> a imagem</p>
                                    <small>Deixe vazio para manter a atual</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-card">
                        <h2>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            </svg>
                            Categoria
                        </h2>

                        <div class="form-group">
                            <label for="categoria_id">Selecione a categoria</label>
                            <select id="categoria_id" name="categoria_id">
                                <option value="">Sem categoria</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= $produto['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="form-actions">
                        <a href="listar.php" class="btn-secondary">Cancelar</a>
                        <button type="submit" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                            Atualizar Produto
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </main>
</div>

<script>
    // Contador de caracteres
    const textarea = document.getElementById('descricao_curta');
    const counter  = document.getElementById('counter');
    if (textarea && counter) {
        const update = () => counter.textContent = textarea.value.length;
        textarea.addEventListener('input', update);
        update();
    }

    // Preview da nova imagem
    const inputImg = document.getElementById('imagem');
    const preview  = document.getElementById('uploadPreview');
    if (inputImg && preview) {
        inputImg.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (ev) => {
                preview.innerHTML = `
                    <img src="${ev.target.result}" alt="Preview">
                    <p class="upload-filename">${file.name}</p>
                    <small>Nova imagem selecionada</small>
                `;
                preview.classList.add('has-image');
            };
            reader.readAsDataURL(file);
        });
    }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>