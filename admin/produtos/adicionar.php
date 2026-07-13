<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/admin_flash.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Adicionar Produto - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome            = trim($_POST['nome'] ?? '');
    $nome_curto      = trim($_POST['nome_curto'] ?? '');
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

    // Validação da imagem
    if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Selecione uma imagem válida.';
    }

    if (mb_strlen($descricao_curta) > 200) {
        $errors[] = 'A descrição curta não pode ter mais de 200 caracteres.';
    }

    if (mb_strlen($nome_curto) > 50) {
        $errors[] = 'O nome curto não pode ter mais de 50 caracteres.';
    }

    if (empty($errors)) {
        try {
            // Gerar nome único
            $extensao   = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nomeImagem = uniqid('prod_', true) . '.' . strtolower($extensao);

            // Caminho físico
            $caminhoFisico = __DIR__ . '/../../assets/img/prods/' . $nomeImagem;

            // Caminho que vai para o banco
            $caminhoBanco = 'assets/img/prods/' . $nomeImagem;

            if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoFisico)) {
                throw new Exception('Erro ao salvar imagem.');
            }

            // Inserir no banco
            $stmt = $pdo->prepare("
                INSERT INTO produtos (nome, nome_curto, descricao, descricao_curta, preco, imagem, estoque, categoria_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nome,
                $nome_curto ?: null,
                $descricao,
                $descricao_curta,
                $preco,
                $caminhoBanco,
                $estoque,
                $categoria_id
            ]);

            $novoId = (int) $pdo->lastInsertId();

            // 🆕 Resposta AJAX
            if (isAjax()) {
                jsonSuccess('Produto adicionado com sucesso.', [
                    'id'       => $novoId,
                    'redirect' => 'listar.php',
                ]);
            }

            // Fluxo normal (sem AJAX)
            setFlash('sucesso', 'Produto adicionado com sucesso.');
            header('Location: listar.php');
            exit;

        } catch (Exception $e) {
            $errors[] = 'Erro ao adicionar produto.';
        }
    }

    // 🆕 Se AJAX e tem erros → devolve JSON
    if (isAjax() && !empty($errors)) {
        jsonError('Corrija os erros abaixo.', $errors);
    }
}

$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho da página -->
        <section class="section page-header">
            <div>
                <h1>Adicionar Produto</h1>
                <p>Preencha os dados abaixo para cadastrar um novo produto.</p>
            </div>

            <a href="listar.php" class="btn-secondary" data-history-back>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Voltar
            </a>
        </section>

        <!-- Erros (fallback sem JS) -->
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

        <!-- Formulário AJAX -->
        <form method="POST" enctype="multipart/form-data" class="admin-form" data-ajax>

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
                                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                                   placeholder="Ex: Samsung Galaxy S24 Ultra 512GB - Preto Titanium"
                                   required>
                            <small class="label-hint" style="margin-top: 6px; display: block;">
                                Nome completo que aparece nas páginas de detalhes e listagens.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="nome_curto">
                                Nome curto
                                <span class="label-hint">(exibido no Home, máx. 50 caracteres)</span>
                            </label>
                            <input type="text" id="nome_curto" name="nome_curto"
                                   value="<?= htmlspecialchars($_POST['nome_curto'] ?? '') ?>"
                                   placeholder="Ex: Galaxy S24 Ultra"
                                   maxlength="50">
                            <small class="char-counter"><span id="counter-curto">0</span>/50</small>
                        </div>

                        <div class="form-group">
                            <label for="descricao_curta">
                                Descrição curta
                                <span class="label-hint">(aparece nos cards, máx. 200 caracteres)</span>
                            </label>
                            <textarea id="descricao_curta" name="descricao_curta"
                                      rows="2" maxlength="200"
                                      placeholder="Uma breve descrição atraente..."><?= htmlspecialchars($_POST['descricao_curta'] ?? '') ?></textarea>
                            <small class="char-counter"><span id="counter">0</span>/200</small>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição completa</label>
                            <textarea id="descricao" name="descricao" rows="6"
                                      placeholder="Detalhes técnicos, benefícios, características..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
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
                                       value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>"
                                       placeholder="0.00" required>
                            </div>

                            <div class="form-group">
                                <label for="estoque">Estoque inicial <span class="required">*</span></label>
                                <input type="number" id="estoque" name="estoque"
                                       min="0"
                                       value="<?= htmlspecialchars($_POST['estoque'] ?? '0') ?>"
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

                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="imagem" name="imagem"
                                   accept="image/*" required
                                   class="upload-input">
                            <label for="imagem" class="upload-label">
                                <div class="upload-preview" id="uploadPreview">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <p><strong>Clique para escolher</strong> ou arraste a imagem</p>
                                    <small>PNG, JPG ou WEBP</small>
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
                                        <?= (isset($_POST['categoria_id']) && $_POST['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="form-actions">
                        <a href="listar.php" class="btn-secondary" data-history-back>Cancelar</a>
                        <button type="submit" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Salvar Produto
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </main>
</div>

<script>
    // Contador da descrição curta
    const textarea = document.getElementById('descricao_curta');
    const counter  = document.getElementById('counter');
    if (textarea && counter) {
        const update = () => counter.textContent = textarea.value.length;
        textarea.addEventListener('input', update);
        update();
    }

    // Contador do nome curto
    const nomeCurto = document.getElementById('nome_curto');
    const counterC  = document.getElementById('counter-curto');
    if (nomeCurto && counterC) {
        const upd = () => counterC.textContent = nomeCurto.value.length;
        nomeCurto.addEventListener('input', upd);
        upd();
    }

    // Preview da imagem
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
                    <small>Clique para trocar</small>
                `;
                preview.classList.add('has-image');
            };
            reader.readAsDataURL(file);
        });
    }
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>