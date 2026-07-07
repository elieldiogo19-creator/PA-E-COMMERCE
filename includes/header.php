<?php
$baseUrl = $baseUrl ?? '';
$nomeProjeto = $nomeProjeto ?? 'CANZALA, LDA.';
$pageTitle = $pageTitle ?? $nomeProjeto;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/style.css">
    <!-- CSS Global (Navbar, Footer, Base) -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/global.css">

    <!-- CSS específico da página (ex: home.css, produtos.css) -->
    <?php if (isset($pageCSS)): ?>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/<?php echo $pageCSS; ?>.css">
    <?php endif; ?>
</head>

<body>