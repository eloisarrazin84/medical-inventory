<?php
include 'includes/db.php';
include 'includes/send_email.php';
require_once '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Vérifier si l'e-mail existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));

        // Insérer le token dans la table
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->execute([$email, $token]);

        // Envoyer un e-mail avec le lien de réinitialisation
       $reset_link = "https://gestion.outdoorsecours.fr/reset_password.php?token=$token";
$subject = "Réinitialisation de votre mot de passe";
$content = "
    <h1 style='text-align: center;'>Réinitialisation de votre mot de passe</h1>
    <p style='text-align: center;'>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p>
    <div style='text-align: center;'>
        <a href='$reset_link' style='color: #ffffff; background-color: #007bff; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Réinitialiser le mot de passe</a>
    </div>
    <p style='text-align: center; margin-top: 20px;'>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail.</p>
";
$email_status = sendEmail($email, $subject, $content);

        if ($email_status === true) {
            $success = "Un e-mail de réinitialisation a été envoyé.";
        } else {
            $error = $email_status;
        }
    } else {
        $error = "Aucun utilisateur trouvé avec cet e-mail.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Mot de Passe Oublié</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Mot de Passe Oublié</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse E-mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>
</body>
</html>
