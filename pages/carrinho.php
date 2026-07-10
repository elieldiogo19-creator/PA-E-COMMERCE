<?php
ob_start();
session_start();
require __DIR__ . '/../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$navbarMode  = 'simple';
$baseUrl     = '../';
$pageTitle   = 'Carrinho - ' . $nomeProjeto;

// ============================
// DETECTAR AJAX
// ============================
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// ============================
// AÇÕES DO CARRINHO (ANTES DE QUALQUER OUTPUT)
// ============================

// Garantir que o carrinho é um array
if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

/**
 * Calcula totais e responde JSON (se AJAX) ou redireciona (se normal)
 */
function responderCarrinho($extras = []) {
    global $isAjax, $pdo;
    
    if (!$isAjax) {
        header('Location: carrinho.php');
        exit;
    }
    
    // Calcular totais
    $totalItens = 0;
    $totalGeral = 0;
    $novoSubtotalItem = 0;
    
    if (!empty($_SESSION['carrinho'])) {
        try {
            $ids = array_keys($_SESSION['carrinho']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            $stmt = $pdo->prepare("SELECT id, preco FROM produtos WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($produtos as $p) {
                $qtd = (int) $_SESSION['carrinho'][$p['id']];
                $subtotal = $p['preco'] * $qtd;
                $totalGeral += $subtotal;
                $totalItens += $qtd;
                
                if (isset($extras['id']) && $p['id'] == $extras['id']) {
                    $novoSubtotalItem = $subtotal;
                }
            }
        } catch (Exception $e) {
            // Silencia erros
        }
    }
    
    if (ob_get_length()) ob_clean();
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        'sucesso' => true,
        'total_itens' => $totalItens,
        'total_geral' => number_format($totalGeral, 2, ',', '.'),
        'novo_subtotal' => number_format($novoSubtotalItem, 2, ',', '.'),
        'carrinho_vazio' => empty($_SESSION['carrinho'])
    ], $extras));
    exit;
}

// Limpar carrinho
if (isset($_GET['limpar'])) {
    unset($_SESSION['carrinho']);
    responderCarrinho(['acao' => 'limpar']);
}

// Remover item
if (isset($_GET['remover'])) {
    $idRemover = (int) $_GET['remover'];
    if (isset($_SESSION['carrinho'][$idRemover])) {
        unset($_SESSION['carrinho'][$idRemover]);
    }
    responderCarrinho(['acao' => 'remover', 'id' => $idRemover]);
}

// Aumentar quantidade
if (isset($_GET['aumentar'])) {
    $idAumentar = (int) $_GET['aumentar'];
    if (isset($_SESSION['carrinho'][$idAumentar])) {
        $_SESSION['carrinho'][$idAumentar]++;
    }
    responderCarrinho([
        'acao' => 'aumentar', 
        'id' => $idAumentar,
        'nova_quantidade' => $_SESSION['carrinho'][$idAumentar] ?? 0
    ]);
}

// Diminuir quantidade
if (isset($_GET['diminuir'])) {
    $idDiminuir = (int) $_GET['diminuir'];
    $removido = false;
    
    if (isset($_SESSION['carrinho'][$idDiminuir])) {
        $_SESSION['carrinho'][$idDiminuir]--;
        if ($_SESSION['carrinho'][$idDiminuir] <= 0) {
            unset($_SESSION['carrinho'][$idDiminuir]);
            $removido = true;
        }
    }
    
    responderCarrinho([
        'acao' => 'diminuir',
        'id' => $idDiminuir,
        'nova_quantidade' => $removido ? 0 : ($_SESSION['carrinho'][$idDiminuir] ?? 0),
        'removido' => $removido
    ]);
}

// ============================
// SE CHEGOU AQUI = REQUISIÇÃO NORMAL (render da página)
// ============================

// =========================
// MONTAR DADOS DO CARRINHO
// =========================
$carrinho = $_SESSION['carrinho'] ?? [];

$produtosCarrinho = [];
$totalGeral = 0;

if (!empty($carrinho)) {
    require __DIR__ . '/../config/db.php';

    $ids = array_keys($carrinho);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, nome, preco FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $produtosPorId = [];
    foreach ($produtos as $p) {
        $produtosPorId[$p['id']] = $p;
    }

    foreach ($carrinho as $idProduto => $quantidade) {
        if (!isset($produtosPorId[$idProduto])) {
            continue;
        }

        $p = $produtosPorId[$idProduto];
        $quantidade = (int)$quantidade;

        $subtotal = $p['preco'] * $quantidade;
        $totalGeral += $subtotal;

        $produtosCarrinho[] = [
            'id' => $p['id'],
            'nome' => $p['nome'],
            'preco' => $p['preco'],
            'quantidade' => $quantidade,
            'subtotal' => $subtotal,
        ];
    }
}

// =========================
// RENDER
// =========================
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>

<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/carrinho.css">

