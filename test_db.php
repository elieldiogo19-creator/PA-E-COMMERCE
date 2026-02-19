<?php
require __DIR__ . '/config/db.php'; // inclui a conexão ($pdo)

echo "<h1>Teste de conexão com o banco</h1>";

try {
    // só para testar, pega a contagem de usuários
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM usuarios");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row['total'];

    echo "<p>Conexão OK. Usuários cadastrados: $total</p>";
} catch (PDOException $e) {
    echo "<p>Conexão estabelecida, mas a consulta falhou: " . $e->getMessage() . "</p>";
}