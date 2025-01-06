<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #007bff, #ffffff);
            background-attachment: fixed;
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
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container img {
            display: block;
            margin: 0 auto 20px;
            max-width: 120px;
        }

        .login-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-weight: bold;
        }

        .login-container .form-control {
            border-radius: 10px;
            padding-left: 40px;
            transition: box-shadow 0.3s ease;
        }

        .login-container .form-control:focus {
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        .login-container .input-group-text {
            background: none;
            border: none;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password a {
            color: #007bff;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .alert {
            animation: fadeIn 0.5s ease;
        }
    </style>
</head>
<body>
<div class="login-container">
    <!-- Logo -->
    <img src="https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png" alt="Outdoor Secours Logo">

    <!-- Titre -->
    <h1>Connexion</h1>

    <!-- Message d'erreur -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Formulaire -->
    <form method="POST">
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" placeholder="Adresse e-mail" required>
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>

    <!-- Lien mot de passe oublié -->
    <div class="forgot-password">
        <a href="forgot_password.php">Mot de passe oublié ?</a>
    </div>
</div>
<script>
    // Validation de formulaire en temps réel
    document.querySelector('#email').addEventListener('input', function () {
        const email = this.value;
        if (!email.includes('@')) {
            this.setCustomValidity('Veuillez entrer un email valide.');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
</body>
</html>
