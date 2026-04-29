<?php
session_start();
require __DIR__ . '/../config/db.php';

// Get Prod ID from URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// If invalid ID, back to produtos.php
if ($id <= 0) {
    header('Location: ../produtos.php');
    exit;
}

// Verifica se o produto existe no banco
$stmt = $pdo->prepare('SELECT id FROM produtos WHERE id = ?');
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: ../produtos.php');
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

// Depois de adicionar, redireciona para o carrinho
header('Location: ../pages/carrinho.php');
exit;