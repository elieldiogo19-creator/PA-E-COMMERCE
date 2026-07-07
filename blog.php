<?php
session_start();
require __DIR__ . '/config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Blog - ' . $nomeProjeto;
$navbarMode = 'full';
$baseUrl = '';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';

?>

<main>
    <section class="section">
        <h1>Our Blogs</h1>
    
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>