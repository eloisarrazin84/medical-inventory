<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Votre email ou mot de passe est incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .login-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeIn 1.2s ease;
        }
        .login-container img {
            margin-bottom: 20px;
            max-width: 100px;
        }
        .login-container h1 {
            color: #333;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 30px;
            padding-left: 40px;
        }
        .form-control:focus {
            border-color: #2575fc;
            box-shadow: 0 0 5px rgba(37, 117, 252, 0.5);
        }
        .form-control::placeholder {
            color: #aaa;
        }
        .form-group {
            position: relative;
        }
        .form-group i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #aaa;
        }
        .btn {
            background-color: #2575fc;
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            color: #fff;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #6a11cb;
        }
        .forgot-password a {
            color: #2575fc;
            text-decoration: none;
            font-size: 14px;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (max-width: 576px) {
            .login-container {
                padding: 20px;
            }
            .btn {
                font-size: 14px;
                padding: 10px;
            }
            .form-control {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <img src="https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png" alt="Outdoor Secours Logo">
    <h1>Bienvenue !</h1>
    <p class="mb-4">Connectez-vous pour accéder à l'espace de gestion Outdoor Secours.</p>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group mb-3">
            <i class="fas fa-envelope"></i>
            <input type="email" class="form-control" name="email" placeholder="Email" required>
        </div>
        <div class="form-group mb-3">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" name="password" placeholder="Mot de passe" required>
        </div>
        <button type="submit" class="btn w-100">Se connecter</button>
    </form>
    <div class="forgot-password mt-3">
        <a href="forgot_password.php">Mot de passe oublié ?</a>
    </div>
</div>
</body>
</html>
