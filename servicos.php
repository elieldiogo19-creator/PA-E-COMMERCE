<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Serviços - ' . $nomeProjeto;
$navbarMode = 'full';
$baseUrl = '';
$pageCSS = 'servicos'; // Se tiver CSS específico
// ou não define se não tiver CSS extra além do global
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';

?>

<main>
    <section class="section">
        <h1>Nossos Serviços</h1>
    
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>