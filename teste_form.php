<?php
// Sempre que trabalhar com formulário, começa assim:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lê os dados enviados pelo formulário
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Aqui só vamos mostrar na tela (depois vamos validar e usar banco)
    $mensagem = "Você enviou o email: $email e a senha: $senha";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Teste de formulário</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Formulário de teste</h1>

  <?php if (!empty($mensagem)): ?>
    <p><strong><?php echo htmlspecialchars($mensagem); ?></strong></p>
  <?php endif; ?>

  <form method="POST" action="">
    <label>
      E-mail:
      <input type="email" name="email" required>
    </label>
    <br><br>
    <label>
      Senha:
      <input type="password" name="senha" required>
    </label>
    <br><br>
    <button type="submit">Enviar</button>
  </form>
</body>
</html>