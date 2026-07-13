<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/admin_flash.php';
require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle   = 'Detalhes do Pedido - ' . $nomeProjeto;
$baseUrl     = '../../';
$pageCSS     = 'dashboard';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

// Atualizar status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $novoStatus = $_POST['status'] ?? '';
    $permitidos = ['Pendente', 'Confirmado', 'Enviado', 'Concluído', 'Cancelado'];

    if (in_array($novoStatus, $permitidos)) {

        $stmt = $pdo->prepare("
            UPDATE pedidos
            SET status = ?
            WHERE id = ?
        ");
        $stmt->execute([$novoStatus, $id]);

        setFlash('sucesso', 'Status do pedido atualizado.');
        header("Location: visualizar.php?id=" . $id);
        exit;
    }
}

// Buscar pedido
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
$stmt->execute([$id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header('Location: listar.php');
    exit;
}

// Buscar itens
$stmt = $pdo->prepare("
    SELECT ip.*, p.nome, p.imagem
    FROM itens_pedido ip
    JOIN produtos p ON p.id = ip.produto_id
    WHERE ip.pedido_id = ?
");
$stmt->execute([$id]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totais
$subtotal = 0;
$totalItens = 0;
foreach ($itens as $item) {
    $subtotal   += $item['preco_unitario'] * $item['quantidade'];
    $totalItens += $item['quantidade'];
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-dashboard">

        <!-- Cabeçalho -->
        <section class="section page-header">
            <div>
                <h1>
                    Pedido <span class="pedido-id">#<?= (int) $pedido['id'] ?></span>
                </h1>
                <p>
                    Realizado em
                    <strong><?= date('d/m/Y \à\s H:i', strtotime($pedido['criado_em'])) ?></strong>
                    ·
                    <span class="badge badge-<?= strtolower(htmlspecialchars($pedido['status'])) ?>">
                        <?= htmlspecialchars(ucfirst($pedido['status'])) ?>
                    </span>
                </p>
            </div>

            <a href="listar.php" class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Voltar aos pedidos
            </a>
        </section>

        <?php require __DIR__ . '/../includes/admin_flash_render.php'; ?>

        <!-- Grid principal -->
        <div class="pedido-grid">

            <!-- Coluna esquerda: Itens do pedido -->
            <div class="pedido-main">

                <div class="form-card">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.5 7.27L12 12l-8.5-4.73"></path>
                            <path d="M12 22V12"></path>
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                        Itens do Pedido
                        <span class="badge-count"><?= count($itens) ?></span>
                    </h2>

                    <?php if (empty($itens)): ?>
                        <div class="empty-state">
                            <p>Nenhum item registado neste pedido.</p>
                        </div>
                    <?php else: ?>
                        <div class="pedido-items">
                            <?php foreach ($itens as $item): ?>
                                <div class="pedido-item">
                                    <div class="pedido-item-img">
                                        <?php if (!empty($item['imagem'])): ?>
                                            <img src="/PA-E-COMMERCE/<?= htmlspecialchars($item['imagem']) ?>"
                                                 alt="<?= htmlspecialchars($item['nome']) ?>">
                                        <?php else: ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                <polyline points="21 15 16 10 5 21"></polyline>
                                            </svg>
                                        <?php endif; ?>
                                    </div>

                                    <div class="pedido-item-info">
                                        <h4><?= htmlspecialchars($item['nome']) ?></h4>
                                        <div class="pedido-item-meta">
                                            <span>Preço unitário: <strong><?= number_format($item['preco_unitario'], 2, ',', '.') ?> Kz</strong></span>
                                            <span>·</span>
                                            <span>Quantidade: <strong><?= (int) $item['quantidade'] ?></strong></span>
                                        </div>
                                    </div>

                                    <div class="pedido-item-total">
                                        <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?> Kz
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Resumo financeiro -->
                        <div class="pedido-resumo">
                            <div class="resumo-linha">
                                <span>Subtotal (<?= $totalItens ?> <?= $totalItens === 1 ? 'item' : 'itens' ?>)</span>
                                <span><?= number_format($subtotal, 2, ',', '.') ?> Kz</span>
                            </div>
                            <div class="resumo-linha">
                                <span>Envio</span>
                                <span class="text-muted">Grátis</span>
                            </div>
                            <div class="resumo-linha resumo-total">
                                <span>Total</span>
                                <span><?= number_format($pedido['total'], 2, ',', '.') ?> Kz</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Coluna direita: Info cliente + Status -->
            <div class="pedido-sidebar">

                <!-- Card do Cliente -->
                <div class="form-card">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Cliente
                    </h2>

                    <div class="cliente-card">
                        <div class="cliente-avatar-lg">
                            <?= strtoupper(substr($pedido['nome_cliente'], 0, 1)) ?>
                        </div>
                        <div class="cliente-info">
                            <strong><?= htmlspecialchars($pedido['nome_cliente']) ?></strong>
                            <span>Cliente do pedido</span>
                        </div>
                    </div>

                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                E-mail
                            </div>
                            <div class="info-value">
                                <a href="mailto:<?= htmlspecialchars($pedido['email_cliente']) ?>">
                                    <?= htmlspecialchars($pedido['email_cliente']) ?>
                                </a>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Endereço de entrega
                            </div>
                            <div class="info-value">
                                <?= nl2br(htmlspecialchars($pedido['endereco'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card do Status -->
                <div class="form-card">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Estado do Pedido
                    </h2>

                    <form method="POST" class="status-form">
                        <div class="form-group">
                            <label for="status">Alterar estado:</label>
                            <select id="status" name="status">
                                <?php
                                $statusPossiveis = ['Pendente', 'Confirmado', 'Enviado', 'Concluído', 'Cancelado'];
                                foreach ($statusPossiveis as $status):
                                ?>
                                    <option value="<?= $status ?>"
                                        <?= $pedido['status'] === $status ? 'selected' : '' ?>>
                                        <?= $status ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12a9 9 0 0 1-9 9m9-9a9 9 0 0 0-9-9m9 9H3m9 9a9 9 0 0 1-9-9m9 9c-1.657 0-3-4.03-3-9s1.343-9 3-9m0 18c1.657 0 3-4.03 3-9s-1.343-9-3-9m-9 9a9 9 0 0 1 9-9"></path>
                            </svg>
                            Atualizar Estado
                        </button>
                    </form>

                    <!-- Timeline visual -->
                    <div class="status-timeline">
                        <?php
                        $fluxo = ['Pendente', 'Confirmado', 'Enviado', 'Concluído'];
                        $indexAtual = array_search($pedido['status'], $fluxo);
                        $cancelado  = strtolower($pedido['status']) === 'cancelado';
                        ?>

                        <?php foreach ($fluxo as $i => $estado): ?>
                            <?php
                                $classe = 'pending';
                                if (!$cancelado && $indexAtual !== false) {
                                    if ($i < $indexAtual)  $classe = 'done';
                                    if ($i === $indexAtual) $classe = 'current';
                                }
                            ?>
                            <div class="timeline-step <?= $classe ?>">
                                <div class="timeline-dot"></div>
                                <span><?= $estado ?></span>
                            </div>
                        <?php endforeach; ?>

                        <?php if ($cancelado): ?>
                            <div class="timeline-step cancelled">
                                <div class="timeline-dot"></div>
                                <span>Cancelado</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>