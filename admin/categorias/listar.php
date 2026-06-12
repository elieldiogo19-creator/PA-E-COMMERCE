<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$stmt = $pdo->query("
    SELECT c.*, 
           COUNT(p.id) as total_produtos
    FROM categorias c
    LEFT JOIN produtos p ON p.categoria_id = c.id
    GROUP BY c.id
    ORDER BY c.nome ASC
");

$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../includes/header.php';
?>

<main>
<section class="section">

<h1>Categorias</h1>

<p><a href="adicionar.php">➕ Nova Categoria</a></p>

<table border="1" cellpadding="8" width="100%">
<thead>
<tr>
    <th>ID</th>
    <th>Nome</th>
    <th>Produtos</th>
    <th>Ações</th>
</tr>
</thead>
<tbody>

<?php foreach ($categorias as $cat): ?>
<tr>
    <td><?= $cat['id'] ?></td>
    <td><?= htmlspecialchars($cat['nome']) ?></td>
    <td><?= $cat['total_produtos'] ?></td>
    <td>
        <a href="editar.php?id=<?= $cat['id'] ?>">Editar</a> |
        <a href="excluir.php?id=<?= $cat['id'] ?>"
           onclick="return confirm('Excluir categoria?');">
           Excluir
        </a>
    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</section>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>