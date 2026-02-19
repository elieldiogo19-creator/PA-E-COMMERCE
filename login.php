<?php
session_start();
require __DIR__ . '/config/db.php'; // conexão ($pdo)

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pode ser e-mail OU nome
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($login === '' || $senha === '') {
        $erros[] = 'Preencha login e senha.';
    }

    if (empty($erros)) {
        try {
            // Procura usuário cujo email OU nome bate com o que foi digitado
            $stmt = $pdo->prepare('
                SELECT id, nome, email, senha_hash
                FROM usuarios
                WHERE email = ? OR nome = ?
                LIMIT 1
            ');
            $stmt->execute([$login, $login]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
                // Guarda dados básicos na sessão
                $_SESSION['usuario_id']   = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                header('Location: index.php');
                exit;
            } else {
                $erros[] = 'Login ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro ao tentar fazer login: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Login</h1>

  <?php if (!empty($erros)): ?>
    <div class="alert alert-erro">
      <ul>
        <?php foreach ($erros as $erro): ?>
          <li><?php echo htmlspecialchars($erro); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <label>
      E-mail ou nome:
      <input type="text" name="login" required>
    </label>
    <br><br>

    <label>
      Senha:
      <input type="password" name="senha" required>
    </label>
    <br><br>

    <button type="submit">Entrar</button>
  </form>

  <p>Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
</body>
</html>