<main class="carrinho-main">
    <div class="carrinho-container">

        <h1 class="carrinho-titulo">O meu Carrinho</h1>

        <?php if (empty($produtosCarrinho)): ?>
        <!-- CARRINHO VAZIO -->
        <div class="carrinho-vazio">
            <div class="vazio-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
            <h2>O seu carrinho está vazio</h2>
            <p>Adicione produtos ao seu carrinho para continuar</p>
            <div class="vazio-botoes">
                <a href="../produtos.php" class="btn-continuar">Ver Produtos</a>
                <a href="../index.php" class="btn-voltar-home">Página Inicial</a>
            </div>
        </div>

        <?php else: ?>
        <!-- LAYOUT DE 2 COLUNAS: Produtos | Resumo -->
        <div class="carrinho-layout">

            <!-- COLUNA ESQUERDA: LISTA DE PRODUTOS -->
            <div class="carrinho-produtos">

                <?php foreach ($produtosCarrinho as $item): ?>
                <div class="produto-item">

                    <!-- Imagem do produto -->
                    <div class="produto-imagem">
                        <?php 
                                $stmtImg = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
                                $stmtImg->execute([$item['id']]);
                                $imgData = $stmtImg->fetch(PDO::FETCH_ASSOC);
                                $imgPath = !empty($imgData['imagem']) ? '../' . $imgData['imagem'] : '../assets/img/produto-sem-imagem.png';
                                ?>
                        <img src="<?php echo htmlspecialchars($imgPath); ?>"
                            alt="<?php echo htmlspecialchars($item['nome']); ?>"
                            onerror="this.src='../assets/img/produto-sem-imagem.png'">
                    </div>

                    <!-- Informações do produto -->
                    <div class="produto-info">
                        <h3 class="produto-nome"><?php echo htmlspecialchars($item['nome']); ?></h3>
                        <p class="produto-preco-unit">
                            <?php echo number_format($item['preco'], 2, ',', '.'); ?> Kz
                            <span class="preco-unit-label">/ unidade</span>
                        </p>
                    </div>

                    <!-- Ações: Preço Total + Quantidade -->
                    <div class="produto-acoes">

                        <!-- Preço total do item -->
                        <p class="produto-subtotal">
                            <?php echo number_format($item['subtotal'], 2, ',', '.'); ?> Kz
                        </p>

                        <!-- Controles de quantidade -->
                        <div class="quantidade-controle">
                            <a href="#" class="btn-lixeira btn-carrinho-acao" data-acao="remover"
                                data-id="<?php echo (int)$item['id']; ?>"
                                onclick="return confirm('Remover este produto?')" title="Remover">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                    </path>
                                </svg>
                            </a>

                            <a href="#" class="btn-qty btn-carrinho-acao" data-acao="diminuir"
                                data-id="<?php echo (int)$item['id']; ?>">−</a>

                            <span class="qty-numero" data-id="<?php echo (int)$item['id']; ?>">
                                <?php echo (int)$item['quantidade']; ?>
                            </span>

                            <a href="#" class="btn-qty btn-carrinho-acao" data-acao="aumentar"
                                data-id="<?php echo (int)$item['id']; ?>">+</a>
                        </div>

                    </div>

                </div>
                <?php endforeach; ?>

                <!-- Botão limpar carrinho -->
                <div class="carrinho-acoes-extras">
                    <a href="#" class="btn-limpar btn-carrinho-acao" data-acao="limpar"
                        onclick="return confirm('Tem certeza que deseja limpar todo o carrinho?')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                            </path>
                        </svg>
                        Limpar carrinho
                    </a>

                    <a href="../produtos.php" class="btn-continuar-comprando">
                        ← Continuar comprando
                    </a>
                </div>

            </div>

            <!-- COLUNA DIREITA: RESUMO DO PEDIDO -->
            <aside class="carrinho-resumo">

                <h2 class="resumo-titulo">Resumo do Pedido</h2>

                <!-- Detalhes do pedido -->
                <div class="resumo-detalhes">
                    <div class="resumo-linha">
                        <span>Subtotal</span>
                        <span><?php echo number_format($totalGeral, 2, ',', '.'); ?> Kz</span>
                    </div>
                    <div class="resumo-linha">
                        <span>Envio</span>
                        <span class="envio-gratis">Grátis</span>
                    </div>
                </div>

                <hr class="resumo-linha-separadora">

                <!-- Total -->
                <div class="resumo-total">
                    <span>Total</span>
                    <span class="total-valor"><?php echo number_format($totalGeral, 2, ',', '.'); ?> Kz</span>
                </div>

                <!-- Botão Finalizar Compra -->
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                <a href="checkout.php" class="btn-comprar-final">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    FINALIZAR COMPRA
                </a>
                <?php else: ?>
                <a href="/PA-E-COMMERCE/auth/login.php?from=checkout" class="btn-comprar-final"
                    onclick="return confirm('Precisa iniciar sessão para finalizar a compra. Deseja continuar?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    FINALIZAR COMPRA
                </a>
                <?php endif; ?>

            </aside>

        </div>

        <?php endif; ?>

    </div>

    </div>
</main>

<!-- BARRA FIXA MOBILE (só aparece em telas pequenas) -->
<?php if (!empty($produtosCarrinho)): ?>
<div class="mobile-checkout-bar">
    <div class="mobile-total">
        <span class="mobile-total-label">Total (<?php echo count($produtosCarrinho); ?>
            <?php echo count($produtosCarrinho) === 1 ? 'produto' : 'produtos'; ?>)</span>
        <span class="mobile-total-valor"><?php echo number_format($totalGeral, 2, ',', '.'); ?> Kz</span>
    </div>

    <?php if (!empty($_SESSION['usuario_id'])): ?>
    <a href="checkout.php" class="mobile-btn-comprar">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        COMPRAR
    </a>
    <?php else: ?>
    <a href="/PA-E-COMMERCE/auth/login.php?from=checkout" class="mobile-btn-comprar"
        onclick="return confirm('Precisa iniciar sessão para finalizar a compra. Deseja continuar?')">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        COMPRAR
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>