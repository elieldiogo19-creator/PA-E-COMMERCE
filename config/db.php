<?php
$host = 'localhost';
$dbname = 'pa_ecommerce'; // o MESMO nome que você criou no phpMyAdmin
$user = 'root';
$pass = ''; // padrão do XAMPP é usuário root sem senha

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na conexão com o banco: ' . $e->getMessage());
}