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
        /*body {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }*/
        /*body {
            background: linear-gradient(120deg, #ff7eb3, #ff758c, #f06292);
            color: white;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            background-size: 400% 400%; 
            animation: gradientAnimation 10s ease infinite;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }*/
        body {
    background: linear-gradient(to bottom, #007BFF, #FF4136, #28A745);
    /* Yuxarıdan aşağıya göy, qırmızı, yaşıl keçid */
    color: white;
    font-family: Arial, sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}


        .container {
            max-width: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .form-container {
            display: none;
        }
        .form-container.active {
            display: block;
        }
        .toggle-buttons {
            text-align: center;
            margin-bottom: 20px;
        }
        .toggle-buttons button {
            margin: 0 5px;
        }
        input {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn-primary, .btn-success {
            background-color: #6a11cb;
            border: none;
        }
        .btn-primary:hover, .btn-success:hover {
            background-color: #2575fc;
        }
        .alert {
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Register və Login</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <div class="toggle-buttons">
        <button class="btn btn-primary" id="show-register">Qeydiyyat</button>
        <button class="btn btn-secondary" id="show-login">Daxil Ol</button>
    </div>
    <!-- Register Form -->
    <div id="register-form" class="form-container">
        <h3 class="text-center">Qeydiyyat</h3>
        <form method="POST" action="index.php">
            <div class="mb-3">
                <input type="text" class="form-control" id="name" name="name" placeholder="Ad" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="E-poçt" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Şifrə" required>
            </div>
            <button type="submit" name="register" class="btn btn-success w-100">Qeydiyyat</button>
        </form>
    </div>
    <!-- Login Form -->
    <div id="login-form" class="form-container">
        <h3 class="text-center">Daxil Ol</h3>
        <form method="POST" action="index.php">
            <div class="mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="E-poçt" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Şifrə" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Daxil Ol</button>
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
