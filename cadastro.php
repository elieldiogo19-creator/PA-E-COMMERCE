<?php
require __DIR__ . '/config/db.php'; // conexão ($pdo)

$erros = [];
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome   = trim($_POST['nome']   ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $senha  = $_POST['senha']  ?? '';
  $senha2 = $_POST['senha2'] ?? '';

  // Validações básicas
  if ($nome === '' || $email === '' || $senha === '' || $senha2 === '') {
    $erros[] = 'Preencha todos os campos.';
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'E-mail inválido.';
  }

  if ($senha !== $senha2) {
    $erros[] = 'As senhas não conferem.';
  }

  if (strlen($senha) < 6) {
    $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
  }

  // Se não tem erro de validação, tenta salvar no banco
  if (empty($erros)) {
    try {
      // Verificar se já existe usuário com esse e-mail OU esse nome
      // verificar email
      $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
            $erros[] = 'Este e-mail já está em uso.';
      }

      // verificar nome
      $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE nome = ? LIMIT 1');
      $stmt->execute([$nome]);
      if  ($stmt->fetch()) {
              $erros[] = 'Este nome de utilizador já está em uso.';
      } else {
        // Gerar hash da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Inserir usuário
        $stmt = $pdo->prepare(
          'INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)'
        );
        $stmt->execute([$nome, $email, $senhaHash]);

        $sucesso = 'Cadastro realizado com sucesso! Você já pode fazer login.';
        header("Location: login.php?cadastro=ok"); exit;
      }
    } catch (PDOException $e) {
      $erros[] = 'Ocorreu um erro interno. Tente novamente.' . $e->getMessage();
    }
    
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Cadastro de Usuário</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <h1>Cadastro</h1>

  <?php if (!empty($erros)): ?>
    <div class="alert alert-erro">
      <ul>
        <?php foreach ($erros as $erro): ?>
          <li><?php echo htmlspecialchars($erro); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($sucesso): ?>
    <div class="alert alert-sucesso">
      <?php echo htmlspecialchars($sucesso); ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <label>
      Nome:
      <input type="text" name="nome" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
    </label>
    <br><br>

    <label>
      E-mail:
      <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
    </label>
    <br><br>

    <label>
      Senha:
      <input type="password" name="senha" required>
    </label>
    <br><br>

    <label>
      Confirmar senha:
      <input type="password" name="senha2" required>
    </label>
    <br><br>

    <button type="submit">Cadastrar</button>
  </form>
  <p>Já tem conta? <a href="login.php">Entrar</a></p>
</body>

</html>