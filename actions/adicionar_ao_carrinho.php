<?php
session_start();
require __DIR__ . '/../config/db.php';

// Detecta se é requisição AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Pega ID (funciona via GET ou POST)
$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

// Validação
if ($id <= 0) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => false, 'mensagem' => 'Produto inválido']);
        exit;
    }
    header('Location: ../produtos.php');
    exit;
}

// Verifica se produto existe
$stmt = $pdo->prepare('SELECT id, nome FROM produtos WHERE id = ?');
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => false, 'mensagem' => 'Produto não encontrado']);
        exit;
    }
    header('Location: ../produtos.php');
    exit;
}

// Inicializa carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adiciona ou incrementa
if (isset($_SESSION['carrinho'][$id])) {
    $_SESSION['carrinho'][$id] += 1;
} else {
    $_SESSION['carrinho'][$id] = 1;
}

// Calcula total de itens
$totalItens = 0;
foreach ($_SESSION['carrinho'] as $qtd) {
    $totalItens += (int) $qtd;
}

// Resposta
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Produto adicionado ao carrinho',
        'produto_nome' => $produto['nome'],
        'total_itens' => $totalItens
    ]);
    exit;
}

// Se não é AJAX, redireciona pro carrinho (comportamento original)
header('Location: ../pages/carrinho.php');
exit;