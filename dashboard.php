<?php
session_start();

// İstifadəçi login olmayıbsa, yönləndir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body>
<div class="container mt-5">
    <h1>Xoş gəldiniz, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    <p>Bu sizin dashboard səhifənizdir.</p>
    <a href="logout.php" class="btn btn-danger">Çıxış</a>
</div>
</body>
</html>
