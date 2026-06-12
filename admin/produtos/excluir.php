<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

// Verificar se já foi vendido
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM itens_pedido 
    WHERE produto_id = ?
");
$stmt->execute([$id]);
$totalVendido = $stmt->fetchColumn();

if ($totalVendido > 0) {
    header('Location: listar.php?erro=vendido');
    exit;
}

// Buscar imagem antes de apagar
$stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($produto) {

    // Apagar imagem física
    $imagemFisica = __DIR__ . '/../../' . $produto['imagem'];

    if (file_exists($imagemFisica)) {
        unlink($imagemFisica);
    }

    // Apagar do banco
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: listar.php');
exit;