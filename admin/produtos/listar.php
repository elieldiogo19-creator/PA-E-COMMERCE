<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /PA-E-COMMERCE/admin/login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

$nomeProjeto = 'CANZALA, LDA.';
$pageTitle = 'Gerir Produtos - ' . $nomeProjeto;
$baseUrl = '../../';

try {
    $stmt = $pdo->query("
        SELECT id, nome, preco, imagem, criado_em
        FROM produtos
        ORDER BY id DESC
    ");

    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $produtos = [];
}

require_once __DIR__ . '/../../includes/header.php';
?>

<main>
    <section class="section">
        <h1>Gerir Produtos</h1>

        <p>
            <a href="adicionar.php">➕ Adicionar novo produto</a>
        </p>

        <?php if (empty($produtos)): ?>
            <p>Nenhum produto encontrado.</p>
        <?php else: ?>

            <table border="1" cellpadding="8" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($produtos as $produto): ?>

                        <tr>
                            <td><?= $produto['id'] ?></td>

                            <td>
                                <?php if (!empty($produto['imagem'])): ?>
                                    <img src="/PA-E-COMMERCE/<?= htmlspecialchars($produto['imagem']) ?>"
                                         width="60">
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($produto['nome']) ?></td>

                            <td>
                                <?= number_format($produto['preco'], 2, ',', '.') ?> Kz
                            </td>

                            <td>
                                <?= date('d/m/Y H:i', strtotime($produto['criado_em'])) ?>
                            </td>

                            <td>
                                <a href="editar.php?id=<?= $produto['id'] ?>">Editar</a> |
                                <a href="excluir.php?id=<?= $produto['id'] ?>"
                                   onclick="return confirm('Tem certeza que deseja excluir este produto?');">
                                   Excluir
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>