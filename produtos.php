<?php
session_start();
require __DIR__ . '/config/db.php';

try {
    $stmt = $pdo->query('SELECT id, nome, descricao, preco, imagem FROM produtos ORDER BY criado_em DESC');
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao buscar produtos: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Produtos</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <!-- HEADER simples (teu mano depois deixa bonito) -->
  <header class="header">
    <h1>Produtos</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="produtos.php">Produtos</a>
      <a href="carrinho.php">Carrinho</a>
      <a href="login.php">Login</a>
    </nav>
  </header>

  <main>
    <h2>Lista de produtos</h2>

    <?php if (empty($produtos)): ?>
      <p>Nenhum produto cadastrado.</p>
    <?php else: ?>
      <div class="lista-produtos">
        <?php foreach ($produtos as $produto): ?>
          <article class="produto">
            <?php if (!empty($produto['imagem'])): ?>
              <img
                src="<?php echo htmlspecialchars($produto['imagem']); ?>"
                alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                style="max-width: 150px;"
              >
            <?php endif; ?>

            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

            <p>
              <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
            </p>

            <p><strong>
               <?php echo number_format($produto['preco'], 2, ',', '.'); ?> AOA
            </strong></p>

            <!-- Botão/link de carrinho (vamos implementar depois) -->
            <a href="adicionar_ao_carrinho.php?id=<?php echo $produto['id']; ?>">
              Adicionar ao carrinho
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>