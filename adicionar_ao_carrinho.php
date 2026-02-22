<?php
session_start();
require __DIR__ . '/config/db.php';

// Pega o id do produto da URL: adicionar_ao_carrinho.php?id=123
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: produtos.php');
    exit;
}

// Opcional: verificar se o produto existe mesmo no banco
$stmt = $pdo->prepare('SELECT id FROM produtos WHERE id = ?');
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    // Produto não existe
    header('Location: produtos.php');
    exit;
}

// Inicializa o carrinho se ainda não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Se já existe esse produto no carrinho, incrementa
if (isset($_SESSION['carrinho'][$id])) {
    $_SESSION['carrinho'][$id] += 1;
} else {
    $_SESSION['carrinho'][$id] = 1;
}

// Depois de adicionar, redireciona para o carrinho ou para a página anterior
header('Location: carrinho.php');
exit;