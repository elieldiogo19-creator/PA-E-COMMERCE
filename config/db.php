<?php
$host = 'localhost';
$dbname = 'pa_ecommerce'; // da same name of phpMyAdmin
$user = 'root';
$pass = ''; // XAMPP default user='root', pass=''

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na conexão com o banco: ' . $e->getMessage());
}