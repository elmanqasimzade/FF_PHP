<?php
require 'db.php';
session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        // Qeydiyyat üçün işlənəcək kod
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        
        if (strlen($password) < 8) {
            $message = "Şifrə ən azı 8 simvoldan ibarət olmalıdır.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Doğru e-poçt formatı daxil edin.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            try {
                $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashed_password]);
                $message = "Qeydiyyat uğurlu oldu! İndi login edə bilərsiniz.";
            } catch (PDOException $e) {
                $message = "Bu e-poçt artıq istifadə olunur.";
            }
        }
    } elseif (isset($_POST['login'])) {
        // Login üçün işlənəcək kod
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "E-poçt və ya şifrə yalnışdır.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Register və Login</title>
    <style>
        .form-container {
            display: none;
        }
        .form-container.active {
            display: block;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Register və Login</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <div class="text-center mb-4">
        <button class="btn btn-primary" id="show-register">Qeydiyyat</button>
        <button class="btn btn-secondary" id="show-login">Daxil Ol</button>
    </div>
    <!-- Register Form -->
    <div id="register-form" class="form-container">
        <h3>Qeydiyyat</h3>
        <form method="POST" action="index.php">
            <div class="mb-3">
                <label for="name" class="form-label">Ad:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-poçt:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifrə:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="register" class="btn btn-success">Qeydiyyat</button>
        </form>
    </div>
    <!-- Login Form -->
    <div id="login-form" class="form-container">
        <h3>Daxil Ol</h3>
        <form method="POST" action="index.php">
            <div class="mb-3">
                <label for="email" class="form-label">E-poçt:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifrə:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Daxil Ol</button>
        </form>
    </div>
</div>
<script>
    const showRegister = document.getElementById('show-register');
    const showLogin = document.getElementById('show-login');
    const registerForm = document.getElementById('register-form');
    const loginForm = document.getElementById('login-form');

    showRegister.addEventListener('click', () => {
        registerForm.classList.add('active');
        loginForm.classList.remove('active');
    });

    showLogin.addEventListener('click', () => {
        loginForm.classList.add('active');
        registerForm.classList.remove('active');
    });

    // Default olaraq login formu göstərilsin
    showLogin.click();
</script>
</body>
</html>
