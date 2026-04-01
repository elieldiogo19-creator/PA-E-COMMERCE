<?php
$baseUrl = $baseUrl ?? '';
$nomeProjeto = $nomeProjeto ?? 'CANZALA LDA,';
$pageTitle = $pageTitle ?? $nomeProjeto;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/style.css">
</head>
<body>