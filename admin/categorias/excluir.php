<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}
require_once __DIR__ . '/../includes/admin_flash.php';
require_once __DIR__ . '/../../config/db.php';

$id = (int) ($_GET['id'] ?? 0);

// Verificar se possui produtos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = ?");
$stmt->execute([$id]);
$total = $stmt->fetchColumn();

if ($total > 0) {
    setFlash('erro', 'Categoria não pode ser excluída porque possui produtos associados.');
    header('Location: listar.php?erro=produtos');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
$stmt->execute([$id]);

setFlash('sucesso', 'Categoria excluída com sucesso.');
header('Location: listar.php');
exit;