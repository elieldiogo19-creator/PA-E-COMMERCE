<?php
require_once __DIR__ . '/auth.php';

$nomeProjeto = 'CANZALA LDA';
$pageTitle = 'Dashboard Admin - ' . $nomeProjeto;
$baseUrl = '../';

require_once __DIR__ . '/../includes/header.php';
?>

<main>
    <section class="section">
        <h1>Painel do Administrador</h1>

        <p>
            Bem-vindo, 
            <strong><?= htmlspecialchars($_SESSION['admin_nome'], ENT_QUOTES, 'UTF-8') ?></strong>.
        </p>

        <ul>
            <li><a href="produtos/">Gerir produtos</a></li>
            <li><a href="pedidos/">Ver pedidos</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>