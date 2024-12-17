<?php
$host = 'localhost';
$dbname = 'user_system';
$username = 'root'; // PhpMyAdmin istifadəçi adı
$password = '12345678'; // PhpMyAdmin şifrəniz

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Verilənlər bazası ilə əlaqə qurulmadı: " . $e->getMessage());
}
?>
